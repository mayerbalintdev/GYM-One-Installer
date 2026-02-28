<?php
// ============================================================
//  GYM One Installer – Helper
// ============================================================

require_once __DIR__ . '/config.php';

function writeLog(string $stage, string $message): void
{
    $line = sprintf("[%s] [%s] %s\n", date('Y-m-d H:i:s'), strtoupper($stage), $message);
    file_put_contents(LOG_FILE, $line, FILE_APPEND | LOCK_EX);
}

function getLocationByIP(string $ip): string
{
    if (in_array($ip, ['127.0.0.1', '::1'], true)) {
        return 'Localhost';
    }

    $context  = stream_context_create(['http' => ['timeout' => 3]]);
    $response = @file_get_contents("http://ip-api.com/json/{$ip}", false, $context);

    if ($response) {
        $data = json_decode($response, true);
        if (isset($data['status']) && $data['status'] === 'success') {
            return "{$data['country']}, {$data['regionName']}, {$data['city']}";
        }
    }

    return 'Unknown Location';
}
function logServerData(): void
{
    $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $location = getLocationByIP($clientIp);

    writeLog('Starter', sprintf(
        'Server IP: %s | OS: %s | PHP: %s | Client IP: %s | Location: %s',
        $_SERVER['SERVER_ADDR'] ?? 'Unknown',
        php_uname(),
        phpversion(),
        $clientIp,
        $location
    ));
}

function loadTranslations(): array
{
    $langFiles = glob(LANG_DIR . '*.json');
    $languages = [];
    foreach ($langFiles as $file) {
        $code = strtoupper(pathinfo($file, PATHINFO_FILENAME));
        $languages[$code] = $code;
    }

    if (isset($_GET['lang'])) {
        $requested = preg_replace('/[^a-zA-Z]/', '', $_GET['lang']);
        if (file_exists(LANG_DIR . "{$requested}.json")) {
            $_SESSION['lang'] = $requested;
        }
    }

    $lang     = $_SESSION['lang'] ?? 'GB';
    $langFile = LANG_DIR . "{$lang}.json";

    if (!file_exists($langFile)) {
        $langFile = LANG_DIR . 'GB.json';
        if (!file_exists($langFile)) {
            die('Language file not found.');
        }
    }

    $translations = json_decode(file_get_contents($langFile), true) ?? [];

    return [
        'lang'         => $lang,
        'languages'    => $languages,
        'translations' => $translations,
    ];
}


function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function requireStage(string $currentStage): void
{
    $order          = STAGE_ORDER;
    $currentIndex   = array_search($currentStage, $order, true);

    if ($currentIndex === false || $currentIndex === 0) {
        return; 
    }

    $previousStage  = $order[$currentIndex - 1];
    $completed      = $_SESSION['completed_stages'] ?? [];

    if (!in_array($previousStage, $completed, true)) {
        header('Location: /' . $order[0] . '/');
        exit();
    }
}

function completeStage(string $stage): void
{
    if (!isset($_SESSION['completed_stages'])) {
        $_SESSION['completed_stages'] = [];
    }
    if (!in_array($stage, $_SESSION['completed_stages'], true)) {
        $_SESSION['completed_stages'][] = $stage;
    }
}

function setupSubscriptionReminderJob(string $scriptPath, string $time = '08:00'): void
{
    [$hour, $minute] = explode(':', $time);

    if (stripos(PHP_OS, 'WIN') === 0) {
        $phpPath  = trim((string) shell_exec('where php'));
        $taskName = 'GYMOneReminder';
        $cmd      = "schtasks /Create /SC DAILY /TN {$taskName} /TR \"\\\"{$phpPath}\\\" \\\"{$scriptPath}\\\"\" /ST {$time} /F";
        exec($cmd);
    } else {
        $phpPath = trim((string) shell_exec('which php')) ?: '/usr/bin/php';
        $cronJob = intval($minute) . ' ' . intval($hour) . " * * * {$phpPath} {$scriptPath}";

        $currentCrontab = (string) shell_exec('crontab -l -u www-data 2>/dev/null');

        if (strpos($currentCrontab, $scriptPath) === false) {
            $tmpFile = tempnam(sys_get_temp_dir(), 'cron');
            file_put_contents($tmpFile, $currentCrontab . PHP_EOL . $cronJob . PHP_EOL);
            exec("crontab -u www-data {$tmpFile}");
            unlink($tmpFile);
        }
    }
}
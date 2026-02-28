<?php
session_start();

require_once __DIR__ . '/../helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

if (empty($_POST['servername']) || empty($_POST['username'])) {
    header('Location: index.php?error=missing_fields');
    exit();
}

$servername = trim($_POST['servername']);
$username   = trim($_POST['username']);
$password   = $_POST['password'] ?? '';
$dbname     = preg_replace('/[^a-zA-Z0-9_]/', '', trim($_POST['dbname'] ?: 'gymone'));

if (empty($dbname)) {
    $dbname = 'gymone';
}

try {
    $conn = new mysqli($servername, $username, $password);
    if ($conn->connect_error) {
        throw new Exception('Connection error: ' . $conn->connect_error);
    }

    $stmt = $conn->prepare("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    if (!$stmt || !$stmt->execute()) {
        throw new Exception('Database creation failed: ' . $conn->error);
    }
    $stmt->close();

    $conn->select_db($dbname);
    if ($conn->error) {
        throw new Exception('Cannot select database: ' . $conn->error);
    }

    writeLog('STAGE3', "✅ DB connected – host: {$servername}, db: {$dbname}");
    $envData = implode("\n", [
        'DB_SERVER='   . $servername,
        'DB_USERNAME=' . $username,
        'DB_PASSWORD=' . $password,
        'DB_NAME='     . $dbname,
        '',
    ]);

    if (!is_dir(TEMP_DIR)) {
        mkdir(TEMP_DIR, 0750, true);
    }

    file_put_contents(TEMP_DIR . '.env', $envData, LOCK_EX);

    $scriptPath = realpath(__DIR__ . '/../crontab/send_reminders.php');
    if ($scriptPath) {
        setupSubscriptionReminderJob($scriptPath, '08:00');
        writeLog('STAGE3', '⏰ Cron job beállítva: ' . $scriptPath);
    }

    $conn->close();

    header('Location: ../stage4/');
    exit();

} catch (Exception $e) {
    writeLog('STAGE3', '❌ ' . $e->getMessage());
    header('Location: index.php?error=connection_failed');
    exit();
}
<?php
session_start();

require_once __DIR__ . '/../helpers.php';

$langData     = loadTranslations();
$lang         = $langData['lang'];
$languages    = $langData['languages'];
$translations = $langData['translations'];
$assetBase    = '../';

$envFile = TEMP_DIR . '.env';

function readEnvFile(string $path): array
{
    $data = [];
    if (!file_exists($path)) {
        return $data;
    }
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $key   = trim($key);
        $value = trim($value);
        if ($key !== '') {
            $data[$key] = $value;
        }
    }
    return $data;
}

function writeEnvFile(string $path, array $data): bool
{
    $lines = [];
    foreach ($data as $key => $value) {
        $lines[] = $key . '=' . $value;
    }
    return file_put_contents($path, implode("\n", $lines) . "\n", LOCK_EX) !== false;
}

$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $smtpHost       = mb_substr(trim($_POST['smtp_host']       ?? ''), 0, 255);
    $smtpPort       = max(1, min(65535, (int)($_POST['smtp_port'] ?? 587)));
    $smtpEncryption = in_array($_POST['smtp_encryption'] ?? '', ['TLS', 'SSL']) 
                        ? $_POST['smtp_encryption'] 
                        : 'TLS';
    $smtpUsername   = mb_substr(trim($_POST['smtp_username'] ?? ''), 0, 255);
    $smtpPassword   = $_POST['smtp_password'] ?? '';

    $envData = readEnvFile($envFile);

    $envData['MAIL_HOST']       = $smtpHost;
    $envData['MAIL_PORT']       = (string)$smtpPort;
    $envData['MAIL_USERNAME']   = $smtpUsername;
    $envData['MAIL_PASSWORD']   = $smtpPassword;
    $envData['MAIL_ENCRYPTION'] = $smtpEncryption;

    if (writeEnvFile($envFile, $envData)) {
        writeLog('STAGE8', '✅ SMTP configuration saved.');
        header('Location: ../lastone/');
        exit();
    } else {
        writeLog('STAGE8', '❌ Failed to write SMTP data to .env file.');
        $errorMessage = $translations['unexpected-error'] ?? 'Failed to save configuration.';
    }
}

$envData = readEnvFile($envFile);
?>
<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container justify-content-center">
  <div class="row text-center justify-content-center">

    <div class="col-md-8 mx-auto text-center mb-5">
      <h1 class="mb-3 fw-semibold"><?php echo e($translations['mailpage'] ?? 'Mail Settings'); ?></h1>
      <p class="lead mb-4 fs-4">
        <?php echo e($translations['installerVersion'] ?? ''); ?> &ndash; <?php echo e(INSTALLER_VERSION); ?>
      </p>
    </div>

    <div class="col-md-8 mx-auto text-center mb-5">
      <div class="card">
        <div class="card-body">

          <div class="alert" role="alert">
            <div class="d-inline-block fs-1 lh-1 text-danger bg-danger bg-opacity-25 p-4 rounded-pill mb-2">
              <i class="bi bi-exclamation"></i>
            </div>
            <p class="lead"><?php echo e($translations['mail-installer'] ?? ''); ?></p>
          </div>

          <?php if ($errorMessage): ?>
            <div class="alert alert-danger">
              <i class="bi bi-x-circle-fill me-2"></i><?php echo e($errorMessage); ?>
            </div>
          <?php endif; ?>

          <form method="POST" autocomplete="off">
            <div class="row text-start">

              <div class="col-md-6 mb-3">
                <label for="smtp_host" class="form-label">
                  SMTP <?php echo e($translations['host'] ?? 'Host'); ?>:
                </label>
                <input type="text" class="form-control" id="smtp_host" name="smtp_host"
                       value="<?php echo e($envData['MAIL_HOST'] ?? ''); ?>"
                       placeholder="smtp.gmail.com">
              </div>

              <div class="col-md-3 mb-3">
                <label for="smtp_port" class="form-label">
                  SMTP <?php echo e($translations['port'] ?? 'Port'); ?>:
                </label>
                <input type="number" class="form-control" id="smtp_port" name="smtp_port"
                       min="1" max="65535"
                       value="<?php echo e($envData['MAIL_PORT'] ?? '587'); ?>">
              </div>

              <div class="col-md-3 mb-3">
                <label for="smtp_encryption" class="form-label">
                  SMTP <?php echo e($translations['encry'] ?? 'Encryption'); ?>:
                </label>
                <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                  <option value="TLS" <?php echo ($envData['MAIL_ENCRYPTION'] ?? 'TLS') === 'TLS' ? 'selected' : ''; ?>>TLS</option>
                  <option value="SSL" <?php echo ($envData['MAIL_ENCRYPTION'] ?? '') === 'SSL' ? 'selected' : ''; ?>>SSL</option>
                </select>
              </div>

              <div class="col-md-6 mb-3">
                <label for="smtp_username" class="form-label">
                  SMTP <?php echo e($translations['username'] ?? 'Username'); ?>:
                </label>
                <input type="text" class="form-control" id="smtp_username" name="smtp_username"
                       value="<?php echo e($envData['MAIL_USERNAME'] ?? ''); ?>"
                       placeholder="you@example.com" autocomplete="off">
              </div>

              <div class="col-md-6 mb-3">
                <label for="smtp_password" class="form-label">
                  SMTP <?php echo e($translations['password'] ?? 'Password'); ?>:
                </label>
                <input type="password" class="form-control" id="smtp_password" name="smtp_password"
                       value="<?php echo e($envData['MAIL_PASSWORD'] ?? ''); ?>"
                       autocomplete="new-password">
              </div>

            </div>

            <div class="text-center mt-2">
              <button type="submit" class="btn btn-primary btn-lg">
                <?php echo e($translations['save'] ?? 'Save'); ?>
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
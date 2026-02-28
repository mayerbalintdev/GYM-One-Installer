<?php
session_start();

require_once __DIR__ . '/../helpers.php';

$langData     = loadTranslations();
$lang         = $langData['lang'];
$languages    = $langData['languages'];
$translations = $langData['translations'];
$assetBase    = '../';

$requiredExtensions = [
    'bcmath', 'calendar', 'readline', 'mysqlnd', 'bz2',
    'openssl', 'curl', 'fileinfo', 'gd', 'gettext',
    'mbstring', 'exif', 'mysqli', 'pdo_mysql', 'ftp',
    'zip',
];

$minPhpVersion     = '8.0';
$currentPhpVersion = phpversion();
$loadedExtensions  = get_loaded_extensions();
$missingExtensions = array_diff($requiredExtensions, $loadedExtensions);
$phpOk             = version_compare($currentPhpVersion, $minPhpVersion) >= 0;

$dbHost = $dbUser = $dbPass = $dbName = '';

$envFile = TEMP_DIR . '.env';
if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        switch (trim($key)) {
            case 'DB_SERVER':   $dbHost = trim($value); break;
            case 'DB_USERNAME': $dbUser = trim($value); break;
            case 'DB_PASSWORD': $dbPass = trim($value); break;
            case 'DB_NAME':     $dbName = trim($value); break;
        }
    }
}

function checkDbConnection(string $host, string $user, string $pass, string $db): bool
{
    $conn = @new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        return false;
    }
    $conn->close();
    return true;
}

$dbConnected = checkDbConnection($dbHost, $dbUser, $dbPass, $dbName);

if (empty($_SESSION['stage4_logged'])) {
    writeLog('STAGE4', '🔍 Requirements check started.');
    writeLog('STAGE4', $phpOk
        ? "✅ PHP {$currentPhpVersion} >= {$minPhpVersion}"
        : "❌ PHP {$currentPhpVersion} < {$minPhpVersion} (minimum required)"
    );
    foreach ($requiredExtensions as $ext) {
        $status = in_array($ext, $loadedExtensions, true) ? '✅' : '❌';
        writeLog('STAGE4', "{$status} Extension: {$ext}");
    }
    writeLog('STAGE4', $dbConnected
        ? '✅ Database connection successful.'
        : '❌ Database connection failed.'
    );
    $_SESSION['stage4_logged'] = true;
}

$allOk = $phpOk && empty($missingExtensions) && $dbConnected;
?>
<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container justify-content-center">
  <div class="row text-center justify-content-center">

    <div class="col-md-8 mx-auto text-center mb-5">
      <h1 class="mb-3 fw-semibold"><?php echo e($translations['requirements'] ?? 'Requirements'); ?></h1>
      <p class="lead mb-4 fs-4">
        <?php echo e($translations['installerVersion'] ?? ''); ?> &ndash; <?php echo e(INSTALLER_VERSION); ?>
      </p>
    </div>

    <div class="col-md-8 mx-auto mb-5">
      <div class="card">
        <div class="card-body">
          <ul class="list-group list-group-flush">

            <li class="list-group-item d-flex justify-content-between align-items-center">
              <?php echo e($translations['php-version'] ?? 'PHP Version'); ?>
              (<?php echo e($currentPhpVersion); ?>)
              <span class="badge <?php echo $phpOk ? 'bg-success' : 'bg-danger'; ?>">
                <i class="bi <?php echo $phpOk ? 'bi-check-lg' : 'bi-x-lg'; ?>"></i>
              </span>
            </li>

            <?php foreach ($requiredExtensions as $ext): ?>
              <?php $ok = in_array($ext, $loadedExtensions, true); ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <code><?php echo e($ext); ?></code>
                &ndash; <?php echo e($translations['extension'] ?? 'extension'); ?>
                <span class="badge <?php echo $ok ? 'bg-success' : 'bg-danger'; ?>">
                  <i class="bi <?php echo $ok ? 'bi-check-lg' : 'bi-x-lg'; ?>"></i>
                </span>
              </li>
            <?php endforeach; ?>

            <li class="list-group-item d-flex justify-content-between align-items-center">
              <?php echo e($translations['success-conn'] ?? 'Database connection'); ?>
              <span class="badge <?php echo $dbConnected ? 'bg-success' : 'bg-danger'; ?>">
                <i class="bi <?php echo $dbConnected ? 'bi-check-lg' : 'bi-x-lg'; ?>"></i>
              </span>
            </li>

          </ul>
        </div>

        <div class="card-footer text-center">
          <?php if ($allOk): ?>
            <a href="../stage5/" class="btn btn-primary">
              <?php echo e($translations['continue'] ?? 'Continue'); ?>
            </a>
          <?php else: ?>
            <button class="btn btn-secondary" disabled>
              <?php echo e($translations['continue'] ?? 'Continue'); ?>
            </button>
            <?php if (!$phpOk): ?>
              <p class="mt-2 text-danger small">
                <?php echo e($translations['min-php'] ?? 'Minimum PHP version'); ?>:
                <?php echo e($minPhpVersion); ?>
              </p>
            <?php endif; ?>
            <?php if (!empty($missingExtensions)): ?>
              <p class="mt-2 text-danger small">
                <?php echo e($translations['missing-extensions'] ?? 'Missing extensions'); ?>: <code><?php echo e(implode(', ', $missingExtensions)); ?></code>
              </p>
            <?php endif; ?>
            <?php if (!$dbConnected): ?>
              <p class="mt-2 text-danger small">
                <?php echo e($translations['notabletoconnectdatabase'] ?? 'Database connection failed'); ?>.
                <a href="../stage3/">Vissza a beállításhoz</a>
              </p>
            <?php endif; ?>
          <?php endif; ?>
        </div>

      </div>
    </div>

  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
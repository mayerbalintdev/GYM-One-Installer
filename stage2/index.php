<?php
// ============================================================
//  GYM One Installer – Stage 2: Jogosultság ellenőrzés
// ============================================================
session_start();

require_once __DIR__ . '/../helpers.php';

$langData     = loadTranslations();
$lang         = $langData['lang'];
$languages    = $langData['languages'];
$translations = $langData['translations'];
$assetBase    = '../';
$parentDir = dirname(__DIR__);

// Az egész gyökérmappának írhatónak kell lennie a telepítéshez
$allWritable = is_writable($parentDir);
$problemDirs = $allWritable ? [] : [$parentDir];
?>
<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container justify-content-center">
  <div class="row text-center justify-content-center">

    <div class="col-md-8 mx-auto text-center mb-5">
      <h1 class="mb-3 fw-semibold"><?php echo e($translations['777codepage'] ?? ''); ?></h1>
      <p class="lead mb-4 fs-4">
        <?php echo e($translations['installerVersion'] ?? ''); ?> &ndash; <?php echo e(INSTALLER_VERSION); ?>
      </p>
      <p class="lead"><?php echo e($translations['777codetext'] ?? ''); ?></p>
    </div>

    <div class="col-md-8 mx-auto text-center mb-5">
      <div class="card">
        <div class="card-body mt-2">

          <?php if (!$allWritable): ?>
            <p class="alert alert-warning">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <?php echo e($translations['notreq'] ?? ''); ?>
            </p>
            <p class="text-muted small"><?php echo e($translations['consolerun'] ?? ''); ?></p>
            <?php foreach ($problemDirs as $dir): ?>
              <pre class="bg-dark text-light p-3 rounded text-start mb-1">sudo chmod -R 777 <?php echo e($dir); ?></pre>
              <pre class="bg-dark text-light p-3 rounded text-start">sudo chown -R www-data:www-data <?php echo e($dir); ?></pre>
            <?php endforeach; ?>
          <?php else: ?>
            <p class="alert alert-success">
              <i class="bi bi-check-circle-fill me-2"></i>
              <?php echo e($translations['goodreq'] ?? ''); ?>
            </p>
          <?php endif; ?>

          <button id="continueButton" class="btn btn-primary mt-4"
            <?php echo !$allWritable ? 'disabled' : ''; ?>>
            <?php echo e($translations['continue'] ?? 'Continue'); ?>
          </button>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
  document.getElementById('continueButton').addEventListener('click', function () {
    window.location.href = '../stage3/';
  });
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
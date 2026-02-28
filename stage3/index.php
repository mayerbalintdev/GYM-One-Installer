<?php
session_start();

require_once __DIR__ . '/../helpers.php';

$langData     = loadTranslations();
$lang         = $langData['lang'];
$languages    = $langData['languages'];
$translations = $langData['translations'];
$assetBase    = '../';

$errorMessage = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'connection_failed':
            $errorMessage = $translations['notabletoconnectdatabase'] ?? 'Connection failed.';
            break;
        case 'missing_fields':
            $errorMessage = $translations['allform'] ?? 'Fill all fields.';
            break;
        default:
            $errorMessage = $translations['unexpected-error'] ?? 'Unexpected error.';
    }
}
?>
<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container justify-content-center">
  <div class="row text-center justify-content-center">

    <div class="col-md-8 mx-auto text-center mb-5">
      <h1 class="mb-3 fw-semibold"><?php echo e($translations['database-header'] ?? ''); ?></h1>
      <p class="lead mb-4 fs-4">
        <?php echo e($translations['installerVersion'] ?? ''); ?> &ndash; <?php echo e(INSTALLER_VERSION); ?>
      </p>
    </div>

    <div class="col-md-8 mx-auto text-center mb-5">
      <div class="card">
        <div class="card-body text-start">

          <?php if ($errorMessage): ?>
            <div class="alert alert-danger" role="alert">
              <i class="bi bi-x-circle-fill me-2"></i>
              <?php echo e($errorMessage); ?>
            </div>
          <?php endif; ?>

          <form action="process_form.php" method="post" autocomplete="off">

            <div class="mb-3">
              <label for="servername" class="form-label">
                <?php echo e($translations['db-host'] ?? 'Host'); ?>
              </label>
              <input type="text" class="form-control" id="servername"
                     name="servername" placeholder="localhost" required>
            </div>

            <div class="mb-3">
              <label for="username" class="form-label">
                <?php echo e($translations['db-username'] ?? 'Username'); ?>
              </label>
              <input type="text" class="form-control" id="username"
                     name="username" required autocomplete="username">
            </div>

            <div class="mb-3">
              <label for="password" class="form-label">
                <?php echo e($translations['db-password'] ?? 'Password'); ?>
              </label>
              <input type="password" class="form-control" id="password"
                     name="password" autocomplete="current-password">
            </div>

            <div class="mb-3">
              <label for="dbname" class="form-label">
                <?php echo e($translations['db-name'] ?? 'Database name'); ?>
              </label>
              <input type="text" class="form-control" id="dbname"
                     name="dbname" placeholder="gymone">
              <div class="form-text">
                <?php echo $translations['db-name-help'] ?? ''; ?>
              </div>
            </div>

            <div class="text-center mt-4">
              <button type="submit" class="btn btn-primary btn-lg text-white">
                <?php echo e($translations['continue'] ?? 'Continue'); ?>
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>

  </div>
</div>

<?php include __DIR__ . '/../partials/footer.php'; ?>
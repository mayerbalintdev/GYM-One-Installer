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
    $errorMessage = match($_GET['error']) {
        'missing_fields' => $translations['allform']          ?? 'Please fill all required fields.',
        'env_write'      => $translations['unexpected-error'] ?? 'Failed to write configuration.',
        default          => $translations['unexpected-error'] ?? 'Unexpected error.',
    };
}
?>
<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container justify-content-center">
  <div class="row text-center justify-content-center">

    <div class="col-md-8 mx-auto text-center mb-5">
      <h1 class="mb-3 fw-semibold"><?php echo e($translations['business-data'] ?? 'Business Data'); ?></h1>
      <p class="lead mb-4 fs-4">
        <?php echo e($translations['installerVersion'] ?? ''); ?> &ndash; <?php echo e(INSTALLER_VERSION); ?>
      </p>
    </div>

    <div class="col-md-8 mx-auto text-start mb-5">
      <div class="card">
        <div class="card-body">

          <?php if ($errorMessage): ?>
            <div class="alert alert-danger">
              <i class="bi bi-x-circle-fill me-2"></i><?php echo e($errorMessage); ?>
            </div>
          <?php endif; ?>

          <form action="add_to_env.php" method="POST" autocomplete="off">

            <div class="row">
              <div class="col-sm-10">
                <div class="mb-3">
                  <label for="businessName" class="form-label">
                    <?php echo e($translations['gym-name'] ?? 'Gym name'); ?>:
                  </label>
                  <input type="text" class="form-control" id="businessName"
                         name="businessName" minlength="2" maxlength="100" required>
                </div>
              </div>
              <div class="col-sm-2">
                <div class="mb-3">
                  <label for="langCode" class="form-label">
                    <?php echo e($translations['lang'] ?? 'Language'); ?>:
                  </label>
                  <select class="form-select" id="langCode" name="langCode" required>
                    <?php foreach ($languages as $code): ?>
                      <option value="<?php echo e($code); ?>">
                        <?php echo e($translations[$code] ?? $code); ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-4">
                <div class="mb-3">
                  <label for="country" class="form-label">
                    <?php echo e($translations['country'] ?? 'Country'); ?>:
                  </label>
                  <input type="text" class="form-control" id="country"
                         name="country" maxlength="100" required>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="mb-3">
                  <label for="city" class="form-label">
                    <?php echo e($translations['city'] ?? 'City'); ?>:
                  </label>
                  <input type="text" class="form-control" id="city"
                         name="city" maxlength="100" required>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="mb-3">
                  <label for="street" class="form-label">
                    <?php echo e($translations['street'] ?? 'Street'); ?>:
                  </label>
                  <input type="text" class="form-control" id="street"
                         name="street" maxlength="100" required>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-4">
                <div class="mb-3">
                  <label for="houseNumber" class="form-label">
                    <?php echo e($translations['hause-no'] ?? 'House No.'); ?>:
                  </label>
                  <input type="text" class="form-control" id="houseNumber"
                         name="houseNumber" maxlength="20" required>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="mb-3">
                  <label for="currency" class="form-label">
                    <?php echo e($translations['currency'] ?? 'Currency'); ?>:
                  </label>
                  <input type="text" class="form-control" id="currency"
                         name="currency" maxlength="10" placeholder="EUR / € " required>
                </div>
              </div>
              <div class="col-sm-4">
                <div class="mb-3">
                  <label for="capacity" class="form-label">
                    <?php echo e($translations['capacityenv'] ?? 'Capacity'); ?>:
                  </label>
                  <input type="number" class="form-control" id="capacity"
                         name="capacity" min="10" max="99999" required>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                <div class="mb-3">
                  <label for="phoneno" class="form-label">
                    <?php echo e($translations['fno'] ?? 'Phone number'); ?>:
                  </label>
                  <input type="tel" class="form-control" id="phoneno"
                         name="phoneno" maxlength="30" required>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                <div class="mb-3">
                  <label for="metakey" class="form-label">
                    <?php echo e($translations['metakeys'] ?? 'Meta keywords'); ?>:
                  </label>
                  <input type="text" class="form-control" id="metakey"
                         name="metakey" maxlength="255" required>
                  <small><code><?php echo e($translations['metakeys-separeate'] ?? 'Separate with commas'); ?></code></small>
                </div>
              </div>
            </div>

            <div class="row">
              <div class="col-sm-12">
                <div class="mb-3">
                  <label for="description" class="form-label">
                    <?php echo e($translations['websitedescription'] ?? 'Website description'); ?>:
                  </label>
                  <textarea class="form-control" id="description" name="description"
                            rows="3" minlength="20" maxlength="500" required></textarea>
                </div>
              </div>
            </div>

            <div class="text-center mt-3">
              <button type="submit" class="btn btn-primary btn-lg">
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
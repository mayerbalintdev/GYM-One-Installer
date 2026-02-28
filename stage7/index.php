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
        'password_mismatch' => $translations['passwordspattern_5']  ?? 'Passwords do not match.',
        'db_error'          => $translations['unexpected-error']     ?? 'Database error.',
        'missing_fields'    => $translations['allform']              ?? 'Fill all required fields.',
        default             => $translations['unexpected-error']     ?? 'Unexpected error.',
    };
}
?>
<?php include __DIR__ . '/../partials/header.php'; ?>

<div class="container justify-content-center">
  <div class="row text-center justify-content-center">

    <div class="col-md-8 mx-auto text-center mb-5">
      <h1 class="mb-3 fw-semibold"><?php echo e($translations['boss-register'] ?? 'Admin Registration'); ?></h1>
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
            <p class="lead"><?php echo e($translations['first-member'] ?? ''); ?></p>
          </div>

          <?php if ($errorMessage): ?>
            <div class="alert alert-danger">
              <i class="bi bi-x-circle-fill me-2"></i><?php echo e($errorMessage); ?>
            </div>
          <?php endif; ?>

          <form action="reg.php" method="POST" autocomplete="off">

            <div class="mb-3 text-start">
              <label for="username" class="form-label">
                <?php echo e($translations['username'] ?? 'Username'); ?>
              </label>
              <input type="text" class="form-control" id="username"
                     name="username" minlength="5" maxlength="50" required>
            </div>

            <div class="mb-3 text-start">
              <div class="row">
                <div class="col">
                  <label for="firstname" class="form-label">
                    <?php echo e($translations['firstname'] ?? 'First name'); ?>
                  </label>
                  <input type="text" class="form-control" id="firstname"
                         name="firstname" maxlength="50" required>
                </div>
                <div class="col">
                  <label for="lastname" class="form-label">
                    <?php echo e($translations['lastname'] ?? 'Last name'); ?>
                  </label>
                  <input type="text" class="form-control" id="lastname"
                         name="lastname" maxlength="50" required>
                </div>
              </div>
            </div>

            <div class="mb-3 text-start">
              <div class="row">
                <div class="col">
                  <label for="password" class="form-label">
                    <?php echo e($translations['password'] ?? 'Password'); ?>
                  </label>
                  <input type="password" class="form-control" id="password"
                         name="password"
                         pattern="(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*.,?])[A-Za-z0-9!@#$%^&*.,?]{8,}"
                         required>
                </div>
                <div class="col">
                  <label for="confirm_password" class="form-label">
                    <?php echo e($translations['password-confirm'] ?? 'Confirm password'); ?>
                  </label>
                  <input type="password" class="form-control" id="confirm_password"
                         name="confirm_password"
                         pattern="(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*.,?])[A-Za-z0-9!@#$%^&*.,?]{8,}"
                         required>
                </div>
              </div>

              <div class="mt-2 text-start">
                <ul class="list-unstyled small">
                  <li id="req-length">❌ <?php echo e($translations['passwordspattern_1'] ?? 'Min. 8 characters'); ?></li>
                  <li id="req-uppercase">❌ <?php echo e($translations['passwordspattern_2'] ?? 'One uppercase letter'); ?></li>
                  <li id="req-number">❌ <?php echo e($translations['passwordspattern_3'] ?? 'One number'); ?></li>
                  <li id="req-special">❌ <?php echo e($translations['passwordspattern_4'] ?? 'One special character'); ?></li>
                  <li id="req-match">❌ <?php echo e($translations['passwordspattern_5'] ?? 'Passwords match'); ?></li>
                </ul>
              </div>
            </div>

            <div class="text-center mt-3">
              <button type="submit" id="submitBtn" class="btn btn-primary btn-lg" disabled>
                <?php echo e($translations['register'] ?? 'Register'); ?>
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
  const password        = document.getElementById('password');
  const password2       = document.getElementById('confirm_password');
  const submitBtn       = document.getElementById('submitBtn');

  const reqLength    = document.getElementById('req-length');
  const reqUppercase = document.getElementById('req-uppercase');
  const reqNumber    = document.getElementById('req-number');
  const reqSpecial   = document.getElementById('req-special');
  const reqMatch     = document.getElementById('req-match');

  const t = <?php echo json_encode([
    'p1' => $translations['passwordspattern_1'] ?? 'Min. 8 characters',
    'p2' => $translations['passwordspattern_2'] ?? 'One uppercase letter',
    'p3' => $translations['passwordspattern_3'] ?? 'One number',
    'p4' => $translations['passwordspattern_4'] ?? 'One special character',
    'p5' => $translations['passwordspattern_5'] ?? 'Passwords match',
  ]); ?>;

  function validatePassword() {
    const val       = password.value;
    const hasLen    = val.length >= 8;
    const hasUpper  = /[A-Z]/.test(val);
    const hasNum    = /[0-9]/.test(val);
    const hasSpec   = /[!@#$%^&*.,?]/.test(val);
    const matches   = val === password2.value && val !== '';

    const set = (el, ok, text) => {
      el.textContent = (ok ? '✔️ ' : '❌ ') + text;
      el.className   = ok ? 'text-success' : 'text-danger';
    };

    set(reqLength,    hasLen,   t.p1);
    set(reqUppercase, hasUpper, t.p2);
    set(reqNumber,    hasNum,   t.p3);
    set(reqSpecial,   hasSpec,  t.p4);
    set(reqMatch,     matches,  t.p5);

    submitBtn.disabled = !(hasLen && hasUpper && hasNum && hasSpec && matches);
  }

  password.addEventListener('input',  validatePassword);
  password2.addEventListener('input', validatePassword);
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
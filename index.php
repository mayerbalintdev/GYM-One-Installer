<?php
session_start();

require_once __DIR__ . '/helpers.php';

if (empty($_SESSION['server_logged'])) {
    logServerData();
    $_SESSION['server_logged'] = true;
}

$langData     = loadTranslations();
$lang         = $langData['lang'];
$languages    = $langData['languages'];
$translations = $langData['translations'];
$assetBase    = '';

$texts = [
    'Welcome!',
    'Üdvözöllek!',
    'Welkom!',
    'Bienvenue!',
    'Willkommen!',
    'Benvenuti!',
    'Velkommen!',
    'Velkommen!',
    'Witamy!',
    'Bun venit!',
    'Добродошли!',
    'Vitajte!',
    'Pozdravljam!',
    'Bienvenido!',
    'Välkommen!',
    'Hoşgeldiniz!',
    'मैं आपका स्वागत करता हूं',
    'أهلاً وسهلاً بك',
];
?>
<?php include __DIR__ . '/partials/header.php'; ?>

<div class="container justify-content-center">
  <div class="row text-center justify-content-center">

    <div class="col-md-8 mx-auto text-center mb-5">
      <img class="img img-fluid mb-3"
           src="https://gymoneglobal.com/assets/img/text-logo.png"
           alt="GYM One Logo" width="35%">
      <h1 id="animated-text" class="mb-3 fw-semibold" aria-live="polite">!</h1>
      <p class="lead mb-4 fs-4">
        <?php echo e($translations['installerVersion'] ?? ''); ?> &ndash; <?php echo e(INSTALLER_VERSION); ?>
      </p>
    </div>

    <div class="col-md-8 mx-auto text-center mb-5">
      <p class="mb-4 fs-4 lead"><?php echo e($translations['lang_select'] ?? ''); ?></p>
      <div class="dropdown">
        <a id="langDropdown" class="btn dropdown-toggle" href="#" role="button"
           data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <img src="https://raw.githubusercontent.com/lipis/flag-icons/main/flags/4x3/<?php echo e(strtolower($lang)); ?>.svg"
               class="svg country" alt="<?php echo e($lang); ?>">
        </a>
        <div class="dropdown-menu" aria-labelledby="langDropdown">
          <?php foreach ($languages as $code => $name): ?>
            <a class="dropdown-item" href="?lang=<?php echo e($code); ?>">
              <img src="https://raw.githubusercontent.com/lipis/flag-icons/main/flags/4x3/<?php echo e(strtolower($code)); ?>.svg"
                   class="svg" alt="<?php echo e($name); ?>">
              <?php echo e($translations[$name] ?? $name); ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <div class="col-md-8 mx-auto text-center mb-5">
      <a href="stage1/" class="btn btn-success btn-lg text-white mx-2 mb-4" role="button">
        <?php echo e($translations['continue'] ?? 'Continue'); ?>
      </a>
    </div>

  </div>
</div>

<script>
  const texts = <?php echo json_encode($texts, JSON_UNESCAPED_UNICODE); ?>;
  let currentIndex = 0;
  const textEl = document.getElementById('animated-text');

  textEl.textContent = texts[currentIndex++];
  textEl.style.opacity = '1';

  setInterval(() => {
    textEl.style.transition = 'opacity .5s';
    textEl.style.opacity    = '0';
    setTimeout(() => {
      textEl.textContent   = texts[currentIndex];
      textEl.style.opacity = '1';
      currentIndex = (currentIndex + 1) % texts.length;
    }, 500);
  }, 2000);
</script>

<?php include __DIR__ . '/partials/footer.php'; ?>
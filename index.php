<?php
session_start();

function logServerData() {
  $logFile = __DIR__ . '/LOG.log';

  $serverIp = $_SERVER['SERVER_ADDR'] ?? 'Unknown';
  $phpVersion = phpversion();
  $osInfo = php_uname();
  $clientIp = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
  $location = getLocationByIP($clientIp);
  $currentDate = date('Y-m-d H:i:s');

  $logMessage = sprintf(
      "[%s] [Starter] Server IP: %s | OS: %s | PHP Version: %s | Client IP: %s | Location: %s\n",
      $currentDate,
      $serverIp,
      $osInfo,
      $phpVersion,
      $clientIp,
      $location
  );

  file_put_contents($logFile, $logMessage, FILE_APPEND);
}

function getLocationByIP($ip) {
  $apiUrl = "http://ip-api.com/json/{$ip}";
  
  // API hívás
  $response = file_get_contents($apiUrl);
  if ($response) {
      $data = json_decode($response, true);
      if ($data['status'] === 'success') {
          return $data['country'] . ', ' . $data['regionName'] . ', ' . $data['city'];
      }
  }

  return 'Unknown Location';
}

logServerData();


// DEF INFO
$github_url = "https://github.com/mayerbalintdev/GYM-One";
$discord_url = "https://gymoneglobal.com/discord";
$installer_version = "V1.2.1";

$langDir = __DIR__ . "/assets/lang/";
$langFiles = glob($langDir . "*.json");
$languages = [];

foreach ($langFiles as $file) {
  $code = strtoupper(pathinfo($file, PATHINFO_FILENAME));
  $languages[$code] = $code;
}

if (isset($_GET['lang']) && file_exists($langDir . "{$_GET['lang']}.json")) {
  $_SESSION['lang'] = $_GET['lang'];
}

$copyrightyear = date("Y");


$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'GB';
$langFile = $langDir . "$lang.json";

if (file_exists($langFile)) {
  $translations = json_decode(file_get_contents($langFile), true);
} else {
  die("A nyelvi fájl nem található: $langFile");
}
$texts = [
  'Welcome!',           // English (Angol)
  'Üdvözöllek!',        // Hungarian (Magyar)
  'Welkom!',            // Dutch (Holland)
  'Bienvenue!',         // French (Francia)
  'Willkommen!',        // German (Német)
  'Benvenuti!',         // Italian (Olasz)
  'Velkommen!',         // Norwegian (Norvég)
  'Velkommen!',         // Danish (Dán)
  'Witamy!',            // Polish (Lengyel)
  'Bun venit!',         // Romanian (Román)
  'Добродошли!',        // Serbian (Szerb)
  'Vitajte!',           // Slovak (Szlovák)
  'Pozdravljam!',       // Slovenian (Szlovén)
  'Bienvenido!',        // Spanish (Spanyol)
  'Välkommen!',         // Swedish (Svéd)
  'Hoşgeldiniz!',       // Turkish (Török)
  'मैं आपका स्वागत करता हूं',   // Hindi (Hindi)
  'أهلاً وسهلاً بك'          // Arabic (Arab)
];
?>


<!DOCTYPE html>
<html lang="GB">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="shortcut icon" href="https://gymoneglobal.com/assets/img/logo.png" type="image/x-icon">
  <title>GYM One - <?php echo $translations["install"]; ?></title>
</head>

<body>
  <div class="mt-5"></div>
  <div class="container justify-content-center">
    <div class="row text-center justify-content-center">
      <div class="col-md-8 mx-auto text-center mb-5">
      <img class="img img-fluid" src="https://gymoneglobal.com/assets/img/text-logo.png" alt="GYM One Logo" width="35%">
        <h1 id="animated-text" class="mb-3 fw-semibold">!</h1>
        <p class="lead mb-4 fs-4"><?php echo $translations["installerVersion"]; ?> - <?php echo $installer_version; ?>
        </p>
      </div>
      <div class="col-md-8 mx-auto text-center mb-5">
        <p class="mb-4 fs-4 lead"><?php echo $translations["lang_select"]; ?></p>
        <div class="dropdown">
          <a id="langDropdown" class="btn dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown"
            aria-haspopup="true" aria-expanded="false">
            <img
              src="https://raw.githubusercontent.com/lipis/flag-icons/main/flags/4x3/<?php echo strtolower($lang); ?>.svg"
              class="svg country" alt="<?php echo $lang; ?>">
          </a>
          <div class="dropdown-menu" aria-labelledby="langDropdown">
            <?php foreach ($languages as $code => $name): ?>
              <a class="dropdown-item" href="#" onclick="changeLanguage('<?php echo $code; ?>')">
                <img
                  src="https://raw.githubusercontent.com/lipis/flag-icons/main/flags/4x3/<?php echo strtolower($code); ?>.svg"
                  class="svg" alt="<?php echo $name; ?>">
                <?php echo $translations[$name]; ?>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
      <div class="col-md-8 mx-auto text-center mb-5">
        <a href="stage1/" class="btn btn-success btn-lg text-white mx-2 mb-4" role="button"
          aria-pressed="true"><?php echo $translations["continue"]; ?></a>
      </div>
    </div>
  </div>
  <div class="mt-5"></div>
  <div class="footer-waves">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 8" fill="#252525">
      <path opacity="0.7" d="M0 8 V8 C20 0, 40 0, 60 8 V8z"></path>
      <path d="M0 8 V5 Q25 10 55 5 T100 4 V8z"></path>
    </svg>
  </div>
  <div class="footer">
    <div class="container">
      <div class="row gy-4">
        <div class="col-md-4 mb-1">
          <h2 class="mb-4">
            <img src="https://gymoneglobal.com/assets/img/text-color-logo.png" alt="GYM One Logo" height="105">
          </h2>

          <p><?php echo $translations["herotext"]; ?></p>
        </div>
        <div class="col-md-3 offset-md-1">
          <h2 class="text-light mb-4"></h2>
        </div>

        <div class="col-md-2 offset-md-1">
          <h2 class="text-light mb-4"><?php echo $translations["links"]; ?></h2>

          <ul class="list-unstyled links">
            <li><a href="<?php echo $github_url; ?>" target="_blank" rel="noopener noreferrer">GitHub</a></li>
            <li><a href="<?php echo $discord_url; ?>" target="_blank" rel="noopener noreferrer">Discord</a></li>
            <li><a href="https://gymoneglobal.com/support"><?php echo $translations["support-us"]; ?></a></li>
          </ul>
        </div>
      </div>

      <div class="border-top border-secondary pt-3 mt-3">
        <p class="small text-center mb-0">
          Copyright © <?php echo $copyrightyear; ?> GYM One - <?php echo $translations["copyright"]; ?>. &nbsp;<svg
            xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-heart-fill"
            viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314">
            </path>
          </svg> </p>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
    integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
    crossorigin="anonymous"></script>
  <script>
    function changeLanguage(lang) {
      window.location.href = '?lang=' + lang;
    }
  </script>
  <script>
    const texts = <?php echo json_encode($texts); ?>;
    let currentIndex = 0;
    const textElement = document.getElementById('animated-text');

    function changeText() {
      textElement.style.opacity = 0;
      setTimeout(() => {
        textElement.textContent = texts[currentIndex];
        textElement.style.opacity = 1;
        currentIndex = (currentIndex + 1) % texts.length;
      }, 1000);
    }
    textElement.textContent = texts[currentIndex];
    textElement.style.opacity = 1;
    currentIndex++;

    setInterval(changeText, 2000);
  </script>
</body>

</html>
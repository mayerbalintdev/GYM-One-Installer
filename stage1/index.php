<?php
session_start();

// DEF INFO
$github_url = "https://github.com/mayerbalintdev/";
$discord_url = "https://gymoneglobal.com/discord";
$installer_version = "V1.2.0";

$langDir = __DIR__ . "/../assets/lang/";
$langFiles = glob($langDir . "*.json");
$languages = [];

foreach ($langFiles as $file) {
  $code = strtoupper(pathinfo($file, PATHINFO_FILENAME));
  $languages[$code] = $code;
}

if (isset($_GET['lang']) && file_exists($langDir . "{$_GET['lang']}.json")) {
  $_SESSION['lang'] = $_GET['lang'];
}

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'GB';
$langFile = $langDir . "$lang.json";

$copyrightyear = date("Y");

if (file_exists($langFile)) {
  $translations = json_decode(file_get_contents($langFile), true);
} else {
  die("A nyelvi fájl nem található: $langFile");
}

function logDocumentAcceptance($documentNumber)
{
  $logFile = "../LOG.log";
  $logMessage = "[" . date("Y-m-d H:i:s") . "] [STAGE1] ✅  Document $documentNumber accepted." . PHP_EOL;
  file_put_contents($logFile, $logMessage, FILE_APPEND);
}

function setupSubscriptionReminderJob($scriptPath, $time = "08:00") {
    list($hour, $minute) = explode(":", $time);
    if (stripos(PHP_OS, "WIN") === 0) {
        $phpPath = trim(shell_exec("where php"));
        $taskName = "GYMOneReminder";
        $command = "schtasks /Create /SC DAILY /TN $taskName /TR \"\\\"$phpPath\\\" \\\"$scriptPath\\\"\" /ST $time /F";
        exec($command);
    } else {
        $phpPath = trim(shell_exec("which php"));
        if (!$phpPath) {
            $phpPath = "/usr/bin/php";
        }

        $cronJob = intval($minute) . " " . intval($hour) . " * * * $phpPath $scriptPath";

        $currentCrontab = shell_exec("crontab -l -u www-data 2>/dev/null");

        if (strpos($currentCrontab, $scriptPath) === false) {
            $newCrontab = $currentCrontab . PHP_EOL . $cronJob . PHP_EOL;
            $tmpFile = tempnam(sys_get_temp_dir(), 'cron');
            file_put_contents($tmpFile, $newCrontab);
            exec("crontab -u www-data $tmpFile");
            unlink($tmpFile);
        }
    }
}

$scriptPath = dirname(__DIR__) . "/crontab/send_reminders.php";
setupSubscriptionReminderJob($scriptPath, "08:00");
?>

<!DOCTYPE html>
<html lang="GB">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="shortcut icon" href="https://gymoneglobal.com/assets/img/logo.png" type="image/x-icon">
  <title>GYM One - <?php echo $translations["install"]; ?></title>
</head>

<body>
  <div class="mt-5"></div>
  <div class="container justify-content-center">
    <div class="row text-center justify-content-center">
      <div class="col-md-8 mx-auto text-center mb-5">
        <h1 class="mb-3 fw-semibold"><?php echo $translations["legalpage"]; ?></h1>
        <p class="lead mb-4 fs-4"><?php echo $translations["installerVersion"]; ?> - <?php echo $installer_version; ?>
        <p class="lead"><?php echo $translations["youcanacceptsomethings"]; ?></p>
        </p>
      </div>
      <div class="col-md-8 mx-auto text-center mb-5">
        <div class="card">
          <div class="card-body">
            <embed id="documentViewer" src="../assets/docs/document1.pdf#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" width="100%" height="400px">
            <div class="form-check mt-3 d-flex align-items-center">
              <input type="checkbox" class="form-check-input custom-checkbox" id="acceptTerms" onclick="toggleButton()">
              <label class="form-check-label ms-2" for="acceptTerms">
                <?php echo $translations["accept-docs"]; ?>
              </label>
            </div>
            <button id="continueButton" class="btn btn-primary mt-3"
              disabled><?php echo $translations["continue"]; ?></button>
          </div>
        </div>
      </div>
    </div>
  </div>
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
          </svg>
          - <a href="https://www.mayerbalint.hu/">Mayer Bálint</a>
        </p>
      </div>
    </div>
  </div>
  <script>
    let currentDocument = 1;
    const totalDocuments = 6;

    function toggleButton() {
      const checkbox = document.getElementById("acceptTerms");
      const continueButton = document.getElementById("continueButton");

      continueButton.disabled = !checkbox.checked;
    }

    document.getElementById("continueButton").addEventListener("click", function() {
      if (currentDocument < totalDocuments) {
        logDocumentAcceptance(currentDocument);

        currentDocument++;
        document.getElementById("documentViewer").src =
          `../assets/docs/document${currentDocument}.pdf#toolbar=0&navpanes=0&scrollbar=0`;

        document.getElementById("acceptTerms").checked = false;
        this.disabled = true;
      } else {
        window.location.href = "../stage2";
      }
    });

    function logDocumentAcceptance(documentNumber) {
      const xhr = new XMLHttpRequest();
      xhr.open("POST", "", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.send("document=" + documentNumber);
    }
  </script>
  <script>
    if (window.location.href.indexOf('?') === -1) {
      const urlParams = new URLSearchParams(window.location.search);
      if (urlParams.has('document')) {
        const documentNumber = urlParams.get('document');
        if (documentNumber >= 1 && documentNumber <= 6) {
          <?php
          if (isset($_POST['document'])) {
            $documentNumber = intval($_POST['document']);
            logDocumentAcceptance($documentNumber);
          }
          ?>
        }
      }
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
    integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
    crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
    integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
    crossorigin="anonymous"></script>
</body>

</html>
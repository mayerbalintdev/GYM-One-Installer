<?php
ob_start();
session_start();

require_once __DIR__ . '/../helpers.php';

$langData     = loadTranslations();
$lang         = $langData['lang'];
$languages    = $langData['languages'];
$translations = $langData['translations'];
$assetBase    = '../';

$dbHost = $dbUser = $dbPass = $dbName = '';
$envFile = TEMP_DIR . '.env';

if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $value = trim($value);
        switch (trim($key)) {
            case 'DB_SERVER':   $dbHost = $value; break;
            case 'DB_USERNAME': $dbUser = $value; break;
            case 'DB_PASSWORD': $dbPass = $value; break;
            case 'DB_NAME':     $dbName = $value; break;
        }
    }
}

$conn = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    writeLog('STAGE6', '❌ DB connection failed: ' . $conn->connect_error);
    header('Location: ../stage3/?error=connection_failed');
    exit();
}

if (empty($_SESSION['stage6_db_logged'])) {
    writeLog('STAGE6', "✅ DB connected – db: {$dbName} on {$dbHost}");
    $_SESSION['stage6_db_logged'] = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
    ob_end_clean();
    header('Content-Type: application/json; charset=utf-8');

    $sqlFile = __DIR__ . '/../assets/SQL/installer.sql';

    if (!file_exists($sqlFile)) {
        writeLog('STAGE6', '❌ installer.sql not found.');
        echo json_encode([
            'status'   => 'error',
            'message'  => 'installer.sql not found.',
            'progress' => 0,
        ]);
        exit();
    }

    $queries  = file_get_contents($sqlFile);
    $rawCmds  = array_filter(array_map('trim', explode(';', $queries)));
    $total    = count($rawCmds);
    $done     = 0;
    $lastError = null;

    foreach ($rawCmds as $command) {
        if (preg_match('/^\s*\/\*!\d+.*\*\/\s*$/s', $command)) {
            $done++;
            continue;
        }

        try {
            if (!$conn->query($command)) {
                $lastError = $conn->error;
                break;
            }
        } catch (mysqli_sql_exception $e) {
            $lastError = $e->getMessage();
            break;
        }
        $done++;
    }

    $conn->close();

    if ($lastError !== null) {
        writeLog('STAGE6', "❌ SQL error after {$done}/{$total} commands: {$lastError}");
        echo json_encode([
            'status'   => 'error',
            'message'  => $lastError,
            'progress' => round($done / max($total, 1) * 100),
        ]);
    } else {
        writeLog('STAGE6', "✅ SQL installation complete – {$done} commands executed.");
        echo json_encode([
            'status'   => 'success',
            'message'  => $translations['sql-success'] ?? 'Installation successful.',
            'progress' => 100,
        ]);
    }
    exit();
}

ob_end_clean();
?>
<?php include __DIR__ . '/../partials/header.php'; ?>

<style>
  .progress-bar   { transition: width 0.4s ease; }
  #continueButton { display: none; }
</style>

<div class="container justify-content-center">
  <div class="row text-center justify-content-center">

    <div class="col-md-8 mx-auto text-center mb-5">
      <h1 class="mb-3 fw-semibold"><?php echo e($translations['database-upload'] ?? 'Database Setup'); ?></h1>
      <p class="lead mb-4 fs-4">
        <?php echo e($translations['installerVersion'] ?? ''); ?> &ndash; <?php echo e(INSTALLER_VERSION); ?>
      </p>
    </div>

    <div class="col-md-8 mx-auto text-center mb-5 mt-5">
      <div class="card">
        <div class="card-body">

          <div class="text-center mt-4">
            <button id="installBtn" class="btn btn-primary btn-lg">
              <?php echo e($translations['database-upload-btn'] ?? 'Install Database'); ?>
            </button>
          </div>

          <div class="mt-4">
            <div class="progress" style="height: 30px;">
              <div class="progress-bar bg-success" role="progressbar"
                   style="width: 0%;" id="progressBar"
                   aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                0%
              </div>
            </div>
            <div class="mt-4 text-center fw-semibold" id="statusMessage"></div>
            <div class="text-center mt-4">
              <a href="../stage7/" id="continueButton" class="btn btn-success btn-lg">
                <?php echo e($translations['next'] ?? 'Next'); ?>
              </a>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div>
</div>

<script>
  const i18n = {
    preparing : <?php echo json_encode($translations['sql-preparing']       ?? 'Preparing...'); ?>,
    reading   : <?php echo json_encode($translations['sql-read']            ?? 'Reading SQL...'); ?>,
    running   : <?php echo json_encode($translations['sql-runningcommands'] ?? 'Running commands...'); ?>,
    finishing : <?php echo json_encode($translations['sql-final']           ?? 'Finishing...'); ?>,
    success   : <?php echo json_encode($translations['sql-success']         ?? 'Success!'); ?>,
    netError  : <?php echo json_encode($translations['network-error']       ?? 'Network error.'); ?>,
  };

  document.getElementById('installBtn').addEventListener('click', function () {
    const btn           = this;
    const progressBar   = document.getElementById('progressBar');
    const statusMessage = document.getElementById('statusMessage');
    const continueBtn   = document.getElementById('continueButton');

    btn.disabled    = true;
    btn.textContent = i18n.preparing;

    const fakeSteps = [
      { pct: 25, msg: i18n.preparing },
      { pct: 50, msg: i18n.reading   },
      { pct: 75, msg: i18n.running   },
    ];

    let step = 0;

    function setProgress(pct, msg, cssClass = '') {
      progressBar.style.width   = pct + '%';
      progressBar.textContent   = pct + '%';
      progressBar.setAttribute('aria-valuenow', pct);
      statusMessage.textContent = msg;
      statusMessage.className   = 'mt-4 text-center fw-semibold' + (cssClass ? ' ' + cssClass : '');
    }

    const fakeInterval = setInterval(() => {
      if (step < fakeSteps.length) {
        const s = fakeSteps[step++];
        setProgress(s.pct, s.msg);
      } else {
        clearInterval(fakeInterval);
        setProgress(75, i18n.finishing);

        fetch('', {
          method : 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body   : 'install=1',
        })
        .then(r => r.json())
        .then(data => {
          if (data.status === 'success') {
            setProgress(100, i18n.success, 'text-success');
            continueBtn.style.display = 'inline-block';
          } else {
            setProgress(data.progress ?? 75, '❌ ' + data.message, 'text-danger');
            progressBar.classList.replace('bg-success', 'bg-danger');
            btn.disabled    = false;
            btn.textContent = i18n.preparing;
          }
        })
        .catch(() => {
          setProgress(75, i18n.netError, 'text-danger');
          progressBar.classList.replace('bg-success', 'bg-danger');
          btn.disabled    = false;
          btn.textContent = i18n.preparing;
        });
      }
    }, 800);
  });
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
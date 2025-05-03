<?php
session_start();

// DEF INFO
$github_url = "https://github.com/mayerbalintdev/";
$discord_url = "https://gymoneglobal.com/discord";
$installer_version = "V1.0.1";

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

if (file_exists($langFile)) {
    $translations = json_decode(file_get_contents($langFile), true);
} else {
    die("A nyelvi fájl nem található: $langFile");
}
$copyrightyear = date("Y");
?>
<?php

function read_env_file($file_path)
{
    $env_file = file_get_contents($file_path);
    $env_lines = explode("\n", $env_file);
    $env_data = [];

    foreach ($env_lines as $line) {
        $line_parts = explode('=', $line);
        if (count($line_parts) == 2) {
            $key = trim($line_parts[0]);
            $value = trim($line_parts[1]);
            $env_data[$key] = $value;
        }
    }

    return $env_data;
}

$env_data = read_env_file('../temp/.env');

$db_host = $env_data['DB_SERVER'] ?? '';
$db_username = $env_data['DB_USERNAME'] ?? '';
$db_password = $env_data['DB_PASSWORD'] ?? '';
$db_name = $env_data['DB_NAME'] ?? '';


$host = $db_host;
$username = $db_username;
$password = $db_password;
$database = $db_name;

$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die('Hiba az adatbázishoz való kapcsolódásban: ' . mysqli_connect_error());
} else {
    $logMessage = "[" . date("Y-m-d H:i:s") . "] [STAGE6] ✅ Successful database connection to the database: $db_name on host: $db_host\n";
    file_put_contents("../LOG.log", $logMessage, FILE_APPEND);
}


$response = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
    $sqlFile = '../assets/SQL/installer.sql';

    if (file_exists($sqlFile)) {
        $queries = file_get_contents($sqlFile);
        $sqlCommands = explode(';', $queries);

        foreach ($sqlCommands as $command) {
            $command = trim($command);
            if (!empty($command)) {
                if (!mysqli_query($conn, $command)) {
                    $response['status'] = 'error';
                    $response['message'] = $translations["sql-preparing"] . ' ' . mysqli_error($conn);
                    break;
                }
            }
        }
        if (!isset($response['status'])) {
            $response['status'] = 'success';
            $response['message'] = $translations["sql-success"];
            $logMessage = "[" . date("Y-m-d H:i:s") . "] [STAGE6] ✅ The installation was successful, the SQL commands ran.\n";
            file_put_contents("../LOG.log", $logMessage, FILE_APPEND);
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'The installer.sql file cannot be found.';
        $logMessage = "[" . date("Y-m-d H:i:s") . "] [STAGE6] ❌ The installer.sql file cannot be found.\n";
        file_put_contents("../LOG.log", $logMessage, FILE_APPEND);
    }

    echo json_encode($response);
    exit;
}
?>
<!DOCTYPE html>
<html lang="HU">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="shortcut icon" href="https://gymoneglobal.com/assets/img/logo.png" type="image/x-icon">

    <title>GYM One - <?php echo $translations["install"]; ?></title>
</head>
<style>
    .progress-bar {
        transition: width 0.5s ease;
    }

    .status-message {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.5s ease, transform 0.5s ease;
    }

    .status-message.active {
        opacity: 1;
        transform: translateY(0);
    }

    #continueButton {
        display: none;
    }
</style>

<body>
    <div class="mt-5"></div>
    <div class="container justify-content-center">
        <div class="row text-center justify-content-center">
            <div class="col-md-8 mx-auto text-center mb-5">
                <h1 class="mb-3 fw-semibold"><?php echo $translations["database-upload"]; ?></h1>
                <p class="lead mb-4 fs-4"><?php echo $translations["installerVersion"]; ?> -
                    <?php echo $installer_version; ?>
                </p>
            </div>
            <div class="col-md-8 mx-auto text-center mb-5 mt-5">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center mt-4">
                            <button id="installBtn" class="btn btn-primary"><?php echo $translations["database-upload-btn"]; ?></button>
                        </div>
                        <div class="mt-4">
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: 0%;" id="progressBar"></div>
                            </div>
                            <div class="mt-4 text-center" id="statusMessage"></div>
                            <div class="text-center mt-4">
                                <a href="../stage7/" id="continueButton" class="btn btn-success"><?php echo $translations["next"]; ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-waves mt-5">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 8" fill="#252525">
            <path opacity="0.7" d="M0 8 V8 C20 0, 40 0, 60 8 V8z"></path>
            <path d="M0 8 V5 Q25 10 55 5 T100 4 V8z"></path>
        </svg>
    </div>
    <div class="footer">
        <div class="container">
            <div class="row gy-4 mb-5">
                <div class="col-md-4 mb-1">
                    <h2 class="mb-4">
                        <img src="https://gymoneglobal.com/assets/img/text-color-logo.png" alt="GYM One Logo"
                            height="105">
                    </h2>

                    <p><?php echo $translations["herotext"]; ?></p>
                </div>
                <div class="col-md-3 offset-md-1">
                    <h2 class="text-light mb-4"></h2>
                </div>

                <div class="col-md-2 offset-md-1">
                    <h2 class="text-light mb-4"><?php echo $translations["links"]; ?></h2>

                    <ul class="list-unstyled links">
                        <li><a href="<?php echo $github_url; ?>" target="_blank" rel="noopener noreferrer">GitHub</a>
                        </li>
                        <li><a href="<?php echo $discord_url; ?>" target="_blank" rel="noopener noreferrer">Discord</a>
                        </li>
                        <li><a href="https://gymoneglobal.com/support"><?php echo $translations["support-us"]; ?></a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="border-top border-secondary pt-3 mt-3">
                <p class="small text-center mb-0">
                    Copyright © <?php echo $copyrightyear; ?> GYM One - <?php echo $translations["copyright"]; ?>. &nbsp;<svg
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-heart-fill" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314">
                        </path>
                    </svg>
                    - <a href="https://www.mayerbalint.hu/">Mayer Bálint</a>
                </p>
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
        document.getElementById('installBtn').addEventListener('click', function() {
            const progressBar = document.getElementById('progressBar');
            const statusMessage = document.getElementById('statusMessage');
            const continueButton = document.getElementById('continueButton');
            const messages = [
                '<?php echo $translations["sql-preparing"]; ?>',
                '<?php echo $translations["sql-read"]; ?>',
                '<?php echo $translations["sql-runningcommands"]; ?>',
                '<?php echo $translations["sql-final"]; ?>'
            ];

            let progress = 0;
            let messageIndex = 0;

            const interval = setInterval(() => {
                progress += 25;
                progressBar.style.width = progress + '%';

                if (messageIndex < messages.length) {
                    statusMessage.textContent = messages[messageIndex++];
                    statusMessage.classList.add('active');
                }

                if (progress === 100) {
                    clearInterval(interval);

                    fetch('', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: 'install=1'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                statusMessage.textContent = '<?php echo $translations["sql-success"]; ?>';
                                statusMessage.classList.add('text-success');
                                continueButton.style.display = 'inline-block';
                                continueButton.classList.add('active');
                            } else {
                                statusMessage.textContent = '<?php echo $translations["unexpected-error"]; ?> ' + data.message;
                                statusMessage.classList.add('text-danger');
                                progressBar.classList.remove('bg-success');
                                progressBar.classList.add('bg-danger');
                            }
                        })
                        .catch(() => {
                            statusMessage.textContent = '<?php echo $translations["network-error"]; ?>';
                            statusMessage.classList.add('text-danger');
                            progressBar.classList.remove('bg-success');
                            progressBar.classList.add('bg-danger');
                        });
                }
            }, 2500);
        });
    </script>

</body>

</html>
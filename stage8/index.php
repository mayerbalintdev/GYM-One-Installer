<?php
session_start();

// DEF INFO
$github_url = "https://github.com/mayerbalintdev/";
$discord_url = "https://gymoneglobal.com/discord";
$installer_version = "V1.0.0";

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

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'HU';
$langFile = $langDir . "$lang.json";

if (file_exists($langFile)) {
    $translations = json_decode(file_get_contents($langFile), true);
} else {
    die("A nyelvi fájl nem található: $langFile");
}
?>
<?php
$required_extensions = array(
    'mysqli',
    'curl'
);

$enabled_extensions = get_loaded_extensions();
$min_php_version = '1.0.0';

$current_php_version = phpversion();

?>
<?php
function check_database_connection($host, $username, $password, $database)
{
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        return false;
    } else {
        return true;
    }
}

$env_file = file_get_contents('../temp/.env');
$env_lines = explode("\n", $env_file);

$db_host = '';
$db_username = '';
$db_password = '';
$db_name = '';

foreach ($env_lines as $line) {
    $line_parts = explode('=', $line);
    if (count($line_parts) == 2) {
        $key = trim($line_parts[0]);
        $value = trim($line_parts[1]);
        if ($key === 'DB_SERVER') {
            $db_host = $value;
        } elseif ($key === 'DB_USERNAME') {
            $db_username = $value;
        } elseif ($key === 'DB_PASSWORD') {
            $db_password = $value;
        } elseif ($key === 'DB_NAME') {
            $db_name = $value;
        }
    }
}

$env_file = '../temp/.env';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $smtp_host = isset($_POST['smtp_host']) ? $_POST['smtp_host'] : '';
    $smtp_port = isset($_POST['smtp_port']) ? $_POST['smtp_port'] : '';
    $smtp_encryption = isset($_POST['smtp_encryption']) ? $_POST['smtp_encryption'] : 'TLS';
    $smtp_username = isset($_POST['smtp_username']) ? $_POST['smtp_username'] : '';
    $smtp_password = isset($_POST['smtp_password']) ? $_POST['smtp_password'] : '';

    $env_data = [];
    if (file_exists($env_file)) {
        $file_contents = file_get_contents($env_file);
        foreach (explode("\n", $file_contents) as $line) {
            if (!empty($line) && strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $env_data[trim($key)] = trim($value);
            }
        }
    }

    $new_entries = [
        "MAIL_HOST" => $smtp_host,
        "MAIL_PORT" => $smtp_port,
        "MAIL_USERNAME" => $smtp_username,
        "MAIL_PASSWORD" => $smtp_password,
        "MAIL_ENCRYPTION" => $smtp_encryption
    ];

    foreach ($new_entries as $key => $value) {
        if (!isset($env_data[$key])) {
            $env_data[$key] = $value;
        }
    }

    $env_content = "";
    foreach ($env_data as $key => $value) {
        $env_content .= "$key=$value\n";
    }

    if (file_put_contents($env_file, $env_content)) {
        header("Location: ../lastone");
        $logMessage = "[" . date("Y-m-d H:i:s") . "] [STAGE8] ✅ SMTP data successfully updated\n";
        file_put_contents("../LOG.log", $logMessage, FILE_APPEND);
    } else {
        $logMessage = "[" . date("Y-m-d H:i:s") . "] [STAGE8] ❌ SMTP data could not be written to file. The file is not created or has been manipulated\n";
        file_put_contents("../LOG.log", $logMessage, FILE_APPEND);
    }
} else {
    $env_data = [];
    if (file_exists($env_file)) {
        $file_contents = file_get_contents($env_file);
        foreach (explode("\n", $file_contents) as $line) {
            if (!empty($line) && strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $env_data[trim($key)] = trim($value);
            }
        }
    }
}

$database_connected = check_database_connection($db_host, $db_username, $db_password, $db_name);
$copyrightyear = date("Y");

?>


<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <title>GYM One - <?php echo $translations["install"]; ?></title>
    <link rel="shortcut icon" href="https://gymoneglobal.com/assets/img/logo.png" type="image/x-icon">
</head>

<body>
    <div class="mt-5"></div>
    <div class="container justify-content-center">
        <div class="row text-center justify-content-center">
            <div class="col-md-8 mx-auto text-center mb-5">
                <h1 class="mb-3 fw-semibold"><?php echo $translations["mailpage"]; ?></h1>
                <p class="lead mb-4 fs-4"><?php echo $translations["installerVersion"]; ?> -
                    <?php echo $installer_version; ?>
                </p>
            </div>
            <div class="col-md-8 mx-auto text-center mb-5">
                <div class="card">
                    <div class="card-body">
                        <div class="alert" role="alert">
                            <div class="d-inline-block fs-1 lh-1 text-danger bg-danger bg-opacity-25 p-4 rounded-pill">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-exclamation" viewBox="0 0 16 16">
                                    <path
                                        d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.553.553 0 0 1-1.1 0z" />
                                </svg>
                            </div>
                            <p class="lead"><?php echo $translations["mail-installer"]; ?></p>
                        </div>
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="smtp_host" class="form-label">SMTP <?php echo $translations["host"]; ?>:</label>
                                    <input type="text" class="form-control" id="smtp_host" name="smtp_host" value="<?= htmlspecialchars($env_data['MAIL_HOST'] ?? '') ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="smtp_port" class="form-label">SMTP <?php echo $translations["port"]; ?>:</label>
                                    <input type="number" min="1" max="65535" class="form-control" id="smtp_port" name="smtp_port" value="<?= htmlspecialchars($env_data['MAIL_PORT'] ?? '') ?>">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="smtp_encryption" class="form-label">SMTP <?php echo $translations["encry"]; ?>:</label>
                                    <select class="form-select" id="smtp_encryption" name="smtp_encryption">
                                        <option value="TLS" <?= ($env_data['MAIL_ENCRYPTION'] ?? '') == 'TLS' ? 'selected' : '' ?>>TLS</option>
                                        <option value="SSL" <?= ($env_data['MAIL_ENCRYPTION'] ?? '') == 'SSL' ? 'selected' : '' ?>>SSL</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="smtp_username" class="form-label">SMTP <?php echo $translations["username"]; ?>:</label>
                                    <input type="password" class="form-control" id="smtp_username" name="smtp_username" value="<?= htmlspecialchars($env_data['MAIL_USERNAME'] ?? '') ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="smtp_password" class="form-label">SMTP <?php echo $translations["password"]; ?>:</label>
                                    <input type="password" class="form-control" id="smtp_password" name="smtp_password" value="<?= htmlspecialchars($env_data['MAIL_PASSWORD'] ?? '') ?>">
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><?php echo $translations["save"]; ?></button>
                        </form>

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
                        <img src="https://gymoneglobal.com/assets/img/text-color-logo.png" alt="GYM.One" height="105">
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
                        <li><a href="https://gymoneglobal.com/support"><?php echo $translations["support-us"]; ?></a></li>
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
</body>

</html>
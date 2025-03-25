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

$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'GB';
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
    'curl',
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

$database_connected = check_database_connection($db_host, $db_username, $db_password, $db_name);

$copyrightyear = date("Y");

?>


<!DOCTYPE html>
<html lang="GB">

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

<body>
    <div class="mt-5"></div>
    <div class="container justify-content-center">
        <div class="row text-center justify-content-center">
            <div class="col-md-8 mx-auto text-center mb-5">
                <h1 class="mb-3 fw-semibold"><?php echo $translations["business-data"]; ?></h1>
                <p class="lead mb-4 fs-4"><?php echo $translations["installerVersion"]; ?> -
                    <?php echo $installer_version; ?>
                </p>
            </div>
            <div class="col-md-8 mx-auto text-center mb-5">
                <div class="card">
                    <div class="card-body">
                        <form action="add_to_env.php" method="POST">
                            <div class="mb-3">
                                <label for="businessName"
                                    class="form-label"><?php echo $translations["gym-name"]; ?></label>
                                <input type="text" class="form-control" id="businessName" name="businessName" required>
                            </div>
                            <div class="mb-3">
                                <label for="langCode" class="form-label"><?php echo $translations["lang"]; ?></label>
                                <select class="form-select" id="langCode" name="langCode" required>
                                    <?php foreach ($languages as $code): ?>
                                        <option value="<?php echo $code; ?>"><?php echo $translations["$code"]; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="country" class="form-label"><?php echo $translations["country"]; ?>:</label>
                                <input type="text" class="form-control" id="country" name="country" required>
                            </div>
                            <div class="mb-3">
                                <label for="city" class="form-label"><?php echo $translations["city"]; ?>:</label>
                                <input type="text" class="form-control" id="city" name="city" required>
                            </div>
                            <div class="mb-3">
                                <label for="street" class="form-label"><?php echo $translations["street"]; ?>:</label>
                                <input type="text" class="form-control" id="street" name="street" required>
                            </div>
                            <div class="mb-3">
                                <label for="houseNumber"
                                    class="form-label"><?php echo $translations["hause-no"]; ?>:</label>
                                <input type="text" class="form-control" id="houseNumber" name="houseNumber" required>
                            </div>
                            <div class="mb-3">
                                <label for="phoneno"
                                    class="form-label"><?php echo $translations["fno"]; ?>:</label>
                                <input type="text" class="form-control" id="phoneno" name="phoneno" required>
                            </div>
                            <button type="submit"
                                class="btn btn-primary"><?php echo $translations["continue"]; ?></button>
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
                    Copyright © <?php echo $copyrightyear;?> GYM One - <?php echo $translations["copyright"]; ?>. &nbsp;<svg
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
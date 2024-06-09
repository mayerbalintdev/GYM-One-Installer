<?php
// Nyelvválasztás kezelése
$lang = 'HU';
if (isset($_GET['lang']) && file_exists(__DIR__ . "/lang/{$_GET['lang']}.json")) {
    $lang = $_GET['lang'];
}

// Nyelvi fájl betöltése
$langFile = __DIR__ . "/lang/$lang.json";
if (file_exists($langFile)) {
    $translations = json_decode(file_get_contents($langFile), true);
} else {
    die("A nyelvi fájl nem található: $langFile");
}

// Ellenőrzések
$installerVersion = 'Beta';
$minPhpVersion = '8.1';
$url_GYMONE = "www.roman.hu";
$tos_url = "nig.com";
$requiredExtensions = [
    'curl',
];

$allRequirementsMet = true;
$phpVersionCheck = version_compare(PHP_VERSION, $minPhpVersion, '>=');
$extensionsCheck = [];

foreach ($requiredExtensions as $extension) {
    $extensionsCheck[$extension] = extension_loaded($extension);
    if (!$extensionsCheck[$extension]) {
        $allRequirementsMet = false;
    }
}

// Nyelvi fájlok és zászlók meghatározása
$langDir = __DIR__ . "/lang";
$langFiles = array_diff(scandir($langDir), ['..', '.']);
$languages = [];

foreach ($langFiles as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) == 'json') {
        $langCode = pathinfo($file, PATHINFO_FILENAME);
        $languages[$langCode] = ucfirst(strtolower($langCode));
    }
}
?>

<?php
// Ha az űrlap elküldték
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Adatok ellenőrzése és .env fájlba írása
    $db_host = $_POST['db_host'];
    $db_username = $_POST['db_username'];
    $db_password = $_POST['db_password'];
    $db_name = $_POST['db_name'];

    // Ellenőrzés és .env fájl írása
    if (!empty($db_host) && !empty($db_username) && !empty($db_name)) {
        $env_content = "DB_HOST=$db_host\nDB_USERNAME=$db_username\nDB_PASSWORD=$db_password\nDB_NAME=$db_name";
        file_put_contents('.env', $env_content);
        echo '<div class="container mt-3 alert alert-success">Az adatokat sikeresen mentettük a .env fájlba!</div>';
    } else {
        echo '<div class="container mt-3 alert alert-danger">Kérjük, töltse ki az összes mezőt!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gym ONE - Installer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .flag {
            width: 30px;
            height: 20px;
            margin: 5px;
            cursor: pointer;
        }

        body {
            background-color: royalblue;
        }

        .copyright {
            margin-top: 20px;
            font-size: 12px;
            text-align: center;
            color: #6c757d;
        }
    </style>
</head>

<body>
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card text-center" id="step1"> <!-- ID hozzáadása -->
            <img src="https://cloud.mayerbalint.hu/gym_One.png" class="img img-fluid logo" alt="Logo">
            <div class="card-body">
                <h5 class="card-title"><?php echo $translations['requirementsCheck']; ?></h5>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item"><?php echo $translations['installerVersion']; ?>:
                        <?php echo $installerVersion; ?>
                    </li>

                    <li class="list-group-item">
                        <?php echo $translations['phpVersion']; ?> (<?php echo PHP_VERSION; ?>):
                        <?php if ($phpVersionCheck): ?>
                            <span class="text-success">&#10003;</span>
                        <?php else: ?>
                            <span class="text-danger">&#10007;</span>
                            <?php $allRequirementsMet = false; ?>
                        <?php endif; ?>
                    </li>

                    <?php foreach ($requiredExtensions as $extension): ?>
                        <li class="list-group-item">
                            <?php echo $extension; ?>     <?php echo $translations['extension']; ?>:
                            <?php if ($extensionsCheck[$extension]): ?>
                                <span class="text-success">&#10003;</span>
                            <?php else: ?>
                                <span class="text-danger">&#10007;</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <br>
                <button type="button" class="btn btn-primary" onclick="showStep2()" <?php if (!$allRequirementsMet)
                    echo 'disabled'; ?>><?php echo $translations['continue']; ?></button> <!-- onclick hozzáadása -->
                <br>
                <div class="form-group">
                    <label for="languageSelect"><?php echo $translations['lang_select']; ?></label>
                    <div>
                        <?php foreach ($languages as $code => $name): ?>
                            <img src="https://raw.githubusercontent.com/lipis/flag-icons/main/flags/4x3/<?php echo strtolower($code); ?>.svg"
                                class="flag" alt="<?php echo $name; ?>" onclick="changeLanguage('<?php echo $code; ?>')">
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <p>Copyright © 2025-2027 <a href="<?php echo $url_GYMONE; ?>">GYM One </a>-
                    <?php echo $translations['copyright']; ?>.
                </p>
            </div>
        </div>
        <div class="card text-center d-none" id="step2"> <!-- ID hozzáadása, és elrejtése a d-none class-szal -->
            <img src="https://cloud.mayerbalint.hu/gym_One.png" class="img img-fluid logo" alt="Logo">
            <div class="card-body">
                <h5 class="card-title"><?php echo $translations['tos-header']; ?></h5>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="enableButton">
                    <label class="form-check-label" for="enableButton">
                        <h3><?php echo $translations['tos-btn']; ?> <a
                                href="<?php $tos_url; ?>"><?php echo $translations['tos-checkbox']; ?></a></h3>
                    </label>
                </div>

                <script>
                    document.getElementById('enableButton').addEventListener('change', function () {
                        var button = document.getElementById('myButton');
                        if (this.checked) {
                            button.removeAttribute('disabled');
                        } else {
                            button.setAttribute('disabled', 'disabled');
                        }
                    });
                </script>

                <button type="button" class="btn btn-primary" id="myButton" disabled onclick="showStep3()">
                    <?php echo $translations['continue']; ?></button> <!-- onclick hozzáadása -->
                <br>
                <div class="form-group">
                    <label for="languageSelect"><?php echo $translations['lang_select']; ?></label>
                    <div>
                        <?php foreach ($languages as $code => $name): ?>
                            <img src="https://raw.githubusercontent.com/lipis/flag-icons/main/flags/4x3/<?php echo strtolower($code); ?>.svg"
                                class="flag" alt="<?php echo $name; ?>" onclick="changeLanguage('<?php echo $code; ?>')">
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <p>Copyright © 2025-2027 <a href="<?php echo $url_GYMONE; ?>">GYM One </a>-
                    <?php echo $translations['copyright']; ?>.
                </p>
            </div>
        </div>
        <!-- STEP 3 -->
        <div class="card text-center d-none" id="step3"> <!-- ID hozzáadása, és elrejtése a d-none class-szal -->
            <img src="https://cloud.mayerbalint.hu/gym_One.png" class="img img-fluid logo" alt="Logo">
            <div class="card-body">
                <h5 class="card-title"><?php echo $translations['database-header']; ?></h5>

                <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                    <div class="form-group">
                        <label for="db_host"><?php echo $translations['database-host']; ?></label>
                        <input type="text" class="form-control" id="db_host" name="db_host" required>
                    </div>
                    <div class="form-group">
                        <label for="db_username"><?php echo $translations['username']; ?></label>
                        <input type="text" class="form-control" id="db_username" name="db_username" required>
                    </div>
                    <div class="form-group">
                        <label for="db_password"><?php echo $translations['password']; ?></label>
                        <input type="password" class="form-control" id="db_password" name="db_password">
                    </div>
                    <div class="form-group">
                        <label for="db_name"><?php echo $translations['dbname']; ?></label>
                        <input type="text" class="form-control" id="db_name" name="db_name" required>
                    </div>
                    <br>
                    <button type="submit" class="btn btn-primary" onclick="showStep4()
                        name=" submit"><?php echo $translations['continue']; ?></button>
                </form>
                <div class="form-group">
                    <label for="languageSelect"><?php echo $translations['lang_select']; ?></label>
                    <div>
                        <?php foreach ($languages as $code => $name): ?>
                            <img src="https://raw.githubusercontent.com/lipis/flag-icons/main/flags/4x3/<?php echo strtolower($code); ?>.svg"
                                class="flag" alt="<?php echo $name; ?>" onclick="changeLanguage('<?php echo $code; ?>')">
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <p>Copyright © 2025-2027 <a href="<?php echo $url_GYMONE; ?>">GYM One </a>-
                    <?php echo $translations['copyright']; ?>.
                </p>
            </div>
            <!-- STEP 4 -->

            <div class="card text-center d-none" id="step4"> <!-- ID hozzáadása, és elrejtése a d-none class-szal -->
                <img src="https://cloud.mayerbalint.hu/gym_One.png" class="img img-fluid logo" alt="Logo">
                <div class="card-body">
                    <h5 class="card-title"><?php echo $translations['database-header']; ?></h5>

                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="form-group">
                            <label for="username">Felhasználónév:</label>
                            <input type="text" class="form-control" id="username" name="username">
                        </div>
                        <div class="form-group">
                            <label for="password">Jelszó:</label>
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Létrehozás</button>
                    </form>
                    <div class="form-group">
                        <label for="languageSelect"><?php echo $translations['lang_select']; ?></label>
                        <div>
                            <?php foreach ($languages as $code => $name): ?>
                                <img src="https://raw.githubusercontent.com/lipis/flag-icons/main/flags/4x3/<?php echo strtolower($code); ?>.svg"
                                    class="flag" alt="<?php echo $name; ?>"
                                    onclick="changeLanguage('<?php echo $code; ?>')">
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-muted">
                    <p>Copyright © 2025-2027 <a href="<?php echo $url_GYMONE; ?>">GYM One </a>-
                        <?php echo $translations['copyright']; ?>.
                    </p>
                </div>
            </div>
        </div>
        <script>
            function changeLanguage(lang) {
                window.location.href = '?lang=' + lang;
            }

            function showStep2() {
                document.getElementById('step1').classList.add('d-none'); // Az első kártya elrejtése
                document.getElementById('step2').classList.remove('d-none'); // A második kártya megjelenítése
            }
            function showStep3() {
                document.getElementById('step2').classList.add('d-none'); // Az első kártya elrejtése
                document.getElementById('step3').classList.remove('d-none'); // A második kártya megjelenítése
            }
            function showStep4() {
                document.getElementById('step3').classList.add('d-none'); // Az első kártya elrejtése
                document.getElementById('step4').classList.remove('d-none'); // A második kártya megjelenítése
            }
        </script>
</body>

</html>
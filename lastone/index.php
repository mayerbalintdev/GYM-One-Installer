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

$copyrightyear = date("Y");

if (file_exists($langFile)) {
    $translations = json_decode(file_get_contents($langFile), true);
} else {
    die("A nyelvi fájl nem található: $langFile");
}

?>

<?php
$repoOwner = 'mayerbalintdev';
$repoName = 'GYM-ONE';
$outputDir = dirname(__DIR__) . '/gym-one-latest';
$tempZipFile = __DIR__ . '/GYM-One-main.zip';
$tempDirName = dirname(__DIR__) . '/temp';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['install'])) {
    try {
        $zipUrl = "https://github.com/{$repoOwner}/{$repoName}/archive/refs/heads/main.zip";
        $ch = curl_init($zipUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-Script');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $zipContent = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('Curl hiba a ZIP fájl letöltésekor: ' . curl_error($ch));
        }

        curl_close($ch);

        if ($zipContent === false) {
            throw new Exception('Nem sikerült letölteni a ZIP fájlt.');
        }

        if (file_put_contents($tempZipFile, $zipContent) === false) {
            throw new Exception('Nem sikerült menteni a ZIP fájlt.');
        }

        $zip = new ZipArchive();
        if ($zip->open($tempZipFile) === true) {
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0777, true);
            }

            $firstDir = $zip->getNameIndex(0);
            if ($firstDir) {
                $tempExtractDir = $outputDir . '_temp';
                mkdir($tempExtractDir, 0777, true);
                $zip->extractTo($tempExtractDir);
                $zip->close();

                $sourceDir = $tempExtractDir . '/' . trim($firstDir, '/');
                $files = scandir($sourceDir);
                foreach ($files as $file) {
                    if ($file !== '.' && $file !== '..') {
                        rename($sourceDir . '/' . $file, $outputDir . '/' . $file);
                    }
                }

                deleteDirectory($tempExtractDir);
            } else {
                throw new Exception('Nem sikerült megállapítani a ZIP fájl elsődleges mappáját.');
            }

            echo "A GYM One main ága sikeresen letöltve és kicsomagolva ide: $outputDir\n";
        } else {
            throw new Exception('Nem sikerült kicsomagolni a ZIP fájlt.');
        }

        $parentDir = dirname(__DIR__);
        $files = scandir($parentDir);

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..' && $file !== 'temp' && $file !== 'gym-one-latest') {
                $filePath = $parentDir . DIRECTORY_SEPARATOR . $file;

                if (is_dir($filePath)) {
                    deleteDirectory($filePath);
                } else {
                    unlink($filePath);
                }
            }
        }

        unlink($tempZipFile);

        if (is_dir($tempDirName)) {
            $tempFiles = scandir($tempDirName);
            foreach ($tempFiles as $file) {
                if ($file !== '.' && $file !== '..') {
                    rename($tempDirName . '/' . $file, $parentDir . '/' . $file);
                }
            }
            rmdir($tempDirName);
            echo "A temp mappa tartalma áthelyezve, és a temp mappa törölve.\n";
        } else {
            echo "A temp mappa nem található, nincs mit áthelyezni.\n";
        }

        if (is_dir($outputDir)) {
            $filesInOutputDir = scandir($outputDir);
            foreach ($filesInOutputDir as $file) {
                if ($file !== '.' && $file !== '..') {
                    rename($outputDir . '/' . $file, $parentDir . '/' . $file);
                }
            }
            rmdir($outputDir);
            echo "A gym-one-latest mappa tartalma áthelyezve, és a gym-one-latest mappa törölve.\n";
        } else {
            echo "A gym-one-latest mappa nem található, nincs mit áthelyezni.\n";
        }

        echo '<script type="text/javascript">
        // Átirányítjuk a jelenlegi ablakot
        window.location.href = "../index.php";

        // Megnyitjuk az új oldalt egy új ablakban
        window.open("https://gymoneglobal.com", "_blank");
      </script>';
        exit;
    } catch (Exception $e) {
        echo "Hiba: " . $e->getMessage() . "\n";
    }
}

/**
 *
 * @param string $dir Törlendő mappa útvonala
 */
function deleteDirectory($dir)
{
    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        $itemPath = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($itemPath)) {
            deleteDirectory($itemPath);
        } else {
            unlink($itemPath);
        }
    }
    rmdir($dir);
}
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
                <h1 class="mb-3 fw-semibold"><?php echo $translations["installer"]; ?></h1>
                <p class="lead mb-4 fs-4"><?php echo $translations["installerVersion"]; ?> - <?php echo $installer_version; ?>
                </p>
            </div>
            <div class="col-md-8 mx-auto text-center mb-5">
                <div class="card">
                    <div class="card-body">
                        <p class="text-center"><?php echo $translations["click-toinstall"];?></p>

                        <form method="POST">
                            <button type="submit" name="install" class="btn btn-primary btn-lg w-100"><?php echo $translations["installbtnlastone"];?></button>
                        </form>

                        <div class="install-progress mt-3">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0;" id="progress-bar"></div>
                            </div>
                            <p id="status-message" class="text-center mt-2"> - </p>
                        </div>
                    </div>
                </div>
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
</body>

</html>
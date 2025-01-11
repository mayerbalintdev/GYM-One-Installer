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
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GYM One Telepítés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 50px;
        }

        .install-progress {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center">GYM One Telepítő</h1>
        <p class="text-center">Kattints a gombra a telepítés indításához.</p>

        <form method="POST">
            <button type="submit" name="install" class="btn btn-primary btn-lg w-100">Telepítés indítása</button>
        </form>

        <div class="install-progress mt-3">
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0;" id="progress-bar"></div>
            </div>
            <p id="status-message" class="text-center mt-2">Telepítés folyamatban...</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelector('form').onsubmit = function() {
            document.querySelector('.install-progress').style.display = 'block';

            let progressBar = document.getElementById('progress-bar');
            let statusMessage = document.getElementById('status-message');
            let progress = 0;

            let interval = setInterval(function() {
                progress += 6;
                progressBar.style.width = progress + '%';

                if (progress >= 100) {
                    clearInterval(interval);
                    statusMessage.textContent = 'Telepítés befejeződött!';
                }
            }, 1000);
        }
    </script>
</body>

</html>
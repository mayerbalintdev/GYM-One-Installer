<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fájl Letöltése és Kicsomagolása</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #343a40;
        }
        .container {
            max-width: 600px;
            margin-top: 100px;
            padding: 20px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .progress {
            height: 25px;
        }
        #output {
            margin-top: 15px;
            font-size: 1.1em;
        }
    </style>
</head>
<body>
<div class="container">
    <h1 class="text-center mb-4">Fájl Letöltése és Kicsomagolása</h1>
    <button id="downloadButton" class="btn btn-primary btn-lg btn-block">Letöltés Indítása</button>
    <div class="progress mt-3" id="progressBar" style="display: none;">
        <div class="progress-bar" role="progressbar" style="width: 0%;" id="progress" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
    </div>
    <div id="output" class="mt-3"></div>
</div>

<script>
    document.getElementById('downloadButton').addEventListener('click', function () {
        this.disabled = true;
        document.getElementById('progressBar').style.display = 'block';
        document.getElementById('progress').style.width = '0%';
        document.getElementById('progress').innerText = '0%';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', window.location.href, true);
        xhr.onreadystatechange = function () {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                document.getElementById('output').innerText = xhr.responseText;
                document.getElementById('progressBar').style.display = 'none';
                document.getElementById('downloadButton').disabled = false;
            }
        };

        xhr.upload.onprogress = function (e) {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                document.getElementById('progress').style.width = percentComplete + '%';
                document.getElementById('progress').innerText = Math.round(percentComplete) + '%';
            }
        };

        xhr.send(new FormData());
    });
</script>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $keepFolder = realpath(__DIR__ . '/../temp');
    $rootDir = realpath(__DIR__ . '/..');

    $zipUrl = 'LINK';

    $tempZipPath = $keepFolder . '/release.zip';
    $zipFileContents = file_get_contents($zipUrl);

    if ($zipFileContents === false) {
        die('Nem sikerült a zip fájlt letölteni.');
    }

    file_put_contents($tempZipPath, $zipFileContents);

    $zip = new ZipArchive;
    if ($zip->open($tempZipPath) === TRUE) {
        $tempExtractPath = $keepFolder . '/extracted_files';
        mkdir($tempExtractPath);
        $zip->extractTo($tempExtractPath);
        $zip->close();
        echo 'A legfrissebb release sikeresen letöltve és kicsomagolva a temp/extracted_files mappába.<br>';

        function deleteDir($dir) {
            global $keepFolder;

            if (realpath($dir) === $keepFolder) {
                return;
            }

            if (!is_dir($dir)) {
                return;
            }

            $files = array_diff(scandir($dir), ['.', '..']);

            foreach ($files as $file) {
                $filePath = "$dir/$file";
                if (is_dir($filePath)) {
                    deleteDir($filePath);
                } else {
                    unlink($filePath);
                }
            }

            rmdir($dir);
        }

        if (is_dir($rootDir)) {
            $items = array_diff(scandir($rootDir), ['.', '..', 'temp']);
            foreach ($items as $item) {
                $itemPath = "$rootDir/$item";
                if (is_dir($itemPath)) {
                    deleteDir($itemPath);
                } else {
                    unlink($itemPath);
                }
            }
        }

        $files = array_diff(scandir($tempExtractPath), ['.', '..']);
        foreach ($files as $file) {
            $source = "$tempExtractPath/$file";
            $destination = "$rootDir/$file";

            $maxRetries = 5;
            $retries = 0;

            while (!rename($source, $destination) && $retries < $maxRetries) {
                $retries++;
                echo "A fájl használatban van: $file. Újrapróbálkozás ($retries/$maxRetries)...<br>";
                sleep(2);
            }

            if ($retries === $maxRetries) {
                echo "Nem sikerült áthelyezni a fájlt: $file.<br>";
            }
        }

        $remainingFiles = array_diff(scandir($tempExtractPath), ['.', '..']);
        if (empty($remainingFiles)) {
            rmdir($tempExtractPath);
        } else {
            echo "Nem sikerült törölni a temp/extracted_files mappát, mert nem üres.<br>";
        }

        if (file_exists($tempZipPath)) {
            unlink($tempZipPath);
        } else {
            echo "A zip fájl nem található, ezért nem lehet törölni.<br>";
        }

        $envFilePath = $keepFolder . '/.env';
        if (file_exists($envFilePath)) {
            if (rename($envFilePath, $rootDir . '/.env')) {
                echo ".env fájl sikeresen áthelyezve.<br>";
            } else {
                echo "Nem sikerült áthelyezni a .env fájlt.<br>";
            }
        } else {
            echo ".env fájl nem található a temp mappában.<br>";
        }

        $remainingTempFiles = array_diff(scandir($keepFolder), ['.', '..']);
        if (empty($remainingTempFiles)) {
            rmdir($keepFolder);
            echo "A temp mappa sikeresen törölve.<br>";
        } else {
            echo "A temp mappa nem üres, ezért nem törölhető.<br>";
        }

        header('Location: ../');
        exit();

    } else {
        die('Nem sikerült kicsomagolni a release fájlt.');
    }
}
?>

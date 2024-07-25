<?php
if (isset($_POST['download'])) {
    // GitHub URL
    $url = 'https://github.com/';
    $tempDir = __DIR__ . '/../temp/';
    $zipFile = $tempDir . 'Azuriom-1.1.10.zip';
    
    // 1. Letöltés GitHub-ról
    $zipResource = fopen($zipFile, "w");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FAILONERROR, true);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_AUTOREFERER, true);
    curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_FILE, $zipResource);
    $page = curl_exec($ch);
    if (!$page) {
        echo "Hiba történt a letöltés során: " . curl_error($ch);
    }
    curl_close($ch);
    fclose($zipResource);

    // 2. Ellenőrzés, hogy a letöltés sikeres volt-e
    if (file_exists($zipFile)) {
        echo "Sikeresen letöltöttük a legfrissebb verziót.";

        // 3. Az összes mappa és fájl törlése, kivéve a temp-et és a zip fájlt
        $dir = new RecursiveDirectoryIterator(__DIR__, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($dir, RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $file) {
            $filePath = $file->getRealPath();
            if ($file->isDir() && $file->getFilename() !== 'temp') {
                deleteDir($filePath);
            } elseif ($file->isFile() && basename($filePath) !== 'index.php' && basename($filePath) !== 'Azuriom-1.1.10.zip') {
                unlink($filePath);
            }
        }

        // 4. Az archívum kibontása egy mappával hátrébb
        $zip = new ZipArchive;
        if ($zip->open($zipFile) === TRUE) {
            $zip->extractTo(__DIR__ . '/..');
            $zip->close();
            echo " Az archívum sikeresen ki lett bontva.";
        } else {
            echo " Az archívum kibontása nem sikerült.";
        }
    } else {
        echo " Nem sikerült letölteni a fájlt.";
    }
}

function deleteDir($dirPath) {
    if (!is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath is not a directory");
    }
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dirPath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }
    rmdir($dirPath);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Letöltés</title>
</head>
<body>
    <form method="post">
        <button type="submit" name="download">Letöltés</button>
    </form>
</body>
</html>

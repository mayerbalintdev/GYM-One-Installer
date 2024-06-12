<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <title>GitHub Release Letöltése</title>
</head>
<body>
    <h1>GitHub Release Letöltése</h1>
    <form method="post">
        <button type="submit" name="download">Letöltés</button>
    </form>

    <?php
    if (isset($_POST['download'])) {
        $repoOwner = 'felhasznalonev';
        $repoName = 'repo-nev';
        $url = "https://api.github.com/repos/$repoOwner/$repoName/releases/latest";

        $options = [
            'http' => [
                'header' => "User-Agent: request"
            ]
        ];
        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        $release = json_decode($response, true);

        if ($release && isset($release['assets'][0]['browser_download_url'])) {
            $downloadUrl = $release['assets'][0]['browser_download_url'];
            $filename = 'latest_release.zip';

            if (file_put_contents($filename, file_get_contents($downloadUrl))) {
                echo "<p>Sikeresen letöltöttük: $filename</p>";
                
                // Töröl minden fájlt és mappát kivéve a ZIP fájlt és a temp mappát
                $files = glob('*');
                foreach ($files as $file) {
                    if ($file !== $filename && $file !== 'temp' && $file !== basename(__FILE__)) {
                        if (is_dir($file)) {
                            array_map('unlink', glob("$file/*.*"));
                            rmdir($file);
                        } else {
                            unlink($file);
                        }
                    }
                }

                // Létrehozza a temp mappát, ha nem létezik
                if (!is_dir('temp')) {
                    mkdir('temp');
                }

                // Kicsomagolja a ZIP fájlt a fő mappába
                $zip = new ZipArchive;
                if ($zip->open($filename) === TRUE) {
                    $zip->extractTo('.');
                    $zip->close();
                    echo "<p>A fájl sikeresen kicsomagolva a fő mappába.</p>";
                } else {
                    echo "<p>Nem sikerült kicsomagolni a ZIP fájlt.</p>";
                }
            } else {
                echo "<p>Hiba történt a letöltés során.</p>";
            }
        } else {
            echo "<p>Nem sikerült lekérni a legfrissebb kiadást.</p>";
        }
    }
    ?>
</body>
</html>

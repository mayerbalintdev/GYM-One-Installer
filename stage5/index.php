<?php
// Adatbázis-kapcsolat beállításai
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'unname';

// Kapcsolódás az adatbázishoz
$conn = mysqli_connect($host, $username, $password, $database);

if (!$conn) {
    die('Hiba az adatbázishoz való kapcsolódásban: ' . mysqli_connect_error());
}

// SQL fájl futtatása gombnyomásra
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
    $sqlFile = '../assets/SQL/installer.sql';

    if (file_exists($sqlFile)) {
        $queries = file_get_contents($sqlFile);

        $sqlCommands = explode(';', $queries);

        foreach ($sqlCommands as $command) {
            $command = trim($command);
            if (!empty($command)) {
                if (!mysqli_query($conn, $command)) {
                    die('Hiba történt a parancs futtatása közben: ' . mysqli_error($conn));
                }
            }
        }
        echo 'Az SQL fájl sikeresen futtatva.';
    } else {
        echo 'Az installer.sql fájl nem található.';
    }
}
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQL Telepítő</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Adatbázis Telepítő</h1>
        <div class="text-center mt-4">
            <form method="post">
                <button type="submit" name="install" class="btn btn-primary">SQL Futtatása</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

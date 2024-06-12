<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ellenőrizzük, hogy minden mező ki van-e töltve
    if (isset($_POST["servername"]) && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["dbname"])) {
        // Mentjük az adatokat az .env fájlba
        $env_data = "DB_SERVER=" . $_POST["servername"] . "\n";
        $env_data .= "DB_USERNAME=" . $_POST["username"] . "\n";
        $env_data .= "DB_PASSWORD=" . $_POST["password"] . "\n";
        $env_data .= "DB_NAME=" . $_POST["dbname"];

        // Az .env fájlba írás
        $env_file = fopen("../temp/.env", "w");
        fwrite($env_file, $env_data);
        fclose($env_file);

        // Átirányítás a sikeres űrlapfeldolgozás után
        header("Location: ../stage3/");
        exit();
    } else {
        // Ha valamelyik mező nincs kitöltve, visszairányítjuk az űrlapra
        header("Location: index.php");
        exit();
    }
} else {
    // Ha nem POST kérést kapunk, visszairányítjuk az űrlapra
    header("Location: index.php");
    exit();
}
?>

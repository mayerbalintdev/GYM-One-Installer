<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["servername"]) && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["dbname"])) {
        $servername = $_POST["servername"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $dbname = $_POST["dbname"];

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            header("Location: index.php?error=connection_failed");
            exit();
        } else {
            $env_data = "DB_SERVER=" . $servername . "\n";
            $env_data .= "DB_USERNAME=" . $username . "\n";
            $env_data .= "DB_PASSWORD=" . $password . "\n";
            $env_data .= "DB_NAME=" . $dbname;

            $env_file = fopen("../temp/.env", "w");
            fwrite($env_file, $env_data);
            fclose($env_file);

            header("Location: ../stage3/");
            exit();
        }
    } else {
        header("Location: index.php?error=missing_fields");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>

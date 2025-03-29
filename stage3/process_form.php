<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["servername"]) && isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["dbname"])) {
        $servername = $_POST["servername"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $dbname = "gymone";

        $log_file = "../LOG.log";

        try {
            $conn = new mysqli($servername, $username, $password, $dbname);

            if ($conn->connect_error) {
                throw new Exception("Connection Error: " . $conn->connect_error);
            }

            $success_message = " ✅ Successful connection to the database: IP: $servername, Database: $dbname";
            file_put_contents($log_file, "[" . date("Y-m-d H:i:s") . "] [STAGE3]" . $success_message . PHP_EOL, FILE_APPEND);

            $env_data = "DB_SERVER=" . $servername . "\n";
            $env_data .= "DB_USERNAME=" . $username . "\n";
            $env_data .= "DB_PASSWORD=" . $password . "\n";
            $env_data .= "DB_NAME=" . $dbname;

            $env_file = fopen("../temp/.env", "w");
            fwrite($env_file, $env_data);
            fclose($env_file);

            header("Location: ../stage4/");
            exit();
        } catch (mysqli_sql_exception $e) {
            $error_message = " ❌ MySQL error: " . $e->getMessage();
            file_put_contents($log_file, "[" . date("Y-m-d H:i:s") . "] [STAGE3]" . $error_message . PHP_EOL, FILE_APPEND);

            header("Location: index.php?error=connection_failed");
            exit();
        } catch (Exception $e) {
            $error_message = " ❌ Common error: " . $e->getMessage();
            file_put_contents($log_file, "[" . date("Y-m-d H:i:s") . "] [STAGE3]" . $error_message . PHP_EOL, FILE_APPEND);

            header("Location: index.php?error=connection_failed");
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

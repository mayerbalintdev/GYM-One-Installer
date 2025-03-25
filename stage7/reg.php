<?php
function check_database_connection($host, $username, $password, $database)
{
    $conn = new mysqli($host, $username, $password, $database);
    if ($conn->connect_error) {
        return false;
    } else {
        return $conn;
    }
}

$env_file = file_get_contents('../temp/.env');
$env_lines = explode("\n", $env_file);

$db_host = '';
$db_username = '';
$db_password = '';
$db_name = '';

foreach ($env_lines as $line) {
    $line_parts = explode('=', $line);
    if (count($line_parts) == 2) {
        $key = trim($line_parts[0]);
        $value = trim($line_parts[1]);
        if ($key === 'DB_SERVER') {
            $db_host = $value;
        } elseif ($key === 'DB_USERNAME') {
            $db_username = $value;
        } elseif ($key === 'DB_PASSWORD') {
            $db_password = $value;
        } elseif ($key === 'DB_NAME') {
            $db_name = $value;
        }
    }
}

$conn = check_database_connection($db_host, $db_username, $db_password, $db_name);

if (!$conn) {
    die("Database connection failed");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $is_boss = 1;
    $userid = 9999999999;

    if ($password != $confirm_password) {
        die("Password and confirm password do not match");
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);


    $sql = "INSERT INTO workers (userid, firstname, lastname, username, password_hash, is_boss) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("sssssi", $userid, $firstname, $lastname, $username, $password_hash, $is_boss);

    if ($stmt->execute()) {
        header("Location: ../stage8");
        $logMessage = "[" . date("Y-m-d H:i:s") . "] [STAGE7] ✅ Boss account added: $username ($firstname $lastname) \n";
        file_put_contents("../LOG.log", $logMessage, FILE_APPEND);
        exit();
    } else {
        echo "Error: " . $conn->error;
        $logMessage = "[" . date("Y-m-d H:i:s") . "] [STAGE7] ❌ ERROR DURING ADDING: $username ($firstname $lastname) \n";
        file_put_contents("../LOG.log", $logMessage, FILE_APPEND);
    }

    $stmt->close();
    $conn->close();
}
?>

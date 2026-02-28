<?php
// ============================================================
//  GYM One Installer – Stage 7: Admin regisztráció feldolgozás
// ============================================================
ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);
session_start();

require_once __DIR__ . '/../helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// ------------------------------------------------------------
// Input validáció
// ------------------------------------------------------------
$username         = mb_substr(trim($_POST['username']  ?? ''), 0, 50);
$firstname        = mb_substr(trim($_POST['firstname'] ?? ''), 0, 50);
$lastname         = mb_substr(trim($_POST['lastname']  ?? ''), 0, 50);
$password         = $_POST['password']         ?? '';
$confirmPassword  = $_POST['confirm_password'] ?? '';

if (empty($username) || empty($firstname) || empty($lastname) || empty($password)) {
    header('Location: index.php?error=missing_fields');
    exit();
}

// Jelszó egyezés – szerver oldali ellenőrzés is!
if ($password !== $confirmPassword) {
    header('Location: index.php?error=password_mismatch');
    exit();
}

// Jelszó erősség szerver oldalon is ellenőrizve
if (
    strlen($password) < 8 ||
    !preg_match('/[A-Z]/', $password) ||
    !preg_match('/[0-9]/', $password) ||
    !preg_match('/[!@#$%^&*.,?]/', $password)
) {
    header('Location: index.php?error=missing_fields');
    exit();
}

// ------------------------------------------------------------
// .env biztonságos beolvasása
// ------------------------------------------------------------
$dbHost = $dbUser = $dbPass = $dbName = '';
$envFile = TEMP_DIR . '.env';

if (file_exists($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        [$key, $value] = array_pad(explode('=', $line, 2), 2, '');
        $value = trim($value);
        switch (trim($key)) {
            case 'DB_SERVER':   $dbHost = $value; break;
            case 'DB_USERNAME': $dbUser = $value; break;
            case 'DB_PASSWORD': $dbPass = $value; break;
            case 'DB_NAME':     $dbName = $value; break;
        }
    }
}

// ------------------------------------------------------------
// DB kapcsolat
// ------------------------------------------------------------
$conn = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    writeLog('STAGE7', '❌ DB connection failed: ' . $conn->connect_error);
    header('Location: index.php?error=db_error');
    exit();
}

// ------------------------------------------------------------
// Admin fiók létrehozása
// userid: AUTO_INCREMENT az adatbázisban, nem adjuk meg manuálisan!
// is_boss = 1 → admin jogkör
// ------------------------------------------------------------
$passwordHash = password_hash($password, PASSWORD_BCRYPT);

// userid lekérése – ha a tábla nem AUTO_INCREMENT, generáljuk manuálisan
$result = $conn->query('SELECT COALESCE(MAX(userid), 0) + 1 AS next_id FROM workers');
$userId = $result ? (int)$result->fetch_assoc()['next_id'] : 1;

$stmt = $conn->prepare(
    'INSERT INTO workers (userid, firstname, lastname, username, password_hash, is_boss)
     VALUES (?, ?, ?, ?, ?, 1)'
);

if (!$stmt) {
    writeLog('STAGE7', '❌ Prepare failed: ' . $conn->error);
    header('Location: index.php?error=db_error');
    exit();
}

$stmt->bind_param('issss', $userId, $firstname, $lastname, $username, $passwordHash);

if ($stmt->execute()) {
    writeLog('STAGE7', "✅ Boss account created: {$username} ({$firstname} {$lastname})");
    $stmt->close();
    $conn->close();
    header('Location: ../stage8/');
    exit();
} else {
    writeLog('STAGE7', "❌ Insert failed for: {$username} – " . $stmt->error);
    $stmt->close();
    $conn->close();
    header('Location: index.php?error=db_error');
    exit();
}
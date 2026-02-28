<?php

session_start();

require_once __DIR__ . '/../helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}


function sanitizeEnvValue(string $value): string
{
    return trim($value);
}

function getPost(string $key, int $maxLen = 255): string
{
    return mb_substr(trim($_POST[$key] ?? ''), 0, $maxLen);
}

$businessName = getPost('businessName', 100);
$langCode     = preg_replace('/[^a-zA-Z]/', '', getPost('langCode', 5));
$country      = getPost('country', 100);
$city         = getPost('city', 100);
$street       = getPost('street', 100);
$houseNumber  = getPost('houseNumber', 20);
$phoneno      = getPost('phoneno', 30);
$currency     = preg_replace('/[^A-Za-z]/', '', getPost('currency', 10));
$metakey      = getPost('metakey', 255);
$description  = getPost('description', 500);
$capacity     = max(10, min(99999, (int)($_POST['capacity'] ?? 10)));

if (empty($businessName) || empty($langCode) || empty($country) ||
    empty($city) || empty($street) || empty($houseNumber)) {
    writeLog('STAGE5', '❌ Missing required fields.');
    header('Location: index.php?error=missing_fields');
    exit();
}

$appVersion = INSTALLER_VERSION;
$googleKey  = '-';
$autoaccept = 'false';
$about      = 'Example text';

$envFile = TEMP_DIR . '.env';

$lines = [
    '',
    'BUSINESS_NAME=' . sanitizeEnvValue($businessName),
    'LANG_CODE='     . sanitizeEnvValue($langCode),
    'COUNTRY='       . sanitizeEnvValue($country),
    'CITY='          . sanitizeEnvValue($city),
    'STREET='        . sanitizeEnvValue($street),
    'HOUSE_NUMBER='  . sanitizeEnvValue($houseNumber),
    'PHONE_NO='      . sanitizeEnvValue($phoneno),
    'CURRENCY='      . sanitizeEnvValue($currency),
    'META_KEY='      . sanitizeEnvValue($metakey),
    'DESCRIPTION='   . sanitizeEnvValue($description),
    'APP_VERSION='   . sanitizeEnvValue($appVersion),
    'GOOGLE_KEY='    . sanitizeEnvValue($googleKey),
    'CAPACITY='      . (int)$capacity,
    'ABOUT='         . sanitizeEnvValue($about),
    'AUTOACCEPT='    . $autoaccept,
];

$result = file_put_contents($envFile, implode("\n", $lines) . "\n", FILE_APPEND | LOCK_EX);

if ($result === false) {
    writeLog('STAGE5', '❌ Failed to write .env file.');
    header('Location: index.php?error=env_write');
    exit();
}

writeLog('STAGE5', sprintf(
    '✅ ENV updated – Gym: %s | Lang: %s | Location: %s, %s, %s %s',
    $businessName, $langCode, $country, $city, $street, $houseNumber
));

header('Location: ../stage6/');
exit();
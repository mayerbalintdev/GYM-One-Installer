<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $businessName = $_POST["businessName"];
    $langCode = $_POST["langCode"];
    $country = $_POST["country"];
    $city = $_POST["city"];
    $street = $_POST["street"];
    $houseNumber = $_POST["houseNumber"];
    $phoneno = $_POST["phoneno"];
    $currency = $_POST["currency"];
    $metakey = $_POST["metakey"];
    $description = $_POST["description"];
    $version = "V1.1.0";
    $googlekey = "-";
    $capacity = $_POST["capacity"];


    if (!empty($businessName) && !empty($langCode) && !empty($country) && !empty($city) && !empty($street) && !empty($houseNumber)) {
        $envFile = fopen("../temp/.env", "a");

        if ($envFile) {
            fwrite($envFile, "\n");
            fwrite($envFile, "BUSINESS_NAME=$businessName\n");
            fwrite($envFile, "LANG_CODE=$langCode\n");
            fwrite($envFile, "COUNTRY=$country\n");
            fwrite($envFile, "CITY=$city\n");
            fwrite($envFile, "STREET=$street\n");
            fwrite($envFile, "HOUSE_NUMBER=$houseNumber\n");
            fwrite($envFile, "PHONE_NO=$phoneno\n");
            fwrite($envFile, "CURRENCY=$currency\n");
            fwrite($envFile, "META_KEY=$metakey\n");
            fwrite($envFile, "DESCRIPTION=$description\n");
            fwrite($envFile, "APP_VERSION=$version\n");
            fwrite($envFile, "GOOGLE_KEY=$googlekey\n");
            fwrite($envFile, "CAPACITY=$capacity\n");
            fwrite($envFile, "ABOUT=Example text\n");

            

            fclose($envFile);

            $logMessage = "[" . date("Y-m-d H:i:s") . "] [STAGE5] ✅ ENV file update successful! Gym name set: $businessName, Language: $langCode, Country: $country, City: $city, Street: $street, House Number: $houseNumber\n";
            file_put_contents("../LOG.log", $logMessage, FILE_APPEND);

            header("Location: ../stage6");
            exit();
        } else {
            $logMessage = "[" . date("Y-m-d H:i:s") . "] [STAGE5] ❌ Failed to open .env file for writing!\n";
            file_put_contents("../LOG.log", $logMessage, FILE_APPEND);

            echo "Nem sikerült megnyitni az .env fájlt!";
        }
    } else {
        $logMessage = "[" . date("Y-m-d H:i:s") . "] [STAGE5] ❌ Missing data! Gym name or other required fields are empty.\n";
        file_put_contents("../LOG.log", $logMessage, FILE_APPEND);

        echo "Missing data! Gym name or other required fields are empty.";
    }
}
?>

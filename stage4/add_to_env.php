<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // POST adatok beolvasása
    $businessName = $_POST["businessName"];
    $langCode = $_POST["langCode"];
    $country = $_POST["country"];
    $city = $_POST["city"];
    $street = $_POST["street"];
    $houseNumber = $_POST["houseNumber"];

    // Ellenőrizd, hogy minden adat meg van-e adva
    if (!empty($businessName) && !empty($langCode) && !empty($country) && !empty($city) && !empty($street) && !empty($houseNumber)) {
        // Megnyitás az .env fájlhoz írás céljából
        $envFile = fopen("../temp/.env", "a");

        // Írás az .env fájlba
        fwrite($envFile, "\n");
        fwrite($envFile, "BUSINESS_NAME=\"$businessName\"\n");
        fwrite($envFile, "LANG_CODE=\"$langCode\"\n");
        fwrite($envFile, "COUNTRY=\"$country\"\n");
        fwrite($envFile, "CITY=\"$city\"\n");
        fwrite($envFile, "STREET=\"$street\"\n");
        fwrite($envFile, "HOUSE_NUMBER=\"$houseNumber\"\n");

        // Fájl bezárása
        fclose($envFile);

        header("Location: ../stage5");
        exit();
    } else {
        echo "Hiányzó adatok!";
    }
}
?>

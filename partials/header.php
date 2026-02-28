<?php
$pageTitle = $pageTitle ?? ($translations['install'] ?? 'Install');
?>
<!DOCTYPE html>
<html lang="<?php echo e($lang ?? 'GB'); ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="<?php echo e($assetBase ?? '../'); ?>assets/css/style.css">
  <link rel="shortcut icon" href="https://gymoneglobal.com/assets/img/logo.png" type="image/x-icon">
  <title>GYM One – <?php echo e($pageTitle); ?></title>
</head>
<body>
<div class="mt-5"></div>
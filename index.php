<?php

use Klein\Klein;

require_once __DIR__ . '/vendor/autoload.php';

$klein = new Klein();

$klein->respond('GET', '/', function () {
    require_once __DIR__ . "/index_fr.html";
});
$klein->respond('POST', '/contactForm', function () {
    require_once __DIR__ . "/include/contactForm.php";
});


$klein->respond('GET', '/en', function () {
    require_once __DIR__ . "/index_en.html";
});

$klein->dispatch();

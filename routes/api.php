<?php

use app\Controllers\Api\AesController;

define('PATH', "/");


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    echo json_encode('ammar');
}


$AesController = new AesController;
$request = $_SERVER["REQUEST_URI"];

switch ($request) {
    case PATH .  "api/encrypt?planeText=" . @$_GET["planeText"]:
        $AesController->encrypt($_GET["planeText"]);
        break;
    case PATH .  "api/decrypt?cipherText=" . @$_GET['cipherText']:
        $AesController->decrypt($_GET["cipherText"]);
        break;
    default:
        echo json_encode([
            "data" => null,
            'status' => false,
            "error" => "This URL not found",
            "statusCode" => 404
        ]);
        break;
}

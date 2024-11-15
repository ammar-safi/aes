<?php

use app\Controllers\Api\AesController;

define('PATH', "/");


$AesController = new AesController;
$request = $_SERVER["REQUEST_URI"];

switch ($request) {
    case PATH .  "api/encrypt?planeText=" . @$_GET["planeText"]:
        $AesController->encrypt($_GET["planeText"]);
        break;
    case PATH .  "api/decrypt?cipherText=" . @$_GET['cipherText']:
        $AesController->decrypt($_GET["cipherText"]);
        break;
    case PATH : 
    case PATH ."api":
    case PATH ."api/":
        echo json_encode([
            "data" => [
                "message" => "This_application_was_made_by_Eng_AmmarSafi_😇"
            ],
            'status' => true,
            "error" => null,
            "statusCode" => 200
        ]);
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

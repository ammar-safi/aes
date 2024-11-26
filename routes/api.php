<?php

use app\Controllers\Api\AesController;
use app\Controllers\Api\ResponseController;

define('PATH', "/index.php/");


$AesController = new AesController;
$ResponseController = new ResponseController;
$request = $_SERVER["REQUEST_URI"];
switch ($request) {
    case PATH .  "api/encrypt?planeText=" . checkIfSet('planeText'):
        $AesController->encrypt();
        break;

    case PATH .  "api/decrypt?cipherText=" . checkIfSet('cipherText'):
        $AesController->decrypt();
        break;

    case '/':
    case PATH:
    case PATH . "api":
    case PATH . "api/":
        $ResponseController->helloMessage();
        break;

    default:
        $ResponseController->notFoundUrl();
        break;
}


function checkIfSet($queryPar) {
    return (isset($_GET[$queryPar]) ? $_GET[$queryPar] : '') ;
}
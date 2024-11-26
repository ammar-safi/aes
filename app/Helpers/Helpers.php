<?php

namespace app\Helpers ;

function checkIfSet($queryPar) {
    return (isset($_GET[$queryPar]) ? $_GET[$queryPar] : '') ;
}
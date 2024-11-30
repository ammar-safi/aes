<?php

/**
 * My functions
 * Any function here will be loaded automatically in any file in this project
 */





/**
 * This function check if the query params is set in the URL or not 
 * 
 * @param queryPar : the name of the query params 
 * @return $_GET[$queryPar] if the query params exist or return an empty string 
 */

function checkIfSet($queryPar)
{
    return (isset($_GET[$queryPar]) ? $_GET[$queryPar] : '');
}

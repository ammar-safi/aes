<?php

namespace app\Traits;

trait Validation
{
    function validateString($text): bool
    {
        $pattern = "/^[a-zA-Z0-9-_ ]+$/";
        $result = preg_match($pattern, trim($text));
        return  $result === 1 ; 
    }
    function validateLength($text, $length = 16): bool
    {
        $pattern = "/^.{1," . $length . "}$/";
        $result = preg_match($pattern, trim($text));
        return $result === 1 ;
    }
    function validateCipherText($text): bool
    {
        $pattern = "/^[a-fA-F0-9]{32}$/i";
        $result = preg_match($pattern, trim($text));
        return $result === 1 ;
    }
}



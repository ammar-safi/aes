<?php

namespace app\controllers\api;

use app\controllers\Controller;
use app\Traits\GeneralTrait;
use app\Traits\Validation;
use Exception;

class AesController extends Controller
{
    use Validation;
    use GeneralTrait;

    public function __construct()
    {
        $this->config = require_once __DIR__ . "../../../../config/app.php";
        $this->initializeParams();
        $this->keyExpansion();
    }

    private function initializeParams()
    {
        $this->key = $this->config['APP-KEY'];
        $this->rcon = $this->config['RCON'];
        $this->sbox = $this->config["S-BOX"];
        $this->inverseSbox  = $this->config["INVERSE-S-BOX"];
    }


    public function encrypt($plaintext)
    {
        if (!$this->validateString($plaintext)) {
            $message["message"] = "The input must containing only numbers or English letter";
            echo $this->ValidationError($message);
            exit;
        }
        if (!$this->validateLength($plaintext)) {
            $message["message"] = "The length must be 16 byte or less'16 letter'";
            echo $this->ValidationError($message);
            exit;
        }

        try {
            $plaintext = $this->padding($plaintext);
            $state = $this->textToMatrix($plaintext);

            $this->addRoundKey($state, $this->keyExpansion[0]);

            for ($round = 1; $round < 10; $round++) {
                $this->subBytes($state);
                $this->shiftRows($state);
                $this->mixColumns($state);
                $this->addRoundKey($state, $this->keyExpansion[$round]);
            }

            $this->subBytes($state);
            $this->shiftRows($state);
            $this->addRoundKey($state, $this->keyExpansion[10]);

            $cipherText =  $this->matrixToText($state);
            $data["cipherText"] = bin2hex($cipherText);

            echo $this->SuccessResponse($data);
        } catch (Exception $error) {
        }
    }

    public function decrypt($cipherText)
    {
        if (!$this->validateCipherText($cipherText)) {
            $message["message"] = "The input must be hexa string and 32 byte '32 letter'";
            echo $this->ValidationError($message);
            exit;
        }
        try {
            $cipherText = hex2bin($cipherText);
            $state = $this->textToMatrix($cipherText);

            $this->addRoundKey($state, $this->keyExpansion[10]);

            for ($round = 9; $round > 0; $round--) {
                $this->inverseShiftRows($state);
                $this->inverseSubBytes($state);
                $this->addRoundKey($state, $this->keyExpansion[$round]);
                $this->inverseMixColumns($state);
            }

            $this->inverseShiftRows($state);
            $this->inverseSubBytes($state);
            $this->addRoundKey($state, $this->keyExpansion[0]);

            $plaintext = $this->matrixToText($state);
            $plaintext = $this->removePadding($plaintext);
            $data['plaintext'] = $plaintext;
            echo $this->SuccessResponse($data);
        } catch (Exception $error) {
        }
    }
}

<?php

namespace app\controllers;

class Controller
{

    protected $config;
    protected $sbox = [];
    protected $inverseSbox = [];
    protected $key;
    protected $keyExpansion = [];
    protected $rcon;

    protected function keyExpansion()
    {
        $roundKey = [];
        $roundKey[0] = $this->textToMatrix($this->key);

        for ($i = 1; $i < 11; $i++) {
            $roundKey[$i] = [];
            $temp = $roundKey[$i - 1][3];
            $temp = array_merge(array_slice($temp, 1), array_slice($temp, 0, 1));

            for ($j = 0; $j < 4; $j++) {
                $temp[$j] = $this->sbox[$temp[$j]];
            }

            $temp[0] ^= $this->rcon[$i - 1];

            for ($j = 0; $j < 4; $j++) {
                $roundKey[$i][$j] = [];
                for ($k = 0; $k < 4; $k++) {
                    $roundKey[$i][$j][$k] = $roundKey[$i - 1][$j][$k] ^ $temp[$k] ^ $roundKey[$i - 1][$j][$k];
                }
            }

            for ($j = 1; $j < 4; $j++) {
                for ($k = 0; $k < 4; $k++) {
                    $roundKey[$i][$j][$k] ^= $roundKey[$i][$j - 1][$k];
                }
            }
        }

        $this->keyExpansion = $roundKey;
    }

    protected function padding($text, $blockSize = 16)
    {
        $pad = $blockSize - (strlen($text) % $blockSize);
        return $text . str_repeat(chr($pad), $pad);
    }

    protected function removePadding($text)
    {
        $pad = ord($text[strlen($text) - 1]); 
        if ($pad <= 0 || $pad > strlen($text)) { 
            return $text; 
        }
       
        if (substr($text, -$pad) === str_repeat(chr($pad), $pad)) {
            return substr($text, 0, -$pad); 
        }
        return $text; 
    }
    
    protected function addRoundKey(&$state, $roundKey)
    {
        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 4; $j++) {
                $state[$i][$j] ^= $roundKey[$i][$j];
            }
        }
    }

    protected function subBytes(&$state)
    {
        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 4; $j++) {
                $state[$i][$j] = $this->sbox[$state[$i][$j]];
            }
        }
    }

    protected function inverseSubBytes(&$state)
    {
        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 4; $j++) {
                $state[$i][$j] = $this->inverseSbox[$state[$i][$j]];
            }
        }
    }

    protected function shiftRows(&$state)
    {
        $state[1] = array_merge(array_slice($state[1], 1), array_slice($state[1], 0, 1));
        $state[2] = array_merge(array_slice($state[2], 2), array_slice($state[2], 0, 2));
        $state[3] = array_merge(array_slice($state[3], 3), array_slice($state[3], 0, 3));
    }

    protected function inverseShiftRows(&$state)
    {
        $state[1] = array_merge(array_slice($state[1], -1), array_slice($state[1], 0, -1));
        $state[2] = array_merge(array_slice($state[2], -2), array_slice($state[2], 0, -2));
        $state[3] = array_merge(array_slice($state[3], -3), array_slice($state[3], 0, -3));
    }

    protected function mixColumns(&$state)
    {
        for ($i = 0; $i < 4; $i++) {
            $a = $state[0][$i];
            $b = $state[1][$i];
            $c = $state[2][$i];
            $d = $state[3][$i];

            $state[0][$i] = $this->galoisMultiply(0x02, $a) ^ $this->galoisMultiply(0x03, $b) ^ $c ^ $d;
            $state[1][$i] = $a ^ $this->galoisMultiply(0x02, $b) ^ $this->galoisMultiply(0x03, $c) ^ $d;
            $state[2][$i] = $a ^ $b ^ $this->galoisMultiply(0x02, $c) ^ $this->galoisMultiply(0x03, $d);
            $state[3][$i] = $this->galoisMultiply(0x03, $a) ^ $b ^ $c ^ $this->galoisMultiply(0x02, $d);
        }
    }
    protected function inverseMixColumns(&$state)
    {
        for ($i = 0; $i < 4; $i++) {
            $a = $state[0][$i];
            $b = $state[1][$i];
            $c = $state[2][$i];
            $d = $state[3][$i];

            $state[0][$i] = $this->galoisMultiply(0x0e, $a) ^ $this->galoisMultiply(0x0b, $b) ^ $this->galoisMultiply(0x0d, $c) ^ $this->galoisMultiply(0x09, $d);
            $state[1][$i] = $this->galoisMultiply(0x09, $a) ^ $this->galoisMultiply(0x0e, $b) ^ $this->galoisMultiply(0x0b, $c) ^ $this->galoisMultiply(0x0d, $d);
            $state[2][$i] = $this->galoisMultiply(0x0d, $a) ^ $this->galoisMultiply(0x09, $b) ^ $this->galoisMultiply(0x0e, $c) ^ $this->galoisMultiply(0x0b, $d);
            $state[3][$i] = $this->galoisMultiply(0x0b, $a) ^ $this->galoisMultiply(0x0d, $b) ^ $this->galoisMultiply(0x09, $c) ^ $this->galoisMultiply(0x0e, $d);
        }
    }
    protected function galoisMultiply($a, $b)
    {
        $result = 0;
        while ($b) {
            if ($b & 1) {
                $result ^= $a;
            }
            $a <<= 1;
            if ($a & 0x100) {
                $a ^= 0x11b;
            }
            $b >>= 1;
        }
        return $result;
    }

    protected function textToMatrix($text)
    {
        $matrix = str_split($text, 4);
        for ($i = 0; $i < 4; $i++) {
            $matrix[$i] = array_map('ord', str_split($matrix[$i]));
        }
        return $matrix;
    }

    protected function matrixToText($matrix)
    {
        $text = '';
        for ($i = 0; $i < 4; $i++) {
            foreach ($matrix[$i] as $byte) {
                $text .= chr($byte);
            }
        }
        return $text;
    }
}

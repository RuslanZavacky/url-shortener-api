<?php

namespace AppBundle\Services\Encoder;

class Base implements EncoderInterface
{
    const ALPHABET = '0123456789bcdfghjkmnpqrstvwxyzBCDFGHJKLMNPQRSTVWXYZ_';

    /**
     * @var int
     */
    private $base;

    public function __construct()
    {
        $this->base = strlen(self::ALPHABET);
    }

    /**
     * @param int $sequence
     * @return string
     */
    public function encode($sequence)
    {
        $str = '';
        while ($sequence > 0) {
            $str = substr(self::ALPHABET, ($sequence % $this->base), 1) . $str;
            $sequence = floor($sequence / $this->base);
        }
        return $str;
    }

    public function decode($code)
    {
        $num = 0;
        $len = strlen($code);
        for ($i = 0; $i < $len; $i++) {
            $num = $num * $this->base + strpos(self::ALPHABET, $code[$i]);
        }
        return $num;
    }
}
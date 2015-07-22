<?php

namespace Rz\Bundle\UrlShortenerBundle\Services\Encoder;

interface EncoderInterface
{
    /**
     * @param $sequence
     */
    public function encode($sequence);

    /**
     * @param $code
     */
    public function decode($code);
} 
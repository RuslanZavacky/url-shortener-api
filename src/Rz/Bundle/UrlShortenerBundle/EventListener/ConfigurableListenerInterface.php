<?php

namespace Rz\Bundle\UrlShortenerBundle\EventListener;


interface ConfigurableListenerInterface
{
    public function isEnabled();
    public function enable();
    public function disable();
} 
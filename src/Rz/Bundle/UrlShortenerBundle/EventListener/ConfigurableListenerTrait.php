<?php
namespace Rz\Bundle\UrlShortenerBundle\EventListener;

trait ConfigurableListenerTrait
{
    protected $enabled = false;

    public function isEnabled()
    {
        return $this->enabled === true;
    }

    public function enable()
    {
        $this->enabled = true;
    }

    public function disable()
    {
        $this->enabled = false;
    }
}

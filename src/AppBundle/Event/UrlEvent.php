<?php
namespace AppBundle\Event;


use AppBundle\Entity\Url;
use Symfony\Component\EventDispatcher\Event;

class UrlEvent extends Event
{
    private $url;

    public function __construct(Url $url = null)
    {
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }
} 
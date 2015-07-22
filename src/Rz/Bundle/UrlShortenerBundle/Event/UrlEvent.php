<?php
namespace Rz\Bundle\UrlShortenerBundle\Event;

use Rz\Bundle\UrlShortenerBundle\Entity\Url;
use Rz\Bundle\UrlShortenerBundle\Services\Shortener;
use Symfony\Component\EventDispatcher\Event;

class UrlEvent extends Event
{
    private $url;
    private $type;
    private $additional;

    public function __construct(Url $url = null, $type = Shortener::NOTIFY_TYPE_REDIRECT, $additional = [])
    {
        $this->url = $url;
        $this->type = $type;
        $this->additional = $additional;
    }

    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getAdditional()
    {
        return $this->additional;
    }
} 
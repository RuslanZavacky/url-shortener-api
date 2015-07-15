<?php
namespace AppBundle\EventListener;

use AppBundle\Event\UrlEvent;

class MessageListener
{
    public function notify(UrlEvent $event)
    {
        // TODO: implement amqp message listener
    }
} 
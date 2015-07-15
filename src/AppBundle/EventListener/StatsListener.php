<?php
namespace AppBundle\EventListener;

use AppBundle\Event\UrlEvent;
use Doctrine\ORM\EntityManagerInterface;

class StatsListener
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function notify(UrlEvent $event)
    {
        // TODO: implement statistics updates
    }
} 
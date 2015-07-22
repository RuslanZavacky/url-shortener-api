<?php
namespace Rz\Bundle\UrlShortenerBundle\EventListener;

use Rz\Bundle\UrlShortenerBundle\Entity\Url;
use Rz\Bundle\UrlShortenerBundle\Entity\UrlStat;
use Rz\Bundle\UrlShortenerBundle\Event\UrlEvent;
use Doctrine\ORM\EntityManagerInterface;
use Rz\Bundle\UrlShortenerBundle\Services\Shortener;

class StatsListener implements ConfigurableListenerInterface
{
    use ConfigurableListenerTrait;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function notify(UrlEvent $event)
    {
        if (!$this->isEnabled()
            || !$event->getUrl()
            || $event->getType() !== Shortener::NOTIFY_TYPE_REDIRECT
        ) {
            return false;
        }

        $additional = $event->getAdditional();

        $ip = !empty($additional['ip']) ? $additional['ip'] : null;

        if (!$ip) {
            return false;
        }

        $urlRepository = $this->em->getRepository('Rz\Bundle\UrlShortenerBundle\Entity\Url');
        $urlStatRepository = $this->em->getRepository('Rz\Bundle\UrlShortenerBundle\Entity\UrlStat');

        /** @var Url $url */
        $url = $urlRepository->find($event->getUrl()->getId());
        $url->incrementRedirectCount();
        $url->setLastRedirectOn(new \DateTime());

        $urlStat = $urlStatRepository->findOneBy([
            'Url' => $url,
            'ip' => $ip
        ]);

        if (!$urlStat) {
            $url->incrementUniqueRedirectCount();

            $urlStat = new UrlStat();
            $urlStat->setUrl($url);
            $urlStat->setCreated(new \DateTime());
            $urlStat->setIp($ip);

            if (!empty($additional['user-agent'])) {
                $urlStat->setUserAgent($additional['user-agent']);
            }

            $this->em->persist($urlStat);
        }

        $this->em->persist($url);
        $this->em->flush();

        return true;
    }
} 
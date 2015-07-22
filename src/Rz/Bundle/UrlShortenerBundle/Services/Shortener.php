<?php

namespace Rz\Bundle\UrlShortenerBundle\Services;

use Rz\Bundle\UrlShortenerBundle\UrlShortenerEvents;
use Rz\Bundle\UrlShortenerBundle\Event\UrlEvent;
use Rz\Bundle\UrlShortenerBundle\Services\Encoder\Base;
use Rz\Bundle\UrlShortenerBundle\Services\Encoder\EncoderInterface;
use Rz\Bundle\UrlShortenerBundle\Entity\Url;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class Shortener
{
    const NOTIFY_TYPE_REDIRECT = 'redirect';
    const NOTIFY_TYPE_CREATE = 'create';

    /**
     * @var Base
     */
    private $encoder;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EncoderInterface $encoder
     * @param RouterInterface $router
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        EncoderInterface $encoder,
        RouterInterface $router,
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $dispatcher
    )
    {
        $this->encoder = $encoder;
        $this->router = $router;
        $this->em = $entityManager;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param Url $url
     * @return Url|object
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function encode(Url $url)
    {
        $this->em->beginTransaction();

        try {
            $urlRepository = $this->em->getRepository('Rz\Bundle\UrlShortenerBundle\Entity\Url');

            $entity = $urlRepository->findOneBy([
                'url' => $url->getUrl()
            ]);

            if ($entity) {
                /** @var Url $url */
                $url = $entity;
            } else {
                $url->setNew(true);

                $this->em->persist($url);
                $this->em->flush();

                $url->setCode($this->encoder->encode($url->getId()));

                $params = ['encoded' => $url->getCode()];

                if (!$url->isDefaultSequence()) {
                    $params['index'] = $url->getSequence();
                }

                $url->setShortUrl(
                    $this->router->generate('app_url_go', $params, UrlGeneratorInterface::ABSOLUTE_URL)
                );

                $this->em->persist($url);
                $this->em->flush();
            }

            $this->em->getConnection()->commit();
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();

            throw $e;
        }

        return $url;
    }

    /**
     * @param string $urlEncoded
     * @param null|int $index
     * @return Url
     */
    public function decode($urlEncoded, $index = null)
    {
        $repository = $this->em->getRepository('Rz\Bundle\UrlShortenerBundle\Entity\Url');

        /** @var Url $url */
        return $repository->findOneBy([
            'id' => $this->encoder->decode($urlEncoded)
        ]);
    }

    /**
     * @param Url $url
     * @param string $type
     * @param array $additional
     */
    public function notify(Url $url, $type = self::NOTIFY_TYPE_REDIRECT, $additional = [])
    {
        $this->dispatcher->dispatch(UrlShortenerEvents::NOTIFY, new UrlEvent($url, $type, $additional));
    }
}
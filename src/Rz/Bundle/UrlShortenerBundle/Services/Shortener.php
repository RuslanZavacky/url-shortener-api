<?php

namespace Rz\Bundle\UrlShortenerBundle\Services;

use Hateoas\Representation\CollectionRepresentation;
use Hateoas\Representation\PaginatedRepresentation;
use Rz\Bundle\UrlShortenerBundle\Entity\Repository\UrlStatRepository;
use Rz\Bundle\UrlShortenerBundle\Model\StatisticsCollectionParameters;
use Rz\Bundle\UrlShortenerBundle\UrlShortenerBundle;
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

                $params = ['code' => $url->getCode()];

                if (!$url->isDefaultSequence()) {
                    $params['index'] = $url->getSequence();
                }

                $url->setShortUrl(
                    $this->router->generate(UrlShortenerBundle::URL_GO, $params, UrlGeneratorInterface::ABSOLUTE_URL)
                );

                $this->em->persist($url);
                $this->em->flush();
            }

            $this->em->getConnection()->commit();

            return $url;
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();

            throw $e;
        }
    }

    /**
     * @param string $urlEncoded
     * @return Url
     */
    public function decode($urlEncoded)
    {
        $repository = $this->em->getRepository('Rz\Bundle\UrlShortenerBundle\Entity\Url');

        /** @var Url $url */
        return $repository->findOneBy([
            'id' => $this->encoder->decode($urlEncoded)
        ]);
    }

    /**
     * @param int $id
     * @param StatisticsCollectionParameters $parameters
     * @return Url
     */
    public function statistics($id, StatisticsCollectionParameters $parameters)
    {
        /** @var UrlStatRepository $repository */
        $repository = $this->em->getRepository('Rz\Bundle\UrlShortenerBundle\Entity\UrlStat');

        $count = $repository->getUrlStatCount($id);

        $statistics = $repository->getUrlStats(
            $id,
            $parameters->getFirstResult(),
            $parameters->getMaxResults()
        );

        $collection = new CollectionRepresentation($statistics);

        $paginatedCollection = new PaginatedRepresentation(
            $collection,
            'shortener_get_api_statistics', // route
            ['id' => $id],
            $parameters->getPage(),
            $parameters->getLimit(),
            $parameters->getPages($count),
            'page',
            'limit',
            true
        );

        return $paginatedCollection;
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

    /**
     * @param array $url
     * @param string $type
     * @param array $additional
     */
    public function notifyCollection($url = [], $type = self::NOTIFY_TYPE_REDIRECT, $additional = [])
    {
//        $this->dispatcher->dispatch(UrlShortenerEvents::NOTIFY, new UrlEvent($url, $type, $additional));
    }
}
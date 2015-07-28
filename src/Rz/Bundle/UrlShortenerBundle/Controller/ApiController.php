<?php

namespace Rz\Bundle\UrlShortenerBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Rz\Bundle\UrlShortenerBundle\Entity\Url;
use Rz\Bundle\UrlShortenerBundle\Model\StatisticsCollectionParameters;
use Rz\Bundle\UrlShortenerBundle\Services\Shortener;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Sensio;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ApiController
 *
 * @package UrlShortenerBundle\Controller
 */
class ApiController extends ControllerAbstract
{
    /**
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function postAction(Request $request)
    {
        $url = new Url();
        $url->setup(json_decode($request->getContent(), true));

        $shortener = $this->getShortener();

        if ($encoded = $shortener->encode($url)) {
            if ($encoded->isNew()) {
                // Notify only when record is created
                $shortener->notify(
                    $encoded,
                    Shortener::NOTIFY_TYPE_CREATE,
                    $this->getClientInfoFromRequest($request)
                );
            }

            return new JsonResponse(
                $encoded->toArray(),
                $encoded->isNew() ? Response::HTTP_CREATED : Response::HTTP_OK
            );
        }

        return new JsonResponse(
            null,
            Response::HTTP_BAD_REQUEST
        );
    }

    /**
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function postBatchAction(Request $request)
    {
        $urls = new ArrayCollection();

        $records = json_decode($request->getContent(), true);
        foreach ($records as $record) {
            $url = new Url();
            $url->setup($record);

            $urls->add($url);
        }

        $shortener = $this->getShortener();

        $responseCollection = [];
        $notifyCollection = [];

        foreach ($urls as $url) {
            $encoded = $shortener->encode($url);
            $responseCollection[] = $encoded->toArray();

            if ($encoded->isNew()) {
                $notifyCollection[] = $encoded;
            }
        }

        if ($notifyCollection) {
            // Notify only when records are created
            $shortener->notifyCollection(
                $notifyCollection,
                Shortener::NOTIFY_TYPE_CREATE,
                $this->getClientInfoFromRequest($request)
            );
        }

        return new JsonResponse($responseCollection, Response::HTTP_OK);
    }

    /**
     * @param null $code
     * @return JsonResponse
     */
    public function getDecodeAction($code = null)
    {
        $shortener = $this->getShortener();

        if ($url = $shortener->decode($code)) {
            return new JsonResponse($url->toArray(), Response::HTTP_OK);
        }

        return new JsonResponse(null, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param string|null $id
     * @param Request $request
     * @return JsonResponse
     */
    public function getStatisticsAction($id, Request $request)
    {
        return new JsonResponse(
            $this->getShortener()->statistics(
                $id,
                new StatisticsCollectionParameters($request->query->all())
            ),
            Response::HTTP_OK
        );
    }
}

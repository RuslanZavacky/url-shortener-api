<?php

namespace Rz\Bundle\UrlShortenerBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\RestBundle\View\View;
use Rz\Bundle\UrlShortenerBundle\Entity\Url;
use Rz\Bundle\UrlShortenerBundle\Model\StatisticsCollectionParameters;
use Rz\Bundle\UrlShortenerBundle\Services\Shortener;
use FOS\RestBundle\Controller\Annotations as FOS;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Sensio;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Ecentria\Libraries\EcentriaRestBundle\Annotation as EcentriaAnnotation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Stopwatch\Stopwatch;

/**
 * Class ApiController
 *
 * @package UrlShortenerBundle\Controller
 */
class ApiController extends ControllerAbstract
{
    /**
     *
     * @param Url $url
     * @param Request $request
     *
     * @FOS\Route(
     *      pattern = "/urls",
     *      options = {
     *          "expose" = true
     *      }
     * )
     *
     * @Sensio\ParamConverter(
     *      "url",
     *      class="Rz\Bundle\UrlShortenerBundle\Entity\Url",
     *      converter = "url_shortener.converter.url",
     * )
     *
     * @Method({"POST"})
     *
     * @return View
     */
    public function postAction(Url $url, Request $request)
    {
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

            return $this->view($encoded, $encoded->isNew() ? Response::HTTP_CREATED : Response::HTTP_OK);
        }

        return $this->view(null, Response::HTTP_BAD_REQUEST);
    }

    /**
     *
     * @param ArrayCollection $urls
     * @param Request $request
     *
     * @FOS\Route(
     *      pattern = "/urls/batches",
     *      options = {
     *          "expose" = true
     *      }
     * )
     *
     * @Sensio\ParamConverter(
     *      "urls",
     *      class="Rz\Bundle\UrlShortenerBundle\Entity\Url",
     *      converter = "url_shortener.converter.url_collection",
     * )
     *
     * @return View
     */
    public function postBatchAction(ArrayCollection $urls, Request $request)
    {
        $shortener = $this->getShortener();

        $responseCollection = [];
        $notifyCollection = [];

        foreach ($urls as $url) {
            $encoded = $shortener->encode($url);
            $responseCollection[] = $encoded;

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

        return $this->view($responseCollection, Response::HTTP_OK);
    }

    /**
     * @FOS\Route(
     *      pattern="/urls/{code}/decode",
     *      requirements = {
     *          "id" = "[\d]+"
     *      },
     *      options = {
     *          "expose" = true
     *      }
     * )
     *
     * @Method({"GET"})
     *
     * @param null $code
     * @return View
     */
    public function getDecodeAction($code = null)
    {
        $shortener = $this->getShortener();

        if ($url = $shortener->decode($code)) {
            return $this->view($url, 203);
        }

        return $this->view(new Url(), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @FOS\Route(
     *      pattern="/urls/{id}/statistics",
     *      requirements = {
     *          "id" = "[\d]+"
     *      },
     *      options = {
     *          "expose" = true
     *      }
     * )
     * @Method({"GET"})
     *
     * @Sensio\ParamConverter(
     *      "parameters",
     *      class="Rz\Bundle\UrlShortenerBundle\Model\StatisticsCollectionParameters",
     *      converter = "ecentria.api.converter.model",
     *      options = {"query" = true}
     * )
     *
     * @EcentriaAnnotation\AvoidTransaction()
     *
     * @param string|null $id
     * @param StatisticsCollectionParameters $parameters
     * @return View
     */
    public function getStatisticsAction($id, StatisticsCollectionParameters $parameters)
    {
        return $this->view($this->getShortener()->statistics($id, $parameters), 200);
    }
}

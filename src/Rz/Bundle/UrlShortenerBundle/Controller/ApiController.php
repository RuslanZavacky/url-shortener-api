<?php

namespace Rz\Bundle\UrlShortenerBundle\Controller;

use Rz\Bundle\UrlShortenerBundle\Entity\Url;
use Rz\Bundle\UrlShortenerBundle\Services\Shortener;
use FOS\RestBundle\Controller\Annotations as FOS;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Sensio;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Ecentria\Libraries\EcentriaRestBundle\Annotation as EcentriaAnnotation;

/**
 * Class ApiController
 *
 * @EcentriaAnnotation\Transactional(
 *      model="Rz\Bundle\UrlShortenerBundle\Entity\Url",
 *      relatedRoute="app_url_go"
 * )
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
     *      pattern = "/api/1.0/urls",
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
     * @return JsonResponse
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

            return new JsonResponse($encoded->toArray());
        }

        return new JsonResponse([]);
    }

    /**
     * @FOS\Route(
     *      pattern="api/1.0/urls/{encoded}/decode.{_format}",
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
     * @param null $encoded
     * @return JsonResponse
     */
    public function getDecodeAction($encoded = null)
    {
        $shortener = $this->getShortener();

        if ($url = $shortener->decode($encoded)) {
            return $this->view($url, 200);
        }

        return new JsonResponse([]);
    }
}

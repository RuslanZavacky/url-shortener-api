<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Url;
use AppBundle\Services\Shortener;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations as FOS;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Sensio;
use Symfony\Component\HttpFoundation\Response;
use Ecentria\Libraries\EcentriaRestBundle\Annotation as EcentriaAnnotation;

/**
 * Class UrlController
 *
 * @package AppBundle\Controller
 */
class UrlController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @Route("/")
     * @Method({"GET"})
     *
     * @return JsonResponse
     */
    public function getAction()
    {
        return new JsonResponse([]);
    }

    /**
     * @Route(
     *   "/go/{encoded}/{index}",
     *   name="app_url_go",
     *   defaults={"index" = null},
     *   requirements={"encoded"=".+", "index"="\d+"}
     * )
     * @Method({"GET"})
     *
     * @param string $encoded
     * @param string $paramsEncoded
     * @param string $index
     *
     * @return RedirectResponse
     */
    public function goAction($encoded = null, $paramsEncoded = null, $index = null)
    {
        $shortener = $this->getShortener();

        if ($url = $shortener->decode($encoded, $paramsEncoded, $index)) {
            $shortener->notify($url);

            return new RedirectResponse($url->getOriginalUrl());
        }

        /**
         * TODO: decode short URL, if do not exists, show 404
         * TODO: If URL found, and rabbit is enabled, send message to the rabbit queue (event)
         * TODO: prepare redirect response, and update counters for link
         */

        return new Response('', Response::HTTP_NOT_FOUND);
    }

    /**
     *
     * @param Url $url
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
     *      class="AppBundle\Entity\Url",
     *      converter = "app.url.converter.url",
     * )
     *
     * @Method({"POST"})
     *
     * @return JsonResponse
     */
    public function postAction(Url $url)
    {
        if ($encoded = $this->getShortener()->encode($url)) {
            return new JsonResponse($encoded->toArray());
        }

        return new JsonResponse([]);
    }

    /**
     * @Route("/api/1.0/urls/{encoded}/decode")
     * @Method({"GET"})
     *
     * @param null $encoded
     * @return JsonResponse
     */
    public function getDecodeAction($encoded = null)
    {
        $shortener = $this->getShortener();

        if ($url = $shortener->decode($encoded)) {
            return new JsonResponse($url->toArray());
        }

        return new JsonResponse([]);
    }

    /**
     * @return Shortener
     */
    private function getShortener()
    {
        return $this->get('app.url.shortener');
    }
}

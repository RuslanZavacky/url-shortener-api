<?php

namespace Rz\Bundle\UrlShortenerBundle\Controller;

use Rz\Bundle\UrlShortenerBundle\Services\Shortener;
use FOS\RestBundle\Controller\Annotations as FOS;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Sensio;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ecentria\Libraries\EcentriaRestBundle\Annotation as EcentriaAnnotation;

/**
 * Class UrlController
 *
 * @package UrlShortenerBundle\Controller
 */
class UrlController extends ControllerAbstract
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
     *   defaults={"index" = 1},
     *   requirements={"encoded"=".+", "index"="\d+"}
     * )
     * @Method({"GET"})
     *
     * @param string $encoded
     * @param string $index
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function goAction($encoded = null, $index = null, Request $request)
    {
        $shortener = $this->getShortener();

        if ($url = $shortener->decode($encoded, $index)) {
            $shortener->notify(
                $url,
                Shortener::NOTIFY_TYPE_REDIRECT,
                $this->getClientInfoFromRequest($request)
            );

            return new RedirectResponse($url->getOriginalUrl());
        }

        return new Response('', Response::HTTP_NOT_FOUND);
    }
}

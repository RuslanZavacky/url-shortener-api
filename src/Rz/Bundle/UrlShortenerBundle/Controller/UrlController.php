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
        return $this->view(null, Response::HTTP_OK);
    }

    /**
     * @Route(
     *   "/go/{code}/{index}",
     *   name="shortener_go",
     *   defaults={"index" = 1, "code"=""},
     *   requirements={"code"=".+", "index"="\d+"}
     * )
     * @Method({"GET"})
     *
     * @param string $code
     * @param string $index
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function goAction($code = null, $index = null, Request $request)
    {
        $shortener = $this->getShortener();

        if ($url = $shortener->decode($code, $index)) {
            $shortener->notify(
                $url,
                Shortener::NOTIFY_TYPE_REDIRECT,
                $this->getClientInfoFromRequest($request)
            );

            return new RedirectResponse($url->getOriginalUrl());
        }

        return $this->view(null, Response::HTTP_NOT_FOUND);
    }
}

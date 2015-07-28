<?php

namespace Rz\Bundle\UrlShortenerBundle\Controller;

use Rz\Bundle\UrlShortenerBundle\Services\Shortener;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Sensio;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UrlController
 *
 * @package UrlShortenerBundle\Controller
 */
class UrlController extends ControllerAbstract
{
    /**
     * @return JsonResponse
     */
    public function getAction()
    {
        return new JsonResponse(null, Response::HTTP_OK);
    }

    /**
     * @param string $code
     * @param string $index
     * @param Request $request
     *
     * @return RedirectResponse|JsonResponse
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

        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
}

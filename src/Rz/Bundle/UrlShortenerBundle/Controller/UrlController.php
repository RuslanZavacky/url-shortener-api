<?php

namespace Rz\Bundle\UrlShortenerBundle\Controller;

use Rz\Bundle\UrlShortenerBundle\Entity\Url;
use Rz\Bundle\UrlShortenerBundle\Services\Shortener;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
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
        return $this->get('url_shortener.shortener');
    }

    /**
     * @param Request $request
     * @return array
     */
    private function getClientInfoFromRequest(Request $request)
    {
        return [
            'ip'         => $request->getClientIp(),
            'user-agent' => $request->headers->get('User-Agent')
        ];
    }
}

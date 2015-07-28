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
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerAware;
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
class ControllerAbstract extends ContainerAware
{
    /**
     * @return Shortener
     */
    protected function getShortener()
    {
        return $this->container->get('url_shortener.shortener');
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getClientInfoFromRequest(Request $request)
    {
        return [
            'ip'         => $request->getClientIp(),
            'user-agent' => $request->headers->get('User-Agent')
        ];
    }
}

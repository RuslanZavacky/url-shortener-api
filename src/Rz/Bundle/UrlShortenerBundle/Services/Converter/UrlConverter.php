<?php

namespace Rz\Bundle\UrlShortenerBundle\Services\Converter;

use Rz\Bundle\UrlShortenerBundle\Entity\Url;
use Ecentria\Libraries\EcentriaRestBundle\Model\Alias;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\Serializer;
use Rz\Bundle\UrlShortenerBundle\Helpers;

class UrlConverter implements ParamConverterInterface
{
    /**
     * Serializer
     *
     * @var Serializer
     */
    protected $serializer;

    /**
     * Constructor
     *
     * @param Serializer $serializer Serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * Stores the object in the request.
     *
     * @param Request $request The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return bool True if the object has been successfully set, else false
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $name = $configuration->getName();
        $class = $configuration->getClass();

        $content = json_decode($request->getContent(), true);

        /** @var Url $model */
        $model = $this->deserializeUrl($content, $class);

        $request->attributes->set($name, $model);
        $request->attributes->set(Alias::DATA, $name);

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return bool True if the object is supported, else false
     */
    public function supports(ParamConverter $configuration)
    {
        return true;
    }

    protected function deserializeUrl($content, $class)
    {
        $content['original_url'] = $content['url'];

        $parsedUrl = parse_url($content['url']);

        $urlParts = $parsedUrl;

        if (!empty($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $parsedQuery);

            if ($parsedQuery) {
                ksort($parsedQuery);
                $urlParts['query'] = http_build_query($parsedQuery);
                $content['query_param'] = $parsedQuery;
            }
        }

        $content['url'] = Helpers\Url::buildUrl($urlParts);

        /** @var Url $model */
        return $this->serializer->deserialize(json_encode($content), $class, 'json');
    }
} 
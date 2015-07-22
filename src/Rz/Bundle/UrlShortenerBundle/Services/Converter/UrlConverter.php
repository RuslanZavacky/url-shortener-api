<?php

namespace Rz\Bundle\UrlShortenerBundle\Services\Converter;

use Rz\Bundle\UrlShortenerBundle\Entity\Url;
use Ecentria\Libraries\EcentriaRestBundle\Model\Alias;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use JMS\Serializer\Serializer;

class UrlConverter implements ParamConverterInterface
{
    /**
     * Serializer
     *
     * @var Serializer
     */
    private $serializer;

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

        $contentDecoded = json_decode($request->getContent(), true);

        $contentDecoded['original_url'] = $contentDecoded['url'];

        $parsedUrl = parse_url($contentDecoded['url']);

        $urlParts = $parsedUrl;

        if (!empty($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $parsedQuery);

            if ($parsedQuery) {
                ksort($parsedQuery);
                $urlParts['query'] = http_build_query($parsedQuery);
                $contentDecoded['query_param'] = $parsedQuery;
            }
        }

        $contentDecoded['url'] = $this->buildUrl($urlParts);

        /** @var Url $model */
        $model = $this->serializer->deserialize(json_encode($contentDecoded), $class, 'json');

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

    private function buildUrl($url = [])
    {
        $scheme = isset($url['scheme']) ? $url['scheme'] . '://' : '';
        $host = isset($url['host']) ? $url['host'] : '';
        $port = isset($url['port']) ? ':' . $url['port'] : '';
        $user = isset($url['user']) ? $url['user'] : '';
        $pass = isset($url['pass']) ? ':' . $url['pass'] : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = isset($url['path']) ? $url['path'] : '';
        $query = isset($url['query']) ? '?' . $url['query'] : '';
        $fragment = isset($url['fragment']) ? '#' . $url['fragment'] : '';

        return "$scheme$user$pass$host$port$path$query$fragment";
    }

} 
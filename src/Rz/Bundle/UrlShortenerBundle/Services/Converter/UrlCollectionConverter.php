<?php

namespace Rz\Bundle\UrlShortenerBundle\Services\Converter;

use Doctrine\Common\Collections\ArrayCollection;
use Ecentria\Libraries\EcentriaRestBundle\Model\Alias;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;

class UrlCollectionConverter extends UrlConverter
{
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

        $contents = json_decode($request->getContent(), true);

        $collection = new ArrayCollection();
        foreach ($contents as $content) {
            $collection->add($this->deserializeUrl($content, $class));
        }

        $request->attributes->set($name, $collection);
        $request->attributes->set(Alias::DATA, $name);

        return true;
    }
} 
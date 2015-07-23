<?php

namespace Rz\Bundle\UrlShortenerBundle;

use Rz\Bundle\UrlShortenerBundle\DependencyInjection\Compiler\ListenersCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class UrlShortenerBundle extends Bundle
{
    const URL_GO = 'shortener_go';

    /**
     * Builds the bundle
     *
     * @param ContainerBuilder $container Container
     *
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ListenersCompilerPass(), PassConfig::TYPE_OPTIMIZE);
    }
}

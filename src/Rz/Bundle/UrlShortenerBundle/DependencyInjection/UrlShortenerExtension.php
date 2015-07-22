<?php

namespace Rz\Bundle\UrlShortenerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class UrlShortenerExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (!empty($config['messaging'])) {
            $container->setParameter('url_shortener.messaging.enabled', $config['messaging']['enabled']);
            $container->setParameter('url_shortener.messaging.producer_name', $config['messaging']['producer_name']);
            $container->setParameter('url_shortener.messaging', $config['messaging']);
        }

        if (!empty($config['stats'])) {
            $container->setParameter('url_shortener.stats.enabled', $config['stats']['enabled']);
            $container->setParameter('url_shortener.stats', $config['stats']);
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'url_shortener';
    }
}
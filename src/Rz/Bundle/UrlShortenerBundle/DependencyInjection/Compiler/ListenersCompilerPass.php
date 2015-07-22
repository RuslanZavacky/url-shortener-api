<?php
namespace Rz\Bundle\UrlShortenerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class ListenersCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $this->enableMessaging($container);
        $this->enableStats($container);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function enableMessaging(ContainerBuilder $container)
    {
        $messagingEnabled = $container->getParameter('url_shortener.messaging.enabled');
        $hasDefinition = $container->hasDefinition('url_shortener.listeners.message');

        if ($messagingEnabled && $hasDefinition) {
            $definition = $container->getDefinition('url_shortener.listeners.message');
            $definition->addMethodCall('enable');

            $producerName = $container->getParameter('url_shortener.messaging.producer_name');

            if ($producerName) {
                $producerName = 'old_sound_rabbit_mq.' . $producerName . '_producer';
                $definition->addMethodCall('setProducer', [new Reference($producerName)]);
            }
        }
    }
    /**
     * @param ContainerBuilder $container
     */
    private function enableStats(ContainerBuilder $container)
    {
        $messagingEnabled = $container->getParameter('url_shortener.stats.enabled');
        $hasDefinition = $container->hasDefinition('url_shortener.listeners.stats');

        if ($messagingEnabled && $hasDefinition) {
            $definition = $container->getDefinition('url_shortener.listeners.stats');
            $definition->addMethodCall('enable');
        }
    }
} 
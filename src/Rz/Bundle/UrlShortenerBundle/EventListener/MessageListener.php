<?php
namespace Rz\Bundle\UrlShortenerBundle\EventListener;

use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Rz\Bundle\UrlShortenerBundle\Event\UrlEvent;

class MessageListener implements ConfigurableListenerInterface
{
    use ConfigurableListenerTrait;

    /** @var ProducerInterface */
    private $producer;

    public function setProducer(ProducerInterface $producer = null)
    {
        $this->producer = $producer;
    }

    public function notify(UrlEvent $event)
    {
        if (!$this->isEnabled() || !$event->getUrl() || !$this->producer) {
            return false;
        }

        try {
            $message = array_merge([
                'url' => $event->getUrl()->toArray(),
                'type' => $event->getType(),
                'created_on' => (new \DateTime())->format(\DateTime::ISO8601)
            ], $event->getAdditional());

            $this->producer->publish(json_encode($message));
        } catch (\Exception $e) {
            // TODO: log message
        }

        return true;
    }
} 
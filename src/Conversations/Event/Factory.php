<?php

namespace Nexmo\Conversations\Event;

class Factory
{
    public static function build(array $data) : EventInterface
    {
        switch ($data['type']) {
            case 'text':
                $event = new TextEvent();
                break;
            default:
                throw new \RuntimeException('Unknown event type');
                break;
        }

        $event->fromArray($data);
        return $event;
    }
}

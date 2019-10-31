<?php

namespace Nexmo\Conversations;

use Nexmo\Conversations\Event\Client as EventClient;

class Hydrator
{
    /**
     * @var EventClient
     */
    protected $eventClient;

    public function __construct(EventClient $eventClient)
    {
        $this->eventClient = $eventClient;
    }

    public function hydrate(array $data)
    {
        $conversation = new Conversation();
        $conversation->createFromArray($data);
        $conversation->setEventClient($this->eventClient);

        return $conversation;
    }
}
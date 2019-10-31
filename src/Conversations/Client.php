<?php

namespace Nexmo\Conversations;

use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Entity\Collection;
use Nexmo\Conversations\Event\Event;

class Client implements ClientAwareInterface
{
    use ClientAwareTrait;

    protected $api;

    public function delete(Conversation $conversation) : void
    {
        $this->api->deleteConversation($conversation);
    }

    protected function fillGenerators(Conversation $conversation) : Conversation
    {
        $conversation->setEvents($this->api->getEventsGenerator($conversation));
        $conversation->getEvents()->setPrototype(Event::class);

        return $conversation;
    }

    public function get(string $id) : Conversation
    {
        $data = $this->api->getConversation($id);

        $conversation = new Conversation();
        $conversation->createFromArray($data);
        $conversation = $this->fillGenerators($conversation);

        return $conversation;
    }

    public function create(Conversation $conversation) : Conversation
    {
        $response = $this->api->createConversation($conversation);
        $conversation->createFromArray($response);
        $conversation = $this->fillGenerators($conversation);

        return $conversation;
    }

    public function search(FilterInterface $filter = null) : Collection
    {
        $collection = $this->api->searchConversations($filter);
        $collection->setPrototype(Conversation::class);

        return $collection;
    }

    public function setAPI(API $api)
    {
        $this->api = $api;
    }

    public function update(Conversation $conversation) : Conversation
    {
        $data = $this->api->updateConversation($conversation);
        $conversation->createFromArray($data);

        return $conversation;
    }
}

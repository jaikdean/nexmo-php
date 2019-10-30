<?php

namespace Nexmo\Conversations;

use Nexmo\Entity\Collection;

class Client
{
    protected $api;

    public function delete(Conversation $conversation) : void
    {
        $this->api->deleteConversation($conversation);
    }

    public function get(string $id) : Conversation
    {
        $data = $this->api->getConversation($id);

        $conversation = new Conversation();
        $conversation->createFromArray($data);

        return $conversation;
    }

    public function create(Conversation $conversation) : Conversation
    {
        $response = $this->api->createConversation($conversation);
        $conversation->createFromArray($response);

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

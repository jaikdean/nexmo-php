<?php

namespace Nexmo\Conversations;

use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Client\OpenAPIResource;
use Nexmo\Entity\Collection;

class Client implements ClientAwareInterface
{
    use ClientAwareTrait;

    /**
     * @var API
     */
    protected $conversationAPI;
    
    /**
     * @var Hydrator
     */
    protected $hydrator;

    public function __construct(OpenAPIResource $conversationAPI, Hydrator $hydrator)
    {
        $this->conversationAPI = $conversationAPI;
        $this->hydrator = $hydrator;
    }

    public function delete(Conversation $conversation) : void
    {
        $this->conversationAPI->deleteConversation($conversation);
    }

    public function get(string $id) : Conversation
    {
        $data = $this->conversationAPI->get($id);
        $conversation = $this->hydrator->hydrate($data);

        return $conversation;
    }

    public function create(Conversation $conversation) : Conversation
    {
        $response = $this->conversationAPI->create($conversation);
        $conversation = $this->hydrator->hydrate($response);

        return $conversation;
    }

    public function search(FilterInterface $filter = null) : Collection
    {
        $collection = $this->conversationAPI->search($filter);
        $collection->setHydrator($this->hydrator);

        return $collection;
    }

    public function setAPI(API $api)
    {
        $this->api = $api;
    }

    public function update(Conversation $conversation) : Conversation
    {
        $data = $this->conversationAPI->update($conversation);
        $conversation = $this->hydrator->hydrate($data);

        return $conversation;
    }
}

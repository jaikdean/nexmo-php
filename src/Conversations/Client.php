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
     * @var OpenAPIResource
     */
    protected $api;

    /**
     * @var Hydrator
     */
    protected $hydrator;

    public function __construct(OpenAPIResource $api, Hydrator $hydrator)
    {
        $this->api = $api;
        $this->hydrator = $hydrator;
    }

    public function create(Conversation $conversation) : Conversation
    {
        $response = $this->getApi()->create($conversation);
        $conversation = $this->hydrator->hydrate($response);

        return $conversation;
    }

    public function delete(Conversation $conversation) : void
    {
        $this->getApi()->delete($conversation);
    }

    public function get(string $id) : Conversation
    {
        $data = $this->getApi()->get($id);
        $conversation = $this->hydrator->hydrate($data);

        return $conversation;
    }

    public function getApi() : OpenAPIResource
    {
        return $this->api;
    }

    public function search(FilterInterface $filter = null) : Collection
    {
        $collection = $this->getApi()->search($filter);
        $collection->setHydrator($this->hydrator);

        return $collection;
    }

    public function update(Conversation $conversation) : Conversation
    {
        $data = $this->getApi()->update($conversation);
        $conversation = $this->hydrator->hydrate($data);

        return $conversation;
    }
}

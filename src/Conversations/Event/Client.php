<?php

namespace Nexmo\Conversations\Event;

use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Client\OpenAPIResource;
use Nexmo\Entity\Collection;
use Nexmo\Entity\FilterInterface;

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

    public function create(Event $event) : Event
    {
        $response = $this->getApi()->create($event);
        $event = $this->hydrator->hydrate($response);

        return $event;
    }

    public function delete(Event $event) : void
    {
        $this->getApi()->delete($event);
    }

    public function get(string $id) : Event
    {
        $data = $this->getApi()->get($id);
        $event = $this->hydrator->hydrate($data);

        return $event;
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
}

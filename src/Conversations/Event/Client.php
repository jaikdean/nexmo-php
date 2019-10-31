<?php

namespace Nexmo\Conversations\Event;

use Nexmo\Entity\Collection;
use Nexmo\Entity\FilterInterface;

class Client
{
    protected $api;

    protected $hydrator;

    public function __construct(API $api, Hydrator $hydrator)
    {
        $this->api = $api;
        $this->hydrator = $hydrator;
    }

    public function create(Event $event) : Event
    {
        $response = $this->api->create($event);
        $event = $this->hydrator->hydrate($response);

        return $event;
    }

    public function delete(Event $event) : void
    {
        $this->api->delete($event);
    }

    public function get(string $id) : Event
    {
        $data = $this->getAPI()->get($id);
        $event = $this->hydrator->hydrate($data);

        return $event;
    }

    public function getAPI() : API
    {
        return $this->api;
    }

    public function search(FilterInterface $filter = null) : Collection
    {
        $collection = $this->api->search($filter);
        $collection->setHydrator($this->hydrator);

        return $collection;
    }
}

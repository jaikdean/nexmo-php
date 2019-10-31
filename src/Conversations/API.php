<?php

namespace Nexmo\Conversations;

use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Entity\Collection;
use Nexmo\Entity\SimpleFilter;
use Zend\Diactoros\Request;

class API implements ClientAwareInterface
{
    use ClientAwareTrait;

    protected $baseUri = '/v0.1/conversations';

    public function createConversation(Conversation $conversation)
    {
        $body = $conversation->toArray();
        unset($body['id'], $body['timestamp']);

        $request = new Request(
            $this->getClient()->getApiUrl() . $this->baseUri,
            'POST',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($body));
        $response = $this->getClient()->send($request);

        if ($response->getStatusCode() != '200') {
            throw $this->getException($response);
        }

        return json_decode($response->getBody()->getContents(), true);
    }

    public function deleteConversation(Conversation $conversation) : void
    {
        $uri = $this->getClient()->getApiUrl() . $this->baseUri . '/' . $conversation->getId();
        $request = new Request($uri, 'DELETE');

        $response = $this->getClient()->send($request);
        $body = json_decode($response->getBody()->getContents(), true);
    }

    public function getConversation($id)
    {
        $uri = $this->getClient()->getApiUrl() . $this->baseUri . '/' . $id;
        $request = new Request($uri, 'GET', 'php://temp', ['accept' => 'application/json']);

        $response = $this->getClient()->send($request);
        $body = json_decode($response->getBody()->getContents(), true);

        return $body;
    }

    public function getEventsGenerator(Conversation $conversation) : Collection
    {
        $events = new Collection();
        $events
            ->setCollectionName('events')
            ->setCollectionPath(
                $this->getClient()->getApiUrl() . $this->baseUri . '/' . $conversation->getId() . '/events'
            )
        ;
        $events->setClient($this->client);
        $events->rewind();

        return $events;
    }

    public function searchConversations(FilterInterface $filter = null) : Collection
    {
        if (is_null($filter)) {
            $filter = new SimpleFilter();
        }

        $collection = new Collection();
        $collection
            ->setFilter($filter)
            ->setCollectionName('conversations')
            ->setCollectionPath($this->getClient()->getApiUrl() . $this->baseUri)
        ;
        $collection->setClient($this->client);
        $collection->rewind();

        return $collection;
    }

    public function updateConversation(Conversation $conversation) : array
    {
        $body = $conversation->toArray();

        $request = new Request(
            $this->getClient()->getApiUrl() . $this->baseUri . '/' . $conversation->getId(),
            'PUT',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($body));
        $response = $this->getClient()->send($request);

        if ($response->getStatusCode() != '200') {
            throw $this->getException($response);
        }

        return json_decode($response->getBody()->getContents(), true);
    }
}

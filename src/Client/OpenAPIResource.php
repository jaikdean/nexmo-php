<?php

namespace Nexmo\Client;

use Nexmo\Entity\ArrayHydrateInterface;
use Nexmo\Entity\Collection;
use Nexmo\Entity\EmptyFilter;
use Nexmo\Entity\FilterInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;

class OpenAPIResource implements ClientAwareInterface
{
    use ClientAwareTrait;

    protected $baseUri;

    protected $collectionName;

    public function create(ArrayHydrateInterface $entity)
    {
        $body = $entity->toArray();
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

    public function delete(ArrayHydrateInterface $entity) : void
    {
        $uri = $this->getClient()->getApiUrl() . $this->baseUri . '/' . $entity->getId();
        $request = new Request($uri, 'DELETE');

        $response = $this->getClient()->send($request);
    }

    public function get($id)
    {
        $uri = $this->getClient()->getApiUrl() . $this->baseUri . '/' . $id;
        $request = new Request($uri, 'GET', 'php://temp', ['accept' => 'application/json']);

        $response = $this->getClient()->send($request);
        $body = json_decode($response->getBody()->getContents(), true);

        return $body;
    }

    public function getCollectionName() : string
    {
        return $this->collectionName;
    }

    protected function getException(ResponseInterface $response)
    {
        $body = json_decode($response->getBody()->getContents(), true);
        $status = $response->getStatusCode();

        // Error responses aren't consistent. Some are generated within the
        // proxy and some are generated within voice itself. This handles
        // both cases

        // This message isn't very useful, but we shouldn't ever see it
        $errorTitle = 'Unexpected error';

        if (isset($body['title'])) {
            $errorTitle = $body['title'];
        }

        if (isset($body['error_title'])) {
            $errorTitle = $body['error_title'];
        }

        if ($status >= 400 and $status < 500) {
            $e = new Exception\Request($errorTitle, $status);
        } elseif ($status >= 500 and $status < 600) {
            $e = new Exception\Server($errorTitle, $status);
        } else {
            $e = new Exception\Exception('Unexpected HTTP Status Code');
            throw $e;
        }

        return $e;
    }

    public function search(FilterInterface $filter = null) : Collection
    {
        if (is_null($filter)) {
            $filter = new EmptyFilter();
        }

        $collection = new Collection();
        $collection
            ->setFilter($filter)
            ->setCollectionName($this->getCollectionName())
            ->setCollectionPath($this->getClient()->getApiUrl() . $this->baseUri)
        ;
        $collection->setClient($this->client);
        $collection->rewind();

        return $collection;
    }

    public function setBaseUri(string $uri) : self
    {
        $this->baseUri = $uri;
        return $this;
    }

    public function setCollectionName(string $name) : self
    {
        $this->collectionName = $name;
        return $this;
    }

    public function update(ArrayHydrateInterface $entity) : array
    {
        $body = $entity->toArray();

        $request = new Request(
            $this->getClient()->getApiUrl() . $this->baseUri . '/' . $entity->getId(),
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

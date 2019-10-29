<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2018 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Conversations\Event;

use InvalidArgumentException;
use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Entity\CollectionInterface;
use Nexmo\Entity\CollectionTrait;
use Nexmo\Entity\JsonResponseTrait;
use Nexmo\Entity\JsonSerializableTrait;
use Nexmo\Entity\NoRequestResponseTrait;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Nexmo\Client\Exception;
use Nexmo\Client\Exception\Request as NexmoRequest;
use Nexmo\Client\Exception\Server;
use Nexmo\Conversations\Conversation;
use Nexmo\Conversations\Event\EventInterface;
use Nexmo\Conversations\Event\Factory;
use RuntimeException;

class Collection implements ClientAwareInterface, CollectionInterface, \ArrayAccess
{
    use ClientAwareTrait;
    use CollectionTrait;
    use JsonSerializableTrait;
    use NoRequestResponseTrait;
    use JsonResponseTrait;

    /**
     * Base conversation that we will up against
     * @var Conversation
     */
    protected $conversation;

    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }

    public static function getCollectionName()
    {
        return 'conversations';
    }

    public function getCollectionPath()
    {
        return '/v0.1/' . $this->getCollectionName() . '/' . $this->conversation->getId();
    }

    public function hydrateEntity($data, $idOrConversation)
    {
        $event = Factory::build($data);
        return $event;
    }

    public function hydrateAll($events)
    {
        $hydrated = [];
        foreach ($events as $event) {
            $hydrated[] = $this->hydrateEntity($event, $event['id']);
        }

        return $hydrated;
    } 

    public function __invoke(Filter $filter = null) : self
    {
        if (!is_null($filter)) {
            $this->setFilter($filter);
        }

        return $this;
    }

    public function create($conversation)
    {
        return $this->post($conversation);
    }

    public function post(Conversation $conversation) : Conversation
    {
        $body = $conversation->toArray();

        $request = new Request(
            $this->getClient()->getApiUrl() . $this->getCollectionPath(),
            'POST',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($body));
        $response = $this->getClient()->send($request);

        if ($response->getStatusCode() != '200') {
            throw $this->getException($response);
        }

        $body = json_decode($response->getBody()->getContents(), true);
        $conversation = new Conversation($body['id']);
        $conversation->createFromArray($body);
        $conversation->setClient($this->getClient());

        return $conversation;
    }

    public function get($eventId)
    {
        $event = new Event();
        $event->setId($eventId);
        $event->setClient($this->getClient());
        $event->get();

        return $event;
    }

    public function getEvents(Conversation $conversation)
    {
        $uri = sprintf(
            "%s%s/%s/events",
            $this->getClient()->getApiUrl(),
            $this->getCollectionPath(),
            $conversation->getId()
        );

        $request = new Request(
            $uri,
            'GET',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $response = $this->getClient()->send($request);
        $body = json_decode($response->getBody()->getContents(), true);
        var_dump($body);
    }

    protected function getException(ResponseInterface $response)
    {
        $body = json_decode($response->getBody()->getContents(), true);
        $status = $response->getStatusCode();

        // This message isn't very useful, but we shouldn't ever see it
        $errorTitle = 'Unexpected error';

        if (isset($body['description'])) {
            $errorTitle = $body['description'];
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

    public function offsetExists($offset)
    {
        return true;
    }

    /**
     * @param mixed $conversation
     * @return Conversation
     */
    public function offsetGet($conversation)
    {
        if (!($conversation instanceof Conversation)) {
            $conversation = new Conversation($conversation);
        }

        $conversation->setClient($this->getClient());
        return $conversation;
    }

    public function offsetSet($offset, $value)
    {
        throw new \RuntimeException('can not set collection properties');
    }

    public function offsetUnset($offset)
    {
        throw new \RuntimeException('can not unset collection properties');
    }

    public function delete(Conversation $conversation) : bool
    {
        $request = new Request(
            $this->getClient()->getApiUrl() . $this->getCollectionPath() . '/' . $conversation->getId(),
            'DELETE',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $response = $this->client->send($request);

        if ($response->getStatusCode() == 204) {
            return true;
        } else {
            $this->getException($response);
        }

        return false;
    }

    public function update(Conversation $conversation) : bool
    {
        $body = $conversation->toArray();

        $request = new Request(
            $this->getClient()->getApiUrl() . $this->getCollectionPath() . '/' . $conversation->getId(),
            'PUT',
            'php://temp',
            ['content-type' => 'application/json']
        );

        $request->getBody()->write(json_encode($body));
        $response = $this->client->send($request);

        if ($response->getStatusCode() == 200) {
            return true;
        } else {
            $this->getException($response);
        }

        return false;
    }
}

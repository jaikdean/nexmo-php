<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

declare(strict_types = 1);

namespace Nexmo\Conversations;

use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Entity\EntityInterface;
use Nexmo\Entity\JsonResponseTrait;
use Nexmo\Entity\JsonSerializableTrait;
use Nexmo\Entity\JsonUnserializableInterface;
use Nexmo\Entity\NoRequestResponseTrait;
use Nexmo\User\Collection as UserCollection;
use Nexmo\User\User;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Nexmo\Client\Exception;
use Nexmo\User\Filter;

class Conversation implements EntityInterface, \JsonSerializable, JsonUnserializableInterface, ClientAwareInterface
{
    use NoRequestResponseTrait;
    use JsonSerializableTrait;
    use JsonResponseTrait;
    use ClientAwareTrait;

    protected $data = [];

    public function __construct($id = null)
    {
        $this->data['id'] = $id;
    }

    public function setName(string $name) : self
    {
        $this->data['name'] = $name;
        return $this;
    }

    public function setDisplayName(string $name) : self
    {
        $this->data['display_name'] = $name;
        return $this;
    }

    public function setImageUrl(string $url) : self
    {
        $this->data['image_url'] = $url;
        return $this;
    }

    public function setProperties(array $properties) : self
    {
        $this->data['properties'] = $properties;
        return $this;
    }

    public function setProperty(string $key, string $value) : self
    {
        $this->data['properties'][$key] = $value;
        return $this;
    }

    public function getId() : string
    {
        if (isset($this->data['uuid'])) {
            return $this->data['uuid'];
        }
        return $this->data['id'];
    }

    public function getName() : string
    {
        return $this->data['name'];
    }

    public function getDisplayName() : string
    {
        return $this->data['display_name'];
    }

    public function getImageUrl() : string
    {
        return $this->data['image_url'];
    }

    public function getProperties() : array
    {
        return $this->data['properties'];
    }

    public function getProperty($key) : array
    {
        return $this->data['properties'];
    }

    public function __toString()
    {
        return (string)$this->getId();
    }


    public function get() : self
    {
        $request = new Request(
            $this->getClient()->getApiUrl() . Collection::getCollectionPath() . '/' . $this->getId(),
            'GET'
        );

        $response = $this->getClient()->send($request);

        if ($response->getStatusCode() != '200') {
            throw $this->getException($response);
        }

        $data = json_decode($response->getBody()->getContents(), true);
        $this->jsonUnserialize($data);

        return $this;
    }

    public function jsonSerialize() : array
    {
        return $this->toArray();
    }

    public function jsonUnserialize(array $json)
    {
        $this->createFromArray($json);
    }

    public function members(Filter $filter = null) : UserCollection
    {
        $userCollection = new UserCollection();
        $userCollection->setClient($this->getClient());

        if (is_null($filter)) {
            $filter = new Filter();
            $filter->setConversationId($this->getId());
        }

        $userCollection->setFilter($filter);
        return $userCollection;
    }

    public function addMember(User $user)
    {
        return $this->sendPostAction($user, 'join');
    }

    public function inviteMember(User $user)
    {
        return $this->sendPostAction($user, 'invite');
    }

    public function removeMember(User $user)
    {
        $response = $this->getClient()->delete(
            $this->getClient()->getApiUrl() . Collection::getCollectionPath() . '/' . $this->getId() .'/members/'. $user->getId()
        );

        if ($response->getStatusCode() != '204') {
            throw $this->getException($response);
        }
    }

    public function sendPostAction(User $user, $action, $channel = 'app') : User
    {
        if (is_null($user->getId())) {
            throw new \InvalidArgumentException('User must be created before it can be used with a Conversation');
        }

        $body = [
            'user_id' => $user->getId(),
            'action' => $action,
            'channel' => ['type' => $channel]
        ];

        $response = $this->getClient()->post(
            $this->getClient()->getApiUrl() . Collection::getCollectionPath() . '/' . $this->getId() .'/members',
            $body
        );

        if ($response->getStatusCode() != '200') {
            throw $this->getException($response);
        }

        $body = json_decode($response->getBody()->getContents(), true);

        $user = new User($body['user_id']);
        $user->jsonUnserialize($body);
        $user->setClient($this->getClient());

        return $user;
    }

    protected function getException(ResponseInterface $response) : \Exception
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

    public function toArray() : array
    {
        return $this->data;
    }

    public function createFromArray(array $data) : void
    {
        if (array_key_exists('id', $data)) {
            $this->data['id'] = $data['id'];
        }

        if (array_key_exists('name', $data)) {
            $this->setName($data['name']);
        }

        if (array_key_exists('display_name', $data)) {
            $this->setDisplayName($data['display_name']);
        }

        if (array_key_exists('image_url', $data)) {
            $this->setImageUrl($data['image_url']);
        }

        if (array_key_exists('properties', $data)) {
            $this->setProperties($data['properties']);
        }
    }
}

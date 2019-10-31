<?php

namespace Nexmo\Conversations\Event;

use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Conversations\Conversation;
use Nexmo\Entity\Collection;
use Nexmo\Entity\FilterInterface;
use Nexmo\Entity\SimpleFilter;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;
use Nexmo\Client\Exception;
use Nexmo\Client\OpenAPIResource;

class API extends OpenAPIResource implements ClientAwareInterface
{
    protected $collectionName = 'events';

    public function setConversation(Conversation $conversation)
    {
        $this->setBaseUri('/v0.1/conversations/' . $conversation->getId() . '/events');
    }
}

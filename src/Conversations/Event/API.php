<?php

namespace Nexmo\Conversations\Event;

use Nexmo\Conversations\Conversation;
use Nexmo\Client\OpenAPIResource;

class API extends OpenAPIResource
{
    protected $collectionName = 'events';

    public function setConversation(Conversation $conversation)
    {
        $this->setBaseUri('/v0.1/conversations/' . $conversation->getId() . '/events');
    }
}

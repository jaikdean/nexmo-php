<?php

namespace Nexmo\Conversations\Member;

use Nexmo\Conversations\Conversation;
use Nexmo\Client\OpenAPIResource;
use Zend\Diactoros\Request;

class API extends OpenAPIResource
{
    protected $collectionName = 'members';

    public function setConversation(Conversation $conversation)
    {
        $this->setBaseUri('/v0.1/conversations/' . $conversation->getId() . '/members');
    }
}

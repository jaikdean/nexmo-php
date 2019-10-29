<?php

namespace Nexmo\Conversations\Event;

class TextEvent extends AbstractEvent implements EventInterface
{
    protected $type = "text";
}

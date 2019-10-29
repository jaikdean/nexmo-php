<?php

namespace Nexmo\Conversations\Event;

abstract class AbstractEvent
{
    /**
     * Contextual information about the event
     * @var array
     */
    protected $body = [];

    /**
     * ID of this specific event
     * @var string
     */
    protected $id;

    /**
     * Member who created the event
     * @var string
     */
    protected $from;

    /**
     * Timestamp of the event
     * @var \DateTime
     */
    protected $timestamp;

    /**
     * Type of event
     * @var string
     */
    protected $type;

    public function getBody() : array
    {
        return $this->body;
    }

    public function getId() : string
    {
        return $this->id;
    }

    public function getFrom() : string
    {
        return $this->from;
    }

    public function getTimestamp() : \DateTime
    {
        return $this->timestamp;
    }

    public function getType() : string
    {
        return $this->type;
    }

    public function setBody(array $body) : self
    {
        $this->body = $body;
        return $this;
    }

    public function setId(string $id) : self
    {
        $this->id = $id;
        return $this;
    }

    public function setFrom($from) : self
    {
        $this->from = $from;
        return $this;
    }

    public function setTimestamp(\DateTimeImmutable $timestamp) : self
    {
        $this->timestamp = $timestamp;
        return $this;
    }
}
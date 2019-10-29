<?php

namespace Nexmo\Conversations\Event;

interface EventInterface
{
    public function getBody() : array;

    public function getId() : string;

    public function getFrom() : string;

    public function getTimestamp() : \DateTime;

    public function getType() : string;

    public function setBody(array $body) : self;

    public function setId(string $id) : self;

    public function setFrom($from) : self;

    public function setTimestamp(\DateTimeImmutable $timestamp) : self;
}

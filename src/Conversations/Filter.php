<?php

namespace Nexmo\Conversations;

use Nexmo\Entity\FilterInterface;

class Filter implements FilterInterface
{
    protected $id;

    public function setId(string $id): void
    {
        $this->id = $id;
    }
    
    public function getQuery() : array
    {
        return ['id' => $this->id];
    }
}

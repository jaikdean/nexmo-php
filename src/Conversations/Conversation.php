<?php

namespace Nexmo\Conversations;

use Nexmo\Entity\Collection;

class Conversation
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $displayName;

    /**
     * @var Collection
     */
    protected $events;

    /**
     * @var string
     */
    protected $imageUrl;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array<string, string>
     */
    protected $properties = [];

    /**
     * @var \DateTimeImmutable
     */
    protected $timestamp;

    public function createFromArray($data)
    {
        if (array_key_exists('id', $data)) {
            $this->setId($data['id']);
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

        if (array_key_exists('timestamp', $data)) {
            $this->setTimestamp(new \DateTimeImmutable($data['timestamp']['created']));
        }
    }

    public function toArray() : array
    {
        $data = [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'display_name' => $this->getDisplayName(),
            'image_url' => $this->getImageUrl(),
            
        ];

        if ($this->getTimestamp()) {
            $data['timestamp'] = ['created' => $this->getTimestamp()->format(\DateTimeInterface::RFC3339_EXTENDED)];
        }
        return $data;
    }

    public function getId() : ?string
    {
        return $this->id;
    }

    public function getDisplayName() : ?string
    {
        return $this->displayName;
    }

    public function getEvents()
    {
        return $this->events;
    }

    public function getImageUrl() : ?string
    {
        return $this->imageUrl;
    }

    public function getName() : ?string
    {
        return $this->name;
    }

    public function getProperties() : array
    {
        return $this->properties;
    }

    public function getProperty($key) : string
    {
        return $this->properties[$key];
    }

    public function getTimestamp() : ?\DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setId(string $id) : self
    {
        $this->id = $id;
        return $this;
    }

    public function setDisplayName(string $displayName) : self
    {
        $this->displayName = $displayName;
        return $this;
    }

    public function setEvents(Collection $events) : self
    {
        $this->events = $events;
        return $this;
    }

    public function setImageUrl(string $url) : self
    {
        $this->imageUrl = $url;
        return $this;
    }

    public function setName(string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    public function setProperties(array $properties) : self
    {
        $this->properties = $properties;
        return $this;
    }

    public function setProperty(string $key, string $value) : self
    {
        $this->properties[$key] = $value;
        return $this;
    }

    public function setTimestamp(\DateTimeImmutable $timestamp) : self
    {
        $this->timestamp = $timestamp;
        return $this;
    }
}

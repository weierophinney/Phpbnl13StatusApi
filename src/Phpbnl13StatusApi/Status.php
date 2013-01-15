<?php

namespace Phpbnl13StatusApi;

use Zend\Math\Rand;

/**
 * Status post
 */
class Status implements StatusInterface
{
    protected $id;
    protected $type;
    protected $text;
    protected $user;
    protected $timestamp;
    protected $imageUrl;
    protected $linkUrl;
    protected $linkTitle;

    protected $validTypes = array(
        self::TYPE_STATUS,
        self::TYPE_IMAGE,
        self::TYPE_LINK,
    );

    public function setId($id)
    {
        if (!preg_match('/^[a-f0-9]{5,40}$/', $id)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Identifier provided, "%s", does not appear to be a valid sha1',
                $id
            ));
        }
        $this->id = $id;
    }

    public function setType($type)
    {
        if (!in_array($type, $this->validTypes)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Type provided, "%s", is invalid; please specify one of "%s"',
                $type,
                implode(', ', $this->validTypes)
            ));
        }
        $this->type = $type;
    }

    public function setText($text)
    {
        if (!is_string($text) || empty($text)) {
            throw new Exception\InvalidArgumentException(
                'Text must be a non-empty string'
            );
        }
        $this->text = $text;
    }

    public function setUser($user)
    {
        if (!is_string($user) || empty($user)) {
            throw new Exception\InvalidArgumentException(
                'User must be a non-empty string'
            );
        }
        $this->user = $user;
    }

    public function setTimestamp($timestamp)
    {
        if (!is_numeric($timestamp)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Timestamp provided, "%s", is invalid; please provide a valid timestamp integer',
                $timestamp
            ));
        }
        $this->timestamp = (int) $timestamp;
    }

    public function setImageUrl($url)
    {
        if (null !== $url
            && !filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Image URL must be valid; "%s" fails validation',
                $url
            ));
        }
        $this->imageUrl = $url;
    }

    public function setLinkUrl($url)
    {
        if (null !== $url
            && !filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)
        ) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Link URL must be valid; "%s" fails validation',
                $url
            ));
        }
        $this->linkUrl = $url;
    }

    public function setLinkTitle($title)
    {
        if (null !== $title 
            && (!is_string($title) || empty($title))
        ) {
            throw new Exception\InvalidArgumentException(
                'Link title must be a non-empty string'
            );
        }
        $this->title = $title;
    }


    public function getId()
    {
        if (!$this->id) {
            $this->setId($this->generateId());
        }
        return $this->id;
    }

    public function getType()
    {
        if (!$this->type) {
            $this->setType(self::TYPE_STATUS);
        }
        return $this->type;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getTimestamp()
    {
        if (!$this->timestamp) {
            $this->setTimestamp($_SERVER['REQUEST_TIME']);
        }
        return $this->timestamp;
    }

    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    public function getLinkUrl()
    {
        return $this->linkUrl;
    }

    public function getLinkTitle()
    {
        return $this->linkTitle;
    }

    protected function generateId()
    {
        $type  = $this->getType();
        $seed  = Rand::getFloat();
        $seed .= ':' . $type . ':' . $this->getTimestamp() . ':' . $this->getUser() . ':';
        switch ($type) {
            case self::TYPE_STATUS:
                $seed .= $this->getText();
                break;
            case self::TYPE_IMAGE:
                $seed .= $this->getImageUrl();
                break;
            case self::TYPE_LINK:
                $seed .= $this->getLinkUrl();
                break;
        }
        return hash('sha1', $seed);
    }
}

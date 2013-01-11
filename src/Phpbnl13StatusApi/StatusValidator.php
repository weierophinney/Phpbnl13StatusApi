<?php

namespace Phpbnl13StatusApi;

class StatusValidator
{
    public function isValid(StatusInterface $status)
    {
        $user = $status->getUser();
        if (!is_string($user) || empty(trim($user))) {
            return false;
        }

        $id = $status->getId();
        if (!$id) {
            return false;
        }

        $type = $status->getType();
        switch ($type) {
            case StatusInterface::TYPE_STATUS:
                return $this->validateStatus($status);
                break;
            case StatusInterface::TYPE_IMAGE:
                return $this->validateImage($status);
                break;
            case StatusInterface::TYPE_LINK:
                return $this->validateLink($status);
                break;
            default:
                return false;
        }
    }

    public function validateStatus(StatusInterface $status)
    {
        $text = $status->getText();
        if (!is_string($text) || empty(trim($text))) {
            return false;
        }
        return true;
    }

    public function validateImage(StatusInterface $status)
    {
        $url = $status->getImageUrl();
        if (!is_string($url) || empty(trim($url))) {
            return false;
        }
        return true;
    }

    public function validateLink(StatusInterface $status)
    {
        $url = $status->getLinkUrl();
        if (!is_string($url) || empty(trim($url))) {
            return false;
        }
        return true;
    }
}

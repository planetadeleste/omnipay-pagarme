<?php

namespace Omnipay\Pagarme\Message;

class CreateHookRequest extends AbstractRequest
{
    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setHookId($sValue): self
    {
        return $this->setParameter('hook_id', $sValue);
    }

    /**
     * @return string|null
     */
    public function getHookId():? string
    {
        return $this->getParameter('hook_id');
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return [];
    }

    /**
     * @return string
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getEndpoint(): string
    {
        $this->validate('hook_id');

        return $this->endpoint.'hooks/'.$this->getHookId().'/retry';
    }
}
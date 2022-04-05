<?php

namespace Omnipay\Pagarme\Message;

class ListCustomerCardsRequest extends AbstractRequest
{

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return [];
    }

    public function getHttpMethod(): string
    {
        return 'GET';
    }

    /**
     * @return string
     * @throws \Omnipay\Common\Exception\InvalidRequestException
     */
    public function getEndpoint(): string
    {
        $this->validate('customer_id');

        return $this->endpoint.'customers/'.$this->getCustomerId().'/cards';
    }

    /**
     * @param  string  $sValue
     *
     * @return $this
     */
    public function setCustomerId(string $sValue): self
    {
        return $this->setParameter('customer_id', $sValue);
    }

    /**
     * @return string|null
     */
    public function getCustomerId(): ?string
    {
        return $this->getParameter('customer_id');
    }
}
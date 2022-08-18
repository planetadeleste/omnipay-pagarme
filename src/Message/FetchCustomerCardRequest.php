<?php

namespace Omnipay\Pagarme\Message;

class FetchCustomerCardRequest extends ListCustomerCardsRequest
{
    public function getEndpoint(): string
    {
        $this->validate('card_id');

        return parent::getEndpoint().'/'.$this->getCardId();
    }
}
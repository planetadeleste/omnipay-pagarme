<?php

namespace Omnipay\Pagarme\Message;

class FetchHookRequest extends CreateHookRequest
{

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
        $this->validate('hook_id');

        return $this->endpoint.'hooks/'.$this->getHookId();
    }
}
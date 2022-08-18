<?php

namespace Omnipay\Pagarme\Message;

class ListHookRequest extends FetchHookRequest
{

    /**
     * @param  mixed  $sValue
     *
     * @return $this
     */
    public function setStatus($sValue): self
    {
        return $this->setParameter('status', $sValue);
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->getParameter('status');
    }

    /**
     * @param  mixed  $sValue
     *
     * @return $this
     */
    public function setWebhookEvent($sValue): self
    {
        return $this->setParameter('webhook_event', $sValue);
    }

    /**
     * @return string|null
     */
    public function getWebhookEvent(): ?string
    {
        return $this->getParameter('webhook_event');
    }

    /**
     * @param  mixed  $sValue
     *
     * @return $this
     */
    public function setCreatedSince($sValue): self
    {
        return $this->setParameter('created_since', $sValue);
    }

    /**
     * @return string|null
     */
    public function getCreatedSince(): ?string
    {
        return $this->getParameter('created_since');
    }

    /**
     * @param  mixed  $sValue
     *
     * @return $this
     */
    public function setCreatedUntil($sValue): self
    {
        return $this->setParameter('created_until', $sValue);
    }

    /**
     * @return string|null
     */
    public function getCreatedUntil(): ?string
    {
        return $this->getParameter('created_until');
    }

    /**
     * @param  mixed  $sValue
     *
     * @return $this
     */
    public function setPage($sValue): self
    {
        return $this->setParameter('page', $sValue);
    }

    /**
     * @param  mixed  $sValue
     *
     * @return $this
     */
    public function setSize($sValue): self
    {
        return $this->setParameter('size', $sValue);
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint.'hooks/';
    }

    public function getQuery(): array
    {
        $arQuery = ['page' => $this->getPage(), 'size' => $this->getSize()];

        if ($sStatus = $this->getStatus()) {
            $arQuery['status'] = $sStatus;
        }

        if ($sEvent = $this->getWebhookEvent()) {
            $arQuery['webhook_event'] = $sEvent;
        }

        if ($sDateFrom = $this->getCreatedSince()) {
            $arQuery['created_since'] = $sDateFrom;
        }

        if ($sDateTo = $this->getCreatedUntil()) {
            $arQuery['created_until'] = $sDateTo;
        }

        return $arQuery;
    }

    /**
     * @return string|null
     */
    public function getPage(): ?string
    {
        return $this->getParameter('page', 1);
    }

    /**
     * @return string|null
     */
    public function getSize(): ?string
    {
        return $this->getParameter('size', 10);
    }
}
<?php

namespace Omnipay\Pagarme\Traits;

trait CashPaymentTrait
{
    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setConfirm($sValue): self
    {
        return $this->setParameter('confirm', $sValue);
    }

    /**
     * @return string|null
     */
    public function getConfirm():? string
    {
        return $this->getParameter('confirm');
    }
}
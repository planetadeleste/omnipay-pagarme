<?php

namespace Omnipay\Pagarme;

class SubscriptionItem extends \Omnipay\Common\Item
{
    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setStatus($sValue): self
    {
        return $this->setParameter('status', $sValue);
    }

    /**
     * @return string|null
     */
    public function getStatus():? string
    {
        return $this->getParameter('status');
    }

    /**
     * @param array $sValue
     * @return $this
     */
    public function setPricingScheme(array $sValue): self
    {
        return $this->setParameter('pricing_scheme', $sValue);
    }

    /**
     * @return string|null
     */
    public function getPricingScheme():? string
    {
        return $this->getParameter('pricing_scheme');
    }
}
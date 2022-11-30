<?php

namespace Omnipay\Pagarme;

class PriceSchema extends \Omnipay\Common\Item
{
    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setMininumPrice($sValue): self
    {
        return $this->setParameter('mininum_price', $sValue);
    }

    /**
     * @return string|null
     */
    public function getMininumPrice():? string
    {
        return $this->getParameter('mininum_price');
    }

    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setSchemeType($sValue): self
    {
        return $this->setParameter('scheme_type', $sValue);
    }

    /**
     * @return string|null
     */
    public function getSchemeType():? string
    {
        return $this->getParameter('scheme_type');
    }

    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setPriceBrackets($sValue): self
    {
        return $this->setParameter('price_brackets', $sValue);
    }

    /**
     * @return string|null
     */
    public function getPriceBrackets():? string
    {
        return $this->getParameter('price_brackets');
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->getParameters();
    }
}
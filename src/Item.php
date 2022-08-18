<?php

namespace Omnipay\Pagarme;

class Item extends \Omnipay\Common\Item
{
    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setCode($sValue): self
    {
        return $this->setParameter('code', $sValue);
    }

    /**
     * @return string|null
     */
    public function getCode():? string
    {
        return $this->getParameter('code');
    }

    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setCategory($sValue): self
    {
        return $this->setParameter('category', $sValue);
    }

    /**
     * @return string|null
     */
    public function getCategory():? string
    {
        return $this->getParameter('category');
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $data = $this->getParameters();
        $data['amount'] = $this->getPrice();
        unset($data['price']);

        return $data;
    }
}
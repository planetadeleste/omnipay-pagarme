<?php

namespace Omnipay\Pagarme\Traits;

trait PixPaymentTrait
{
    /**
     * @param int $sValue
     *
     * @return $this
     */
    public function setExpiresIn(int $sValue): self
    {
        return $this->setParameter('expires_in', $sValue);
    }

    /**
     * @return int|null
     */
    public function getExpiresIn(): ?int
    {
        return $this->getParameter('expires_in');
    }

    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setExpiresAt($sValue): self
    {
        return $this->setParameter('expires_at', $sValue);
    }

    /**
     * @return string|null
     */
    public function getExpiresAt():? string
    {
        return $this->getParameter('expires_at');
    }

    /**
     * @param array $sValue
     *
     * @return $this
     */
    public function setAdditionalInformation(array $sValue): self
    {
        return $this->setParameter('additional_information', $sValue);
    }

    /**
     * @return array|null
     */
    public function getAdditionalInformation():? array
    {
        return $this->getParameter('additional_information');
    }
}
<?php

namespace Omnipay\Pagarme\Traits;

trait CardPaymentTrait
{
    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setCardToken($sValue): self
    {
        return $this->setParameter('card_token', $sValue);
    }

    /**
     * @return string|null
     */
    public function getCardToken():? string
    {
        return $this->getParameter('card_token');
    }

    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setRecurrence($sValue): self
    {
        return $this->setParameter('recurrence', $sValue);
    }

    /**
     * @return string|null
     */
    public function getRecurrence():? string
    {
        return $this->getParameter('recurrence');
    }

    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setCardId($sValue): self
    {
        return $this->setParameter('card_id', $sValue);
    }

    /**
     * @return string|null
     */
    public function getCardId():? string
    {
        return $this->getParameter('card_id');
    }

    /**
     * @param int $sValue
     *
     * @return $this
     */
    public function setMerchantCategoryCode(int $sValue): self
    {
        return $this->setParameter('merchant_category_code', $sValue);
    }

    /**
     * @return int|null
     */
    public function getMerchantCategoryCode():? int
    {
        return $this->getParameter('merchant_category_code');
    }

    /**
     * @param array $sValue
     * @return $this
     */
    public function setAuthentication(array $sValue): self
    {
        return $this->setParameter('authentication', $sValue);
    }

    /**
     * @return array|null
     */
    public function getAuthentication():? array
    {
        return $this->getParameter('authentication');
    }

    /**
     * @param bool $sValue
     * @return $this
     */
    public function setAutoRecovery(bool $sValue): self
    {
        return $this->setParameter('auto_recovery', $sValue);
    }

    /**
     * @return bool|null
     */
    public function getAutoRecovery():? bool
    {
        return $this->getParameter('auto_recovery');
    }

    /**
     * @param array $sValue
     * @return $this
     */
    public function setPayload(array $sValue): self
    {
        return $this->setParameter('payload', $sValue);
    }

    /**
     * @return array|null
     */
    public function getPayload():? array
    {
        return $this->getParameter('payload');
    }

    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setOperationType($sValue): self
    {
        return $this->setParameter('operation_type', $sValue);
    }

    /**
     * @return string|null
     */
    public function getOperationType():? string
    {
        return $this->getParameter('operation_type');
    }

    /**
     * Get installments.
     *
     * @return integer the number of installments
     */
    public function getInstallments(): int
    {
        return $this->getParameter('installments') ?? 1;
    }

    /**
     * Set Installments.
     *
     * The number must be between 1 and 12.
     * If the payment method is boleto defaults to 1.
     *
     * @param integer $value
     * @return $this
     */
    public function setInstallments(int $value): self
    {
        return $this->setParameter('installments', $value);
    }
}
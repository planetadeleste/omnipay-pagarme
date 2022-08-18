<?php

namespace Omnipay\Pagarme\Traits;

trait BoletoPaymentTrait
{
    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setInstructions($sValue): self
    {
        return $this->setParameter('instructions', $sValue);
    }

    /**
     * @return string|null
     */
    public function getInstructions():? string
    {
        return $this->getParameter('instructions');
    }

    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setDueAt($sValue): self
    {
        return $this->setParameter('due_at', $sValue);
    }

    /**
     * @return string|null
     */
    public function getDueAt():? string
    {
        return $this->getParameter('due_at');
    }

    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setNossoNumero($sValue): self
    {
        return $this->setParameter('nosso_numero', $sValue);
    }

    /**
     * @return string|null
     */
    public function getNossoNumero():? string
    {
        return $this->getParameter('nosso_numero');
    }

    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setType($sValue): self
    {
        return $this->setParameter('type', $sValue);
    }

    /**
     * @return string|null
     */
    public function getType():? string
    {
        return $this->getParameter('type');
    }

    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setDocumentNumber($sValue): self
    {
        return $this->setParameter('document_number', $sValue);
    }

    /**
     * @return string|null
     */
    public function getDocumentNumber():? string
    {
        return $this->getParameter('document_number');
    }

    /**
     * @param mixed $sValue
     * @return $this
     */
    public function setStatementDescriptor($sValue): self
    {
        return $this->setParameter('statement_descriptor', $sValue);
    }

    /**
     * @return string|null
     */
    public function getStatementDescriptor():? string
    {
        return $this->getParameter('statement_descriptor');
    }
}
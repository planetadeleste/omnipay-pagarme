<?php

namespace Omnipay\Pagarme\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Pagarme\Traits\HasAttributeTrait;

/**
 * Pagarme Response
 *
 * This is the response class for all Pagarme requests.
 *
 * @see \Omnipay\Pagarme\Gateway
 *
 * @property-read string $id
 * @property-read string $message
 * @property-read array  $errors
 */
class Response extends AbstractResponse
{
    use HasAttributeTrait;

    /**
     * Get the transaction reference.
     *
     * @return string|null
     */
    public function getTransactionReference(): ?string
    {
        return $this->id;
    }

    /**
     * Get a card reference, for createCard or createCustomer requests.
     *
     * @return string|null
     */
    public function getCardReference(): ?string
    {
        if (isset($this->data['object']) && 'card' === $this->data['object']) {
            if (!empty($this->data['id'])) {
                return $this->data['id'];
            }
        } elseif (isset($this->data['object']) && 'transaction' === $this->data['object']) {
            return $this->data['card']['id'];
        }

        return null;
    }

    /**
     * Get a customer reference, for createCustomer requests.
     *
     * @return string|null
     */
    public function getReferenceId(): ?string
    {
        return $this->id;
    }

    /**
     * Get the error message from the response.
     *
     * Returns null if the request was successful.
     *
     * @return string|null
     */
    public function getMessage(): ?string
    {
        if ($this->isSuccessful()) {
            return null;
        }

        if (($arErrors = $this->getErrors()) && !empty($arErrors)) {
            $sMessage = is_array($arErrors) ? array_values($arErrors)[0] : $arErrors;
            return is_array($sMessage) ? $sMessage[0] : $sMessage;
        }

        return $this->data['refuse_reason'];
    }

    /**
     * Is the transaction successful?
     *
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return empty($this->getErrors());
    }

    /**
     * @return array|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

    /**
     * Get the boleto_url, boleto_barcode and boleto_expiration_date in the
     * transaction object.
     *
     * @return array|null the boleto_url, boleto_barcode and boleto_expiration_date
     */
    public function getBoleto(): ?array
    {
        if (isset($this->data['object']) && 'transaction' === $this->data['object']) {
            if ($this->data['boleto_url']) {
                return [
                    'boleto_url'             => $this->data['boleto_url'],
                    'boleto_barcode'         => $this->data['boleto_barcode'],
                    'boleto_expiration_date' => $this->data['boleto_expiration_date'],
                ];
            } else {
                return null;
            }
        }

        return null;
    }

    /**
     * Get the Calculted Installments provided by Pagar.me API.
     *
     * @return array|null the calculated installments
     */
    public function getCalculatedInstallments(): ?array
    {
        if (isset($this->data['installments'])) {
            return $this->data['installments'];
        } else {
            return null;
        }
    }
}

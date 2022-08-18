<?php
/**
 * Credit Card class
 */

namespace Omnipay\Pagarme;

use Omnipay\Common\CreditCard as Card;

/**
 * Credit Card class
 *
 * This class extends the Omnipay's Credit Card
 * allowing the addition of a new attribute "holder_document_number".
 *
 * Example:
 *
 * <code>
 *   // Define credit card parameters, which should look like this
 *   $parameters = [
 *      "number" => "4000000000000010",
 *      "holder_name" => "Tony Stark",
 *      "holder_document" => "93095135270",
 *      "exp_month" => 1,
 *      "exp_year" => 30,
 *      "cvv" => "351",
 *      "brand" => "Mastercard",
 *      "label" => "Sua bandeira",
 *      "billing_address" => [
 *          "line_1" => "375, Av. General Osorio, Centro",
 *          "line_2" => "7º Andar",
 *          "zip_code" => "220000111",
 *          "city" => "Rio de Janeiro",
 *          "state" => "RJ",
 *          "country" => "BR"
 *      ],
 *      "options" => [
 *          "verify_card" => true
 *      ]
 *   ];
 *
 *   // Create a credit card object
 *   $card = new CreditCard($parameters);
 * </code>
 */
class CreditCard extends Card
{
    /**
     * Get Document number (CPF or CNPJ).
     *
     * @return string|null
     */
    public function getHolderDocumentNumber(): ?string
    {
        return $this->getParameter('holder_document');
    }

    /**
     * Set Document Number (CPF or CNPJ)
     *
     * Non-numeric characters are stripped out of the document number, so
     * it's safe to pass in strings such as "224.158.178-40" etc.
     *
     * @param string $value Parameter value
     *
     * @return CreditCard provides a fluent interface.
     */
    public function setHolderDocumentNumber(string $value): CreditCard
    {
        // strip non-numeric characters
        return $this->setParameter('holder_document', preg_replace('/\D/', '', $value));
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setLabel($sValue): self
    {
        return $this->setParameter('label', $sValue);
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->getParameter('label');
    }

    /**
     * @param mixed $sValue
     *
     * @return $this
     */
    public function setBillingAddressId($sValue): self
    {
        return $this->setParameter('billing_address_id', $sValue);
    }

    /**
     * @return string|null
     */
    public function getBillingAddressId(): ?string
    {
        return $this->getParameter('billing_address_id');
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $arKeys = ['number', 'cvv'];
        $arCard = array_filter($this->getParameters(), function ($sKey) use ($arKeys) {
            return in_array($sKey, $arKeys);
        }, ARRAY_FILTER_USE_KEY);

        return $arCard + [
            'holder_name'     => $this->getName() ?? $this->getFirstName(),
            'exp_month'       => $this->getExpiryMonth(),
            'exp_year'        => $this->getExpiryYear(),
            'brand'           => $this->getBrand(),
            'billing_address' => Address::createFromCard($this)->getParameters()
        ];
    }
}

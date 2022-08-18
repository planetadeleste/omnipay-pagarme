<?php
/**
 * Pagarme Calculate Installments Request
 */

namespace Omnipay\Pagarme\Message;

/**
 * Pagarme Calculate Installments Request
 *
 * You can use Pagar.me API to calculate installments
 * for a purchase.
 *
 * <code>
 *   // Do a GET request on the gateway
 *   $transaction = $gateway->calculateInstallments(array(
 *       'max_installments'   => 12,
 *       'free_installments'  => 3,
 *       'interest_rate'      => 1.12,
 *       'amount'             => '1200.00',
 *   ));
 *
 *   $response = $transaction->send();
 *
 *   if ($response->isSuccessful()) {
 *       echo "Calculate Installments request was successful!\n";
 *       $installments = $response->getCalculatedInstallments();
 *       echo "Calculated Installments = " . $installments . "\n";
 *   }
 * </code>
 *
 * @see Omnipay\Pagarme\Gateway
 * @link https://docs.pagar.me/api/?shell#estados-das-transaes
 */
class InstallmentsRequest extends AbstractRequest
{
    /**
     * Get Interest Rate.
     *
     * @return float
     */
    public function getInterestRate(): float
    {
        return $this->getParameter('interest_rate');
    }
    
    /**
     * Set Interest Rate.
     *
     * @param float $value
     * @return InstallmentsRequest provides a fluent interface.
     */
    public function setInterestRate($value): InstallmentsRequest
    {
        return $this->setParameter('interest_rate', $value);
    }
    
    /**
     * Get Max Installments.
     *
     * @return integer
     */
    public function getMaxInstallments(): int
    {
        return $this->getParameter('max_installments');
    }
    
    /**
     * Set Max Installments.
     *
     * @param integer $value
     * @return InstallmentsRequest provides a fluent interface.
     */
    public function setMaxInstallments($value): InstallmentsRequest
    {
        return $this->setParameter('max_installments', $value);
    }
    
    /**
     * Get Free Installments.
     *
     * @return integer
     */
    public function getFreeInstallments(): int
    {
        return $this->getParameter('free_installments');
    }
    
    /**
     * Set Free Installments.
     *
     * @param integer $value
     * @return InstallmentsRequest provides a fluent interface.
     */
    public function setFreeInstallments($value): InstallmentsRequest
    {
        return $this->setParameter('free_installments', $value);
    }
    
    /**
     * Get HTTP method used by InstallmentsRequest.
     *
     * @return string
     */
    public function getHttpMethod(): string
    {
        return 'GET';
    }
    
    public function getQuery(): array
    {
        $this->validate('amount', 'interest_rate', 'max_installments');
        
        $data = array();
        $data['api_key'] = $this->getApiKey();
        $data['amount'] = $this->getAmountInteger();
        $data['max_installments'] = $this->getMaxInstallments();
        $data['free_installments'] = $this->getFreeInstallments();
        $data['interest_rate'] = $this->getInterestRate();
        
        return $data;
    }
    
    protected function getOptions(): array
    {
        $options['query'] = $this->getQuery();
         
        return $options;
    }
    public function getData(): array
    {
        return [];
    }
    
    public function getEndpoint(): string
    {
        return $this->endpoint.'transactions/calculate_installments_amount';
    }
}

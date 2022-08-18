<?php

namespace Omnipay\Pagarme;

use PagarmeCoreApiLib\Models\CreateAddressRequest;
use PagarmeCoreApiLib\Models\CreateBoletoPaymentRequest;
use PagarmeCoreApiLib\Models\CreateCardOptionsRequest;
use PagarmeCoreApiLib\Models\CreateCardRequest;
use PagarmeCoreApiLib\Models\CreatePaymentRequest;
use PagarmeCoreApiLib\Models\CreatePixPaymentRequest;
use PagarmeCoreApiLib\Models\CreateShippingRequest;

class Helper extends \Omnipay\Common\Helper
{
    /**
     * @param array $arParams
     *
     * @return \PagarmeCoreApiLib\Models\CreateBoletoPaymentRequest
     */
    public static function toBoletoRequest(array $arParams): CreateBoletoPaymentRequest
    {
        if ($arBillingAddress = self::arrayGet($arParams, 'billing_address')) {
            self::arraySet($arParams, 'billing_address', self::toAddressRequest($arBillingAddress));
        }

        return self::arrayToParams(new CreateBoletoPaymentRequest(), $arParams);
    }

    /**
     * @param array         $arData
     * @param string        $sKey
     * @param mixed|null    $fallback
     * @param callable|null $sValidateFunc
     *
     * @return mixed|null
     */
    public static function arrayGet(array $arData, string $sKey, $fallback = null, callable $sValidateFunc = null)
    {
        if (empty($arData[$sKey])) {
            return $fallback;
        }

        $sValue = $arData[$sKey];

        if ($sValidateFunc && !call_user_func($sValidateFunc, $sValue)) {
            return $fallback;
        }

        return $sValue;
    }

    /**
     * @param array  $arData
     * @param string $sKey
     * @param mixed  $sValue
     *
     * @return void
     */
    public static function arraySet(array &$arData, string $sKey, $sValue)
    {
        $arData[$sKey] = $sValue;
    }

    /**
     * @param array $arParams
     *
     * @return \PagarmeCoreApiLib\Models\CreateAddressRequest
     */
    public static function toAddressRequest(array $arParams): CreateAddressRequest
    {
        return self::arrayToParams(new CreateAddressRequest(), $arParams);
    }

    /**
     * @param array $arParams
     *
     * @return CreateShippingRequest
     */
    public static function toShippingRequest(array $arParams): CreateShippingRequest
    {
        if ($arAddress = self::arrayGet($arParams, 'address')) {
            self::arraySet($arParams, 'address', self::toAddressRequest($arAddress));
        }

        return self::arrayToParams(new CreateShippingRequest(), $arParams);
    }

    /**
     * @param object|string $obTarget
     * @param array         $arParams
     *
     * @return null|CreateAddressRequest|CreateBoletoPaymentRequest|CreateCardOptionsRequest|CreateCardRequest|CreatePaymentRequest|CreatePixPaymentRequest|CreateShippingRequest
     */
    public static function arrayToParams($obTarget, array $arParams)
    {
        if (empty($arParams)) {
            return null;
        }

        if (is_string($obTarget)) {
            $obTarget = new $obTarget();
        }

        foreach ($arParams as $sKey => $sValue) {
            $sKey = \Str::camel($sKey);
            $obTarget->{$sKey} = $sValue;
        }

        return $obTarget;
    }

    /**
     * @param array $arParams
     *
     * @return \PagarmeCoreApiLib\Models\CreatePixPaymentRequest
     */
    public static function toPixRequest(array $arParams): CreatePixPaymentRequest
    {
        return self::arrayToParams(new CreatePixPaymentRequest(), $arParams);
    }

    /**
     * @param array $arParams
     *
     * @return \PagarmeCoreApiLib\Models\CreateCardRequest
     */
    public static function toCreditCardRequest(array $arParams): CreateCardRequest
    {
        if ($arBillingAddress = self::arrayGet($arParams, 'billing_address')) {
            self::arraySet($arParams, 'billing_address', self::toAddressRequest($arBillingAddress));
        }

        if ($arCardOptions = self::arrayGet($arParams, 'options')) {
            $obCardOptions = self::arrayToParams(new CreateCardOptionsRequest(), $arCardOptions);
            self::arraySet($arParams, 'options', $obCardOptions);
        }


        return self::arrayToParams(new CreateCardRequest(), $arParams);
    }

    /**
     * @param array $arParams
     *
     * @return \PagarmeCoreApiLib\Models\CreatePaymentRequest
     */
    public static function toPaymentRequest(array $arParams): CreatePaymentRequest
    {
        $sPaymentMethod = self::arrayGet($arParams, 'payment_method');
        if ($arPayment = self::arrayGet($arParams, $sPaymentMethod)) {
            $sMethod = 'to'.\Str::studly($sPaymentMethod);
            if (is_callable([static::class, $sMethod])) {
                $obPaymentTypeRequest = forward_static_call([static::class, $sMethod], $arPayment);
                self::arraySet($arParams, $sPaymentMethod, $obPaymentTypeRequest);
            }
        }


        return self::arrayToParams(new CreatePaymentRequest(), $arParams);
    }

    /**
     * @param string $sPhone
     *
     * @return array = ["area_code" => "21", "number" => "000000000"]
     */
    public static function getPhone(string $sPhone): array
    {
        $arPhone = [];
        $phone = preg_replace("/[^0-9]/", "", $sPhone);
        if (substr($phone, 0, 1) === "0") {
            $arPhone['area_code'] = substr($phone, 1, 2);
            $arPhone['number'] = substr($phone, 3);
        } elseif (strlen($phone) < 10) {
            $arPhone['area_code'] = '';
            $arPhone['number'] = $phone;
        } else {
            $arPhone['area_code'] = substr($phone, 0, 2);
            $arPhone['number'] = substr($phone, 2);
        }

        return $arPhone;
    }
}
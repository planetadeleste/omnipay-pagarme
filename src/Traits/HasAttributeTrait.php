<?php

namespace Omnipay\Pagarme\Traits;

use Illuminate\Support\Str;

trait HasAttributeTrait
{
    protected $data;

    public function __get(string $sName)
    {
        $sValue = $this->data[$sName] ?? null;

        if ($this->hasAccessor($sName)) {
            return $this->{$this->getAccesorMethod($sName)}($sValue);
        }

        return $sValue;
    }

    public function __set(string $sName, $sValue)
    {
        $this->data[$sName] = $sValue;

        if ($this->hasMutator($sName)) {
            $this->{$this->getMutatorMethod($sName)}($sValue);
        }
    }

    /**
     * Determine if a get accessor exists for an attribute.
     *
     * @param string $sKey
     *
     * @return bool
     */
    public function hasAccessor(string $sKey): bool
    {
        return method_exists($this, $this->getAccesorMethod($sKey));
    }

    /**
     * get accessor method name
     *
     * @param string $sKey
     *
     * @return string
     */
    public function getAccesorMethod(string $sKey): string
    {
        return 'get'.Str::studly($sKey).'Attribute';
    }

    /**
     * Determine if a set mutator exists for an attribute.
     *
     * @param string $sKey
     *
     * @return bool
     */
    public function hasMutator(string $sKey): bool
    {
        return method_exists($this, $this->getMutatorMethod($sKey));
    }

    /**
     * Get mutator method name
     *
     * @param string $sKey
     *
     * @return string
     */
    public function getMutatorMethod(string $sKey): string
    {
        return 'set'.Str::studly($sKey).'Attribute';
    }

    public function toArray(): array
    {
        $arData = $this->data + $this->accessorsToAttribute();
        $this->sortAttributes($arData);
        return array_map(function ($arItem) {
            if ($arItem instanceof \DateTime) {
                return $arItem->format('Y-m-d');
            }
            return $arItem;
        }, $arData);
    }

    protected function accessorsToAttribute(): array
    {
        $arMethods = array_filter(get_class_methods($this), function ($sName) {
            return substr($sName, 0, 3) === 'get' && substr($sName, -9) === 'Attribute';
        });

        if (empty($arMethods)) {
            return [];
        }

        $arAttributes = [];
        foreach ($arMethods as $sMethod) {
            $sAttr = substr($sMethod, 3, -9);
            if ($this->hasAttribute($sAttr)) {
                continue;
            }

            $arAttributes[$sAttr] = $this->{$sMethod}();
        }

        return $arAttributes;
    }

    /**
     * @param string $sKey
     *
     * @return bool
     */
    public function hasAttribute(string $sKey): bool
    {
        return array_key_exists($sKey, $this->data);
    }

    public function sortAttributes(array &$arData)
    {
        $arSortKeys = $this->getSortKeys();
        if (empty($arSortKeys)) {
            return;
        }

        $arKeys = array_flip($arSortKeys);
        $result = array_replace($arKeys, $arData); // result = sorted keys + values from input +
        $arData = array_intersect_key($result, $arData); // remove keys are not existing in input array
    }

    /**
     * Key array for sort attributes
     *
     * @return array
     */
    public function getSortKeys(): array
    {
        return [];
    }

    public function setAttributes(array $arAttrs)
    {
        $this->data = $arAttrs;
    }
}
<?php

namespace Omnipay\Pagarme;

use Omnipay\Common\ItemInterface;

/**
 * @property \Omnipay\Pagarme\Item[] $items
 */
class ItemBag extends \Omnipay\Common\ItemBag
{
    /**
     * Add an item to the bag
     *
     *
     * @param ItemInterface|array $item An existing item, or associative array of item parameters
     */
    public function add($item)
    {
        if ($item instanceof ItemInterface) {
            $this->items[] = $item;
        } else {
            $this->items[] = new Item($item);
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        if (!$this->count()) {
            return [];
        }

        $data = [];
        foreach ($this->items as $obItem) {
            $data[] = $obItem->getData();
        }

        return $data;
    }
}
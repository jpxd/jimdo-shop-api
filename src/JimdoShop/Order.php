<?php

namespace JimdoShop;

class Order
{
    private Shop $shop;
    public int $orderNumber;
    public string $customerName;
    public string $orderDate;
    public float $orderTotal;
    public bool $isShipped;
    public bool $isPaid;
    public int $orderId;
    public ?OrderDetails $details;

    public static function fromRow(Shop $shop, array $orderRow): Order
    {
        $order = new Order();
        $order->shop = $shop;
        $order->orderNumber = $orderRow[0];
        $order->customerName = $orderRow[1];
        $order->orderDate = $orderRow[2];
        $order->orderTotal = Tools::parsePrice($orderRow[3]);
        $order->isShipped = boolval($orderRow[4]);
        $order->isPaid = boolval($orderRow[5]);
        $order->orderId = $orderRow[6];
        return $order;
    }
}
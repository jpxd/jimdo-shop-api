<?php

namespace JimdoShop;

class Order
{
    private Shop $shop;
    public int $orderNumber;
    public string $customerName;
    public string $orderDate;
    public string $orderTotalFormatted;
    public int $isShipped;
    public int $isPaid;
    public int $orderId;

    public static function fromRow(Shop $shop, array $orderRow): Order
    {
        $order = new Order();
        $order->shop = $shop;
        $order->orderNumber = $orderRow[0];
        $order->customerName = $orderRow[1];
        $order->orderDate = $orderRow[2];
        $order->orderTotalFormatted = $orderRow[3];
        $order->isShipped = $orderRow[4];
        $order->isPaid = $orderRow[5];
        $order->orderId = $orderRow[6];
        return $order;
    }
}
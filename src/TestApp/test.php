<?php

use JimdoShop\Config;
use JimdoShop\Shop;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__ . '/../../vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->usePutenv();
$dotenv->load(__DIR__.'/.env');

$config = Config::fromEnv();
$shop = new Shop($config);

$orderCount = $shop->getOrderCount();
echo "Your shop has $orderCount orders!\n";

$orders = $shop->getOrders(100);
$shop->loadOrderDetails($orders);

echo(json_encode($orders, JSON_PRETTY_PRINT). "\n");

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

$orderCount = $shop->countOrders();
echo "Your shop has $orderCount orders!\n";

echo(json_encode($shop->getOrders(), JSON_PRETTY_PRINT). "\n");

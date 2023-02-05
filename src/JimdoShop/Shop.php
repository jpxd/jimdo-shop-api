<?php

namespace JimdoShop;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;

class Shop
{
    private Config $config;
    private Client $client;
    private CookieJar $jar;

    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->jar = CookieJar::fromArray(
            [
                'JDI' => $this->config->jdi,
                'ClickAndChange' => $this->config->clickAndChange,
                'user_account_id' => $this->config->userAccountId,
            ],
            'cms.e.jimdo.com'
        );
        $this->client = new Client([
            'base_uri' => 'https://cms.e.jimdo.com/',
            'cookies' => $this->jar,
            'headers' => [
                'Accept' => 'application/json, text/javascript, */*; q=0.01',
                'X-Requested-With' => 'XMLHttpRequest',
            ],
        ]);
    }

    public function getOrderCount(): int
    {
        $res = $this->client->get('app/cms/poll/status/');
        $data = json_decode($res->getBody()->getContents());
        return $data->siteadmin->orders;
    }

    public function getAllProductVariants(): array
    {
        $res = $this->client->post('app/siteadmin/shoporder/listproductsdata', [
            'form_params' => [
                'sEcho' => 2,
                'iColumns' => 6,
                'sColumns' => '',
                'iDisplayStart' => 0,
                'iDisplayLength' => 100,
                'mDataProp_0' => 'function',
                'mDataProp_1' => 1,
                'mDataProp_2' => 2,
                'mDataProp_3' => 3,
                'mDataProp_4' => 'function',
                'mDataProp_5' => 5,
                'sSearch' => '',
                'bRegex' => false,
                'sSearch_0' => '',
                'bRegex_0' => false,
                'bSearchable_0' => true,
                'sSearch_1' => '',
                'bRegex_1' => false,
                'bSearchable_1' => true,
                'sSearch_2' => '',
                'bRegex_2' => false,
                'bSearchable_2' => true,
                'sSearch_3' => '',
                'bRegex_3' => false,
                'bSearchable_3' => true,
                'sSearch_4' => '',
                'bRegex_4' => false,
                'bSearchable_4' => true,
                'sSearch_5' => '',
                'bRegex_5' => false,
                'bSearchable_5' => true,
                'iSortCol_0' => 0,
                'sSortDir_0' => 'asc',
                'iSortingCols' => 1,
                'bSortable_0' => true,
                'bSortable_1' => true,
                'bSortable_2' => true,
                'bSortable_3' => true,
                'bSortable_4' => true,
                'bSortable_5' => true,
                'cstok' => $this->config->csToken,
                'websiteid' => $this->config->websiteId,
                'pageid' => ''
            ]
        ]);
        $data = json_decode($res->getBody()->getContents());
        $products = [];
        foreach ($data->payload->aaData as $productRow) {
            $products[] = ProductVariant::fromRow($this, $productRow);
        }
        return $products;
    }

    public function updateStock(int $productId, int $variantId, int $newStockCount): void
    {
        $res = $this->client->post('app/siteadmin/shop/updatestock', [
            'form_params' => [
                'stock' => $newStockCount,
                'module_id' => $productId,
                'variant_id' => $variantId,
                'cstok' => $this->config->csToken,
                'websiteid' => $this->config->websiteId,
                'pageid' => '',
            ]
        ]);
        $data = json_decode($res->getBody()->getContents());
        if ($data->status !== 'success') {
            throw new Exception('Could not update stock');
        }
    }

    public function getOrders(int $limit = 10): array
    {
        $res = $this->client->post('app/siteadmin/shoporder/listcurrentdata', [
            'form_params' => [
                'sEcho' => 3,
                'iColumns' => 6,
                'sColumns' => '',
                'iDisplayStart' => 0,
                'iDisplayLength' => $limit,
                'mDataProp_0' => 'function',
                'mDataProp_1' => 1,
                'mDataProp_2' => 2,
                'mDataProp_3' => 'function',
                'mDataProp_4' => 4,
                'mDataProp_5' => 5,
                'sSearch' => '',
                'bRegex' => false,
                'sSearch_0' => '',
                'bRegex_0' => false,
                'bSearchable_0' => true,
                'sSearch_1' => '',
                'bRegex_1' => false,
                'bSearchable_1' => true,
                'sSearch_2' => '',
                'bRegex_2' => false,
                'bSearchable_2' => true,
                'sSearch_3' => '',
                'bRegex_3' => false,
                'bSearchable_3' => true,
                'sSearch_4' => '',
                'bRegex_4' => false,
                'bSearchable_4' => true,
                'sSearch_5' => '',
                'bRegex_5' => false,
                'bSearchable_5' => true,
                'iSortCol_0' => 0,
                'sSortDir_0' => 'desc',
                'iSortingCols' => 1,
                'bSortable_0' => true,
                'bSortable_1' => true,
                'bSortable_2' => true,
                'bSortable_3' => true,
                'bSortable_4' => true,
                'bSortable_5' => true,
                'cstok' => $this->config->csToken,
                'websiteid' => $this->config->websiteId,
                'pageid' => ''
            ]
        ]);
        $data = json_decode($res->getBody()->getContents());
        $orders = [];
        foreach ($data->payload->aaData as $orderRow) {
            $orders[] = Order::fromRow($this, $orderRow);
        }
        return $orders;
    }

    public function loadOrderDetails(array &$orders): void
    {
        $orderIds = array_column($orders, 'orderId');
        if (empty($orderIds)) {
            return;
        }
        $res = $this->client->post('app/siteadmin/shoporder/orderdetails', [
            'form_params' => [
                'order_ids' => $orderIds,
                'buttons_group' => 'listcurrent',
                'cstok' => $this->config->csToken,
                'websiteid' => $this->config->websiteId,
                'pageid' => ''
            ]
        ]);
        $data = json_decode($res->getBody()->getContents());
        foreach ($data as $orderRes) {
            $orderIndex = array_search($orderRes->orderId, $orderIds);
            $order = $orders[$orderIndex];
            $order->details = OrderDetails::fromHtml($orderRes->html);
        }
    }
}
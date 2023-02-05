<?php

namespace JimdoShop;

use DOMDocument;
use DOMXPath;

class OrderDetails
{

    public string $customerAddress;
    public ?string $customerAddressNote;
    public string $customerEmail;
    public string $shippingCost;
    public string $totalCost;
    public string $paymentType;
    public string $orderTime;
    public ?string $customerNote;

    /**
     * @var OrderPosition[]
     */
    public array $positions;

    public static function fromHtml(string $html): OrderDetails
    {
        $html = '<?xml encoding="utf-8" ?>' . str_replace('<br/>', "\n", $html);

        $dom = new DOMDocument();
        $dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
        $xpath = new DOMXPath($dom);

        $details = new OrderDetails();

        $details->customerAddress = trim($xpath->query('//div[@class="cc-sa-order-customer-adress"]//pre')->item(0)->nodeValue);
        $details->customerEmail = trim($xpath->query('//a[starts-with(@href, "mailto")]')->item(0)->nodeValue);
        $details->customerAddress = trim(str_replace($details->customerEmail, '', $details->customerAddress));

        $details->shippingCost = Tools::parsePrice($xpath->query('//div[@class="cc-sa-order-td cc-sa-order-price shipping-costs"]')->item(0)->nodeValue);
        $details->totalCost = Tools::parsePrice($xpath->query('//div[@class="cc-sa-order-td cc-sa-order-price grand-total"]')->item(0)->nodeValue);
        $details->paymentType = trim($xpath->query('//div[@class="cc-sa-order-payment-note"]//strong')->item(0)->nodeValue);
        $details->orderTime = trim($xpath->query('//div[@class="cc-sa-order-detail cc-sa-order-detail-date"]')->item(0)->nodeValue);

        $details->customerAddressNote = trim($xpath->query('//strong[text()="Anmerkung:"]/ancestor::p')->item(0)->nodeValue ?? '');
        $details->customerAddressNote = trim(str_replace('Anmerkung:', '', $details->customerAddressNote));
        $details->customerAddressNote = empty($details->customerAddressNote) ? null : $details->customerAddressNote;

        $details->customerNote = trim($xpath->query('//strong[text()="Hinweise zur Bestellung:"]/ancestor::p')->item(0)->nodeValue ?? '');
        $details->customerNote = trim(str_replace('Hinweise zur Bestellung:', '', $details->customerNote));
        $details->customerNote = empty($details->customerNote) ? null : $details->customerNote;

        $details->positions = [];
        $positionPaths = $xpath->query('//div[@class="cc-sa-order-tr-td cc-clearover"]');
        foreach ($positionPaths as $positionPath) {
            $position = new OrderPosition();
            $position->fullName = $xpath->query('.//div[@class="cc-sa-order-td cc-sa-order-product"]', $positionPath)->item(0)->nodeValue;
            $position->fullPrice = Tools::parsePrice($xpath->query('.//div[@class="cc-sa-order-td cc-sa-order-price product-entry-total"]', $positionPath)->item(0)->nodeValue);
            $position->singlePrice = Tools::parsePrice($xpath->query('.//div[@class="cc-sa-order-td cc-sa-order-price"]', $positionPath)->item(0)->nodeValue);
            $position->quantity = intval($xpath->query('.//div[@class="cc-sa-order-td cc-sa-order-quantity"]', $positionPath)->item(0)->nodeValue);
            $details->positions[] = $position;
        }

        return $details;
    }

}
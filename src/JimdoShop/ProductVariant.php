<?php

namespace JimdoShop;

class ProductVariant
{
    private Shop $shop;
    public string $productName;
    public string $variantName;
    public string $unknown;
    public int $soldCount;
    public int $stockCount;
    public string $priceFormatted;
    public float $priceValue;
    public int $productId;
    public int $variantId;

    public static function fromRow(Shop $shop, array $productRow): ProductVariant
    {
        $product = new ProductVariant();
        $product->shop = $shop;
        $product->productName = $productRow[0];
        $product->variantName = $productRow[1];
        $product->unknown = $productRow[2];
        $product->soldCount = $productRow[3];
        $product->stockCount = $productRow[4];
        $product->priceFormatted = $productRow[5];
        $product->priceValue = $productRow[6];
        $product->productId = $productRow[7];
        $product->variantId = $productRow[8];
        return $product;
    }

    public function updateStock(int $newStockCount): void
    {
        $this->shop->updateStock($this->productId, $this->variantId, $newStockCount);
        $this->stockCount = $newStockCount;
    }

}
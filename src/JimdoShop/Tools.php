<?php

namespace JimdoShop;

class Tools
{
    public static function parsePrice(string $price): float
    {
        return floatval(str_replace(',', '.', str_replace('.', '', $price)));
    }
}
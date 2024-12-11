<?php

namespace Systemin\SyliusGoogleMerchantCenter\Services;

use Doctrine\Common\Collections\ArrayCollection;



class StockService
{
    public static function getStockAvailability($product): bool
    {
        // Stocker toutes les valeurs de stock des variants
        $variantStocks = new ArrayCollection();

        // À la fin, on veut juste savoir si au moins une des variantes a du stock
        $inStock = false;

        // On boucle sur toutes les variantes du produit
        $variants = $product->getVariants();
        foreach ($variants as $v) {
            $available = $v->isInStock();
            $variantStocks->add($available);
        }

        // Si au moins une des variantes est en stock alors le produit est en stock
        foreach ($variantStocks as $vs) {
            if ($vs) {
                $inStock = true;
            }
        }

        // dd($product->getName(), $variantStocks, $inStock); // Tests

        return $inStock;
    }
}

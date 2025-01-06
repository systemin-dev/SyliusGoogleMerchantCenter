<?php

namespace Systemin\SyliusGoogleMerchantCenter\Services;

use Doctrine\Common\Collections\ArrayCollection;



class StockService
{
    public static function getStockAvailability($product): bool
    {
        // A ce stade là on a un produit activé avec au moins un variant
        // On veut vérifier si au moins un variant a du stock && si ce variant est activé

        $inStock = false;

        // On boucle sur toutes les variantes du produit
        $variants = $product->getVariants();
        foreach ($variants as $v) {
            $inStock  = $inStock || ($v->isInStock() && $v->isEnabled());  // ici on vérifie le stock et l'activitation du variant

        }

        return $inStock;
    }
}

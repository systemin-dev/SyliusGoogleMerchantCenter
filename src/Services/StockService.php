<?php

namespace Systemin\SyliusGoogleMerchantCenter\Services;

use Doctrine\Common\Collections\ArrayCollection;



class StockService
{
    public static function getStockAvailability($product): bool
    {
        // A ce stade-là, on a un produit activé avec au moins un variant
        // On veut vérifier si au moins un variant a du stock disponible, 
        // en prenant en compte les réservations, et si ce variant est activé.

        $inStock = false;

        // On boucle sur toutes les variantes du produit
        $variants = $product->getVariants();
        foreach ($variants as $v) {
            // On récupère les quantités disponibles et réservées
            $onHand = $v->getOnHand();  // Stock disponible
            $onHold = $v->getOnHold();  // Quantité réservée

            // Vérification : il doit rester du stock après avoir pris en compte les réservations
            $availableStock = $onHand - $onHold;

            // Si du stock est disponible et que la variante est activée, on marque comme "en stock"
            $inStock  = $inStock || ( ($availableStock > 0 || !$v->isTracked())  && $v->isEnabled());  // Vérification du stock et activation du variant
        }

        return $inStock;
    }
}

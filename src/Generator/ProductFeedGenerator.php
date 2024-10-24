<?php

namespace Lilian\SyliusGoogleMerchantCenter\Generator;

use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;


class ProductFeedGenerator
{
    private $productRepository;
    private $router;

    public function __construct(ProductRepositoryInterface $productRepository, RouterInterface $router)
    {
        $this->productRepository = $productRepository;
        $this->router = $router;
        
    }

    public function generateFeed(): Response
    {
        $products = $this->productRepository->findAll();
        $xml = new \SimpleXMLElement('<feed/>');
    
        foreach ($products as $product) {
            $item = $xml->addChild('item');
            $item->addChild('id', $product->getId());
            $item->addChild('title', $product->getName());
            $item->addChild('description', $product->getDescription());
    
            // Utiliser le router pour générer le lien à partir du slug du produit
            $locale = $product->getTranslation()->getLocale();
            $link = $this->router->generate('sylius_shop_product_show', [
                'slug' => $product->getSlug(),
                '_locale' => $locale,
            ], RouterInterface::ABSOLUTE_URL);
            $item->addChild('link', $link);
    
            $image = $product->getImages()->first();
            if ($image) {
                $absoluteImageLink =  $this->router->getContext()->getScheme() . '://' . $this->router->getContext()->getHost() . '/media/image/' . $image->getPath();
                $item->addChild('image_link', $absoluteImageLink);
            }
    
            // Champs obligatoires supplémentaires
            // $item->addChild('availability', $product->isInStock() ? 'in stock' : 'out of stock'); // Disponibilité
            // $item->addChild('availability_date', $product->getAvailabilityDate()->format('Y-m-d')); // Date de disponibilité
    
            $variant = $product->getVariants()->first();
            if ($variant) {
                $channelPricing = $variant->getChannelPricings()->first();
                if ($channelPricing !== false) {
                    $price = $channelPricing->getPrice() / 100; // Diviser par 100 si le prix est en centimes
                    $item->addChild('price', $price . ' EUR'); // Ajouter la devise
                }
    
                // Identifiant de groupe d'articles
                // $item->addChild('item_group_id', $variant->getCode());
            }
    
            // Autres champs recommandés
            // $item->addChild('brand', $product->getBrand()); // Marque
            // $item->addChild('condition', 'new'); // Condition, ici on suppose que c'est 'new', ajustez en fonction de votre logique
            // $item->addChild('shipping', 'standard'); // Informations de livraison à ajuster
        }
    
        $response = new Response($xml->asXML());
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }
    
}

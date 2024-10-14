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
            $locale = $product->getTranslation()->getLocale(); // Assurez-vous que getLocale() est une méthode valide.
            $link = $this->router->generate('sylius_shop_product_show', [
                'slug' => $product->getSlug(),
                '_locale' => $locale,
            ], RouterInterface::ABSOLUTE_URL);

            $item->addChild('link', $link);

            $image = $product->getImages()->first();
            // Récupérer le schéma et l'hôte depuis les variables d'environnement
            if ($image) {
                // En utilisant ABSOLUTE_URL, Symfony ajoutera automatiquement le domaine défini dans default_uri
                $absoluteImageLink =  $this->router->getContext()->getScheme() . '://' . $this->router->getContext()->getHost() . '/media/image/' . $image->getPath();
                $item->addChild('image_link', $absoluteImageLink);
            }


            // Calcul du prix
            $variant = $product->getVariants()->first();
            if ($variant) {
                $channelPricing = $variant->getChannelPricings()->first();
                if ($channelPricing !== false) { // Vérifie que la collection n'est pas vide
                    $item->addChild('price',  $channelPricing->getPrice() / 100); // Diviser par 100 si le prix est en centimes
                }
            }
            // Ajoutez d'autres champs requis par Google Merchant Center
        }

        $response = new Response($xml->asXML());
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }
}

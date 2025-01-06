<?php

namespace Systemin\SyliusGoogleMerchantCenter\Generator;


use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Systemin\SyliusGoogleMerchantCenter\Services;
use Systemin\SyliusGoogleMerchantCenter\Services\StockService;

class ProductFeedGenerator
{
    private $productRepository;
    private $router;
    private $url;

    public function __construct(ProductRepositoryInterface $productRepository, RouterInterface $router, string $url)
    {
        $this->productRepository = $productRepository;
        $this->router = $router;
        $this->url = $url;
    }

    public function generateFeed(): Response
    {
        $products = $this->productRepository->findAll();
        $xml = new \SimpleXMLElement('<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0"></rss>');
        $channel = $xml->addChild('channel');
        $channel->addChild('title', 'Ma Pépinière');
        $channel->addChild('link', 'https://ma-pepiniere.fr');
        $channel->addChild('description', 'Découvrez notre collection de plantes et accessoires.');

        foreach ($products as $product) {
            if ($product->isEnabled() && StockService::getStockAvailability($product)) {
                $item = $channel->addChild('item');
                $item->addChild('g:id', $product->getId(), 'http://base.google.com/ns/1.0');
                $item->addChild('title', htmlspecialchars($product->getName()), 'http://base.google.com/ns/1.0');
                $item->addChild('description', htmlspecialchars($product->getDescription()), 'http://base.google.com/ns/1.0');

                // Génération du lien produit
                $locale = $product->getTranslation()->getLocale();
                $link = $this->url . '/' .  $this->router->generate('sylius_shop_product_show', [
                    'slug' => $product->getSlug(),
                    '_locale' => $locale,
                ], RouterInterface::RELATIVE_PATH);
                $item->addChild('link', $link);

                // Image principale
                $image = $product->getImages()->first();
                if ($image) {
                    $absoluteImageLink = $this->url . '/media/image/' . $image->getPath();

                    $item->addChild('g:image_link', $absoluteImageLink, 'http://base.google.com/ns/1.0');
                }
                // On vérifie juste s'il existe une variante    
                $variant = $product->getVariants()->first();

                if ($variant) {
                    // Disponibilité
                    $availability = 'in stock';
                    $item->addChild('g:availability', $availability, 'http://base.google.com/ns/1.0');

                    // Prix
                    $channelPricing = $variant->getChannelPricings()->first();
                    if ($channelPricing !== false) {
                        $price = $channelPricing->getPrice() / 100; // Diviser par 100 si le prix est en centimes
                        $item->addChild('g:price', $price . ' EUR', 'http://base.google.com/ns/1.0');
                    }

                    // Date de disponibilité si précommande
                    if (!$variant->isInStock()) {
                        $availabilityDate = (new \DateTime())->modify('+1 month')->format('Y-m-d\TH:i:s\Z');
                        $item->addChild('g:availability_date', $availabilityDate, 'http://base.google.com/ns/1.0');
                    }

                    // Identifiant de groupe d'articles
                    $item->addChild('g:item_group_id', $variant->getCode(), 'http://base.google.com/ns/1.0');
                }

                // Marque
                $item->addChild('g:brand', 'Ma Pépinière', 'http://base.google.com/ns/1.0');

                // Condition
                $item->addChild('g:condition', 'new', 'http://base.google.com/ns/1.0');

                // Livraison
                $shipping = $item->addChild('g:shipping', '', 'http://base.google.com/ns/1.0');
                $shipping->addChild('g:country', 'FR', 'http://base.google.com/ns/1.0');
                $shipping->addChild('g:price', '14.00 EUR', 'http://base.google.com/ns/1.0'); // Ajustez selon vos règles de livraison
            }
        }

        $response = new Response($xml->asXML());
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }
}

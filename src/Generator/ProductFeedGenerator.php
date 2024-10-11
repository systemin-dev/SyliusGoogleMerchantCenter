<?php

namespace Lilian\SyliusGoogleMerchantCenter\Generator;

use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

class ProductFeedGenerator
{
    private $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
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
            $item->addChild('link', $product->getPermalink());
            $item->addChild('image_link', $product->getImages()->first()->getPath());
            $item->addChild('price', $product->getPrice());
            // Ajoutez d'autres champs requis par Google Merchant Center
        }

        $response = new Response($xml->asXML());
        $response->headers->set('Content-Type', 'text/xml');
        
        return $response;
    }
}

Sylius Google Merchant Center Plugin
Description
This is a Sylius plugin designed to generate a Google Merchant Center XML feed, allowing you to easily integrate your Sylius online store's products into Google Shopping.

The plugin extracts product data, variants, images, prices, and availability, fully complying with Google Merchant Center specifications.

Features
Automatic generation of an XML feed formatted for Google Merchant Center.
Integration of product and variant details, including:
Unique identifier (g:id)
Title (g:title) and description (g:description)
Product page link (link)
Main image (g:image_link)
Price (g:price) and currency
Availability (g:availability)
Brand (g:brand)
Condition (g:condition)
Shipping (g:shipping)
Preorder support with an availability date (g:availability_date).
Item group identification with a group ID (g:item_group_id).
Installation
Install the plugin via Composer:

Run the following command:

bash
Copier le code
composer require systemin-dev/sylius-google-merchant-center
Add the service configuration:

Add the feed generator service in your services.yaml file:

yaml
Copier le code
services:
    Systemin\SyliusGoogleMerchantCenter\Generator\ProductFeedGenerator:
        arguments:
            $productRepository: '@sylius.repository.product'
            $router: '@router'
Configure the route:

Add a route to access the XML feed:

yaml
Copier le code
sylius_google_merchant_feed:
    path: /google-merchant-feed
    controller: Systemin\SyliusGoogleMerchantCenter\Controller\ProductFeedController::generateFeed
Usage
Access the configured URL (e.g., /google-merchant-feed) to generate and retrieve the XML feed. This URL will return an XML file compatible with Google Merchant Center.

Sample Generated Feed
xml
Copier le code
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
  <channel>
    <title>Ma Pépinière</title>
    <link>https://www.ma-pepiniere.fr</link>
    <description>Discover our collection of plants and accessories.</description>
    <item>
      <g:id>1</g:id>
      <g:title>Ficus Elastica</g:title>
      <g:description>A perfect plant for bright interiors.</g:description>
      <link>https://www.ma-pepiniere.fr/product/ficus-elastica</link>
      <g:image_link>https://www.ma-pepiniere.fr/media/image/ficus.jpg</g:image_link>
      <g:availability>in stock</g:availability>
      <g:price>49.99 EUR</g:price>
      <g:brand>Ma Pépinière</g:brand>
      <g:condition>new</g:condition>
      <g:shipping>
        <g:country>FR</g:country>
        <g:price>14.00 EUR</g:price>
      </g:shipping>
    </item>
  </channel>
</rss>
Customization
Modify Feed Information
Feed title, link, and description: Edit the default values in the generateFeed() method of the ProductFeedGenerator class:

php
Copier le code
$channel->addChild('title', 'Your Store Title');
$channel->addChild('link', 'https://www.yourstore.com');
$channel->addChild('description', 'Your custom description.');
Shipping price: Adjust the shipping price according to your rules:

php
Copier le code
$shipping->addChild('g:price', '10.00 EUR', 'http://base.google.com/ns/1.0');
Testing
Verify that the products and variants are displayed correctly in the generated feed.
Upload the feed to Google Merchant Center to validate compliance.
Contribution
Contributions are welcome! Follow these steps to contribute:

Fork the repository.
Create a branch: git checkout -b feature/your-feature.
Make your changes and test them.
Submit a pull request.
License
This project is licensed under the MIT License.
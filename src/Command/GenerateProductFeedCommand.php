<?php

namespace Lilian\SyliusGoogleMerchantCenter\Command;

use Lilian\SyliusGoogleMerchantCenter\Generator\ProductFeedGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateProductFeedCommand extends Command
{
    protected static $defaultName = 'sylius:google-merchant:generate-feed';

    private $productFeedGenerator;

    public function __construct(ProductFeedGenerator $productFeedGenerator)
    {
        parent::__construct();
        $this->productFeedGenerator = $productFeedGenerator;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generate the Google Merchant Center product feed')
            ->setHelp('This command allows you to generate a Google Merchant Center product feed and save it as an XML file.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Generating Google Merchant Center Product Feed');

        // Generate the feed
        $response = $this->productFeedGenerator->generateFeed();

        // Define the path where you want to save the file
        $filePath = '/path/to/your/feed.xml';

        // Save the response content to a file
        file_put_contents($filePath, $response->getContent());

        $io->success('Google Merchant Center feed generated successfully at ' . $filePath);

        return Command::SUCCESS;
    }
}

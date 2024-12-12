<?php

namespace Systemin\SyliusGoogleMerchantCenter\Command;

use Systemin\SyliusGoogleMerchantCenter\Generator\ProductFeedGenerator;
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

        // Ajout de l'heure actuelle au titre
        $currentTime = (new \DateTime())->format('Y-m-d H:i:s');
        $io->title(sprintf('Generating Google Merchant Center Product Feed - %s', $currentTime));


        try {
            // Génération du feed
            $response = $this->productFeedGenerator->generateFeed();

            // Définir le chemin où sauvegarder le fichier xml
            $filePath = 'public/feed.xml';

            // Sauvegarde du contenu dans un fichier
            file_put_contents($filePath, $response->getContent());

            $io->success('Google Merchant Center feed generated successfully at ' . $filePath);
            return Command::SUCCESS;
        } catch (\Exception $e) {

            // Affichage de l'erreur dans la console
            $io->error('An error occurred: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

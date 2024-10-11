<?php

namespace Lilian\SyliusGoogleMerchantCenter;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Lilian\SyliusGoogleMerchantCenter\DependencyInjection\SyliusGoogleMerchantCenterExtension;

class SyliusGoogleMerchantCenterPlugin extends Bundle
{
    use SyliusPluginTrait;

    protected function getContainerExtensionClass(): string
    {
        return SyliusGoogleMerchantCenterExtension::class;
    }
}

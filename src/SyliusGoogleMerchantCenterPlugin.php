<?php

namespace Systemin\SyliusGoogleMerchantCenter;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Systemin\SyliusGoogleMerchantCenter\DependencyInjection\SyliusGoogleMerchantCenterExtension;

class SyliusGoogleMerchantCenterPlugin extends Bundle
{
    use SyliusPluginTrait;

    protected function getContainerExtensionClass(): string
    {
        return SyliusGoogleMerchantCenterExtension::class;
    }
}

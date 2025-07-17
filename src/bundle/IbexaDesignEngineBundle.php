<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\Bundle\DesignEngine;

use Ibexa\Bundle\DesignEngine\DependencyInjection\Compiler\AssetPathResolutionPass;
use Ibexa\Bundle\DesignEngine\DependencyInjection\Compiler\AssetThemePass;
use Ibexa\Bundle\DesignEngine\DependencyInjection\Compiler\TwigThemePass;
use Ibexa\Bundle\DesignEngine\DependencyInjection\DesignConfigParser;
use Ibexa\Bundle\DesignEngine\DependencyInjection\IbexaDesignEngineExtension;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IbexaDesignEngineBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        /** @var \Ibexa\Bundle\Core\DependencyInjection\IbexaCoreExtension $eZExtension */
        $eZExtension = $container->getExtension('ibexa');
        $eZExtension->addConfigParser(new DesignConfigParser());
        $eZExtension->addDefaultSettings(__DIR__ . '/Resources/config', ['default_settings.yaml']);

        $container->addCompilerPass(new TwigThemePass());
        $container->addCompilerPass(new AssetThemePass(), PassConfig::TYPE_OPTIMIZE);
        $container->addCompilerPass(new AssetPathResolutionPass(), PassConfig::TYPE_OPTIMIZE);
    }

    #[\Override]
    public function getContainerExtension(): ?ExtensionInterface
    {
        if (!isset($this->extension)) {
            $this->extension = new IbexaDesignEngineExtension();
        }

        return $this->extension;
    }
}

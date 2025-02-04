<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\DesignEngine\DependencyInjection;

use Ibexa\Bundle\Core\DependencyInjection\Configuration\SiteAccessAware\ConfigurationProcessor;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class IbexaDesignEngineExtension extends Extension
{
    public const string EXTENSION_NAME = 'ibexa_design_engine';

    public function getAlias(): string
    {
        return self::EXTENSION_NAME;
    }

    public function getConfiguration(array $config, ContainerBuilder $container): ?ConfigurationInterface
    {
        return new Configuration();
    }

    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
        $loader->load('default_settings.yaml');

        $configuration = $this->getConfiguration($configs, $container);
        assert(null !== $configuration);
        $config = $this->processConfiguration($configuration, $configs);
        $processor = new ConfigurationProcessor($container, 'ezdesign');

        $this->configureDesigns($config, $processor, $container);
    }

    private function configureDesigns(array $config, ConfigurationProcessor $processor, ContainerBuilder $container): void
    {
        // Always add "standard" design to the list (defaults to application level & override paths only)
        $config['design_list'] += ['standard' => []];
        $container->setParameter('ibexa.design.list', $config['design_list']);
        $container->setParameter('ibexa.design.templates.override_paths', $config['templates_override_paths']);
        $container->setParameter('ibexa.design.templates.path_map', $config['templates_theme_paths']);
        $container->setParameter('ibexa.design.assets.resolution.disabled', $config['disable_assets_pre_resolution']);
    }
}

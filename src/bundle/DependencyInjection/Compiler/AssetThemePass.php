<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\DesignEngine\DependencyInjection\Compiler;

use Ibexa\Contracts\DesignEngine\DesignAwareInterface;
use Ibexa\DesignEngine\Asset\ThemePackage;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Finder\Finder;

class AssetThemePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!($container->hasParameter('kernel.bundles') && $container->hasParameter('webroot_dir') && $container->hasDefinition('assets.packages'))) {
            return;
        }

        $themesPathMap = [
            '_override' => array_merge(
                ['assets'],
                $container->getParameter('ibexa.design.assets.override_paths')
            ),
        ];
        $finder = new Finder();
        // Look for assets themes in bundles.
        foreach ($container->getParameter('kernel.bundles') as $bundleName => $bundleClass) {
            $bundleReflection = new \ReflectionClass($bundleClass);
            $bundleViewsDir = \dirname($bundleReflection->getFileName()) . '/Resources/public';
            $themeDir = $bundleViewsDir . '/themes';
            if (!is_dir($themeDir)) {
                continue;
            }

            /** @var \Symfony\Component\Finder\SplFileInfo $directoryInfo */
            foreach ($finder->directories()->in($themeDir)->depth('== 0') as $directoryInfo) {
                $theme = $directoryInfo->getBasename();
                $bundleAssetDir = strtolower(substr($bundleName, 0, strripos($bundleName, 'bundle')));
                $themesPathMap[$theme][] = 'bundles/' . $bundleAssetDir . '/themes/' . $theme;
            }
        }

        // Look for assets themes at application level (web/assets/themes).
        $appLevelThemeDir = $container->getParameter('webroot_dir') . '/assets/themes';
        if (is_dir($appLevelThemeDir)) {
            foreach ((new Finder())->directories()->in($appLevelThemeDir)->depth('== 0') as $directoryInfo) {
                $theme = $directoryInfo->getBasename();
                $themePaths = isset($themesPathMap[$theme]) ? $themesPathMap[$theme] : [];
                // Application level paths are always top priority.
                array_unshift($themePaths, 'assets/themes/' . $theme);
                $themesPathMap[$theme] = $themePaths;
            }
        }

        foreach ($themesPathMap as $theme => &$paths) {
            $paths = array_unique($paths);
        }

        $pathsByDesign = [];
        foreach ($container->getParameter('ibexa.design.list') as $designName => $themeFallback) {
            // Always add _override theme first.
            array_unshift($themeFallback, '_override');
            foreach ($themeFallback as $theme) {
                // Theme could not be found in expected directories, just ignore.
                if (!isset($themesPathMap[$theme])) {
                    continue;
                }

                foreach ($themesPathMap[$theme] as $path) {
                    $pathsByDesign[$designName][] = $path;
                }
            }
        }

        $themesList = $container->getParameter('ibexa.design.themes.list');
        $container->setParameter(
            'ibexa.design.themes.list',
            array_unique(
                array_merge($themesList, array_keys($themesPathMap))
            )
        );
        $container->setParameter('ibexa.design.assets.path_map', $pathsByDesign);
        $container->findDefinition('ibexadesign.asset_path_resolver')
            ->replaceArgument(0, $pathsByDesign);
        $container->findDefinition('assets.packages')
            ->addMethodCall('addPackage', [DesignAwareInterface::DESIGN_NAMESPACE, $container->findDefinition(ThemePackage::class)]);
    }
}

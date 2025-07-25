<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\DesignEngine\DependencyInjection\Compiler;

use Ibexa\Bundle\DesignEngine\DataCollector\TwigDataCollector;
use Ibexa\DesignEngine\Templating\TemplatePathRegistry;
use Ibexa\DesignEngine\Templating\Twig\TwigThemeLoader;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;

/**
 * Registers defined designs as valid Twig namespaces.
 * A design is a collection of ordered themes (in fallback order).
 * A theme is a collection of one or several template paths.
 */
class TwigThemePass implements CompilerPassInterface
{
    /**
     * @throws \ReflectionException
     */
    public function process(ContainerBuilder $container): void
    {
        if (!($container->hasParameter('kernel.bundles') && $container->hasDefinition(TwigThemeLoader::class))) {
            return;
        }

        $themesPathMap = [
            '_override' => (array)$container->getParameter('ibexa.design.templates.override_paths'),
        ];
        $finder = new Finder();
        // Look for themes in bundles.
        foreach ((array)$container->getParameter('kernel.bundles') as $bundleName => $bundleClass) {
            $bundleReflection = new ReflectionClass($bundleClass);
            $filename = $bundleReflection->getFileName();
            assert(is_string($filename));
            $bundleViewsDir = \dirname($filename) . '/Resources/views';
            $themeDir = $bundleViewsDir . '/themes';
            if (!is_dir($themeDir)) {
                continue;
            }

            /** @var \Symfony\Component\Finder\SplFileInfo $directoryInfo */
            foreach ($finder->directories()->in($themeDir)->depth('== 0') as $directoryInfo) {
                $themesPathMap[$directoryInfo->getBasename()][] = $directoryInfo->getRealPath();
            }
        }

        $twigLoaderDef = $container->findDefinition(TwigThemeLoader::class);
        // Now look for themes at application level
        $twigDefaultPath = $container->getParameter('twig.default_path');
        assert(is_string($twigDefaultPath));
        $appLevelThemesDir = $container->getParameterBag()->resolveValue($twigDefaultPath . '/themes');

        if (is_dir($appLevelThemesDir)) {
            foreach ((new Finder())->directories()->in($appLevelThemesDir)->depth('== 0') as $directoryInfo) {
                $theme = $directoryInfo->getBasename();
                $themePaths = $themesPathMap[$theme] ?? [];
                // Application level paths are always top priority.
                array_unshift($themePaths, $directoryInfo->getRealPath());
                $themesPathMap[$theme] = $themePaths;
            }
        }

        // Now merge with already configured template theme paths
        // Template theme paths defined via config will always have less priority than convention based paths
        $themesPathMap = array_merge_recursive(
            $themesPathMap,
            (array)$container->getParameter('ibexa.design.templates.path_map'),
        );

        // De-duplicate the map
        foreach ($themesPathMap as $theme => &$paths) {
            $paths = array_unique($paths);
        }

        foreach ((array)$container->getParameter('ibexa.design.list') as $designName => $themeFallback) {
            // Always add _override theme first.
            array_unshift($themeFallback, '_override');
            foreach ($themeFallback as $theme) {
                // Theme could not be found in expected directories, just ignore.
                if (!isset($themesPathMap[$theme])) {
                    continue;
                }

                foreach ($themesPathMap[$theme] as $path) {
                    $twigLoaderDef->addMethodCall('addPath', [$path, $designName]);
                }
            }
        }

        $themesList = (array)$container->getParameter('ibexa.design.themes.list');
        $container->setParameter(
            'ibexa.design.themes.list',
            array_unique(
                array_merge($themesList, array_keys($themesPathMap))
            )
        );
        $container->setParameter('ibexa.design.templates.path_map', $themesPathMap);

        $twigDataCollector = $container->findDefinition('data_collector.twig');
        $twigDataCollector->setClass(TwigDataCollector::class);

        if (\count($twigDataCollector->getArguments()) === 1) {
            // In versions of Symfony prior to 3.4, "data_collector.twig" had only one
            // argument, we're adding "twig" service to satisfy constructor overriden
            // in \IbexaDesignEngineBundle\DataCollector\TwigDataCollector
            // which is based on Symfony 3.4 version of base TwigDataCollector
            $twigDataCollector->addArgument(new Reference('twig'));
        }

        $twigDataCollector->addArgument(new Reference(TemplatePathRegistry::class));
    }
}

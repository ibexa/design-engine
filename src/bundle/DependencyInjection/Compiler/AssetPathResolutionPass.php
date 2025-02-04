<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Bundle\DesignEngine\DependencyInjection\Compiler;

use Ibexa\DesignEngine\Asset\AssetPathProvisionerInterface;
use Ibexa\DesignEngine\Asset\ProvisionedPathResolver;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Resolves assets theme paths.
 * Avoids multiple I/O calls at runtime when looking for the right asset path.
 *
 * Will loop over registered theme paths for each design.
 * Within each theme path, will look for any files in order to make a list of all available assets.
 * Each asset is then regularly processed through the AssetPathResolver, like if it were called by asset() Twig function.
 */
class AssetPathResolutionPass implements CompilerPassInterface
{
    /**
     * @throws \Exception
     */
    public function process(ContainerBuilder $container): void
    {
        if ($container->getParameter('ibexa.design.assets.resolution.disabled')) {
            return;
        }

        $resolvedPathsByDesign = $this->preResolveAssetsPaths(
            $container->get(ProvisionedPathResolver::class),
            $container->getParameter('ibexa.design.assets.path_map')
        );

        $container->setParameter('ibexa.design.assets.resolved_paths', $resolvedPathsByDesign);
        $container->findDefinition(ProvisionedPathResolver::class)->replaceArgument(0, $resolvedPathsByDesign);
        $container->setAlias('ibexadesign.asset_path_resolver', new Alias(ProvisionedPathResolver::class));
    }

    private function preResolveAssetsPaths(AssetPathProvisionerInterface $provisioner, array $designPathMap): array
    {
        $resolvedPathsByDesign = [];
        foreach ($designPathMap as $design => $paths) {
            $resolvedPathsByDesign[$design] = $provisioner->provisionResolvedPaths($paths, $design);
        }

        return $resolvedPathsByDesign;
    }
}

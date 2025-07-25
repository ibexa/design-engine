<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\DesignEngine\Asset;

use Symfony\Component\Finder\Finder;

readonly class ProvisionedPathResolver implements AssetPathResolverInterface, AssetPathProvisionerInterface
{
    /**
     * @param array<string, array<string, string>> $resolvedPaths
     */
    public function __construct(
        private array $resolvedPaths,
        private AssetPathResolverInterface $innerResolver,
        private string $webRootDir
    ) {
    }

    /**
     * Looks for $path within pre-resolved paths for provided design.
     * If it cannot be found, fallbacks to original resolver.
     */
    public function resolveAssetPath(string $path, string $design): string
    {
        return $this->resolvedPaths[$design][$path] ?? $this->innerResolver->resolveAssetPath($path, $design);
    }

    public function provisionResolvedPaths(array $assetsPaths, string $design): array
    {
        $webrootDir = $this->webRootDir;
        $assetsLogicalPaths = [];
        foreach ($assetsPaths as $path) {
            foreach ($this->computeLogicalPathFromPhysicalAssets("$webrootDir/$path") as $logicalPath) {
                $assetsLogicalPaths[] = $logicalPath;
            }
        }

        $resolvedPaths = [];
        foreach (array_unique($assetsLogicalPaths) as $logicalPath) {
            $resolvedPaths[$logicalPath] = $this->resolveAssetPath($logicalPath, $design);
        }

        return $resolvedPaths;
    }

    /**
     * Looks for physical assets within $themePath and computes their logical path (i.e. without full path to theme dir).
     *
     * Excludes "themes/" directory under a theme one, in order to avoid recursion.
     * This exclusion mainly applies to override directories,
     * e.g. "assets/", which is both an override dir and where app level themes can be defined.
     *
     * @phpstan-return list<string>
     */
    private function computeLogicalPathFromPhysicalAssets(string $themePath): array
    {
        if (!is_dir($themePath)) {
            return [];
        }

        $logicalPaths = [];
        /** @var \SplFileInfo $fileInfo */
        foreach ((new Finder())->files()->in($themePath)->exclude('themes')->followLinks()->ignoreUnreadableDirs() as $fileInfo) {
            $logicalPaths[] = trim(substr($fileInfo->getPathname(), \strlen($themePath)), '/');
        }

        return $logicalPaths;
    }
}

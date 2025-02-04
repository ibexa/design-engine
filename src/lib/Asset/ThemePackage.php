<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\DesignEngine\Asset;

use Ibexa\Contracts\DesignEngine\DesignAwareInterface;
use Ibexa\DesignEngine\DesignAwareTrait;
use Symfony\Component\Asset\PackageInterface;

class ThemePackage implements PackageInterface, DesignAwareInterface
{
    use DesignAwareTrait;

    private AssetPathResolverInterface $pathResolver;

    private PackageInterface $innerPackage;

    public function __construct(AssetPathResolverInterface $pathResolver, PackageInterface $innerPackage)
    {
        $this->pathResolver = $pathResolver;
        $this->innerPackage = $innerPackage;
    }

    public function getUrl(string $path): string
    {
        return $this->innerPackage->getUrl($this->pathResolver->resolveAssetPath($path, $this->getCurrentDesign()));
    }

    public function getVersion(string $path): string
    {
        return $this->innerPackage->getVersion($this->pathResolver->resolveAssetPath($path, $this->getCurrentDesign()));
    }
}

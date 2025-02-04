<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignEngine\Asset;

/**
 * Interface for asset path resolvers.
 * An asset path resolver will check provided asset path and resolve it for current design.
 */
interface AssetPathResolverInterface
{
    /**
     * Resolves provided asset path within provided design and returns correct asset path.
     *
     * @param string $path   Asset path to resolve
     * @param string $design Design to resolve path for
     */
    public function resolveAssetPath(string $path, string $design): string;
}

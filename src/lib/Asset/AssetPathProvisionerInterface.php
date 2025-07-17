<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\DesignEngine\Asset;

interface AssetPathProvisionerInterface
{
    /**
     * Pre-resolves assets paths for a given design from themes paths, where are stored physical assets.
     * Returns a map with asset logical path as key and its resolved path (relative to webroot dir) as value.
     * Example => ['images/foo.png' => 'asset/themes/some_theme/images/foo.png'].
     *
     * @param list<string> $assetsPaths
     *
     * @return array<string, string>
     */
    public function provisionResolvedPaths(array $assetsPaths, string $design): array;
}

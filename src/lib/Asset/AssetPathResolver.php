<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\DesignEngine\Asset;

use Ibexa\DesignEngine\Exception\InvalidDesignException;
use Psr\Log\LoggerInterface;

class AssetPathResolver implements AssetPathResolverInterface
{
    /**
     * @param array<string, array<int, string>> $designPaths
     */
    public function __construct(
        private readonly array $designPaths,
        private readonly string $webRootDir,
        private readonly ?LoggerInterface $logger = null
    ) {
    }

    public function resolveAssetPath(string $path, string $design): string
    {
        if (!isset($this->designPaths[$design])) {
            throw new InvalidDesignException("Invalid design '$design'");
        }

        foreach ($this->designPaths[$design] as $tryPath) {
            if (file_exists($this->webRootDir . '/' . $tryPath . '/' . $path)) {
                return $tryPath . '/' . $path;
            }
        }

        $this->logger?->warning(
            "Asset '$path' cannot be found in any configured themes.\nTried directories: " . implode(
                ', ',
                array_values($this->designPaths[$design])
            )
        );

        return $path;
    }
}

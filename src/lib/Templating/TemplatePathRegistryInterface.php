<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace Ibexa\DesignEngine\Templating;

/**
 * Registry to map templates logical names and their real path.
 * Mainly used for profiling.
 */
interface TemplatePathRegistryInterface
{
    /**
     * Adds a template path mapping to the registry.
     *
     * @param string $templateName The template logical name
     * @param string $path         Template path
     */
    public function mapTemplatePath(string $templateName, string $path): void;

    public function getTemplatePath(string $templateName): string;

    /**
     * Returns the whole hash map.
     *
     * @return array<string, string>
     */
    public function getPathMap(): array;
}

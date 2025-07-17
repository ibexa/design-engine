<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\DesignEngine\Templating;

use Serializable;

class TemplatePathRegistry implements TemplatePathRegistryInterface, Serializable
{
    /** @var array<string, string> */
    private array $pathMap = [];

    public function __construct(
        private string $kernelRootDir
    ) {
    }

    public function mapTemplatePath(string $templateName, string $path): void
    {
        $this->pathMap[$templateName] = str_replace($this->kernelRootDir . '/', '', $path);
    }

    public function getTemplatePath(string $templateName): string
    {
        return $this->pathMap[$templateName] ?? $templateName;
    }

    public function getPathMap(): array
    {
        return $this->pathMap;
    }

    public function serialize(): ?string
    {
        return serialize([$this->pathMap, $this->kernelRootDir]);
    }

    public function unserialize(string $serialized): void
    {
        [$this->pathMap, $this->kernelRootDir] = unserialize($serialized);
    }

    /**
     * @return array{array<string, string>, string}
     */
    public function __serialize(): array
    {
        return [$this->pathMap, $this->kernelRootDir];
    }

    /**
     * @param array{array<string, string>, string} $data
     */
    public function __unserialize(array $data): void
    {
        [$this->pathMap, $this->kernelRootDir] = $data;
    }
}

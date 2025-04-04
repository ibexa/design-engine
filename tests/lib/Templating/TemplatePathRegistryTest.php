<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Tests\DesignEngine\Templating;

use Ibexa\DesignEngine\Templating\TemplatePathRegistry;
use PHPUnit\Framework\TestCase;

class TemplatePathRegistryTest extends TestCase
{
    private function getExpectedRelativePath(string $templateFullPath, string $kernelRootDir): string
    {
        return str_replace($kernelRootDir . '/', '', $templateFullPath);
    }

    public function testMapTemplatePath(): void
    {
        $kernelRootDir = __DIR__;
        $templateLogicalName = '@foo/bar.html.twig';
        $templateFullPath = $kernelRootDir . '/' . $templateLogicalName;

        $registry = new TemplatePathRegistry($kernelRootDir);
        self::assertSame([], $registry->getPathMap());
        $registry->mapTemplatePath($templateLogicalName, $templateFullPath);
        self::assertSame(
            [$templateLogicalName => $this->getExpectedRelativePath($templateFullPath, $kernelRootDir)],
            $registry->getPathMap()
        );
    }

    public function testGetTemplatePath(): void
    {
        $kernelRootDir = __DIR__;
        $templateLogicalName = '@foo/bar.html.twig';
        $templateFullPath = $kernelRootDir . '/' . $templateLogicalName;

        $registry = new TemplatePathRegistry($kernelRootDir);
        $registry->mapTemplatePath($templateLogicalName, $templateFullPath);
        self::assertSame(
            $this->getExpectedRelativePath($templateFullPath, $kernelRootDir),
            $registry->getTemplatePath($templateLogicalName)
        );
    }

    public function testGetTemplatePathNotMapped(): void
    {
        $kernelRootDir = __DIR__;
        $templateLogicalName = '@foo/bar.html.twig';

        $registry = new TemplatePathRegistry($kernelRootDir);
        self::assertSame($templateLogicalName, $registry->getTemplatePath($templateLogicalName));
    }
}

<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Tests\DesignEngine\Asset;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\DesignEngine\Asset\AssetPathResolverInterface;
use Ibexa\DesignEngine\Asset\ThemePackage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\PackageInterface;

class ThemePackageTest extends TestCase
{
    private AssetPathResolverInterface&MockObject $assetPathResolver;

    private PackageInterface&MockObject $innerPackage;

    private ConfigResolverInterface&MockObject $configResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assetPathResolver = $this->createMock(AssetPathResolverInterface::class);
        $this->innerPackage = $this->createMock(PackageInterface::class);
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
    }

    public function testGetUrl(): void
    {
        $assetPath = 'images/foo.png';
        $fullAssetPath = 'assets/' . $assetPath;
        $currentDesign = 'foo';

        $this->assetPathResolver
            ->expects(self::once())
            ->method('resolveAssetPath')
            ->with($assetPath, $currentDesign)
            ->willReturn($fullAssetPath);
        $this->innerPackage
            ->expects(self::once())
            ->method('getUrl')
            ->with($fullAssetPath)
            ->willReturn("/$fullAssetPath");
        $this->configResolver
            ->method('getParameter')
            ->with('design')
            ->willReturn($currentDesign);

        $package = new ThemePackage($this->assetPathResolver, $this->innerPackage);
        $package->setConfigResolver($this->configResolver);
        self::assertSame("/$fullAssetPath", $package->getUrl($assetPath));
    }

    public function testGetVersion(): void
    {
        $assetPath = 'images/foo.png';
        $fullAssetPath = 'assets/' . $assetPath;
        $currentDesign = 'foo';

        $this->assetPathResolver
            ->expects(self::once())
            ->method('resolveAssetPath')
            ->with($assetPath, $currentDesign)
            ->willReturn($fullAssetPath);
        $version = 'v1';
        $this->innerPackage
            ->expects(self::once())
            ->method('getVersion')
            ->with($fullAssetPath)
            ->willReturn($version);
        $this->configResolver
            ->method('getParameter')
            ->with('design')
            ->willReturn($currentDesign);

        $package = new ThemePackage($this->assetPathResolver, $this->innerPackage);
        $package->setConfigResolver($this->configResolver);
        self::assertSame($version, $package->getVersion($assetPath));
    }
}

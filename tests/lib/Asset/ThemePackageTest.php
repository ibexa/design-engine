<?php

/*
 * This file is part of the EzPlatformDesignEngine package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace Ibexa\Tests\DesignEngine\Asset;

use Ibexa\Core\MVC\ConfigResolverInterface;
use Ibexa\DesignEngine\Asset\AssetPathResolverInterface;
use Ibexa\DesignEngine\Asset\ThemePackage;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Asset\PackageInterface;

class ThemePackageTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Ibexa\DesignEngine\Asset\AssetPathResolverInterface
     */
    private $assetPathResolver;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Symfony\Component\Asset\PackageInterface
     */
    private $innerPackage;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|\Ibexa\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->assetPathResolver = $this->createMock(AssetPathResolverInterface::class);
        $this->innerPackage = $this->createMock(PackageInterface::class);
        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
    }

    public function testGetUrl()
    {
        $assetPath = 'images/foo.png';
        $fullAssetPath = 'assets/' . $assetPath;
        $currentDesign = 'foo';

        $this->assetPathResolver
            ->expects($this->once())
            ->method('resolveAssetPath')
            ->with($assetPath, $currentDesign)
            ->willReturn($fullAssetPath);
        $this->innerPackage
            ->expects($this->once())
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

    public function testGetVersion()
    {
        $assetPath = 'images/foo.png';
        $fullAssetPath = 'assets/' . $assetPath;
        $currentDesign = 'foo';

        $this->assetPathResolver
            ->expects($this->once())
            ->method('resolveAssetPath')
            ->with($assetPath, $currentDesign)
            ->willReturn($fullAssetPath);
        $version = 'v1';
        $this->innerPackage
            ->expects($this->once())
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

class_alias(ThemePackageTest::class, 'EzSystems\EzPlatformDesignEngine\Tests\Asset\ThemePackageTest');

<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Tests\DesignEngine\Asset;

use Ibexa\DesignEngine\Asset\AssetPathResolver;
use Ibexa\DesignEngine\Exception\InvalidDesignException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AssetPathResolverTest extends TestCase
{
    public function testResolveAssetPathFail(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::once())
            ->method('warning');

        $resolver = new AssetPathResolver(['foo' => []], __DIR__, $logger);
        $assetPath = 'images/foo.png';
        self::assertSame($assetPath, $resolver->resolveAssetPath($assetPath, 'foo'));
    }

    /**
     * @covers \Ibexa\DesignEngine\Asset\AssetPathResolver::resolveAssetPath
     */
    public function testResolveInvalidDesign(): void
    {
        $resolver = new AssetPathResolver([], __DIR__);
        $assetPath = 'images/foo.png';
        $this->expectException(InvalidDesignException::class);
        self::assertSame($assetPath, $resolver->resolveAssetPath($assetPath, 'foo'));
    }

    /**
     * @return list<array{
     *     0: array{foo: list<string>},
     *     1: list<string>,
     *     2: string,
     *     3: string
     * }>
     */
    public function resolveAssetPathProvider(): array
    {
        return [
            [
                [
                    'foo' => [
                        'themes/theme1',
                        'themes/theme2',
                        'themes/theme3',
                    ],
                ],
                ['themes/theme2', 'themes/theme3'],
                'images/foo.png',
                'themes/theme2/images/foo.png',
            ],
            [
                [
                    'foo' => [
                        'themes/theme1',
                        'themes/theme2',
                        'themes/theme3',
                    ],
                ],
                ['themes/theme2'],
                'images/foo.png',
                'themes/theme2/images/foo.png',
            ],
            [
                [
                    'foo' => [
                        'themes/theme1',
                        'themes/theme2',
                        'themes/theme3',
                    ],
                ],
                ['themes/theme1', 'themes/theme2', 'themes/theme3'],
                'images/foo.png',
                'themes/theme1/images/foo.png',
            ],
            [
                [
                    'foo' => [
                        'themes/theme1',
                        'themes/theme2',
                        'themes/theme3',
                    ],
                ],
                ['themes/theme3'],
                'images/foo.png',
                'themes/theme3/images/foo.png',
            ],
            [
                [
                    'foo' => [
                        'themes/theme1',
                        'themes/theme2',
                        'themes/theme3',
                    ],
                ],
                [],
                'images/foo.png',
                'images/foo.png',
            ],
        ];
    }

    /**
     * @dataProvider resolveAssetPathProvider
     *
     * @param array{foo: array<string>} $designPaths
     * @param list<string> $existingPaths
     */
    public function testResolveAssetPath(array $designPaths, array $existingPaths, string $path, string $resolvedPath): void
    {
        $webrootDir = vfsStream::setup('web');
        foreach ($designPaths['foo'] as $designPath) {
            if (\in_array($designPath, $existingPaths)) {
                $fileInfo = new \SplFileInfo($designPath . '/' . $path);
                $parent = $webrootDir;
                foreach (explode('/', $fileInfo->getPath()) as $dir) {
                    if (!$parent->hasChild($dir)) {
                        $directory = vfsStream::newDirectory($dir)->at($parent);
                    } else {
                        $directory = $parent->getChild($dir);
                    }

                    $parent = $directory;
                }

                vfsStream::newFile($fileInfo->getFilename())->at($parent)->setContent('Vive le sucre !!!');
            }
        }

        $resolver = new AssetPathResolver($designPaths, $webrootDir->url());
        self::assertSame($resolvedPath, $resolver->resolveAssetPath($path, 'foo'));
    }
}

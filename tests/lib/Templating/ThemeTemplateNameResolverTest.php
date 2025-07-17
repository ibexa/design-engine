<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\Tests\DesignEngine\Templating;

use Ibexa\Contracts\Core\SiteAccess\ConfigResolverInterface;
use Ibexa\DesignEngine\Templating\ThemeTemplateNameResolver;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ThemeTemplateNameResolverTest extends TestCase
{
    private ConfigResolverInterface&MockObject $configResolver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configResolver = $this->createMock(ConfigResolverInterface::class);
    }

    /**
     * @return list<array{
     *     0: ?string,
     *     1: string,
     *     2: string
     * }>
     */
    public function templateNameProvider(): array
    {
        return [
            [null, 'foo.html.twig', 'foo.html.twig'],
            ['my_design', '@ibexadesign/foo.html.twig', '@my_design/foo.html.twig'],
            ['my_design', '@AcmeTest/foo.html.twig', '@AcmeTest/foo.html.twig'],
        ];
    }

    /**
     * @dataProvider templateNameProvider
     */
    public function testResolveTemplateName(?string $currentDesign, string $templateName, string $expectedTemplateName): void
    {
        $this->configResolver
            ->method('getParameter')
            ->with('design')
            ->willReturn($currentDesign);
        $resolver = new ThemeTemplateNameResolver($this->configResolver);
        self::assertSame($expectedTemplateName, $resolver->resolveTemplateName($templateName));
    }

    /**
     * @return list<array{
     *     0: ?string,
     *     1: string,
     *     2: bool
     * }>
     */
    public function isTemplateDesignNamespacedProvider(): array
    {
        return [
            [null, 'foo.html.twig', false],
            ['my_design', '@ibexadesign/foo.html.twig', true],
            ['my_design', '@my_design/foo.html.twig', true],
            ['my_design', '@AcmeTest/foo.html.twig', false],
        ];
    }

    /**
     * @dataProvider isTemplateDesignNamespacedProvider
     */
    public function testIsTemplateDesignNamespaced(?string $currentDesign, string $templateName, bool $expected): void
    {
        $this->configResolver
            ->method('getParameter')
            ->with('design')
            ->willReturn($currentDesign);
        $resolver = new ThemeTemplateNameResolver($this->configResolver);
        self::assertSame($expected, $resolver->isTemplateDesignNamespaced($templateName));
    }
}

<?php

/**
 * @copyright Copyright (C) Ibexa AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace Ibexa\DesignEngine\Templating\Twig;

use Ibexa\DesignEngine\Templating\TemplateNameResolverInterface;
use Ibexa\DesignEngine\Templating\TemplatePathRegistryInterface;
use Twig\Loader\FilesystemLoader;
use Twig\Loader\LoaderInterface;
use Twig\Source;

/**
 * Decorates regular Twig FilesystemLoader.
 * It resolves generic @ibexadesign namespace to the actual current namespace.
 */
class TwigThemeLoader implements LoaderInterface
{
    /**
     * @var \Ibexa\DesignEngine\Templating\TemplateNameResolverInterface
     */
    private TemplateNameResolverInterface $nameResolver;

    /**
     * @var \Ibexa\DesignEngine\Templating\TemplatePathRegistryInterface
     */
    private TemplatePathRegistryInterface $pathRegistry;

    private FilesystemLoader $innerFilesystemLoader;

    public function __construct(
        TemplateNameResolverInterface $templateNameResolver,
        TemplatePathRegistryInterface $templatePathRegistry,
        FilesystemLoader $innerFilesystemLoader
    ) {
        $this->innerFilesystemLoader = $innerFilesystemLoader;
        $this->nameResolver = $templateNameResolver;
        $this->pathRegistry = $templatePathRegistry;
    }

    public function exists(string $name): bool
    {
        return $this->innerFilesystemLoader->exists($this->nameResolver->resolveTemplateName($name));
    }

    public function getSourceContext(string $name): Source
    {
        $source = $this->innerFilesystemLoader->getSourceContext($this->nameResolver->resolveTemplateName((string)$name));
        $this->pathRegistry->mapTemplatePath($source->getName(), $source->getPath());

        return $source;
    }

    public function getCacheKey(string $name): string
    {
        return $this->innerFilesystemLoader->getCacheKey($this->nameResolver->resolveTemplateName($name));
    }

    public function isFresh(string $name, int $time): bool
    {
        return $this->innerFilesystemLoader->isFresh($this->nameResolver->resolveTemplateName($name), $time);
    }

    public function getPaths(string $namespace = FilesystemLoader::MAIN_NAMESPACE): array
    {
        return $this->innerFilesystemLoader->getPaths($namespace);
    }

    public function getNamespaces(): array
    {
        return $this->innerFilesystemLoader->getNamespaces();
    }

    /**
     * @param string|string[] $paths
     */
    public function setPaths(string|array $paths, $namespace = FilesystemLoader::MAIN_NAMESPACE): void
    {
        $this->innerFilesystemLoader->setPaths($paths, $namespace);
    }

    /**
     * @throws \Twig\Error\LoaderError
     */
    public function addPath(string $path, string $namespace = FilesystemLoader::MAIN_NAMESPACE): void
    {
        $this->innerFilesystemLoader->addPath($path, $namespace);
    }

    /**
     * @throws \Twig\Error\LoaderError
     */
    public function prependPath(string $path, string $namespace = FilesystemLoader::MAIN_NAMESPACE): void
    {
        $this->innerFilesystemLoader->prependPath($path, $namespace);
    }
}

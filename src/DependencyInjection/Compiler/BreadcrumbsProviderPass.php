<?php

/*
 * This file is part of the RollerworksBreadcrumbsBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\BreadcrumbsBundle\DependencyInjection\Compiler;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use Rollerworks\Bundle\BreadcrumbsBundle\Annotation\Breadcrumb;
use Rollerworks\Bundle\BreadcrumbsBundle\Annotation\BreadcrumbsProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

/**
 * The BreadcrumbsProviderPass searches for all services tagged
 * with rollerworks_breadcrumb.provider and processes there provided breadcrumbs.
 *
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
final class BreadcrumbsProviderPass implements CompilerPassInterface
{
    const LOADER_TAG_NAME = 'rollerworks_breadcrumb.provider';

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $serviceIds = $container->findTaggedServiceIds(self::LOADER_TAG_NAME);

        if ([] === $serviceIds || !$container->hasDefinition('rollerworks_breadcrumbs.loader')) {
            return;
        }

        $annotationsDir = dirname(dirname(__DIR__)).'/Annotation';
        $annotationReader = new AnnotationReader();

        AnnotationRegistry::registerFile($annotationsDir.'/BreadcrumbsProvider.php');
        AnnotationRegistry::registerFile($annotationsDir.'/Breadcrumb.php');

        $breadcrumbs = [];
        $routes = [];

        $parameters = $container->getParameterBag();

        foreach ($serviceIds as $serviceId => $tags) {
            $breadcrumbs = array_merge(
                $breadcrumbs,
                $this->processBreadcrumbProvider(
                    $parameters,
                    $serviceId,
                    $container->findDefinition($serviceId),
                    $annotationReader
                )
            );
        }

        foreach ($breadcrumbs as $name => $breadcrumb) {
            $breadcrumbs[$name]['trail'] = $this->createBreadcrumbTrail($breadcrumb, $breadcrumbs);
            $routes[$breadcrumb['route']] = $name;
        }

        $container->getDefinition('rollerworks_breadcrumbs.loader')
            ->replaceArgument(0, $breadcrumbs)
            ->replaceArgument(1, $routes);
    }

    private function processBreadcrumbProvider(
        ParameterBag $parameters,
        $serviceId,
        Definition $definition,
        Reader $annotationReader
    ) {
        $class = $parameters->resolveString($definition->getClass());
        $namePrefix = '';
        $routePrefix = '';
        $headParent = '';
        $headExtras = [];

        $classReflection = new \ReflectionClass($class);

        /** @var BreadcrumbsProvider $classAnnotation */
        if ($classAnnotation = $annotationReader->getClassAnnotation($classReflection, BreadcrumbsProvider::class)) {
            $namePrefix = $classAnnotation->getNamePrefix();
            $routePrefix = $classAnnotation->getRoutePrefix();
            $headParent = $parameters->resolveValue($classAnnotation->getParent());
            $headExtras = $classAnnotation->getExtra();
        }

        $breadcrumbs = [];

        foreach ($classReflection->getMethods() as $methodReflection) {
            $annotations = $annotationReader->getMethodAnnotations($methodReflection);

            foreach ($annotations as $annotation) {
                if ($annotation instanceof Breadcrumb) {
                    $name = $parameters->resolveValue($annotation->getFullName() ?: $namePrefix.$annotation->getName());

                    $breadcrumbs[$name] = [
                        'name' => $name,
                        'parent' => $parameters->resolveValue($annotation->getParent()) ?: $headParent,
                        'route' => $parameters->resolveValue(
                            $annotation->getFullRoute() ?: $routePrefix.$annotation->getRoute()
                        ),
                        'class' => $class,
                        'service' => $serviceId,
                        'method' => $methodReflection->getName(),
                        'extra' => $parameters->resolveValue(array_merge($headExtras, $annotation->getExtra())),
                    ];

                    if (empty($breadcrumbs[$name]['parent'])) {
                        $breadcrumbs[$name]['parent'] = null;
                    }
                }
            }
        }

        return $breadcrumbs;
    }

    private function createBreadcrumbTrail(array $breadcrumb, array $breadcrumbs)
    {
        $name = $breadcrumb['name'];
        $loaded = [];

        while (null !== $breadcrumb) {
            $loaded[$breadcrumb['name']] = true;

            if (null === $breadcrumb['parent']) {
                break;
            }

            if (!isset($breadcrumbs[$breadcrumb['parent']])) {
                throw new \RuntimeException(
                    sprintf(
                        'Parent "%s" of breadcrumb "%s" (%s:%s) is not registered.',
                        $breadcrumb['parent'],
                        $name,
                        $breadcrumb['class'],
                        $breadcrumb['method']
                    )
                );
            }

            if (isset($loaded[$breadcrumb['parent']])) {
                throw new \RuntimeException(
                    sprintf(
                        'Circular reference detected with parent of breadcrumb "%s" (%s:%s), path: "%s".',
                        $name,
                        $breadcrumb['class'],
                        $breadcrumb['method'],
                        implode(' -> ', array_keys($loaded))
                    )
                );
            }

            $name = $breadcrumb['parent'];

            $breadcrumb = $breadcrumbs[$breadcrumb['parent']];
        }

        // reverse to make the actual child last
        return array_keys(array_reverse($loaded));
    }
}

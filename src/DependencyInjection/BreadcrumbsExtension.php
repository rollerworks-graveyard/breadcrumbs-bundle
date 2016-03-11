<?php

/*
 * This file is part of the RollerworksBreadcrumbsBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\BreadcrumbsBundle\DependencyInjection;

use Rollerworks\Bundle\BreadcrumbsBundle\BreadcrumbsLoader;
use Rollerworks\Bundle\BreadcrumbsBundle\Twig\BreadcrumbsExtension as TwigBreadcrumbsExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Reference;

final class BreadcrumbsExtension extends Extension
{
    const EXTENSION_ALIAS = 'rollerworks_breadcrumbs';

    public function load(array $configs, ContainerBuilder $container)
    {
        $container->register('rollerworks_breadcrumbs.loader', BreadcrumbsLoader::class)
            ->setArguments([[], [], new Reference('service_container'), new Reference('request_stack')]);

        $container->register('rollerworks_breadcrumbs.twig_extension', TwigBreadcrumbsExtension::class)
            ->setArguments([new Reference('rollerworks_breadcrumbs.loader')])
            ->addTag('twig.extension');
    }

    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        // no-op
    }

    public function getAlias()
    {
        return self::EXTENSION_ALIAS;
    }
}

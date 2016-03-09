<?php

/*
 * This file is part of the RollerworksBreadcrumbsBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\BreadcrumbsBundle;

use Rollerworks\Bundle\BreadcrumbsBundle\DependencyInjection\BreadcrumbsExtension;
use Rollerworks\Bundle\BreadcrumbsBundle\DependencyInjection\Compiler\BreadcrumbsProviderPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RollerworksBreadcrumbsBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new BreadcrumbsExtension();
        }

        return $this->extension;
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new BreadcrumbsProviderPass());
    }

    protected function getContainerExtensionClass()
    {
        return BreadcrumbsExtension::class;
    }
}

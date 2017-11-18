<?php

/*
 * This file is part of the RollerworksBreadcrumbsBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\BreadcrumbsBundle\Twig;

use Rollerworks\Bundle\BreadcrumbsBundle\BreadcrumbsLoader;
use Twig\Extension\AbstractExtension;

class BreadcrumbsExtension extends AbstractExtension
{
    private $breadcrumbsLoader;

    public function __construct(BreadcrumbsLoader $breadcrumbsLoader)
    {
        $this->breadcrumbsLoader = $breadcrumbsLoader;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('rollerworks_get_breadcrumbs_for_route', [$this->breadcrumbsLoader, 'getByRoute']),
            new \Twig_SimpleFunction('rollerworks_get_breadcrumbs', [$this->breadcrumbsLoader, 'getBreadcrumb']),
        ];
    }

    public function getName()
    {
        return 'rollerworks_breadcrumbs';
    }
}

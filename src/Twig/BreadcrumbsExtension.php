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

class BreadcrumbsExtension extends \Twig_Extension
{
    /**
     * @var BreadcrumbsLoader
     */
    private $breadcrumbsLoader;

    /**
     * BreadcrumbExtension constructor.
     */
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

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'rollerworks_breadcrumbs';
    }
}

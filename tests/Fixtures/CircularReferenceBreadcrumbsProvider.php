<?php

/*
 * This file is part of the RollerworksBreadcrumbsBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\BreadcrumbsBundle\Tests\Fixtures;

use Rollerworks\Bundle\BreadcrumbsBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Request;

class CircularReferenceBreadcrumbsProvider
{
    /**
     * @Breadcrumb(name="acme_first", route="acme_first", parent="acme_third")
     */
    public function first()
    {
        return [
            'label' => 'Home',
            'route' => 'acme_homepage',
        ];
    }

    /**
     * @Breadcrumb(name="acme_second", route="acme_second", parent="acme_first")
     */
    public function second()
    {
        return [
            'label' => 'Home',
            'route' => 'acme_homepage',
        ];
    }

    /**
     * @Breadcrumb(name="acme_third", route="acme_third", parent="acme_second")
     */
    public function third()
    {
        return [
            'label' => 'Home',
            'route' => 'acme_homepage',
        ];
    }
}

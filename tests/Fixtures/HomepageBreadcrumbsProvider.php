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

class HomepageBreadcrumbsProvider
{
    /**
     * @Breadcrumb(name="acme.home", route="acme_homepage")
     */
    public function home(Request $request, array $breadcrumb)
    {
        return [
            'label' => 'Home',
            'route' => $breadcrumb['route'],
        ];
    }
}

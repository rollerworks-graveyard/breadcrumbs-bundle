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
use Rollerworks\Bundle\BreadcrumbsBundle\Annotation\BreadcrumbsProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * @BreadcrumbsProvider(namePrefix="acme_customer.", routePrefix="acme_customer_", section="foo")
 */
class WithBaseBreadcrumbsProvider
{
    /**
     * @Breadcrumb(name="home", route="home", label="Account")
     */
    public function home(Request $request, array $breadcrumb)
    {
        return [
            'label' => 'Account',
            'route' => $breadcrumb['route'],
        ];
    }

    /**
     * @Breadcrumb(name="edit", route="edit", label="Edit")
     * @Breadcrumb(name="modify", route="modify", label="Modify")
     */
    public function edit(Request $request, array $breadcrumb)
    {
        return [
            'label' => 'Account',
            'route' => $breadcrumb['route'],
        ];
    }

    /**
     * @Breadcrumb(fullName="acme_account_list", fullRoute="list", label="Edit", section="bar")
     */
    public function listAccounts(Request $request, array $breadcrumb)
    {
        return [
            'label' => 'Account',
            'route' => $breadcrumb['route'],
        ];
    }
}

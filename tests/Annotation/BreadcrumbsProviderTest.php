<?php

/*
 * This file is part of the RollerworksBreadcrumbsBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\BreadcrumbsBundle\Tests\Annotation;

use PHPUnit\Framework\TestCase;
use Rollerworks\Bundle\BreadcrumbsBundle\Annotation\BreadcrumbsProvider;

final class BreadcrumbsProviderTest extends TestCase
{
    /**
     * @test
     */
    public function it_provides_access_to_constructed_information()
    {
        $breadcrumb = new BreadcrumbsProvider(['routePrefix' => 'acme_foo', 'parent' => 'bla']);

        $this->assertEquals('acme_foo', $breadcrumb->getRoutePrefix());
        $this->assertEquals('bla', $breadcrumb->getParent());
        $this->assertEquals([], $breadcrumb->getExtra());
    }

    /**
     * @test
     */
    public function it_places_unknown_arguments_in_extra()
    {
        $breadcrumb = new BreadcrumbsProvider(['routePrefix' => 'acme_foo', 'user' => 'bla', 'bing' => 'bang']);

        $this->assertEquals('acme_foo', $breadcrumb->getRoutePrefix());
        $this->assertNull($breadcrumb->getParent());

        $this->assertEquals(['user' => 'bla', 'bing' => 'bang'], $breadcrumb->getExtra());
    }
}

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
use Rollerworks\Bundle\BreadcrumbsBundle\Annotation\Breadcrumb;

final class BreadcrumbTest extends TestCase
{
    /**
     * @test
     */
    public function it_provides_access_to_constructed_information()
    {
        $breadcrumb = new Breadcrumb(['name' => 'foobar', 'route' => 'acme_foo', 'parent' => 'bla']);

        $this->assertEquals('foobar', $breadcrumb->getName());
        $this->assertEquals('acme_foo', $breadcrumb->getRoute());
        $this->assertNull($breadcrumb->getFullRoute());
        $this->assertEquals('bla', $breadcrumb->getParent());
        $this->assertEquals([], $breadcrumb->getExtra());
    }

    /**
     * @test
     */
    public function it_provides_access_to_fullRoute()
    {
        $breadcrumb = new Breadcrumb(['name' => 'foobar', 'fullRoute' => 'acme_foo', 'parent' => 'bla']);

        $this->assertEquals('foobar', $breadcrumb->getName());
        $this->assertNull($breadcrumb->getRoute());
        $this->assertEquals('acme_foo', $breadcrumb->getFullRoute());
        $this->assertEquals('bla', $breadcrumb->getParent());
        $this->assertEquals([], $breadcrumb->getExtra());
    }

    /**
     * @test
     */
    public function it_errors_when_both_route_and_fullRoute_are_provided()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only "route" or "fullRoute" can be provided.');

        new Breadcrumb(['name' => 'foobar', 'fullRoute' => 'acme_foo', 'route' => 'bla']);
    }

    /**
     * @test
     */
    public function it_places_unknown_arguments_in_extra()
    {
        $breadcrumb = new Breadcrumb(['name' => 'foobar', 'route' => 'acme_foo', 'user' => 'bla', 'bing' => 'bang']);

        $this->assertEquals('foobar', $breadcrumb->getName());
        $this->assertEquals('acme_foo', $breadcrumb->getRoute());
        $this->assertNull($breadcrumb->getParent());
        $this->assertNull($breadcrumb->getFullRoute());

        $this->assertEquals(['user' => 'bla', 'bing' => 'bang'], $breadcrumb->getExtra());
    }
}

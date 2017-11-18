<?php

/*
 * This file is part of the RollerworksBreadcrumbsBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\BreadcrumbsBundle\Tests;

use PHPUnit\Framework\TestCase;
use Rollerworks\Bundle\BreadcrumbsBundle\BreadcrumbsLoader;
use Rollerworks\Bundle\BreadcrumbsBundle\Tests\Fixtures\CustomerBreadcrumbsProvider;
use Rollerworks\Bundle\BreadcrumbsBundle\Tests\Fixtures\HomepageBreadcrumbsProvider;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class BreadcrumbsLoaderTest extends TestCase
{
    /**
     * @var BreadcrumbsLoader
     */
    private $loader;

    protected function setUp()
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::create('/home'));
        $requestStack->push(Request::create('/'));

        $container = $this->prophesize(ContainerInterface::class);
        $container->get('acme.breadcrumbs.home')->willReturn(new HomepageBreadcrumbsProvider());
        $container->get('acme.breadcrumbs.customer')->willReturn(new CustomerBreadcrumbsProvider());

        $this->loader = new BreadcrumbsLoader(
            [
                'acme_customer.home' => [
                    'name' => 'acme_customer.home',
                    'parent' => 'acme.home',
                    'route' => 'acme_customer_home',
                    'class' => CustomerBreadcrumbsProvider::class,
                    'service' => 'acme.breadcrumbs.customer',
                    'method' => 'home',
                    'extra' => ['label' => 'Account'],
                    'trail' => ['acme.home', 'acme_customer.home'],
                ],
                'acme.home' => [
                    'name' => 'acme.home',
                    'parent' => null,
                    'route' => 'acme_homepage',
                    'class' => HomepageBreadcrumbsProvider::class,
                    'service' => 'acme.breadcrumbs.home',
                    'method' => 'home',
                    'extra' => [],
                    'trail' => ['acme.home'],
                ],
            ],
            [
                'acme_customer_home' => 'acme_customer.home',
                'acme_homepage' => 'acme.home',
            ],
            $container->reveal(),
            $requestStack
        );
    }

    /**
     * @test
     */
    public function get_info_of_a_single_breadcrumb()
    {
        $this->assertTrue($this->loader->has('acme_customer.home'));
        $this->assertEquals(
            [
                'name' => 'acme_customer.home',
                'parent' => 'acme.home',
                'route' => 'acme_customer_home',
                'class' => CustomerBreadcrumbsProvider::class,
                'service' => 'acme.breadcrumbs.customer',
                'method' => 'home',
                'extra' => ['label' => 'Account'],
                'trail' => ['acme.home', 'acme_customer.home'],
            ],
            $this->loader->get('acme_customer.home')
        );
    }

    /**
     * @test
     */
    public function get_breadcrumb_as_processed_format()
    {
        $this->assertSame(
            [
                'acme.home' => [
                    'label' => 'Home',
                    'route' => 'acme_homepage',
                ],
                'acme_customer.home' => [
                    'label' => 'Account',
                    'uri' => '/',
                    'route' => 'acme_customer_home',
                ],
            ],
            $this->loader->getBreadcrumb('acme_customer.home')
        );

        $this->assertSame(
            [
                'acme.home' => [
                    'label' => 'Home',
                    'route' => 'acme_homepage',
                ],
            ],
            $this->loader->getBreadcrumb('acme.home')
        );
    }

    /**
     * @test
     */
    public function get_breadcrumb_by_route()
    {
        $this->assertSame(
            [
                'acme.home' => [
                    'label' => 'Home',
                    'route' => 'acme_homepage',
                ],
            ],
            $this->loader->getByRoute('acme_homepage')
        );
    }
}

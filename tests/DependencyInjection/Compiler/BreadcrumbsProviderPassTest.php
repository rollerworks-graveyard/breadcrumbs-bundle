<?php

/*
 * This file is part of the RollerworksBreadcrumbsBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\BreadcrumbsBundle\Tests\DependencyInjection\Compiler;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Rollerworks\Bundle\BreadcrumbsBundle\DependencyInjection\Compiler\BreadcrumbsProviderPass;
use Rollerworks\Bundle\BreadcrumbsBundle\Tests\Fixtures\CircularReferenceBreadcrumbsProvider;
use Rollerworks\Bundle\BreadcrumbsBundle\Tests\Fixtures\CustomerBreadcrumbsProvider;
use Rollerworks\Bundle\BreadcrumbsBundle\Tests\Fixtures\HomepageBreadcrumbsProvider;
use Rollerworks\Bundle\BreadcrumbsBundle\Tests\Fixtures\InvalidParentBreadcrumbsProvider;
use Rollerworks\Bundle\BreadcrumbsBundle\Tests\Fixtures\WithBaseBreadcrumbsProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class BreadcrumbsProviderPassTest extends AbstractCompilerPassTestCase
{
    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new BreadcrumbsProviderPass());

        $this->registerService('rollerworks_breadcrumbs.loader', \stdClass::class)
            ->setArguments([[], null, null]);
    }

    /**
     * @test
     */
    public function find_breadcrumbs_in_a_provider_class()
    {
        $this->registerService('acme.breadcrumbs.home', HomepageBreadcrumbsProvider::class)
            ->addTag(BreadcrumbsProviderPass::LOADER_TAG_NAME);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('rollerworks_breadcrumbs.loader', 0, [
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
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('rollerworks_breadcrumbs.loader', 1, [
            'acme_homepage' => 'acme.home',
        ]);
    }

    /**
     * @test
     */
    public function find_breadcrumbs_with_parent_in_a_provider_class()
    {
        $this->registerService('acme.breadcrumbs.customer', CustomerBreadcrumbsProvider::class)
            ->addTag(BreadcrumbsProviderPass::LOADER_TAG_NAME);

        $this->registerService('acme.breadcrumbs.home', HomepageBreadcrumbsProvider::class)
            ->addTag(BreadcrumbsProviderPass::LOADER_TAG_NAME);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('rollerworks_breadcrumbs.loader', 0, [
            'acme_customer.home' => [
                'name' => 'acme_customer.home',
                'parent' => 'acme.home',
                'route' => 'acme_customer_home',
                'class' => CustomerBreadcrumbsProvider::class,
                'service' => 'acme.breadcrumbs.customer',
                'method' => 'home',
                'extra' => ['label' => 'Account'],
                'trail' => ['acme.home', 'acme_customer.home']
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
        ]);

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('rollerworks_breadcrumbs.loader', 1, [
            'acme_customer_home' => 'acme_customer.home',
            'acme_homepage' => 'acme.home',
        ]);
    }

    /**
     * @test
     */
    public function find_breadcrumbs_with_base_configuration()
    {
        $this->registerService('acme.breadcrumbs.customer', WithBaseBreadcrumbsProvider::class)
            ->addTag(BreadcrumbsProviderPass::LOADER_TAG_NAME);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithArgument(
            'rollerworks_breadcrumbs.loader',
            0,
            [
                'acme_customer.home' => [
                    'name' => 'acme_customer.home',
                    'parent' => null,
                    'route' => 'acme_customer_home',
                    'class' => WithBaseBreadcrumbsProvider::class,
                    'service' => 'acme.breadcrumbs.customer',
                    'method' => 'home',
                    'extra' => [
                        'section' => 'foo',
                        'label' => 'Account',
                    ],
                    'trail' => ['acme_customer.home'],
                ],
                'acme_customer.edit' => [
                    'name' => 'acme_customer.edit',
                    'parent' => null,
                    'route' => 'acme_customer_edit',
                    'class' => WithBaseBreadcrumbsProvider::class,
                    'service' => 'acme.breadcrumbs.customer',
                    'method' => 'edit',
                    'extra' => [
                        'section' => 'foo',
                        'label' => 'Edit',
                    ],
                    'trail' => [
                        'acme_customer.edit',
                    ],
                ],
                'acme_customer.modify' => [
                    'name' => 'acme_customer.modify',
                    'parent' => null,
                    'route' => 'acme_customer_modify',
                    'class' => WithBaseBreadcrumbsProvider::class,
                    'service' => 'acme.breadcrumbs.customer',
                    'method' => 'edit',
                    'extra' => [
                        'section' => 'foo',
                        'label' => 'Modify',
                    ],
                    'trail' => ['acme_customer.modify'],
                ],
                'acme_account_list' => [
                    'name' => 'acme_account_list',
                    'parent' => null,
                    'route' => 'list',
                    'class' => WithBaseBreadcrumbsProvider::class,
                    'service' => 'acme.breadcrumbs.customer',
                    'method' => 'listAccounts',
                    'extra' => [
                        'section' => 'bar',
                        'label' => 'Edit',
                    ],
                    'trail' => [
                        'acme_account_list',
                    ],
                ],
            ]
        );

        $this->assertContainerBuilderHasServiceDefinitionWithArgument('rollerworks_breadcrumbs.loader', 1, [
            'acme_customer_home' => 'acme_customer.home',
            'acme_customer_edit' => 'acme_customer.edit',
            'acme_customer_modify' => 'acme_customer.modify',
            'list' => 'acme_account_list',
        ]);
    }

    /**
     * @test
     */
    public function checks_circular_reference()
    {
        $this->registerService('acme.breadcrumbs.broken', CircularReferenceBreadcrumbsProvider::class)
            ->addTag(BreadcrumbsProviderPass::LOADER_TAG_NAME);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Circular reference detected with parent of breadcrumb "acme_second" '.
            '('.CircularReferenceBreadcrumbsProvider::class.':second), path: "acme_first -> acme_third -> acme_second".'
        );

        $this->compile();
    }

    /**
     * @test
     */
    public function checks_unregistered_parent()
    {
        $this->registerService('acme.breadcrumbs.broken', InvalidParentBreadcrumbsProvider::class)
            ->addTag(BreadcrumbsProviderPass::LOADER_TAG_NAME);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Parent "acme_third" of breadcrumb "acme_first" ('.InvalidParentBreadcrumbsProvider::class.
            ':first) is not registered.'
        );

        $this->compile();
    }
}

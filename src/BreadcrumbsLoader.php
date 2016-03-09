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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class BreadcrumbsLoader
{
    /**
     * @var array
     */
    private $breadcrumbs;

    /**
     * @var array
     */
    private $breadcrumbsByRoute;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * Constructor.
     *
     * @param array              $breadcrumbs
     * @param array              $breadcrumbsByRoute
     * @param ContainerInterface $container
     * @param RequestStack       $requestStack
     */
    public function __construct(
        array $breadcrumbs,
        array $breadcrumbsByRoute,
        ContainerInterface $container,
        RequestStack $requestStack
    ) {
        $this->breadcrumbs = $breadcrumbs;
        $this->breadcrumbsByRoute = $breadcrumbsByRoute;
        $this->container = $container;
        $this->requestStack = $requestStack;
    }

    /**
     * Returns whether a breadcrumb exists with the given name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return isset($this->breadcrumbs[$name]);
    }

    /**
     * Get breadcrumb information (unprocessed).
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException when no breadcrumb is registered
     *                                   by the this name.
     *
     * @return array
     */
    public function get($name)
    {
        if (!isset($this->breadcrumbs[$name])) {
            throw new \InvalidArgumentException(sprintf('No breadcrumb with name "%s" is registered.', $name));
        }

        return $this->breadcrumbs[$name];
    }

    /**
     * Get the processed breadcrumb trail by the route-name.
     *
     * @param string $routeName
     *
     * @return array Returns an empty array when no breadcrumb was found
     *               for the route.
     */
    public function getByRoute($routeName)
    {
        if (!isset($this->breadcrumbsByRoute[$routeName])) {
            return [];
        }

        return $this->getBreadcrumb($this->breadcrumbsByRoute[$routeName]);
    }

    /**
     * Get breadcrumb trail in a processed format.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException when no breadcrumb is registered
     *                                   by the this name.
     *
     * @return array
     */
    public function getBreadcrumb($name)
    {
        if (!isset($this->breadcrumbs[$name])) {
            throw new \InvalidArgumentException(sprintf('No breadcrumb with name "%s" is registered.', $name));
        }

        $request = $this->requestStack->getCurrentRequest();
        $trail = [];

        foreach ($this->breadcrumbs[$name]['trail'] as $name) {
            $trail[$name] = $this->container->get($this->breadcrumbs[$name]['service'])
                ->{$this->breadcrumbs[$name]['method']}(
                    $request,
                    $this->breadcrumbs[$name]
                );
        }

        return $trail;
    }
}

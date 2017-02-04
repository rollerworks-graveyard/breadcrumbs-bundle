<?php

/*
 * This file is part of the RollerworksBreadcrumbsBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\BreadcrumbsBundle\Annotation;

/**
 * @Annotation
 * @Target("METHOD")
 */
final class Breadcrumb
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $fullName;

    /**
     * @var string
     */
    private $route;

    /**
     * @var string
     */
    private $parent;

    /**
     * @var string
     */
    private $fullRoute;

    /**
     * Extra information provided with the annotation.
     *
     * @var array
     */
    private $extra = [];

    public function __construct(array $data)
    {
        foreach ($data as $name => $value) {
            if (property_exists($this, $name)) {
                $this->$name = $value;
            } else {
                $this->extra[$name] = $value;
            }
        }

        if ($this->fullName && $this->name) {
            throw new \InvalidArgumentException(sprintf('Only "name" or "fullName" can be provided.'));
        }

        if ($this->fullRoute && $this->route) {
            throw new \InvalidArgumentException(sprintf('Only "route" or "fullRoute" can be provided.'));
        }
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * @return string
     */
    public function getFullRoute()
    {
        return $this->fullRoute;
    }

    /**
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return array
     */
    public function getExtra()
    {
        return $this->extra;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }
}

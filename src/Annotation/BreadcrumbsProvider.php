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
 * @Target("CLASS")
 */
final class BreadcrumbsProvider
{
    /**
     * @var string
     */
    private $namePrefix;

    /**
     * @var
     */
    private $routePrefix;

    /**
     * @var string
     */
    private $parent;

    /**
     * Extra information provided with the annotation.
     *
     * Marked as mixed as this is only used internally.
     * But it's possible to use this as a name.
     *
     * @var mixed
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
    }

    /**
     * @return string
     */
    public function getNamePrefix()
    {
        return $this->namePrefix;
    }

    /**
     * @return string
     */
    public function getRoutePrefix()
    {
        return $this->routePrefix;
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
}

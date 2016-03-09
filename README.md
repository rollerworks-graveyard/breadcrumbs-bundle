RollerworksBreadcrumbsBundle
============================

Easy breadcrumbs navigation for Symfony powered applications (Not the Symfony FrameworkBundle).

Requirements
------------

You need at least PHP 5.5.

This package requires the Symfony HTTPKernel component and can be used
in any Symfony powered application as long as the Symfony DependencyInjection
component is used for dependency injection.

Installation
------------

To install this package, add `rollerworks/breadcrumbs-bundle` to your composer.json

```bash
$ php composer.phar require rollerworks/breadcrumbs-bundle
```

Now, Composer[Composer] will automatically download all required files, and install them
for you.

[Composer]: https://getcomposer.org/

### Enable the Bundle

**Note:**

> This package is designed for Symfony powered applications,
> while not requiring the Symfony FrameworkBundle. It's expected
> you know enough about Symfony to somehow enable the bundle in your
> application. You should have at least the `request_stack` and `service_container`
> services registered and auto loading set-up.

Installation instructions shown here are limited to usage "with" the [Symfony-standard](https://github.com/symfony/symfony-standard/)
distribution.

After running Composer enable the `RollerworksBreadcrumbsBundle` in your kernel:

```php
<?php

// in AppKernel::registerBundles()
$bundles = [
    // ...
    new Rollerworks\Bundle\BreadcrumbsBundle\RollerworksBreadcrumbsBundle(),
    // ...
];
```

Basic usage
-----------

The RollerworksBreadcrumbsBundle loads breadcrumbs from provider classes, each
provider class can provide one or more breadcrumbs by methods with Annotations.

Say you have an customer section in your application with the following BreadcrumbsProvider
class:

```php

namespace Acme\Customer;

use Rollerworks\Bundle\BreadcrumbsBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Request;

class CustomerBreadcrumbsProvider
{
    /**
     * @Breadcrumb(name="acme_customer_home", route="acme_customer_home")
     */
    public function home(Request $request, array $breadcrumb)
    {
        return [
            'label' => 'Account',
            'route' => $breadcrumb['route'],
        ];
    }
}
```

Now you need to register the provider in the service container, so the BreadcrumbsLoader
can find them:

```xml
<?xml version="1.0" ?>

<container xmlns="http://symfony.com/schema/dic/services"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>
        <service id="acme.breadcrumbs.customer" class="Acme\Customer\CustomerBreadcrumbsProvider">
            <tag name="rollerworks_breadcrumb.provider" />
        </service>
    </services>
</container>
```

Or if you prefer YAML

```yaml
services:
    acme.breadcrumbs.customer:
        class: "Acme\Customer\CustomerBreadcrumbsProvider"
        tags: [ { name: rollerworks_breadcrumb.provider } ]
```

That's it, the `acme_customer_home` breadcrumb is now available for loading.

**Tip:** You can also load the breadcrumb by the route, so you don't have
to configure the breadcrumb for every template. Simple get the current route-name
by the request information.

The breadcrumb method must return an array, but you can decide which keys
and values you want to use :+1:

By convention it's recommended to at least return a label and route.
When the `route` key is missing it will automatically be set by the `route` value
of the annotation.

### Extra parameters

It's possible to pass additional attributes with an annotation:

`@Breadcrumb(name="acme_customer_home", route="acme_customer_home", route_parameters={"foo"="bar"})`

The `route_parameters` attribute is available using `$breadcrumb['extra']['route_parameters']`.

You can use any value or type you want.

**Note:** When you pass the `extra` attribute to annotation it must be an array!
And it will overwrite all other extra parameters.

### Breadcrumb parents trail

Breadcrumbs don't consist of just one, they form a trail to there origin.
Fortunately the RollerworksBreadcrumbsBundle provides a very powerful and easy
way to create breadcrumb trails.

All you need to do is add the `parent` attribute to annotation:

```
@Breadcrumb(name="acme_customer_home", route="acme_customer_home", parent="acme_homepage")
```

The `parent` value must point to another breadcrumb that you want to use a parent.
In this example you would get something like `Home > Account`.

But when `acme_homepage` also has a parent your trail might look something like:
`Acme > Home > Account`.

**Note:** It doesn't matter if the parent is registered later then the current breadcrumb,
all breadcrumbs are collected during the container compiling process, and then there trails
are computed.

There is no limit on the number of parents, but a parent cannot reference
a breadcrumb that is already in the trail, so this will not work: ``Acme > Home > Account > Acme`,
as it would result in an endless loop.

### Base configuration

When define breadcrumbs in a provider, there is a big chance they share
a common or base configuration. For example all or the majority starts with
the same name/route prefix or have the same parent.

Instead of copying or retyping this all by hand there is an easier solution:

```php

namespace Acme\Customer;

use Rollerworks\Bundle\BreadcrumbsBundle\Annotation\BreadcrumbProvider;
use Rollerworks\Bundle\BreadcrumbsBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Request;

/**
 * @BreadcrumbProvider(namePrefix="acme_customer.", routePrefix="acme_customer_", parent="app_homepage")
 */
class CustomerBreadcrumbsProvider
{
    /**
     * @Breadcrumb(name="home", route="home")
     */
    public function home(Request $request, array $breadcrumb)
    {
        return [
            'label' => 'Account',
            'route' => $breadcrumb['route'],
        ];
    }

    /**
     * @Breadcrumb(name="edit", route="edit")
     */
    public function edit(Request $request, array $breadcrumb)
    {
        return [
            'label' => 'Account',
            'route' => $breadcrumb['route'],
        ];
    }
}
```

The `@BreadcrumbProvider` annotation will set the base configuration
for all the breadcrumbs in the class. So the breadcrumb of method `home` will be become
`@Breadcrumb(name="acme_customer.home", route="acme_customer_home", parent="app_homepage")`.

And the same goes for the `edit` method.

But if you don't need this for all methods (or not all attributes) you can still overwrite the
configuration per breadcrumb:

```php

namespace Acme\Customer;

use Rollerworks\Bundle\BreadcrumbsBundle\Annotation\BreadcrumbProvider;
use Rollerworks\Bundle\BreadcrumbsBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Request;

/**
 * @BreadcrumbProvider(namePrefix="acme_customer.", routePrefix="acme_customer_", parent="app_homepage")
 */
class CustomerBreadcrumbsProvider
{
    /**
     * @Breadcrumb(name="home", route="home")
     */
    public function home(Request $request, array $breadcrumb)
    {
        return [
            'label' => 'Account',
            'route' => $breadcrumb['route'],
        ];
    }

    /**
     * @Breadcrumb(name="edit", route="edit")
     */
    public function edit(Request $request, array $breadcrumb)
    {
        return [
            'label' => 'Account',
            'route' => $breadcrumb['route'],
        ];
    }

    /**
     * @Breadcrumb(fullName="acme_customer.group_list", fullRoute="acme_group_list", parent="")
     */
    public function listGroups(Request $request, array $breadcrumb)
    {
        return [
            'label' => 'Account',
            'route' => $breadcrumb['route'],
        ];
    }
}
```

Now the `listGroups` method will ignore the the base configuration,
and use only it's own.

**Note:** The `extra` attribute will overwrite the inherited extra information.

### Multiple `@Breadcrumb` annotations

If your breadcrumbs are very static, eg. only a route, label and some extra information
you can also use a single method for multiple breadcrumbs.

```php

namespace Acme\Customer;

use Rollerworks\Bundle\BreadcrumbsBundle\Annotation\BreadcrumbProvider;
use Rollerworks\Bundle\BreadcrumbsBundle\Annotation\Breadcrumb;
use Symfony\Component\HttpFoundation\Request;

/**
 * @BreadcrumbProvider(namePrefix="acme_customer.", routePrefix="acme_customer_", parent="app_homepage")
 */
class CustomerBreadcrumbsProvider
{
    /**
     * @Breadcrumb(name="acme_customer.home", route="home", label="Account")
     * @Breadcrumb(name="acme_customer.edit", route="edit", label="Edit", parent="acme_customer.home")
     */
    public function action(Request $request, array $breadcrumb)
    {
        return [
            'label' => $breadcrumb['extra']['label'],
            'route' => $breadcrumb['route'],
        ];
    }
}
```

The `action` method will be called for every breadcrumb, but with different data.

### Special notes

The class and method names are unimportant, you can use anything you like.
But methods must be declared `public`. Arguments are not required.

The `$request` provides information about the current request.

The `$breadcrumb` argument provides the normalized information of the breadcrumb:

 * name: Name of the breadcrumb.
 * parent: Parent of the breadcrumb (null when empty).
 * class: Full qualified class-name of the breadcrumbs provider.
 * service: The service-id of the breadcrumbs provider.
 * method: Method name of the provider (basically the current method).
 * extra: All extra attributes provided to the Annotation as an array.
 * trail: The breadcrumb trail from root to current, all values are breadcrumb names.

#### Arguments with container parameters

All parameter values (including extra parameters) are resolved by the ServiceContainer's
parameter-bag. Value `%somevar%` will be replaced with the container parameter value of `%somevar%`.

Only string values are accepted for non-extra parameters.

**Note:** Array values are resolved recursively, meaning all deeper levels are are also resolved.

To use percent sign as-is in a parameter or attribute,
it must be escaped with another percent sign: `foo%%somevar%%bar`.

See also:

https://symfony.com/doc/current/components/dependency_injection/parameters.html
https://symfony.com/doc/current/book/service_container.html#service-parameters

Versioning
----------

For transparency and insight into the release cycle, and for striving
to maintain backward compatibility, this package is maintained under
the Semantic Versioning guidelines as much as possible.

Releases will be numbered with the following format:

`<major>.<minor>.<patch>`

And constructed with the following guidelines:

* Breaking backward compatibility bumps the major (and resets the minor and patch)
* New additions without breaking backward compatibility bumps the minor (and resets the patch)
* Bug fixes and misc changes bumps the patch

For more information on SemVer, please visit <http://semver.org/>.

License
-------

The package is provided under the [MIT license](LICENSE).

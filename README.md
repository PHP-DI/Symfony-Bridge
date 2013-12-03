# PHP-DI integration with Symfony 2

**Work in progress**

[![Build Status](https://travis-ci.org/mnapoli/PHP-DI-Symfony2.png?branch=master)](https://travis-ci.org/mnapoli/PHP-DI-Symfony2)

This library provides integration for PHP-DI v4 with Symfony 2.

[PHP-DI](php-di.org) is a Dependency Injection Container for PHP.

*This library is compatible only with PHP-DI 4.0 and above (currently in development).*

# Use

Require the libraries with Composer:

```json
{
    "require": {
        "mnapoli/php-di": "4.*",
        "mnapoli/php-di-symfony2": "*"
    }
}
```

Now you need to configure Symfony to use the alternative container:

```php
class AppKernel extends Kernel
{
    /**
     * Gets the container's base class.
     *
     * @return string
     */
    protected function getContainerBaseClass()
    {
        return 'DI\Bridge\Symfony\SymfonyContainerBridge';
    }

    /**
     * Initializes the DI container.
     */
    protected function initializeContainer()
    {
        parent::initializeContainer();

        // Configure your container here
        // http://php-di.org/doc/container-configuration
        $builder = new \DI\ContainerBuilder();
        $builder->wrapContainer($this->getContainer());

        $this->getContainer()->setPHPDIContainer($builder->build());
    }
}
```

## Now you can play

You can now define controllers as services, without any configuration, using PHP-DI's magic!

```php
class ProductController
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function indexAction()
    {
        $products = $this->productService->getAllProducts();

        return $this->templating->renderResponse(
            'MyBundle::products.html.twig',
            ['products' => $products]
        );
    }
}
```


## How it works

Here is the workflow of Symfony:

- if cache is not fresh, create a `ContainerBuilder` and compile it
- the compiled container extends the class defined by `Kernel::getContainerBaseClass()`
- require the compiled container

How does it tests that the cache is not fresh (in dev environment)?

- for each class that may be cached (Kernel, Bundle, service…), a metadata is stored in the cache
- the metadata contains the last modification time of the file
- if one of these files has been edited since the last compilation, the cache is not fresh

How PHP-DI integrates:

- PHP-DI redefines `Kernel::getContainerBaseClass()` to use its chained container (`SymfonyContainerBridge`)
- the chained container first tries to get the entry in Symfony's compiled container
- else it looks for the entry in PHP-DI's container (and PHP-DI can leverage its caching here)

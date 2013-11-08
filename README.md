# PHP-DI integration with Symfony 2

**Work in progress**

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
        $builder->setDefinitionCache(new Doctrine\Common\Cache\ArrayCache());

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

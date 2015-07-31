# PHP-DI integration with Symfony 2

[![Build Status](https://travis-ci.org/PHP-DI/Symfony2-Bridge.png?branch=master)](https://travis-ci.org/PHP-DI/Symfony2-Bridge)

This package provides integration for PHP-DI 4 with Symfony 2.

[PHP-DI](http://php-di.org) is a Dependency Injection Container for PHP.

This package is compatible with **PHP-DI 4.x**.

**The documentation is here: http://php-di.org/doc/frameworks/symfony2.html**

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

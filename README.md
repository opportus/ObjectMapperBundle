[![License](https://poser.pugx.org/opportus/object-mapper-bundle/license)](https://packagist.org/packages/opportus/object-mapper-bundle)
[![Latest Stable Version](https://poser.pugx.org/opportus/object-mapper-bundle/v/stable)](https://packagist.org/packages/opportus/object-mapper-bundle)
[![Latest Unstable Version](https://poser.pugx.org/opportus/object-mapper-bundle/v/unstable)](https://packagist.org/packages/opportus/object-mapper-bundle)

This bundle integrates into your Symfony project [`opportus/object-mapper`](https://github.com/opportus/object-mapper), a library that allows copying state of objects to differently typed objects using extensible controls and mapping strategies.

Below is the installation guide specific to this Symfony bundle. For the complete guide, please refer to `opportus/object-mapper`'s homepage: https://github.com/opportus/object-mapper.

## Installation

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require opportus/object-mapper-bundle
```

### Applications that do not use Symfony Flex

#### Step 1 - Download the Bundle

Open a command console, enter your project directory and execute the following command to download the latest stable version of this bundle:

```console
$ composer require opportus/object-mapper-bundle
```

#### Step 2 - Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new Opportus\ObjectMapperBundle\OpportusObjectMapperBundle(),
        );

        // ...
    }

    // ...
}
```

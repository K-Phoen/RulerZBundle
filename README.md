RulerZBundle
============

This bundle integrates [RulerZ](https://github.com/K-Phoen/rulerz) into Symfony.

Installation
------------

Require the bundle:

```
composer require 'kphoen/rulerz-bundle'
```

And declare it into your `app/AppKernel.php` file:

```php
public function registerBundles()
{
    return array(
        // ...
        new KPhoen\RulerZBundle\KPhoenRulerZBundle(),
    );
}
```

Usage
-----

This bundle registers a `rulerz` service which is an instance of `RulerZ\RulerZ`.

```
$rulerz = $this->container->get('rulerz');

$rulerz->filter(/* ... */);
```

### Cache

RulerZ supports caching rules. The backend used for this cache can be specified
directly in the configuration:

```yaml
# app/config/config.yml

kphoen_rulerz:
    cache:
        provider: rulerz_cache
        lifetime: 86400         # seconds, optionnal
```

Where `rulerz_cache` is a service implementing \Doctrine\Common\Cache\Cache.
By default, no cache is used.

*Pro-tip*: the [DoctrineCacheBundle](https://github.com/doctrine/DoctrineCacheBundle)
can be used to easily manage cache backends.

Licence
-------

This bundle is under the [MIT](https://github.com/K-Phoen/rulerz/blob/master/README.md) licence.

# Upgrade from 3.4.1 to 3.5.0

## Configuration

Version 3.5.0 comes with two new features. The first feature are aliases, that can be used to override classes that
has been configured by the DI, the second one is a more convenient cache configuration.

### Aliases

Aliases provides the possiblity to override DI values with a custom implementation. For example, the cache configuration
uses the alias `cache.adapter` to define which cache implementation should be used. If the cache implementation should be
replaced for some circumstances, the new implementation can be defined by overriding the alias target with 

```json
{
  ...
  "aliases": [
    {
      "id": "cache.adapter",
      "target": "import_parallel.cache.adapter.generic"
    }
  ],
  ...
}
```

By default, the cache uses a local adapter implementation that holds the cached data in an array. In the example above,
the default cache implementation will be replace with a PSR-6 compatible adapter implementation that allows e. g. the
usage of Redis.

### Cache

Up from version 3.5.0, it is possible to configure the caches.

It is important to know, that M2IF uses two different types of cache. First, a `static` cache adapter with the DI alias `cache.static` 
that can **NOT** be enabled or disabled. Second, the `configurable` cache type with the DI alias `cache.configurable` that **CAN** be 
enabled or disabled. Both of them supports TTL configuration whereas it is important that the TTL for the static cache type will be 
higher than the time the longest import runs in seconds, as the static cache contains the core data that'll be necessary to run the 
import and will always be enabled.

To disable the `configurable` cache and set the TTL of the `static` cache type to 1440 seconds, add the following lines to
the configuration

```json
{
  ...
  "caches": [
    {
      "type": "cache.static",
      "time": 1440
    },
    {
      "type": "cache.configurable",
      "enable": false
    }
  ],
  ...
}
```

> The registry uses the `static` cache type, whereas the repositories for example uses the `configurable` one.

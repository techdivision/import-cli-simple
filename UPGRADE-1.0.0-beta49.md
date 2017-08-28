# Upgrade from 1.0.0-beta48 to 1.0.0-beta49

## Cache Warming Plug-In

The cache warmer plug-in allows cache warming for repository implementations. 

### Default Cache Warmers

To activate the default cache warmers (actually only one for the EAV attribute option value repository exists), add the following configuration either to the `replace` and the `add-update` configuration (no difference for the EE).

```json
{
  ...
  {
    "name" : "replace",
    "plugins" : [
      {
        "id": "import.plugin.cache.warmer"
      },
      ...
    ]
  }
}
```

### Custom Cache Warmers

To register additional custom cache warmers, the symfony DI ID can be specified as parameter like

```json
{
  ...
  {
    "name" : "replace",
    "plugins" : [
      {
        "id": "import.plugin.cache.warmer",
        "params" : [
        {
          "cache-warmers" : [
          "id.of.your.cache.warmer"
        ]
      },
      ...
    ]
  }
}
```
# Upgrade from 3.3.1 to 3.4.0

## Configuration

With version 3.4.0 configuration has been extended.

### Events

This release comes with additional events as well as the possiblity to register events on operation level.

Beside to possiblity to register global events, up with this version it is possible to register events on
plugin and subject level. This avoids execution of events that only provide functionality for a dedicated 
plugin or subject.

#### Plug-In Level

On Plug-In Level, the following events has been added

* plugin.process.start
* plugin.process.success
* plugin.process.failure

The listeners will only be executed before, after or on failure of the plugin, for which the event has been 
configured for. Configuration will look like

```json
{
  ...
  "operations" : [{
      "name" : "add-update",
      "plugins" : [
        {
          "id": "import.plugin.cache.warmer"
        },
        {
          "id": "import.plugin.global.data"
        },
        {
          "id": "import.plugin.subject",
          "listeners" : [
             {
              "plugin.process.success" : [
                "import_product_tier_price.listener.delete.obsolete.tier_prices"
              ]
            }
          ]
          ...
        }
      ]
    }
    ...
  ]
}
```

#### Subject Level

On Subject Level, the following events has been added

* subject.import.start
* subject.import.success
* subject.import.failure
* subject.export.start
* subject.export.success
* subject.export.failure

As subjects are responsible for importing **AND** exporting artefacts, events for both steps has been added.

The listeners will only be executed before, after or on failure of the subject, for which the event has been 
configured for. Configuration will look like

```json
{
  ...
  "operations" : [{
      "name" : "add-update",
      "plugins" : [
        {
          "id": "import.plugin.cache.warmer"
        },
        {
          "id": "import.plugin.global.data"
        },
        {
          "id": "import.plugin.subject",
          "listeners" : [
             {
              "plugin.process.success" : [
                "import_product_tier_price.listener.delete.obsolete.tier_prices"
              ]
            }
          ]
          "subjects": [
            ...
            {
              "id": "import_product_tier_price.subject.tier_price",
              "identifier": "files",
              "listeners": [
                 {
                  "subject.import.success" : [
                    "import_product.listener.register.sku.to.pk.mapping"
                  ]
                }
              ],
              "file-resolver": {
                "prefix": "product-import-tier-price"
              },
              "observers": [
                {
                  "import": [
                    "import_product_tier_price.observer.tier_price.update"
                  ]
                }
              ]
            },
          },
          ...
        ]
      ]
    }
    ...
  ]
}
```

## Cache

Additional this version comes with a cache refactoring. Up from now, all classes with cache functionality,
uses a PSR-6 compatible cache adapter. This cache adapter is a singleton implementation for each import,
therefore each cache item has to use an unique identifier to avoid caching issues.


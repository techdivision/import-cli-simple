# Upgrade from 3.0.1 to 3.1.0

## Configuration

### Commandline Option --params

Up from version 3.1.0 it is possible to pass global params with the `--params` commandline option, e. g.

```sh
vendor/bin/import-cli import:products \
  --configuration=projects/sampele-data/ce/2.3.x/conf/products/techdivision-import.json \
  --params='{ "params": [ { "website-country-mapping": { "DE": [ "de_de" ], "AT": [ "at_de", "at_de" ] } } ] }'
```

The params has to be JSON encoded and needs the same format as necessary to specifiy them in the main
section of the configuration file.

The params can then be loaded, for example, in an observer with the following code

```php
class MyObserver extends AbstractObserver
{
  
  /**
   * Example implementation to load a param from the global section of the configuration file.
   *
   * @param string $name The param name to load
   *
   * @return mixed The value
   */
  protected loadParams($name)
  {
    return $this->getSubject()->getConfiguration()->getConfiguration()->getParam($name);
  }
}
```

### Dynamic Attribute Option Value/Swatch Creation

Up from version 3.1.0 it is possible to have attribute option values + swatches to be created automatically. Therefore
it is necessary to add additional callbacks to the product import configuration. This can be done with the 
`frontend-input-callbacks` configuration option like

```json
"subjects" : [
  ...,
  {
    "id": "import_product.subject.bunch",
    "identifier": "files",
    "prefix": "product-import",
    "filesystem-adapter" : {
      "id" : "import.adapter.filesystem.factory.league",
      "adapter" : {
        "type" : "League\\Flysystem\\Adapter\\Local"
      }
    },
    "params" : [
      {
        "copy-images" : false,
        "media-directory" : "projects/eglo/tmp"
      }
    ],
    "frontend-input-callbacks": [
      {
        "select": [
          "import_attribute.callback.create.select.option.value",
          "import_product.callback.select"
        ],
        "multiselect": [
          "import_attribute.callback.create.multiselect.option.value",
          "import_product.callback.multiselect"
        ]
      }
    ],
    "observers": [
      {
        "pre-import": [
          "import_product.observer.pre.load.entity.id",
          "import_product_url_rewrite.observer.clear.url.rewrite",
          "import_product.observer.clear.product",
          "import.observer.attribute.set",
          "import.observer.additional.attribute",
          "import_product_ee.observer.url.key",
          "import_product.observer.file.upload",
          "import_product.observer.quality.and.stock.status"
        ]
      },
      {
        "import": [
          "import_product_ee.observer.product",
          "import_product.observer.product.website",
          "import_product.observer.category.product",
          "import_product.observer.product.inventory",
          "import_product_ee.observer.product.attribute",
          "import_product_url_rewrite.observer.product.url.rewrite",
          "import_product_variant.observer.product.variant",
          "import_product_bundle.observer.product.bundle",
          "import_product_media.observer.product.media",
          "import_product_link.observer.product.link"
        ]
      },
      {
        "post-import": [
          "import_product_ee.observer.clean.up"
        ]
      }
    ]
  },
  ...
]
```

The callback configuration is the same for the CE as well as the EE.

### Default Attribute Value Observer

Up to version 3.1.0 the default value for a selected option has been stored 1:1 into the database. This issue has been
fixed with a new observer `import_attribute.observer.attribute.option.default` that has to be registered in the subject's 
configuration for the attribute import like

```json
"subjects" : [
  ...,
  {
    "id": "import_attribute.subject.option",
    "identifier": "files",
    "file-resolver": {
      "prefix": "option-import"
    },
    "observers": [
      {
        "import": [
          "import_attribute.observer.attribute.option",
          "import_attribute.observer.attribute.option.value",
          "import_attribute.observer.attribute.option.swatch",
          "import_attribute.observer.attribute.option.default"
        ]
      }
    ]
  },
  ...
]
```

Do not forget to register it for the `replace` as well as the `add-update` operation.

### Date + Number Converter

The date + number conversion from a source date/number to the expected Magento 2 target format has been refactored.
Therefore a DateConverter and a NumberConverter class has been introduced which replaces the `source-date-format` 
option in the configuration file.

source and number conversion has to be configured on subject level, e. g.

```json
"subjects" : [
  {
    "id": "import.subject.move.files",
    "identifier": "move-files",
    "prefix": "product-import",
    "ok-file-needed": true,,
    "number-converter": {
      "locale": "de_DE"
    },
    "date-converter": {
      "source-date-format": "Y-m-d H:i:s"
    }
  }
]
```

> Whith overriding the ID values of each node, it is possible to replace the converter implementation.

### File Resolver

To make configuration of import file + OK file handling more flexible, the new FileResolver class has been added.

Instead of defining the pattern of the files that has to be imported as well as their appropriate OK file on the
subject level, each subject now has a `file-resolver` node that accepts the following new configuration options

* prefix
* filename
* counter
* suffix
* ok-file-suffix
* element-separator
* pattern-elements

These options allows a very flexible configuration for the pattern which decides which files has to be imported 
or not. You can find more detailed information in the documentation on the M2IF [website](http://www.m2if.com).

It is important that the `prefix` option for all subjects which, until version < 3.1.*, looks like 

```json
"subjects" : [
  {
    "id": "import.subject.move.files",
    "identifier": "move-files",
    "prefix": "product-import",
    "ok-file-needed": true
  }
]
```

has to be replaced with 

```json
"subjects" : [
  {
    "id": "import.subject.move.files",
    "identifier": "move-files",
    "file-resolver": {
      "prefix": "product-import"
    },
    "ok-file-needed": true
  }
]
```

A correct subject configuration up from version 3.1.* will look like 

```json
{
  ...
  "operations" : [
    {
      "name" : "delete",
      "plugins" : [
        {
          "id": "import.plugin.global.data"
        },
        {
          "id": "import.plugin.subject",
          "subjects" : [
            {
              "id": "import.subject.move.files",
              "identifier": "move-files",
              "file-resolver": {
                "prefix": "product-import"
              },
              "ok-file-needed": true
            },
            ...
          ]
        }
      ]
    }
  ]
  ...
}
```
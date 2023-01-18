# Upgrade from 3.0.1 to 3.1.0

## Category Import

This version fixed the following issues from Github

* [#21](https://github.com/techdivision/import-product-variant/issues/21)
* [#50](https://github.com/techdivision/import-category/issues/50)
* [#51](https://github.com/techdivision/import-category/issues/51)
* [#52](https://github.com/techdivision/import-category/issues/52)
* [#55](https://github.com/techdivision/import-category/issues/55)
* [#56](https://github.com/techdivision/import-category/issues/56)

As a result, the configuration file has to be updated with the following changes.

### Magento CE/EE 2.2 + 2.3

The observer `import_category.observer.category.copy` has been added which copies the categories that has to be imported
to a new CSV file before they have been removed with the `import_category.observer.clear.category` in the `replace` mode.

So the configuration for the `replace` operation has to be extended with the new observer like

```json
"subjects" : [
  ...,
  {
    "id": "import_category_ee.subject.bunch",
    "identifier": "files",
    "file-resolver": {
      "prefix": "category-import"
    },
    "observers": [
      {
        "import": [
          "import_category.observer.category.copy",
          "import_category.observer.clear.category"
        ]
      }
    ]
  },
  ...
]
```

### Magento EE 2.2 + 2.3

#### Remove Existing Categories

A new observer `import_category_ee.observer.clear.category` to remove categories has been added. This replaces the old 
one from the CE named `import_category.observer.clear.category` in the `delete` and `replace` operation, e. g.

```json
"subjects" : [
  ...,
  {
    "id": "import_category_ee.subject.bunch",
    "identifier": "files",
    "file-resolver": {
      "prefix": "category-import"
    },
    "observers": [
      {
        "import": [
          "import_category.observer.category.copy",
          "import_category_ee.observer.clear.category"
        ]
      }
    ]
  },
  ...
]
```

#### URL Key and Path Handling

The second new observer `import_category_ee.observer.url.key.and.path` also replaces the old one from the CE named
`import_category.observer.url.key.and.path`. This observer has to be replaced in the `replace` and `add-update`
operations. For example, your configuration should look like this for the `replace` operation

```json
  ...,
  "observers" : [
    {
      "pre-import": [
        "import_category_ee.observer.url.key.and.path",
        "import.observer.attribute.set",
        "import.observer.additional.attribute",
        "import_category.observer.file.upload"
      ]
    },
    {
      "import": [
        "import_category_ee.observer.category",
        "import_category_ee.observer.category.attribute",
        "import_category.observer.category.url.rewrite"
      ]
    },
    {
      "post-import": [
        "import_category_ee.observer.clean.up"
      ]
    }
  ]
]
```

#### URL Rewrite Handling

Finally the new observers `import_category_ee.observer.url.rewrite` and ` `import_category_ee.observer.url.rewrite` replaces 
the old one from the CE named `import_category.observer.url.rewrite` and  `import_category.observer.url.rewrite.update`. As 
the observer above, this has to be replaced in the `replace` as well as the `add-update` operations, e. g. for the `add-update`
operation your configuration should look like this 

```json
"subjects" : [
  ...,
  {
    "id": "import_category_ee.subject.bunch",
    "identifier": "files",
    "file-resolver": {
      "prefix": "url-rewrite"
    },
    "observers": [
      {
        "import": [
          "import_category_ee.observer.url.rewrite.update"
        ]
      }
    ]
  }
  ...
]
```

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
        "media-directory" : "projects/project-name/tmp"
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

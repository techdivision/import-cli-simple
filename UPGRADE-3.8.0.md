# Upgrade from 3.7.4 to 3.8.0

## Logger

The default logger in version 3.7.0 writes log messages to the log file defined in php.ini with the error_log directive. With version 3.8.0 this behaviour has been changed so that the log file is located in the current import directory and has the name system.log. So every import has its own log file which will be archived with the other artifacts even after a successful import if this has been enabled.

Additionally, a log handler has been integrated which automatically outputs the serial of the current import process for each log message. In case all messages are written to a central log file, this makes it easier to separate the log messages, e.g. during debugging. Besides this, the complete initialization of the loggers was changed to DI. In version 3.7.0 the configuration of the loggers was still based on class names, which reduced the flexibility of the loggers somewhat.

The snippet with the default logger configuration now has the following format

```json
{
  "loggers": {
    "system": {
      "id": "import.logger.factory.monolog"
      "channel name": "logger/system"
      "handlers": [
        {
          "id": "import.logger.factory.handler.stream.wrapper"
          "params": {
            "stream": "system.log"
          },
          "formatter": {
            "id": "import.logger.factory.formatter.line"
            "params": {
              "format": "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
              "date format": "Y-m-d H:i:s."
              "allow-inline-breaks": true,
              "ignore-empty-context-and-extra": true
            }
          }
        }
      ],
      "processors": [
        {
          "id": "import.logger.processor.serial"
        },
        {
          "id": "import.logger.processor.memory.peak.usage"
        }
      ]
    }
  }
}
```

## Configuration

With version 3.8.0, M2IF increasingly focuses on a declarative approach. For us, declarative means that almost everything can be adapted to requirements purely via configuration. M2IF can be configured in two different ways. On the one hand, the creation of the objects that ultimately execute the business logic is controlled by a so-called DI Container, which is based on Symfony and can be controlled via XML files. On the other hand, the integrated workflow engine can be used to define the order in which the objects configured via the DI Container are to be executed. If at all, in most cases it is sufficient to adapt the workflow engine. It should almost never be necessary to adjust the DI configuration. With this approach and a very granular division of the business logic within the source code (following the Separation of Concerns pattern), complex and time-consuming adjustments to the source code can be avoided or at least reduced to a minimum.

In previous versions, the Workflow Engine was configured at entity level. For example, there were separate, self-contained configuration files for categories, products, etc., which could either be specified when the application was called via the CLI or whose path to the standard files resulted automatically from the current directory context. In addition to the possibility to overwrite the most common settings via CLI parameters, version 3.8.0 allows not only to overwrite the complete configuration file, but also parts of the configuration if required. For this purpose, it is sufficient to explicitly overwrite the desired configuration values within the project by creating so-called snippets, which leads to a reduction of the configuration effort.

Due to this change, the configuration that was used in previous versions must be modified before updating to 3.8.0.

### Migration Path 3.7.x > 3.8.0

In general we recommend to start with a completely empty configuration when updating to version 3.8.0 and to migrate only those parts of the configuration that are absolutely necessary, because

* they are own extensions which have to be registered as `extension-libraries` or as `additional-vendor-dirs` configuration
* the operation was adapted to the respective project requirements and no longer meets the standard
* it is an mail configuration for the exception logger or other specific adjustments for logging

Everything that is not necessary should not be mapped via snippets. With every adaptation of the standard, the effort increases, e.g. by checking whether something has changed in the standard and whether the configuration must be transferred to your snippets.

#### Snippets

As mentioned before, version 3.8.0 split the configuration from one large file into many small files, the so-called snippets. When running the CLI application, the etc directories of the M2IF libraries are searched for these snippets and merged into one large file that is only stored in memory. Based on the execution context, it is determined whether which Magento edition/version is involved and which entity type is to be imported. Based on this, the corresponding operations are then loaded and executed.

Since version 3.8.0, customization can be done using custom snippets that overwrite the default configuration. Either one large or many small snippets can be stored in a freely configurable directory. By default the directory `<magento-install-dir>/app/etc` is searched for snippets. If the corresponding JSON files are found, it tries to merge the content into the default configuration. Since this is generally a large associative array, the values of the array can be overwritten. If the key is a numeric value that is not already included in the default configuration, the values are appended. For example, to overwrite the `header-mappings` for the product import it is sufficient if the snippet has the following structure

```json
{
  "header-mappings": {
    "catalog_product": {
        "source-column-name": "target-column-name"
    }
  } 
}  
```

#### Shortcuts

In version 3.7.0 operations were used to control the import process as far as possible. So within one operation almost always several plugins with at least one subject and several observers were combined. With version 3.8.0 it was tried to separate these monolithic operations and make them more independent from each other. Thus, almost every operation contains only one plugin with one subject and several observers. Each operation has, whenever possible, only one purpose, e.g. to move the files into a temporary directory. Using the so-called shortcuts, these operations can now be combined and their execution sequence can be determined. This procedure massively increases the reusability of operations and underlines the declarative approach.

Using the shortcuts, the standard functionality can be easily adapted to the requirements of your own project. You can either overwrite the existing shortcuts or simply define your own shortcuts that contain only the desired operations and execute them in the appropriate order. Since version 3.8.0, the automatic creation of option values and categories as well as the validation of the data via the shortcuts has been activated by default, the following snippet, for example, can be used to reconfigure the previous functionality from version 3.7.0.

```json
{
  "shortcuts." {
    "ce": {
      "catalog_product": {
        "my-simple-add-update." [
          "general/general/global-data"
          "general/general/move-files",
          "ce/catalog_product/add-update"
          "ce/catalog_product/add-update.variants"
          "ce/catalog_product/add-update.bundles"
          "ce/catalog_product/add-update.links"
          "ce/catalog_product/add-update.grouped"
          "ce/catalog_product/add-update.media"
          "general/catalog_product/add-update.msi"
          "general/catalog_product/add-update.url-rewrites"
        ]
      }
  }
}
```

To execute the operations defined in the shortcut, it can be called via the CLI with

```sh
bin/import-simple import:products my-simple-add-update
```

#### Operations

In version 3.7.x no context was necessary for the configuration of the operations. With 3.8.0 it is necessary to define for which Magento edition and which entity type an operation can be used. If you want to create a new operation or overwrite an existing one, the level following the "operations" level must contain the abbreviation for the edition, namely ce, ee or general (if the operation can be used for both editions) as key. The key for the next level then contains the value for the product type or again general, if the operation can be used for all product types.

The old format that has been used to define operations

```json
{
  "operations": {
    "add-update:" {... }
  }
}
```

thus be in version 3.8.0

```json
{
  "operations": {
    "ce": {
      "catalog_product": {
        "add-update:" {... }
      }
    }
  }
}
```

#### Parameter

As described in Issue https://github.com/techdivision/import/issues/112, this was originally relatively complex for the configuration of parameters such as subjects. To make the configuration of the parameters a bit more intuitive, the format has been slightly changed. Starting with version 3.8.0 the enclosing array can be omitted and the parameters can be specified directly as key-value pairs. From 

```json
{
  "params": [
    {
      "param1": "test"
      "param2": "so test"
    }
  ]
}
```

will thus

```json
{ 
  "params":{
      "param1": "test" 
      "param2": "so test" 
  }
}
```

#### Header Mappings

Header mappings are part of M2IF since version 1.0. Header mappings can be used to map non-standard columns in CSV files to the standard column names without programming effort. With version 3.8.0, the header mappings, as well as all other configuration options, have been swapped into a separate snippet. In order to make your own mappings within your own project, a corresponding snippet must be created. The format changed from

```json
{  
  "header-mappings" : [ 
    {
      "my_sku_column": "sku" 
      "my_qty_column": "qty"
    }
  ]
}
```

to 

```json
{
  "header-mappings": {
    "catalog_product": {
      "my-column-name": "default-column-name"
    }
  }
}
```

Since there is no entity type specific configuration file, a key, in this case `catalog_product`, must be used to define the entity type matching the mapping.

### New Configuration Options

In addition to the configuration options already existing in version 3.7.0, which can be migrated as described above, version 3.8.0 also introduced new configuration options.

#### Table Prefix

With the new `--db-table-prefix` parameter, it is possible to pass the table prefix that has been used when the Magento setup created the database. The tabble prefix can either be passed as parameter specified in the configuration at the specific database connection like

```json
{
  "databases": [
    {
      "id": "live",
      "default" : true,
      "pdo-dsn": "mysql:host=127.0.0.1;dbname=magento2;charset=utf8",
      "username": "magento",
      "password": "foOQNGEcKS8mZmVH",
      "table-prefix": "test_"
    }
  ]
}
```

whereas the commandline parameter will be preferred if both has been specified.

#### Default Column Values

Sometimes it can be helpful if default values can be stored for columns not contained in the CSV file, e.g. if there are only Simple Products and the column product_type would be superfluous. From version 3.8.0 on, a snippet can be used to define default values for columns not contained in the CSV file. The snippet must have the following format

```json
{
  "default-values": {
    "catalog_product": {
      "my-column": "my-column-value"
    }
  }
}
```

#### Finder Mappings

In order to be able to implement the repositers more generically, so-called Finder mappings were introduced with version 3.8.0. If the repository implementation supports Finder, you can use the Finder mappings to define whether the repository should use a yield-based or a simple Finder with a foreach loop. Since this is a relatively complex topic, we only want to mention this option for the sake of completeness. If in doubt, we recommend implementing a standard repository as in version 3.7.x.

The Finder mapping has also been swapped out to a snippet with the following content and can be overwritten and extended if necessary 

```json
{
  "finder-mappings": {
    "TechDivision\\Import\\Product\\Utils\\SqlStatementKeys::PRODUCT_DATETIMES": "import.repository.finder.factory.yielded",
    "TechDivision\\Import\\Product\\Utils\\SqlStatementKeys::PRODUCT_DECIMALS": "import.repository.finder.factory.yielded",
    "TechDivision\\Import\\Product\\Utils\\SqlStatementKeys::PRODUCT_INTS": "import.repository.finder.factory.yielded",
    "TechDivision\\Import\\Product\\Utils\\SqlStatementKeys::PRODUCT_TEXTS": "import.repository.finder.factory.yielded",
    "TechDivision\\Import\\Product\\Utils\\SqlStatementKeys::PRODUCT_VARCHARS": "import.repository.finder.factory.yielded",
    "TechDivision\\Import\\Product\\Utils\\SqlStatementKeys::PRODUCTS": "import.repository.finder.factory.yielded",
    "TechDivision\\Import\\Product\\Utils\\SqlStatementKeys::PRODUCT": "import.repository.finder.factory.unique",
    "TechDivision\\Import\\Product\\Utils\\SqlStatementKeys::PRODUCT_DATETIMES_BY_PK_AND_STORE_ID": "import.repository.finder.factory.yielded",
    "TechDivision\\Import\\Product\\Utils\\SqlStatementKeys::PRODUCT_DECIMALS_BY_PK_AND_STORE_ID": "import.repository.finder.factory.yielded",
    "TechDivision\\Import\\Product\\Utils\\SqlStatementKeys::PRODUCT_INTS_BY_PK_AND_STORE_ID": "import.repository.finder.factory.yielded",
    "TechDivision\\Import\\Product\\Utils\\SqlStatementKeys::PRODUCT_TEXTS_BY_PK_AND_STORE_ID": "import.repository.finder.factory.yielded",
    "TechDivision\\Import\\Product\\Utils\\SqlStatementKeys::PRODUCT_VARCHARS_BY_PK_AND_STORE_ID": "import.repository.finder.factory.yielded",
    "TechDivision\\Import\\Product\\Utils\\SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID": "import.repository.finder.factory.yielded",
    "TechDivision\\Import\\Product\\Utils\\SqlStatementKeys::PRODUCT_VARCHAR_BY_ATTRIBUTE_CODE_AND_ENTITY_TYPE_ID_AND_STORE_ID_AND_VALUE" : "import.repository.finder.factory.unique"
  } 
}
```
# Version 4.2.1

* Min. PHP Version set to 7.3
* Support PhpUnit 8.0
* Please take a look at [UPGRADE-4.0.0](UPGRADE-4.0.0.md)

## Features

* none


## Bugfixes

* import:
  * Fix warning on validation.json output when `--clear-artefacts=true` (default behavior) is used 

* import-product: 
  * Fix stock status in legacy mode since Magento 2.4.4
  * Extend Interface `ProductBunchProcessorInterface` with method `persistStockItemStatus()`

* import-customer-address: 
  * Override address with existing customer and increment_id

# Version 4.2.0

* Min. PHP Version set to 7.3
* Support PhpUnit 8.0
* Please take a look at [UPGRADE-4.0.0](UPGRADE-4.0.0.md)

## Features

* import:
  * Configurable default parameters "delimiter", "enclosure" and "escape" for the CSV reader
    * define in configuration.json
```
  "delimiter": ",",
  "enclosure": "\"",
  "escape": "\u0000"
```
  * 'escape' is set to `null` by default to be backwward compatible with PHP 7.3
* import-customer:
  * new column `is_active`for customer import
* import-customer-address:
  * new column `is_active`for address import. Over customer import the column named `_address_is_active`.


## Bugfixes
* import-product: 
  * Fix has_options and required_options cleanup on product update
  * Fix header-mapping.json `"allow_backorders": "backorders"`
  * Fix set \Exception() parameter Compatible with php8.1
* import-dbal-collection:  
  * Fix API call with invalid URL
* import-cli:  
  * Fix \Exception() parameter compatibility with php8.1
* import:
  * Define default configuration for CSV reader with Unicode `null` value for `escaper` to avoid crash on json_encoded data in CSV Columns
* import-configuration-jms:
  * Remove default definition from `CsvTrait` to set from configuration.json.
  * see techdivison/import/CHANGELOG.md for more information
* import-customer:
  * Use strict mode handling in customer import
  * remove created_at on update
  * Format DOB without time
  * Clear columns on update to update only fields that not null. Use `__EMPTY__VALUE__` instead
* import-customer-address:
  * fix crash on not existing configuration for `customer/address/telephone_show`
  * Clear columns on update to update only fields that not null. Use `__EMPTY__VALUE__` instead

# Version 4.1.3 

* Min. PHP Version set to 7.3
* Support PhpUnit 8.0
* Please take a look at [UPGRADE-4.0.0](UPGRADE-4.0.0.md)

## Features

* Add #PAC-353 new feature to get magento configuration from api in Pacemaker Enterprise
* Add #PAC-215 option `config-output` as default false to report all configuration json files in logs

## Bugfixes
* import
  * Fix PHP8.1 crash on null parameter instead array
* import-customer: Pac-622: Bugfix- format invalid dob value
* import-customer-address:
  * Fix bugs:
    * PAC-619: Customer Address Import - Error if customer not found
    * PAC-613: Start Customer Address Import only if address information is included
    * PAC-621: Allow mandatory fields with Empty Value if not required

# Version 4.1.2

* Min. PHP Version set to 7.3
* Support PhpUnit 8.0
* Please take a look at [UPGRADE-4.0.0](UPGRADE-4.0.0.md)

## Features

* none

## Bugfixes
* import-product (hotfix)
  * Fix crash on Strict Mode definition in FileUploadObserver

# Version 4.1.1

* Min. PHP Version set to 7.3
* Support PhpUnit 8.0
* Please take a look at [UPGRADE-4.0.0](UPGRADE-4.0.0.md)

## Features

* none

## Bugfixes
* import
  * Missing "copy" function in FilesystemTrait
* import-attribute
  * Fix import swatch attribute with `additional_data`
  * Fix import swatch attribute, calling the wrong sql-statements for the processor config
* import-product
  * Implement Strict Mode in FileUploadObserver on missing file
  
# Version 4.1.0

* Min. PHP Version set to 7.3
* Support PhpUnit 8.0
* Please take a look at [UPGRADE-4.0.0](UPGRADE-4.0.0.md)

## Features

* import-customer-address: `_address_increment_id` for customer import CSV to prepend always create addresses

## Bugfixes

* import-customer 
  * Default etc/configuration/black-list.json
* import-customer-address
  * Don't update default addresses when not marked as default. Prevent resetting default address
* import-product
  * Product import can have a tax_class_name `None`
  * Fix error url-rewrite-import for an existing product on another store.
  * Fix category assignments, if column categories does not exit:
    * If categories column not exit and clean-up-category-product-relations is true, the assignment is not deleted. 
* import-product-variant:
    * Fix error for php8.1: The Exception code must not be empty

# Version 4.0.0

* Min. PHP Version set to 7.3
* Support PhpUnit 8.0
* Please take a look at [UPGRADE-4.0.0](UPGRADE-4.0.0.md)

# Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction

## Bugfixes

* Fix php 7.4 notice
* Fixed #PAC-339: Permission issue when updating import-cli-simple via composer
* import-product-msi: Providing decimal values for "qty" results in inconsistencies
* import-product-media: Clean up gallery images on multi store import
* import-category: getUniqueIdentifier returned category path
* import-product-url-rewrite: Rewrites for the store views are updated unintentionally
* import-product-tier-price:
    * Tier price Import with non-existent website
    * Import of tier price for import-product-scenario
* Fixes #PAC-348: Prevent processing global attributes in all stores
* Prepare generic workflow and defined deprecated interface PrimaryKeyUtilInterface
* PAC-362: Call to a member function getSystemLogger() on null
* import-attribute-set: Fix missing import attribute_group_sort_order
* import: 
  * Clear properties after success unlock
  * Create validation callback for sku relations for grouped, configurables and bundles
  * Fix counter from 'skippedRow'
  * Fix import with UTF-8 BOM and quoted headlines
* import-app-simple: * Fix write error message before shutdown
* import-product-url-rewrite: Duplicate error for url rewrites on replace shortcut
* Fix website relation clean up on Magento Commerce
* Magento 2.4.3 compatibility
* Remove League\Flysystem and set deprecated exception for
  * LeagueFilesystemAdapter
  * LeagueFilesystemAdapterFactory
* Third party module ramsey/uuid can use version ~4.1.0
* import-category: Update multistore categories only on new category from default line
* import-attribute-set: Fix fallback for attribute_group_sort_order when empty
* import-attribute: Missing specification of attribute_set_name and attribute_set_group leads to PHP warning
* import-configuration-jms: Debug log compatibility with jms/serializer >= 2.0
* EavAttributeOptionValueLoader::load use SubjectInterface instead ParamsConfigurationInterface for EntityTypeCode mapping

## Features

* import-product: 
  * Add missing validation for min_qty, min_sale_qty, max_sale_qty, notify_stock_qty, qty_increments, weight fields
  * Optimize ProductValueLoader with bunches of import files
* import-product-ee: Add missing validation for min_qty, min_sale_qty, max_sale_qty, notify_stock_qty, qty_increments, weight fields
* import: 
  * Note attributes entity type for customer attribute
  * Integration strict mode
  * New Returncodes for Missing File (4) and warnings on Strict Mode = false (13)
* import-converter-product-attribute: Implement importing customer attributes
* import-customer: Import customer attributes
* import: Prepare optimize performance cache
* import-product: 
  * Define new finder mappings for prepare optimize performance cache
  * Integration strict mode
* import-product-url-rewrite: Prepare optimize performance cache
* import-product-media: Prepare optimize performance cache
* import-product-media-ee: Prepare optimize performance cache
* import-configuration-jms: 
  * Add #PAC-294: integration of the strict mode
* import-cli:
  * Add `--strict-type` commandline parameter
  * Add functionality to also parse JSON configuration in extension libraries
  * Add #PAC-89: Add debug email command + DebugSendPlugin
  * Add #PAC-57: "--empty-attribute-value-constant" for deleting dedicated attribute values via import by setting a configurable value
  * Add #PAC-349: Support for JMS serializer v2.0 + v3.0
  * Implement import-converter-product-attribute for importing customer attributes
  * Add #PAC-486: Add `--log-file` commandline parameter
* import-app-simple: Add catch for MissingFileException and InvalidDataException
* import-attributes: New validator callbacks
  * Relation between fontend_input and backent_type
  * Improve regex for attribute code like Magento allowed
* import-product-bundle: integration of the strict mode

# Version 4.0.0-alpha10

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed issue with method `UrlKeyUtil::makeUnique()` when import products without category
* Fixed #PAC-347: Parsing of version specific configuration does not work when using PHAR version

## Features

* Add #PAC-349: Support for JMS serializer v2.0 + v3.0
* Migrated PHP Unit integration testsuite to Github Actions

# Version 4.0.0-alpha9

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import-product#165

## Features

* Add techdivision/import#203

# Version 4.0.0-alpha8

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import#209
* Fixed techdivision/import-cli-simple#258
* Fixed #PAC-318: Serialization exception when using a custom database configuration
* Fixed #PAC-320: Children of deleted category are not removed
* Fixed #PAC-335: Properly support slashes in category names

## Features

* None

# Version 4.0.0-alpha7

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed issue with uncaught exception when trying to delete categories 

## Features

* None

# Version 4.0.0-alpha6

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import-category#62
* Fixed #PAC-264: PDOException: SQLSTATE[23000] : Integrity constraint violation: 1062 Duplicate entry xxx.html-0 for key 'URL_REWRITE_REQUEST_PATH_STORE_ID
* Fixed #PAC-265: Also use url_path when generate unique url_key for categories

## Features

* Add #PAC-326: Cross-entity import of URLs (rewrites + redirects)
* Add functionality to clear URL rewrites when categories have been deleted

# Version 4.0.0-alpha5

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Add techdivision/import-product-variant#22
* Add #PAC-252: Only generate category product rewrites if enabled in backend
* Add #PAC-273: Update product 301 URL redirects to forward to active URL rewrite
* Add #PAC-302 + techdivision/import-product-link#27: Functionality to clean-up product links (related, upssell + crosssell) when using `add-update` operation

# Version 4.0.0-alpha4

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed #PAC-212: .OK file filter only supports suffix .csv

## Features

* Add #PAC-227: Dynamic Handling of columns for import of EAV attributes

# Version 4.0.0-alpha3

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import-category#69
* Fixed issue when root category has to be created
* Fixed #PAC-212: .OK file filter only supports suffix .csv
* Fixed #PAC-239: Allow null values for for attribute 'gender' during customer import

## Features

* Add #PAC-89: Add debug email command + DebugSendPlugin

# Version 4.0.0-alpha2

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import-category#68
* Fixed #PAC-243: Fixed issue of attributes that can not be updated

## Features

* Add `"include_in_menu": null` to `etc/configuration/default-values.json`
* Remove stack trace of exception for missing media directories > log a simple debug message instead

# Version 4.0.0-alpha1

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import-category#66
* Fixed #PAC-206: Prevent finder mappings of different libraries to be overwritten

## Features

* Add second log handler to log to console also
* Adjust log messages to log only message with log level `notice` to console
* Remove stack trace of exception for missing media directories > log a simple debug message instead

# Version 3.8.27

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Update symfony dependencies for Magento 2.4 compatibility

# Version 3.8.26

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed invalid member name
* Fixed typo in swift transport sendmail declaration
* Fixed issue in initialization of StreamHandlerFactory
* Fixed techdivision/import-category#64

## Features

* None

# Version 3.8.25

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import-category#65

## Features

* None

# Version 3.8.24

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import-product#156
* Fixed techdivision/import-attribute#52
* Fixed techdivision/import-cli-simple#250
* Fixed #PAC-153: Valdiation of columns for attributes of frontend input type `select` and `multiselect` fails

## Features

* None

# Version 3.8.23

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed #PAC-151: Directive `additional-vendor-dirs` not applied, if working dir is not Magento installation directory

## Features

* None

# Version 3.8.22

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Add techdivision/import#184
* Add techdivision/import-product#155
* Add techdivision/import-attribute#51

# Version 3.8.21

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import#185
* Fixed techdivision/import#186

## Features

* Add #PAC-102: Dedicated CLI command to import videos (professional + enterpries edition)
* Add #PAC-72: Extend dedicated CLI command to delete existing videos (professional + enterprise edition)
* Add #PAC-75: Extend dedicated CLI command to replace existing videos (professional + enterprise edition)

# Version 3.8.20

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed #PAC-130
* Fixed techdivision/import#183

## Features

* None

# Version 3.8.19

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed import-customer-address#18

## Features

* None

# Version 3.8.18

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import#178
* Fixed techdivision/import#182

## Features

* Add #PAC-46
* Add #PAC-47
* Add #PAC-96
* Add default configuration for media + images file dirctory
* Add functionality write a log warning instead throwing an exception if the configured media + images file dirctory are not available

# Version 3.8.17

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Add #PAC-49

# Version 3.8.16

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Add #PAC-48

# Version 3.8.15

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Add #PAC-73

# Version 3.8.14

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import-cli-simple#246 by reverting PR #165

## Features

* None

# Version 3.8.13

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Update console application name
* Add dynamic attribute loader functionality for #PAC-34

# Version 3.8.12

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed #PAC-101
* Fixed techdivision/import-cli#2
* Fixed techdivision/import-cli-simple#242

## Features

* Add #PAC-34
* Add #PAC-52
* Add #PAC-85
* Add techdivision/import#175
* Add techdivision/import-cli-simple#243
* Add techdivision/import-cli-simple#244
* Add techdivision/import-attribute#46 (Possibility to import files for option swatch)
* Add functionality to robo.li build process to invoke PHPCPD, PHPMD and PHPCS when running a build
* Replace old default observer configuration for MSI sources, if inventory_source_items column is missing
* Add functionality to also parse configuration files that'll be available in configured additional vendor directory
* Add functionality to create a default value for column inventory_source_items, if empty (to replace actual default observer)

# Version 3.8.11

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Add entity type code CATALOG_PRODUCT_URL to EntityTypeCodes list
* Add missing mapping EntityTypeCodes::CATALOG_PRODUCT_URL => EntityTypeCodes::CATALOG_PRODUCT to AbstractSubject
* Add missing observer to pre-load entity + row ID for replace operation in delta update mode for techdivision/import-product-link + techdivision/import-product-link-ee

## Features

* Extract the import.configuration.manager DI configuration to techdivision/import-cli to make it overwritable

# Version 3.8.10

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Add compiler implementations for dynamic column handling
* Add functionality for dynamic column handling in customer entity

# Version 3.8.9

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed significant performance issue by removing unnecessary and slow reference clean-up when removing an item from cache

## Features

* None

# Version 3.8.8

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fix associated_skus without qty
* Fixed techdivision/import-product#149
* Fixed techdivision/import-cli-simple#238
* Fixed issue with delta import when SKUs of simples, that are related with grouped, are in database but will not be loaded

## Features

* Remove not referenced legacy code
* Extract dev autoloading (where possible)
* Clear cache references also, when an cache item has been removed
* Add command to import URL rewrites as well as necessary class constants
* Optimize additional attribute destruction to avoid unnecessary warnings
* Allow import of product relations in debug mode whether or not the related simple product exists
* Remove functionality to make given URL keys unique (we assume, that given URL keys HAVE to be provided in a unique manner)

# Version 3.8.7

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Remove mandatory autoload.php from additional vendor directory

# Version 3.8.6

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import-product-ee#57
* Fixed techdivision/import-configuration-jms#33

## Features

* None

# Version 3.8.5

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed issue when creating root categories

## Features

* None

# Version 3.8.4

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed typo in Symfony DI configuration of library techdivision/import-customer-address

## Features

* Compatibility with Magento CE/EE version 2.3.4

# Version 3.8.3

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed incompatibility with several PHP versions
* Fixed invalid product URL rewrite metadata that results in dead product URL rewrites

## Features

* None

# Version 3.8.2

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed issue with missing bundle options

## Features

* None

# Version 3.8.1

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import-attribute#47

## Features

* None

# Version 3.8.0

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import-cli-simple#220
* Fixed techdivision/import-cli-simple#221
* Fixed techdivision/import-cli-simple#222
* Fixed techdivision/import-cli-simple#224
* Fixed techdivision/import-cli-simple#229
* Fixed techdivision/import-cli-simple#233
* Fixed techdivision/import-cli-simple#234
* Add missing event triggers to AbstractSubject
* Use website code instead of ID to relate customer with website

## Features

* Switch to PHPUnit 6.5
* Remove compatibility with PHP 5.6 and 7.1
* Add techdivision/import#112
* Add techdivision/import#146
* Add techdivision/import#162
* Add techdivision/import#163
* Add techdivision/import-product#146
  * Add techdivision/import-cli-simple#216
* Add techdivision/import-configuration-jms#25
* Add converter libraries to community version
* Add new events that will be triggered before and after the header has been initialized
* Add functionality to render recommendations for performce relevant MySQL configurations
* Remove deprecated classes and methods from all dependencies

# Version 3.7.4

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Change artefact prefix for category URL rewrites from url-rewrite to category-url-rewrite to avoid conflicts with product import

## Features

* None

# Version 3.7.3

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import-cli#1
* Fixed techdivision/import-category-ee#34

## Features

* Add cache warmer for product varchar values to improve performance on CSV files with missing url_key value

# Version 3.7.2

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import#156
* Make SQL for loading EAV attribute option values case sensitive

## Features

* Extend additional attribute serializer functionality
* Make SQL for loading EAV attribute option values case sensitive
* Extend observer and callback instanciation with the possibility to use a factory

# Version 3.7.1

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed issue when formatting float/decimals on a localized system

## Features

* None

# Version 3.7.0

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed issue when formatting decimals greater than 999

## Features

* Add support for delta import with relations of bundles, gropued, variants as well as related, cross- and upssell products that a part of the actual import

# Version 3.6.3

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed invalid comparision for Magento Edition + Version in SimpleConfigurationLoader

## Features

* None

# Version 3.6.2

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Add check if version specific configuration file really exists or not to

## Features

* None

# Version 3.6.1

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Extend clean-up-empty-columns configuration by price specifc columns

# Version 3.6.0

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Extract sample data to its own repository techdivision/import-sample-data
* Update composer dependencies to be compatible with Magento 2.2.x as well as 2.3.x
* Replace member variable with default library names with value from DI configuration

# Version 3.5.2

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Update composer dependencies to optimize compatibility for Magento 2.3.2

# Version 3.5.1

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Remove unnecessary attribute set observer from price and inventory import Symfony DI configuration

# Version 3.5.0

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import#147
* Fixed techdivision/import-product-media#36
* Fixed missing method MissingOptionValuesPlugin::isDebugMode()
* Fixed invalid cache initialization on missing default configuration
* Fixed issue with invalid return value of LocalCacheAdapter::isCached($key) method
* Fixed issue when renaming images with the same filename withing one import process
* Fixed issue in SimpleFileResolver that causes an exception when old CSV files without a .ok file are available in the source directory

## Features

* Add techdivision/import-product-media#35
* Add functionality to override DI aliases in the configuration file
* Optimize SQL to load image types in the optimal order for further processing
* Impovements to significantly lower the memory footprint in production mode ~60 %
* Add LocalCacheAdapter implementation for maximum performance and declare it as the default one
* Refactor cache integration for PSR-6 compatibility, optimize for multiprocess and -threaed environments and allow configuration via configuration file

# Version 3.4.1

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed issue with MySQL mode ONLY_FULL_GROUP_BY which is activated by default for MySQL > 5.7.4

## Features

* None

# Version 3.4.0

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import#147
* Fixed techdivision/import-product-tier-price#6
* Fixed techdivision/import-product-tier-price#7

## Features

* Add additional events on plugin and subject level
* Refactor Cache Integration for PSR-6 compliance

# Version 3.3.1

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import-app-simple#24

## Features

* None

# Version 3.3.0

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import-product#136

## Features

* Added techdivision/import-product#132
* Added techdivision/import-cli-simple#201
* Extract registry initialization to separate listeners
* Add --serial commandline option to pass external serial
* Add composite listeners to reduce configuration complexity

# Version 3.2.1

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed invalid attribute group name in attribute example CSV files

## Features

* None

# Version 3.2.0

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Added techdivision/import-cli-simple#198
* Added functionality to copy parent attribute set attribute groups + attribute relations

# Version 3.1.0

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed techdivision/import-product-variant#21
* Fixed techdivision/import-category#50
* Fixed techdivision/import-category#51
* Fixed techdivision/import-category#52
* Fixed techdivision/import-category#55
* Fixed techdivision/import-category#56

## Features

* Add attribute set/gruop import functionality
* Add customer + customer address import functionality
* Update doctrine + JMS serializer dependencies
* Add Converter implementations to convert numbers and dates to the expected Magento 2 format
* Add Serializer to serializer/unserialize field values, e. g. from/to additional_attribute column
* Update configuration initialization to allow passing additional parameters on the command line that'll be merged with the configuration file
* Migrate dynamic creation of EAV attribute option values/swatches form techdivision/import-attribute 2.x version

# Version 3.0.1

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Replace serialize/unserialie with json_encode/json_decode methods in URL rewrite functionality

## Features

* Switch to techdivision/import-product-url-rewrite version 2.0.1

# Version 3.0.0

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Compatibility for Magento 2.3.x

# Version 2.0.0

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Compatibility for Magento 2.2.x

# Version 1.1.1

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Add missing artefact initialization in ExportableTrait::newArtefact() method
* Fixed invalid .inProgress file deletion in AbstractSubject::import() method
* Fixed invalid JMS (un-)serialization name for Subject::$createImportedFile property

## Features

* None

# Version 1.1.0

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fixed issue that creates column original_data also if no original data is available

## Features

* Add functionality to NOT create .imported flagfile based on configuration value to enable multiple subjects processing the same CSV file

# Version 1.0.3

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Inject NULL instead of dummy delete action when creating ProductWebsiteAction instance

## Features

* None

# Version 1.0.2

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* Fix composer dependency for ramsey lib and added compatibility for monolog and Magento 2.2

## Features

* None

# Version 1.0.1

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Removed deprecated rhumsaa library and added Magento 2.2 compatible version to composer.json

# Version 1.0.0

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* Move PHPUnit test from tests of all libraries to tests/unit folder for integration test compatibility reasons

# Version 1.0.0-beta71

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* [techdivision/import] Add missing interface for UrlRewriteAction
* [techdivision/import-product] Fixed invalid order of method invocation in tearDown() method
* [techdivision/import-attribute] Remove unnecessary methods from AttributeBunchProcessorInterface, Add missing interfaces for actions and repositories + Replace class type hints for AttributeBunchProcessor with interfaces
* [techdivision/import-category]  Add missing interfaces for actions and repositories + Replace class type hints for CategoryBunchProcessor with interfaces
* [techdivision/import-category-ee] Add missing interfaces for actions and repositories + Replace class type hints for EeCategoryBunchProcessor with interfaces
* [techdivision/import-product] Add missing interfaces for actions and repositories + Replace class type hints for ProductBunchProcessor with interfaces
* [techdivision/import-product-url-rewrite] Add missing interfaces for actions and repositories + Replace class type hints for ProductUrlRewriteProcessor with interfaces
* [techdivision/import-product-link] Add missing interfaces for actions and repositories + Replace class type hints for ProductLinkProcessor with interfaces
* [techdivision/import-product-media] Add missing interfaces for actions and repositories + Replace class type hints for ProductMediaProcessor with interfaces
* [techdivision/import-product-bundle] Add missing interfaces for actions and repositories + Replace class type hints for ProductBundleProcessor with interfaces
* [techdivision/import-product-variant] Add missing interfaces for actions and repositories + Replace class type hints for ProductVariantProcessor with interfaces
* [techdivision/import-product-ee] Add missing interfaces for actions and repositories + Replace class type hints for EeProductBunchProcessor with interfaces
* [techdivision/import-product-media-ee] Add missing interfaces for actions and repositories * Replace class type hints for EeProductMediaProcessor with interfaces

# Version 1.0.0-beta70

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* [techdivision/import-product] Fixed invalid order of method invocation in tearDown() method
* [techdivision/import-product] Fixed invalid order of method invocation in tearDown() method

## Features

* None

# Version 1.0.0-beta69

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* [techdivision/import-product] Remove stock status create/update functionality because Magento 2 indexer takes care about that

## Features

* [techdivision/import] Refactored Add --single-transaction parameter to configuration + Refactored Add configurable events to AbstractSubject to allow simple extend of artefact and row handling
* [techdivision/import-app-simple] Add events before and after the single transaction start's
* [techdivision/import-attribute] Configure DI to pass event emitter to subjects constructor
* [techdivision/import-category] Configure DI to pass event emitter to subjects constructor
* [techdivision/import-category-ee] Configure DI to pass event emitter to subjects constructor
* [techdivision/import-configuration-jms] Add configuration for single transaction flag
* [techdivision/import-product] Replace type hints for actions in product bunch processor with interfaces + Use interfaces instead of classes to inject product attribute actions in bunch processor
* [techdivision/import-product-bundle] Configure DI to pass event emitter to subjects constructor
* [techdivision/import-product-bundle-ee] Configure DI to pass event emitter to subjects constructor
* [techdivision/import-product-ee] Configure DI to pass event emitter to subjects constructor + Replace type hints for actions in product bunch processor with interfaces
* [techdivision/import-product-link] Configure DI to pass event emitter to subjects constructor
* [techdivision/import-product-link-ee] Configure DI to pass event emitter to subjects constructor
* [techdivision/import-product-media] Configure DI to pass event emitter to subjects constructor
* [techdivision/import-product-media-ee] Configure DI to pass event emitter to subjects constructor
* [techdivision/import-product-url-rewrite] Configure DI to pass event emitter to subjects constructor
* [techdivision/import-variant] Configure DI to pass event emitter to subjects constructor
* [techdivision/import-variant-ee] Configure DI to pass event emitter to subjects constructor

# Version 1.0.0-beta68

## Known Issues

* Up from Magento 2 EE 2.1.10 using the single transaction mode has a massive performance impact (can only be used **WITHOUT** single transaction)

## Bugfixes

* None

## Features

* [techdivision/import] Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements
* [techdivision/import-attribute] Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements
* [techdivision/import-category] Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements
* [techdivision/import-category-ee] Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements
* [techdivision/import-configuration-jms] Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements
* [techdivision/import-ee] Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements
* [techdivision/import-product] Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements
* [techdivision/import-product-bundle] Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements
* [techdivision/import-product-ee] Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements
* [techdivision/import-product-link] Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements
* [techdivision/import-product-link-ee] Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements
* [techdivision/import-product-media] Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements
* [techdivision/import-product-media-ee] Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements
* [techdivision/import-product-url-rewrite] Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements
* [techdivision/import-variant] Refactored DI + switch to new SqlStatementRepositories instead of SqlStatements

# Version 1.0.0-beta67

## Known Issues

* [techdivision/import-product] As products are cached, memory consumption is significantly higher now

## Bugfixes

* [techdivision/import-product] Fixed invalid multiple product update

## Features

* [techdivision/import] Refactor cache warmer functionality for optimized memory management
* [techdivision/import-product] Add product cache warmer functionality for optimized performance
* [techdivision/import-product-ee] Add product cache warmer functionality for optimized performance

# Version 1.0.0-beta66

## Known Issues

* None

## Bugfixes

* [techdivision/import] Update category path handling in order to use store view specific slugs
* [techdivision/import-ee] Update category path handling in order to use store view specific slugs
* [techdivision/import-product] Update category path handling in order to use store view specific slugs
* [techdivision/import-product-url-rewrite] Update category path handling in order to use store view specific slugs

## Features

* None

# Version 1.0.0-beta65

## Known Issues

* None

## Bugfixes

* None

## Features

* [techdivision/import] Update processed file status in AbstractSubject
* [techdivision/import-product] Remove update of processed file status, because of moving it to AbstractSubject
* [techdivision/import-category] Remove unnecessary AbstractAttributeSubject::tearDown() method
* [techdivision/import-attribute] Remove unnecessary AbstractAttributeSubject::tearDown() method

# Version 1.0.0-beta64

## Known Issues

* None

## Bugfixes

* None

## Features

* [techdivision/import] Make image types dynamic and extensible
* [techdivision/import-product] Make image types dynamic and extensible

# Version 1.0.0-beta63

## Known Issues

* None

## Bugfixes

* None

## Features

* [techdivision/import] Add file status to registry when invoking AbstractSubject::tearDown() method
* [techdivision/import] Set serial and filename in MoveFilesSubject::import() method

# Version 1.0.0-beta62

## Known Issues

* None

## Bugfixes

* None

## Features

* [techdivision/import] Add interfaces for observer and callback visitor implementations

# Version 1.0.0-beta61

## Known Issues

* None

## Bugfixes

* None

## Features

* [techdivision/import] Add override parameter to method ExportableTrait::addArtefact() method
* [techdivision/import-attribute] Refactoring to optimize artefact export handling

# Version 1.0.0-beta60

## Known Issues

* None

## Bugfixes

* [techdivision/import-product-url-rewrite] add validation for url rewrite update to prevent that url rewrites are persisted, where target_path and request_path are equal

## Features

* None

# Version 1.0.0-beta59

## Known Issues

* None

## Bugfixes

* None

## Features

* [techdivision/import-attribute] Fix issue for updating attribute option swatch values

# Version 1.0.0-beta58

## Known Issues

* None

## Bugfixes

* [techdivision/import-product-url-rewrite] Fixes bug, where url rewrite update is processed with an exception. This happens, when no rows with an active store_view exist for a given sku.

## Features

* None

# Version 1.0.0-beta57

## Known Issues

* None

## Bugfixes

* [techdivision/import-attribute] Fixes attribute import issue for option translations

## Features

* None

# Version 1.0.0-beta56

## Known Issues

* None

## Bugfixes

* [techdivision/import-product] Fixed error in SQL statement preparation for stock status updates

## Features

* [techdivision/import] Wrap \PDOExceptions in AbstractBaseProcessor for more detailed database releated exceptions
* [techdivision/import-product-ee] Remove unncessary SQL statements for stock status create/update operation

# Version 1.0.0-beta55

## Known Issues

* None

## Bugfixes

* [techdivision/import] Add classes + interfaces for the dynamic attribute loader implementation
* [techdivision/import-product] Add fix to ignore missing columns or columns with empty values when persisting inventory data

## Features

* None

# Version 1.0.0-beta54

## Known Issues

* None

## Bugfixes

* [techdivision/import] Fixed invalid interruption of observer chain when skipping row
* [techdivision/import-product-ee] Fixed invalid Magento Edition in etc/techdivision-import-price.json
* [techdivision/import-product] Skip row instead of continue processing (in debug mode) when product with SKU can not be loaded in LastEntityIdObserver

## Features

* None

# Version 1.0.0-beta53

## Known Issues

* None

## Bugfixes

* None

## Features

* Minor pateches in some libraries

# Version 1.0.0-beta52

## Known Issues

* None

## Bugfixes

* Fixed invalid category URL rewrite functionaliy in a multi-store environment

## Features

* None

# Version 1.0.0-beta51

## Known Issues

* None

## Bugfixes

* None

## Features

* Refactor log messages for clean-up category + media gallery

# Version 1.0.0-beta50

## Known Issues

* None

## Bugfixes

* Fixed invalid handling when URL rewrites have been deleted (e. g. because category product relation has been removed) and re-created

## Features

* Add functionality to remove old image + image tags when products are updated
* Add configurable functionality to remove entity attributes with empty values in column names
* Add configurable functionality to remove old URL rewrites that not longer exists
* Add configurable functionality to remove category product relations that not longer exists in the CSV file

# Version 1.0.0-beta49

## Known Issues

* None

## Bugfixes

* Add cache warming functionality

## Features

* None

# Version 1.0.0-beta48

## Known Issues

* None

## Bugfixes

* Fixed #117 - [Attribute Import fails when invoking add-update after replace operation](https://github.com/techdivision/import-cli-simple/issues/117)

## Features

* None

# Version 1.0.0-beta47

## Known Issues

* Issue [#117](https://github.com/techdivision/import-cli-simple/issues/117) - Attribute Import fails when invoking add-update after replace operation]

## Bugfixes

* Add missing option configuration system-name to AbstractSimpleCommand class

## Features

* None

# Version 1.0.0-beta46

## Known Issues

* Issue [#117](https://github.com/techdivision/import-cli-simple/issues/117) - Attribute Import fails when invoking add-update after replace operation]

## Bugfixes

* None

## Features

* Completely refactored product URL rewrite handling

# Version 1.0.0-beta45

## Known Issues

* URL rewrites for default store view are NOT generated

## Bugfixes

* None

## Features

* Add new library techdivision/import-product-url-rewrite that'll handle product URL rewrites in future releases

# Version 1.0.0-beta44

## Known Issues

* URL rewrites for default store view are NOT generated

## Bugfixes

* None

## Features

* Add basic integration testsuite

# Version 1.0.0-beta43

## Known Issues

* URL rewrites for default store view are NOT generated

## Bugfixes

* Fixed invalid URL rewrite generation in mutli-store environments

## Features

* None

# Version 1.0.0-beta42

## Known Issues

* None

## Bugfixes

* Fixed issue when invoking storeViewHasBeenProcessed($pk, $storeViewCode) method always returns false

## Features

* None

# Version 1.0.0-beta41

## Known Issues

* None

## Bugfixes

* Fixed issue with product import `add-update` operation, that toggles between none and `-1` .html suffix for URL rewrites

## Features

* None

# Version 1.0.0-beta40

## Known Issues

* Product import `add-update` operation toggles between none and `-1` .html suffix for URL rewrites

## Bugfixes

* Fixed issue with missing URL rewrites for additional store views in a multi website setup

## Features

* None

# Version 1.0.0-beta39

## Known Issues

* Product import `add-update` operation toggles between none and `-1` .html suffix for URL rewrites

## Bugfixes

* None

## Features

* Refactoring for better URL rewrite + attribute handling

# Version 1.0.0-beta38

## Bugfixes

* Fixed issue in URL rewrite handling when importing products

## Features

* None

# Version 1.0.0-beta37

## Bugfixes

* None

## Features

* Insert/Update values in catalog_eav_attribute table dynamically

# Version 1.0.0-beta36

## Bugfixes

* Fixed issue techdivision/import-product [#57](https://github.com/techdivision/import-product/issues/75)

## Features

* Make image types + header mappings configurable
* Add new commands import:products:price + import:products:inventory for updating product price + inventory

# Version 1.0.0-beta35

## Bugfixes

* Fixed critical issue because of not mapped column names when try to load a column's value

## Features

* Refactored filesystem handling

# Version 1.0.0-beta34

## Bugfixes

* None

## Features

* Switch to latest library versions

# Version 1.0.0-beta33

## Bugfixes

* Fixed invalid path generation when updating more than two times
* Merge constructor params for logger initialization by name instead of using order

## Features

* None

# Version 1.0.0-beta32

## Bugfixes

* None

## Features

* Switch to latest libraries
* Invoke library PHPUnit tests on build task

# Version 1.0.0-beta31

## Bugfixes

* Bugfix in techdivision/import-product package

## Features

* None

# Version 1.0.0-beta30

## Bugfixes

* Bugfix in techdivision/import package
* Fix issue which caused ID sorted category paths

## Features

* None

# Version 1.0.0-beta29

## Bugfixes

* Update attribute import package
* Fix issue which caused, that all attribute settings were overriten by default values

## Features

* None

# Version 1.0.0-beta28

## Bugfixes

* None

## Features

* Refactor to optimize DI integration

# Version 1.0.0-beta27

## Bugfixes

* None

## Features

* Add command to create a dummy configuration file
* Add configurable CSV import + export adapters
* Update all libraries because of switch to new plugin + subject factory implementations

# Version 1.0.0-beta26

## Bugfixes

* None

## Features

* Update techdivions/import-attribute package
* Update techdivions/import package

# Version 1.0.0-beta25

## Bugfixes

* None

## Features

* Update techdivion/import package

# Version 1.0.0-beta24

## Bugfixes

* None

## Features

* Update import-product package

# Version 1.0.0-beta23

## Bugfixes

* None

## Features

* Update import-attribute package

# Version 1.0.0-beta22

## Bugfixes

* None

## Features

* Update import-attribute package version

# Version 1.0.0-beta21

## Bugfixes

* None

## Features

* Refactor ConfigurationLoader, add SimpleConfigurationLoader
* Refactor command classes for usage with SimpleConfigurationLoader
* Add LibraryLoader class to load external libraries found in configuration
* Switch to latest library version for better DI support

# Version 1.0.0-beta20

## Bugfixes

* None

## Features

* Fixed issue when updating attributes and catalog_eav_attribute table with custom fields
* Add commandline option to enable/disable artefact archiving functionality

# Version 1.0.0-beta19

## Bugfixes

* None

## Features

* Switch to latests import/attribute version
* Update example attribute CSV file

# Version 1.0.0-beta18

## Bugfixes

* None

## Features

* Move JMS configuration loading functionality from AbtractImportCoimmand to ConfigurationLoader class

# Version 1.0.0-beta17

## Bugfixes

* None

## Features

* Refactoring Symfony DI integration

# Version 1.0.0-beta16

## Bugfixes

* Fixed issue with invalid option value processing

## Features

* None

# Version 1.0.0-beta15

## Bugfixes

* Fixed issue when additional attributes contain a comma (,)
* Fixed issue when store view option values are missing

## Features

* None

# Version 1.0.0-beta14

## Bugfixes

* None

## Features

* Add functionality to generate sequential product URL rewrites
* Add --archive-dir option

# Version 1.0.0-beta13

## Bugfixes

* Fixed invalid path to techdivision/import-cli-simple Symfony DI configuration in bootstrap.php

## Features

* None

# Version 1.0.0-beta12

## Bugfixes

* None

## Features

* Updating techdivision/import-attribute library
* Using Symfony DI for application initialisation

# Version 1.0.0-beta11

## Bugfixes

* None

## Features

* Change workflow when trying to read configuration

# Version 1.0.0-beta10

## Bugfixes

* None

## Features

* Add support for EE attribute import

# Version 1.0.0-beta9

## Bugfixes

* None

## Features

* Switch to latest attribute import version
* Update configuration for attribute import

# Version 1.0.0-beta8

## Bugfixes

* None

## Features

* Add system-name commandline option

# Version 1.0.0-beta7

## Bugfixes

* None

## Features

* Switch to latest configuration version that allows params for plugins

# Version 1.0.0-beta6

## Bugfixes

* None

## Features

* Add basic attribute option import functionality

# Version 1.0.0-beta5

## Bugfixes

* None

## Features

* Add basic attribute import functionality

# Version 1.0.0-beta4

## Bugfixes

* None

## Features

* Set correct version number in .semver file

# Version 1.0.0-beta3

## Bugfixes

* None

## Features

* Remove unnecessary configuration options
* Update README.md

# Version 1.0.0-beta2

## Bugfixes

* Throw exception if library does not contain mandatory Symfony DI configuration

## Features

* Throw exception if a additional vendor directory without autoloader has been configured

# Version 1.0.0-beta1

## Bugfixes

* Remove unncessary use statement

## Features

* Integrate Symfony DI functionality

# Version 1.0.0-alpha62

## Bugfixes

* Add new command to create OK files based on CSV files in a directory
* Fixed issue when concurrent access to PID file

## Features

* None

# Version 1.0.0-alpha61

## Bugfixes

* None

## Features

* Update Dockerfile with postfix installation

# Version 1.0.0-alpha60

## Bugfixes

* Remove unnecessary init() method calls on repositories in ImportProcessorFactory

## Features

* None

# Version 1.0.0-alpha59

## Bugfixes

* None

## Features

* Switch to latest techdivision/import-product-ee package
* Refactoring for DI integration

# Version 1.0.0-alpha58

## Bugfixes

* Removed unnecessary use statement in AbstractImportCommand

## Features

* Switch to latest techdivision/import + techdivision/import-product-link version

# Version 1.0.0-alpha57

## Bugfixes

* None

## Features

* Refactor Import + RegistryProcessor initialization

# Version 1.0.0-alpha56

## Bugfixes

* None

## Features

* Add Swift Mailer configuration for logger and MissingOptionValuesPlugin

# Version 1.0.0-alpha55

## Bugfixes

* Fixed path to JMS serializer annotation for Doctrine AnnotationRegistry

## Features

* None

# Version 1.0.0-alpha54

## Bugfixes

* Fixed issue that JMS docblocks are remove when creating PHAR
* Fixed issue that autoloader can not be required when using as library

## Features

* None

# Version 1.0.0-alpha53

## Bugfixes

* None

## Features

* Add optimised error messages/output
* Add generic SwiftMailer logging/mail functionality

# Version 1.0.0-alpha52

## Bugfixes

* None

## Features

* Make logger configuration customizable

# Version 1.0.0-alpha51

## Bugfixes

* Set default operation to add-update instead of replace to avoid data loss

## Features

* Add functionality to temporary store the entity IDs of deleted products, provided by techdivision/import-product:1.0.0-alpha41

# Version 1.0.0-alpha50

## Bugfixes

* None

## Features

* Add functionality to stop processing the operation by a plugin
* Add category import functionality

# Version 1.0.0-alpha49

## Bugfixes

* Fixed missing Generator injection in CoreConfigDataRepository

## Features

* None

# Version 1.0.0-alpha48

## Bugfixes

* None

## Features

* Inject core config data UID generator in import processor

# Version 1.0.0-alpha47

## Bugfixes

* None

## Features

* Unlock PID file when a plugin fails execution

# Version 1.0.0-alpha45

## Bugfixes

* None

## Features

* Switch to latest techdivision/import-product version
#
 Version 1.0.0-alpha44

## Bugfixes

* None

## Features

* Update configuration files for CE/EE category/product import

# Version 1.0.0-alpha43

## Bugfixes

* None

## Features

* Update configuration files for CE/EE category/product import

# Version 1.0.0-alpha42

## Bugfixes

* None

## Features

* Integrate category import functionality for CE/EE without URL rewrite update

# Version 1.0.0-alpha41

## Bugfixes

* None

## Features

* Minor changes for category import functionality

# Version 1.0.0-alpha40

## Bugfixes

* None

## Features

* Refactoring for category import URL rewrite functionality

# Version 1.0.0-alpha39

## Bugfixes

* None

## Features

* Add basic category import functionality for Magento 2 CE

# Version 1.0.0-alpha38

## Bugfixes

* None

## Features

* Refactoring for plugin functionality

# Version 1.0.0-alpha37

## Bugfixes

* None

## Features

* Allow subject configuration to specify if an OK file is necessary to be processed
* Add possibility to specify PID filename in configuration file and override it with a commandline option
* Optimize PID file handling to lock import process only when subjects are configured and an OK file is available if needed by the subject

# Version 1.0.0-alpha36

## Bugfixes

* None

## Features

* Lock import process only, when CSV files are available
* Switch to appserver-io/dist:1.1.4-beta6 to fix CRON issue

# Version 1.0.0-alpha35

## Bugfixes

* Added missing --use-db-id option to explicitly specify database configuration to use

## Features

* Add products:clear:pid command to remove an unused PID file

# Version 1.0.0-alpha34

## Bugfixes

* Fixed issue in expected OK filename preparation

## Features

* None

# Version 1.0.0-alpha33

## Bugfixes

* Refactoring log output + fixed invalid variable name

## Features

* None

# Version 1.0.0-alpha32

## Bugfixes

* None

## Features

* Add handling for multiple database configurations

# Version 1.0.0-alpha31

## Bugfixes

* None

## Features

* Add OK flagfile functionality

# Version 1.0.0-alpha30

## Bugfixes

* None

## Features

* Move callbacks/observers initialisation to AbstractSubject class
* Add --ignore-pid=true option to allow multiple import processes, e. g. for debugging purposes
* Define default callbacks and implement default callbacks for user defined attributes based on attributes frontend_input value

# Version 1.0.0-alpha29

## Bugfixes

* Fixed invalid sorting for import files on Linux in a Docker container

## Features

* None

# Version 1.0.0-alpha28

## Bugfixes

* Fixed issue that import folders are deleted, when NO files for import are available

## Features

* Use PID file to make sure, that only one import can be processed at a time

# Version 1.0.0-alpha27

## Bugfixes

* None

## Features

* Add Dockerfile

# Version 1.0.0-alpha26

## Bugfixes

* None

## Features

* Add initialisation for UrlRewriteProductCategory repository

# Version 1.0.0-alpha25

## Bugfixes

* Fixed invalid name of sample data CSV files

## Features

* Initialize and inject new UrlRewriteProductCategoryAction to handle missing URL rewrite product => category relations

# Version 1.0.0-alpha24

## Bugfixes

* Fixed issue that prevents processing artefact exports
* Throw exceptions in Simple::removeDir() method, when directories/files can not be deleted

## Features

* None

# Version 1.0.0-alpha23

## Bugfixes

* None

## Features

* Implement functionality to handle bunch imports

# Version 1.0.0-alpha22

## Bugfixes

* Fixed invalid log level setting in debug mode

## Features

* Optimize debug output of installed PHP extensions

# Version 1.0.0-alpha21

## Bugfixes

* None

## Features

* Make log level configurable
* Add debug mode to ignore missing SKUs in product relations and missing categories in product category relations

# Version 1.0.0-alpha20

## Bugfixes

* None

## Features

* Add flag to upload images file to CE/EE configuration

# Version 1.0.0-alpha19

## Bugfixes

* Bugfix archive functionality to avoid deleting import folder on exception during import

## Features

* None

# Version 1.0.0-alpha18

## Bugfixes

* Removing invalid class name in Simple::removeDir() method

## Features

* None

# Version 1.0.0-alpha17

## Bugfixes

* None

## Features

* Add archive functionality

# Version 1.0.0-alpha16

## Bugfixes

* None

## Features

* Make source/target folder as well as multiple field delimiter configurable

# Version 1.0.0-alpha15

## Bugfixes

* None

## Features

* Optimize exeception/error handling

# Version 1.0.0-alpha14

## Bugfixes

* None

## Features

* Implement add-update operation for product media EE

# Version 1.0.0-alpha13

## Bugfixes

* None

## Features

* Implement add-update operation for product media (no EE)

# Version 1.0.0-alpha12

## Bugfixes

* None

## Features

* Implement add-update operation for product variants

# Version 1.0.0-alpha11

## Bugfixes

* None

## Features

* Implement add-update operation for product link import

# Version 1.0.0-alpha10

## Bugfixes

* None

## Features

* Implement add-update operation for product bundle import

# Version 1.0.0-alpha9

## Bugfixes

* None

## Features

* Implement add-update operation for product import (NOT bundle, link, media + variant)

# Version 1.0.0-alpha8

## Bugfixes

* None

## Features

* Switch to new create/delete naming convention
* Add basic product update functionality for add-update operation

# Version 1.0.0-alpha7

## Bugfixes

* Fixed invalid creation of ProductLinkProcessor in ProductLinkProcessorFactory
* Remove unnecessary use statements in Operation configuration

## Features

* None

# Version 1.0.0-alpha6

## Bugfixes

* None

## Features

* Now load default store on start-up
* Rename example => projects folder + re-order directory structure to support multiple projects
* Rename ProductProcessorFactory => ProductBunchProcessorFactory
* Fixed some typos

# Version 1.0.0-alpha5

## Bugfixes

* None

## Features

* Add robo.li dependency + configuration, integrate create:phar command to create PHAR archive

# Version 1.0.0-alpha4

## Bugfixes

* None

## Features

* Integrate Operation functionality

# Version 1.0.0-alpha3

## Bugfixes

* None

## Features

* Integrate Clean-Up for products and relations

# Version 1.0.0-alpha2

## Bugfixes

* None

## Features

* Activate replace mode for [URL rewrite functionality](https://github.com/techdivision/import-product/issues/5)

# Version 1.0.0-alpha1

## Bugfixes

* None

## Features

* Refactoring + Documentation to prepare for Github release

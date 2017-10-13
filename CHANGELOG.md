# Version 1.0.0-beta60

## Bugfixes

* [techdivision/import-product-url-rewrite] Add validation for url rewrite update to prevent that url rewrites are persisted, where target_path and request_path are equal.

## Features

* None

# Version 1.0.0-beta59

## Known Issues

* None

## Bugfixes

* [techdivision/import-attribute] Fix issue for updating attribute option swatch values

## Features

* None

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

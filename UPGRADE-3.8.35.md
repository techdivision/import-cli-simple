# Upgrade from 3.8.34 to 3.8.35

## Configuration

Up from this version, a new directive `"update-url-key-from-name": true|false` can be used to protect existing
rewrites to be updated when the product or category name changes. By default, the directive has the value 
`true` to provide backwards compatility.

## Classes

The following classes and interfaces has been extended with the following methods

* TechDivision\Import\Utils\UrlKeyUtil::loadUrlKey()
* TechDivision\Import\Utils\UrlKeyUtilInterface::loadUrlKey()
* TechDivision\Import\Services\UrlKeyAwareProcessorInterface::loadVarcharAttributeByAttributeCodeAndEntityTypeIdAndStoreIdAndPrimaryKey()

> As these classes and interfaces are *NOT* part of the official API we do *NOT* have a major change here
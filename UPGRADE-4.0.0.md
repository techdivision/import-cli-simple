# Upgrade from 3.8.27 to 4.0.0

## Configuration

Up from version `4.0.0-alpha8` the default import path has been changed from `var/importexport` to `var/pacemaker/import`.

## Symfony DI Configuration

### techdivision/import-category

Up from version `4.0.0-alpha8` a new observer with the DI ID `import_category.observer.normalize.path` has been introduced
that is necessary to take care that the category paths are normalized according to the CSV standard. For example, a
valid category path can look like `"Default Category/Example Path"`. For several purposes, it is necessary that the path 
internally will be converted to `"""Default Category""/""Example Path"""` which this observer takes care for.

The following composite observer has been customized

* `import_category.observer.composite.base.delete`
* `import_category.observer.composite.create.replace`
* `import_category.observer.composite.add_update`
* `import_category.observer.composite.base.validate`

For example have a look at the file `symfony/Resources/config/services.xml` where the composite observer for deleting
categories has been exendend from

```
<service id="import_category.observer.composite.base.delete" class="TechDivision\Import\Observers\GenericCompositeObserver">
    <call method="addObserver">
        <argument id="import_category.observer.clear.url.rewrite" type="service"/>
    </call>
    <call method="addObserver">
        <argument id="import_category.observer.clear.category" type="service"/>
    </call>
</service>
```

to

```
<service id="import_category.observer.composite.base.delete" class="TechDivision\Import\Observers\GenericCompositeObserver">
    <call method="addObserver">
        <argument id="import_category.observer.normalize.path" type="service"/>
    </call>
    <call method="addObserver">
        <argument id="import_category.observer.clear.url.rewrite" type="service"/>
    </call>
    <call method="addObserver">
        <argument id="import_category.observer.clear.category" type="service"/>
    </call>
</service>
```

> So do *NOT* forget, if you've a custom Symfony DI configuration in your project, to prepend the observer to your
> configuration. Also keep in mind, that we/you heavily use the composite observers which also needs to be customized.

### techdivision/import-category-ee

Up from version `4.0.0-alpha8`  and according to the changes in the `techdivision/import-category` library,
additionally the Symfony DI configuration of the following composite observers has been extendend with 
the observer `import_category.observer.normalize.path`.

* `import_category_ee.observer.composite.base.delete`
* `import_category_ee.observer.composite.create.replace`
* `import_category_ee.observer.composite.add_update` 

## New Functionality

### Interface for Hook aware Observers

Up from this version, the new interface `TechDivision\Import\Interfaces\HookAwareObserverInterface` can be used
to add the hooks `setUp()` and `tearDown()` to an observer that will be invoked when the parent subject will
be set-up or teared down.

These functions can and should be used to add collected data from an observer to the registry for further
processing. A use case can be found in the `TechDivision\Import\Observers\GenericColumnCollectorObserver`.

## Removed Methods

### techdivision/import

* `TechDivision\Import\Subjects\UrlKeyAwareSubjectInterface::isUrlKeyOf()`

### techdivision/import-category

* `TechDivision\Import\Category\Services\CategoryBunchProcessorInterface::getUrlRewritesByEntityTypeAndEntityId()`
* `TechDivision\Import\Category\Services\CategoryBunchProcessorInterface::getUrlRewritesByEntityTypeAndEntityIdAndStoreId()`

## Removed Classes

As of generalization purposes, many boilerplate classes have been removed and replaced with a 
generic implementation. The removed classes are listed below, grouped by their library name.

### techdivision/import

* `TechDivision\Import\Actions\Processors\AbstractCreateProcessor` without replacement
* `TechDivision\Import\Actions\Processors\AbstractDeleteProcessor` without replacement
* `TechDivision\Import\Actions\Processors\AbstractUpdateProcessor` without replacement
* `TechDivision\Import\Loaders\StoreViewCodeLoader` has been replaced with `\TechDivision\Import\Loaders\GenericMemberNameLoader`
* `TechDivision\Import\Actions\Processors\ImportHistoryCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\ImportHistoryUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\ImportHistoryDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Actions\Processors\StoreCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\StoreUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\StoreGroupCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\StoreGroupUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\StoreWebsiteCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\StoreWebsiteUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\UrlRewriteCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\UrlRewriteUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Actions\Processors\UrlRewriteDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`

### techdivision/import-attribute

* `TechDivision\Import\Attribute\Actions\Processors\AttributeCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\AttributeUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\AttributeDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\AttributeLabelCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\AttributeLabelUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\AttributeLabelDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\AttributeOptionCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\AttributeOptionUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\AttributeOptionDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\AttributeOptionValueCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\AttributeOptionValueUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\AttributeOptionValueDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\AttributeOptionSwatchCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\AttributeOptionSwatchUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\AttributeOptionSwatchDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\EntityAttributeCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\EntityAttributeUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\EntityAttributeDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Attribute\Actions\Processors\CatalogAttributeDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`

### techdivision/import-attribute-set

* `TechDivision\Import\Attribute\Set\Actions\Processors\EavAttributeGroupCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Attribute\Set\Actions\Processors\EavAttributeGroupUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Attribute\Set\Actions\Processors\EavAttributeGroupDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Attribute\Set\Actions\Processors\EavAttributeSetCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Attribute\Set\Actions\Processors\EavAttributeSetUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Attribute\Set\Actions\Processors\EavAttributeSetDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`

### techdivision/import-category

* `TechDivision\Import\Category\Actions\Processors\CategoryCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Category\Actions\Processors\CategoryUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Category\Actions\Processors\CategoryDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Category\Actions\Processors\CategoryDatetimeCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Category\Actions\Processors\CategoryDatetimeUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Category\Actions\Processors\CategoryDecimalCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Category\Actions\Processors\CategoryDecimalUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Category\Actions\Processors\CategoryIntCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Category\Actions\Processors\CategoryIntUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Category\Actions\Processors\CategoryTextCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Category\Actions\Processors\CategoryTextUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Category\Actions\Processors\CategoryVarcharCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Category\Actions\Processors\CategoryVarcharUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Category\Filters\FilterInterface` has been removed without any replacement
* `TechDivision\Import\Category\Filters\CategoryUpgradeFilter` has been removed without any replacement

### techdivision/import-category-ee

* `TechDivision\Import\Category\Ee\Actions\Processors\CategoryUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`

### techdivision/import-customer

* `TechDivision\Import\Customer\Actions\Processors\CustomerCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerDatetimeCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerDatetimeUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerDatetimeDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerDecimalCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerDecimalUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerDecimalDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerIntCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerIntUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerIntDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerTextCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerTextUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerTextDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerVarcharCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerVarcharUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Actions\Processors\CustomerVarcharDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`

### techdivision/import-customer-address

* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressDatetimeCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressDatetimeUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressDatetimeDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressDecimalCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressDecimalUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressDecimalDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressIntCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressIntUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressIntDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressTextCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressTextUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressTextDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressVarcharCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressVarcharUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Customer\Address\Actions\Processors\CustomerAddressVarcharDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`

### techdivision/import-product

* `TechDivision\Import\Product\Actions\Processors\ProductCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Actions\Processors\CategoryProductCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Actions\Processors\CategoryProductDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Actions\Processors\CategoryProductUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductDatetimeCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductDatetimeUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductDatetimeDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductDecimalCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductDecimalUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductDecimalDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductIntCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductIntUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductIntDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductTextCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductTextUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductTextDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductVarcharCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductVarcharUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductVarcharDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Actions\Processors\StockItemDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductRelationCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductWebsiteCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Actions\Processors\ProductWebsiteDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`

### techdivision/import-product-bundle

* `TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleOptionCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleOptionUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleSelectionCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleSelectionUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleOptionValueCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleSelectionPriceCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleSelectionPriceUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`

### techdivision/import-product-bundle-ee

* `TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleOptionCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Bundle\Actions\Processors\ProductBundleSelectionCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`

### techdivision/import-product-link

* `TechDivision\Import\Product\Link\Actions\Processors\ProductLinkCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Link\Actions\Processors\ProductLinkUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Link\Actions\Processors\ProductLinkAttributeIntCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Link\Actions\Processors\ProductLinkAttributeIntUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Link\Actions\Processors\ProductLinkAttributeDecimalCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Link\Actions\Processors\ProductLinkAttributeDecimalUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Link\Actions\Processors\ProductLinkAttributeVarcharCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Link\Actions\Processors\ProductLinkAttributeVarcharUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`

### techdivision/import-product-media

* `TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryValueCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryValueUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Media\Actions\Processors\ProductMediaGalleryValueToEntityCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`

### techdivision/import-product-msi

* `TechDivision\Import\Product\Msi\Actions\Processors\InventorySourceItemCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Msi\Actions\Processors\InventorySourceItemUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Msi\Actions\Processors\InventorySourceItemDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`

### techdivision/import-product-tier-price

* `TechDivision\Import\Product\TierPrice\Actions\Processors\TierPriceCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\TierPrice\Actions\Processors\TierPriceUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\TierPrice\Actions\Processors\TierPriceDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`

### techdivision/import-product-url-rewrite

* `TechDivision\Import\Product\UrlRewrite\Actions\Processors\UrlRewriteProductCategoryCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\UrlRewrite\Actions\Processors\UrlRewriteProductCategoryUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\UrlRewrite\Actions\Processors\UrlRewriteProductCategoryDeleteProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`

### techdivision/import-product-variant

* `TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperAttributeCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperAttributeUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericIdentifierProcessor`
* `TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperAttributeLabelCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperAttributeLabelUpdateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`
* `TechDivision\Import\Product\Variant\Actions\Processors\ProductSuperLinkCreateProcessor` has been replaced with `\TechDivision\Import\Actions\Processors\GenericProcessor`

### techdivision/import-converter-product-category

* `TechDivision\Import\Converter\Product\Category\Observers\Filters\FilterInterface` has been removed without any replacement
* `TechDivision\Import\Converter\Product\Category\Observers\Filters\CategoryUpgradeFilter` has been removed without any replacement

## Moved Classes

While adding addional functionality, e. g. the extended URL handling, it has been necessary to
migrate some classes from existing libraries to another ones or to new libraries.

### techdivision/import > techdivision/import-serializer

* `TechDivision\Import\Serializers\SerializerInterface` > `TechDivision\Import\Serializer\SerializerInterface`
* `TechDivision\Import\Serializers\SerializerAwareInterface` > `TechDivision\Import\Serializer\SerializerAwareInterface`
* `TechDivision\Import\Serializers\ConfigurationAwareSerializerFactoryInterface` > `TechDivision\Import\Serializer\SerializerFactoryInterface`
* `TechDivision\Import\Serializers\ConfigurationAwareSerializerInterface` > `TechDivision\Import\Serializer\ConfigurationAwareSerializerInterface`

### techdivision/import > techdivision/import-serializer-csv

* `TechDivision\Import\Serializers\ValueCsvSerializer` > `TechDivision\Import\Serializer\Csv\ValueCsvSerializer`
* `TechDivision\Import\Serializers\AbstractCsvSerializer` > `TechDivision\Import\Serializer\Csv\AbstractCsvSerializer`
* `TechDivision\Import\Serializers\AdditionalAttributeCsvSerializer` > `TechDivision\Import\Serializer\Csv\AdditionalAttributeCsvSerializer`

### techdivision/import > techdivision/import-cache

* `TechDivision\Import\Cache\CacheAdapterInterface` > `TechDivision\Import\Cache\CacheAdapterInterface`
* `TechDivision\Import\Connection\CachePoolFactoryInterface` > `TechDivision\Import\Cache\CachePoolFactoryInterface`
* `TechDivision\Import\Utils\CacheKeysInterface` > `TechDivision\Import\Cache\Utils\CacheKeysInterface`
* `TechDivision\Import\Utils\CacheKeyUtilInterface` > `TechDivision\Import\Cache\Utils\CacheKeyUtilInterface`
* `TechDivision\Import\Utils\CacheTypes` > `TechDivision\Import\Cache\Utils\CacheTypes`

### techdivision/import > techdivision/import-cache-collection

* `TechDivision\Import\Cache\CacheAdapterTrait` > `TechDivision\Import\Cache\Collection\CacheAdapterTrait`
* `TechDivision\Import\Cache\ConfigurableCacheAdapter` > `TechDivision\Import\Cache\Collection\ConfigurableCacheAdapter`
* `TechDivision\Import\Cache\LocalCacheAdapter` > `TechDivision\Import\Cache\Collection\LocalCacheAdapter`
* `TechDivision\Import\Utils\CacheKeyUtil` > `TechDivision\Import\Cache\Collection\Utils\CacheKeyUtil`

### techdivision/import-cli > techdivision/import-dbal

* `TechDivision\Import\Cli\Connection\ConnectionFactory` > `TechDivision\Import\Dbal\Connection\ConnectionFactory`

### techdivision/import > techdivision/import-dbal

* `TechDivision\Import\Actions\ActionInterface` > `TechDivision\Import\Dbal\Actions\ActionInterface`
* `TechDivision\Import\Actions\IdentifierActionInterface` > `TechDivision\Import\Dbal\Actions\IdentifierActionInterface`
* `TechDivision\Import\Actions\Processors\ProcessorInterface` > `TechDivision\Import\Dbal\Actions\Processors\ProcessorInterface`
* `TechDivision\Import\Connection\ConnectionInterface` > `TechDivision\Import\Dbal\Connection\ConnectionInterface`
* `TechDivision\Import\Repositories\Finders\FinderFactoryInterface` > `TechDivision\Import\Dbal\Repositories\Finders\FinderFactoryInterface`
* `TechDivision\Import\Repositories\Finders\FinderInterface` > `TechDivision\Import\Dbal\Repositories\Finders\FinderInterface`
* `TechDivision\Import\Repositories\Finders\SimpleFinderFactory` > `TechDivision\Import\Dbal\Repositories\Finders\SimpleFinderFactory`
* `TechDivision\Import\Repositories\CachedRepositoryInterface` > `TechDivision\Import\Dbal\Repositories\CachedRepositoryInterface`
* `TechDivision\Import\Repositories\FinderAwareRepositoryInterface` > `TechDivision\Import\Dbal\Repositories\FinderAwareRepositoryInterface`
* `TechDivision\Import\Repositories\FinderAwareEntityRepositoryInterface` > `TechDivision\Import\Dbal\Repositories\FinderAwareEntityRepositoryInterface`
* `TechDivision\Import\Repositories\RepositoryInterface` > `TechDivision\Import\Dbal\Repositories\RepositoryInterface`
* `TechDivision\Import\Repositories\SqlStatementRepositoryInterface` > `TechDivision\Import\Dbal\Repositories\SqlStatementRepositoryInterface`
* `TechDivision\Import\Utils\PrimaryKeyUtilInterface` > `TechDivision\Import\Dbal\Utils\PrimaryKeyUtilInterface`
* `TechDivision\Import\Utils\TablePrefixUtilInterface` > `TechDivision\Import\Dbal\Utils\TablePrefixUtilInterface`
* `TechDivision\Import\Utils\SanitizerInterface` > `TechDivision\Import\Dbal\Utils\SanitizerInterface`
* `TechDivision\Import\Utils\SqlCompilerInterface` > `TechDivision\Import\Dbal\Utils\SqlCompilerInterface`
* `TechDivision\Import\Utils\EntityStatus` > `TechDivision\Import\Dbal\Utils\EntityStatus` (class will still be available but extends the new class only)

### techdivision/import > techdivision/import-dbal-collection

* `TechDivision\Import\Actions\Processors\AbstractBaseProcessor` > `TechDivision\Import\Dbal\Collection\Actions\Processors\AbstractBaseProcessor`
* `TechDivision\Import\Actions\Processors\AbstractProcessor` > `TechDivision\Import\Dbal\Collection\Actions\Processors\AbstractProcessor`
* `TechDivision\Import\Actions\AbstractAction` > `TechDivision\Import\Dbal\Collection\Actions\AbstractAction`
* `TechDivision\Import\Actions\GenericAction` > `TechDivision\Import\Dbal\Collection\Actions\GenericAction`
* `TechDivision\Import\Actions\GenericDynamicIdentifierAction` > `TechDivision\Import\Dbal\Collection\Actions\GenericDynamicIdentifierAction`
* `TechDivision\Import\Actions\GenericIdentifierAction` > `TechDivision\Import\Dbal\Collection\Actions\GenericIdentifierAction`
* `TechDivision\Import\Connection\PDOConnectionWrapper` > `TechDivision\Import\Dbal\Collection\Connection\PDOConnectionWrapper`
* `TechDivision\Import\Repositories\Finders\AbstractFinder` > `TechDivision\Import\Dbal\Collection\Repositories\Finders\AbstractFinder`
* `TechDivision\Import\Repositories\Finders\ConfigurableFinderFactory` > `TechDivision\Import\Dbal\Collection\Repositories\Finders\ConfigurableFinderFactory`
* `TechDivision\Import\Repositories\Finders\SimpleFinder` > `TechDivision\Import\Dbal\Collection\Repositories\Finders\SimpleFinder`
* `TechDivision\Import\Repositories\Finders\UniqueFinder` > `TechDivision\Import\Dbal\Collection\Repositories\Finders\UniqueFinder`
* `TechDivision\Import\Repositories\Finders\UniqueFinderFactory` > `TechDivision\Import\Dbal\Collection\Repositories\Finders\UniqueFinderFactory`
* `TechDivision\Import\Repositories\Finders\YieldedFinder` > `TechDivision\Import\Dbal\Collection\Repositories\Finders\YieldedFinder`
* `TechDivision\Import\Repositories\Finders\YieldedFinderFactory` > `TechDivision\Import\Dbal\Collection\Repositories\Finders\YieldedFinderFactory`
* `TechDivision\Import\Repositories\AbstractCachedRepository` > `TechDivision\Import\Dbal\Collection\Repositories\AbstractCachedRepository`
* `TechDivision\Import\Repositories\AbstractFinderRepository` > `TechDivision\Import\Dbal\Collection\Repositories\AbstractFinderRepository`
* `TechDivision\Import\Repositories\AbstractRepository` > `TechDivision\Import\Dbal\Collection\Repositories\AbstractRepository`
* `TechDivision\Import\Repositories\AbstractSqlStatementRepository` > `TechDivision\Import\Dbal\Collection\Repositories\AbstractSqlStatementRepository`
* `TechDivision\Import\Utils\ColumnSanitizer` > `TechDivision\Import\Dbal\Collection\Utils\ColumnSanitizer`
* `TechDivision\Import\Utils\EventNames` > `TechDivision\Import\Dbal\Collection\Utils\EventNames` (class will still be available and extends the new class)

## Moved Classes from Professional to Community Edition

While adding addional functionality, e. g. the extenden URL handling, it has been necessary to migrate 
some classes from the libraries of the Professional to libraries of the Community Edition.

### techdivision/import-caching > techdivision/import-dbal

* `TechDivision\Import\Caching\Actions\CachedActionInterface` >  `TechDivision\Import\Dbal\Actions\CachedActionInterface`
* `TechDivision\Import\Caching\Repositories\Finders\EntityFinderInterface` > `TechDivision\Import\Dbal\Repositories\Finders\EntityFinderInterface`

### techdivision/import-caching > techdivision/import-dbal-collection

* `TechDivision\Import\Caching\Listeners\CacheUpdateListener` > `TechDivision\Import\Dbal\Collection\Listeners\CacheUpdateListener`
* `TechDivision\Import\Caching\Actions\GenericCachedEventAwareAction` > `TechDivision\Import\Dbal\Collection\Actions\GenericCachedEventAwareAction`
* `TechDivision\Import\Caching\Actions\GenericCachedEventAwareIdentifierAction` > `TechDivision\Import\Dbal\Collection\Actions\GenericCachedEventAwareIdentifierAction`
* `TechDivision\Import\Caching\Repositories\Finders\CachedUniqueEntityFinder` >  `TechDivision\Import\Dbal\Collection\Repositories\Finders\CachedUniqueFinder`
* `TechDivision\Import\Caching\Repositories\Finders\CachedUniqueEntityFinderFactory` > `TechDivision\Import\Dbal\Collection\Repositories\Finders\CachedUniqueFinderFactory`
* `TechDivision\Import\Caching\Repositories\Finders\CachedUniqueFinder` > `TechDivision\Import\Dbal\Collection\Repositories\Finders\CachedUniqueFinder`
* `TechDivision\Import\Caching\Repositories\Finders\CachedUniqueFinderFactory` > `TechDivision\Import\Dbal\Collection\Repositories\Finders\CachedUniqueFinderFactory`
* `TechDivision\Import\Caching\Repositories\Finders\CachedYieldedFinder` >  `TechDivision\Import\Dbal\Collection\Repositories\Finders\CachedUniqueFinder`
* `TechDivision\Import\Caching\Repositories\Finders\CachedYieldedFinderFactory` > `TechDivision\Import\Dbal\Collection\Repositories\Finders\CachedUniqueFinderFactory`
* `TechDivision\Import\Caching\Repositories\Finders\UniqueEntityFinder` > `TechDivision\Import\Dbal\Collection\Repositories\Finders\UniqueEntityFinder`

### techdivision/import-cli-extended > techdivision/import-dbal

* `TechDivision\Import\Cli\Extended\Utils\EntityStatus` > `TechDivision\Import\Dbal\Utils\EntityStatus`

### techdivision/import-cli-extended > techdivision/import-dbal-collection

* `TechDivision\Import\Cli\Extended\Utils\EventNames` > `TechDivision\Import\Dbal\Collection\Utils\EventNames`
* `TechDivision\Import\Cli\Extended\Actions\GenericEventAwareAction` > `TechDivision\Import\Dbal\Collection\Actions\GenericEventAwareAction`
* `TechDivision\Import\Cli\Extended\Actions\GenericEventAwareIdentifierAction` > `TechDivision\Import\Dbal\Collection\Actions\GenericEventAwareIdentifierAction`

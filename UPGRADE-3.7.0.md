# Upgrade from 3.6.3 to 3.7.0

## Configuration

To make configuration more generic, the following DI identifiers for Magento 2 CE have been renamed

* import_product.observer.composite.msi.delete > import_product_msi.observer.composite.delete
* import_product.observer.composite.variant.replace > import_product_variant.observer.composite.replace
* import_product.observer.composite.bundle.replace > import_product_bundle.observer.composite.replace
* import_product.observer.composite.media.replace > import_product_media.observer.composite.replace
* import_product.observer.composite.link.replace" class="import_product_link.observer.composite.replace
* import_product.observer.composite.msi.replace > import_product_msi.observer.composite.replace
* import_product.observer.composite.variant.add_update > import_product_variant.observer.composite.add_update
* import_product.observer.composite.bundle.add_update > import_product_bundle.observer.composite.add_update
* import_product.observer.composite.media.add_update > import_product_media.observer.composite.add_update
* import_product.observer.composite.link.add_update" class="import_product_link.observer.composite.add_update
* import_product.observer.composite.msi.add_update > import_product_msi.observer.composite.add_update

For Magento 2 EE the following DI identifiers for the CE have been renamed

* import_product_ee.observer.composite.variant.replace > import_product_variant_ee.observer.composite.replace
* import_product_ee.observer.composite.bundle.replace > import_product_bundle_ee.observer.composite.replace
* import_product_ee.observer.composite.media.replace > import_product_media_ee.observer.composite.replace
* import_product_ee.observer.composite.link.replace > import_product_link_ee.observer.composite.replace
* import_product_ee.observer.composite.variant.add_update > import_product_variant_ee.observer.composite.add_update
* import_product_ee.observer.composite.bundle.add_update > import_product_bundle_ee.observer.composite.add_update
* import_product_ee.observer.composite.media.add_update > import_product_media_ee.observer.composite.add_update
* import_product_ee.observer.composite.link.add_update > import_product_link_ee.observer.composite.add_update

> ATTENTION: Aliases has been created to ensure backwards compatibility. Those has been marked deprecated and will be removed with version 3.8.0
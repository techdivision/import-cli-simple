# Upgrade from 1.1.1 to 2.0.0

## Configuration

As a result of database schema changes between Magento 2.1.x to Magento 2.2.x, when upgrading from 1.1.1 to 2.0.0 the 
configuration needs the minor updates for the `replace` and the `add-update` operation.

> When using the EE, the DI IDs below has to be suffixed with `_ee`, e. g. `import_product_variant.observer.variant.super.link`
> has to be named `import_product_variant_ee.observer.variant.super.link`.

### Operation: *replace*

For the replace operation the DI ID for the variant observer changes from `import_product_variant.observer.variant`
to `import_product_variant.observer.variant.super.link`. Additionally a new observer, that adds the product relation
has to be added. The DI ID is `import_product_variant.observer.variant.product.relation`. The configuration for the
product variants should look something like

```json
    ...
    {
      "id": "import_product_variant.subject.variant",
      "prefix": "variants",
      "observers": [
        {
          "pre-import": [
            "import.observer.attribute.set"
          ]
        },
        {
          "import": [
            "import_product_variant.observer.variant.super.link",
            "import_product_variant.observer.variant.super.attribute",
            "import_product_variant.observer.variant.product.relation"
          ]
        }
      ]
    }
    ...
```

Beside the configuration for the variants, also the one for the bundles has to be changed. A new observer, that adds 
the product relation has to be added. The DI ID is `import_product_bundle.observer.bundle.product.relation`. The 
configuration for the product bundles should look something like

```json
    ...
    {
      "id": "import_product_bundle.subject.bundle",
      "prefix": "bundles",
      "observers": [
        {
          "import": [
            "import_product_bundle.observer.bundle.option",
            "import_product_bundle.observer.bundle.option.value",
            "import_product_bundle.observer.bundle.selection",
            "import_product_bundle.observer.bundle.selection.price",
            "import_product_bundle.observer.bundle.product.relation"
          ]
        }
      ]
    }
    ...
```

### Operation: *add-update*

For the replace operation the DI ID for the variant observer changes from `import_product_variant.observer.variant.update`
to `import_product_variant.observer.variant.super.link.update`. Additionally a new observer, that adds/update the product 
relation has to be added. The DI ID is `import_product_variant.observer.variant.product.relation.update`. The configuration 
for the product variants should look something like

```json
    ...
    {
      "id": "import_product_variant.subject.variant",
      "prefix": "variants",
      "observers": [
        {
          "pre-import": [
            "import.observer.attribute.set"
          ]
        },
        {
          "import": [
            "import_product_variant.observer.variant.super.link.update",
            "import_product_variant.observer.variant.super.attribute.update",
            "import_product_variant.observer.variant.product.relation.update"
          ]
        }
      ]
    }
    ...
```

Beside the configuration for the variants, also the one for the bundles has to be changed. A new observer, that adds 
the product relation has to be added. The DI ID is `import_product_bundle.observer.bundle.product.relation.update`. 
The configuration for the product bundles should look something like

```json
    ...
    {
      "id": "import_product_bundle.subject.bundle",
      "prefix": "bundles",
      "observers": [
        {
          "import": [
            "import_product_bundle.observer.bundle.option.update",
            "import_product_bundle.observer.bundle.option.value.update",
            "import_product_bundle.observer.bundle.selection.update",
            "import_product_bundle.observer.bundle.selection.price.update",
            "import_product_bundle.observer.bundle.product.relation.update"
          ]
        }
      ]
    }
    ...
```

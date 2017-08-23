# Upgrade from 1.0.0-beta45 to 1.0.0-beta46

## New library techdivision/import-product-url-rewrite

Up from this version a new library, to handle the product URL rewrite functionality, has been introduced.

The new library needs some customizations in the `techdivision-import.json` of your project.

### New Observer to clear existing URL rewrites

First a new observer that removes existing URL rewrites has to be added to the `import_product.subject.bunch` (`import_product_ee.subject.bunch` when using EE) configuration of the`delete` and `replace` operations **BEFORE** the `import_product.observer.clear.product` observer, like

```json
{
  ...
  {
    "id": "import_product.subject.bunch",
    "identifier": "files",
    "prefix": "product-import",
    "observers": [
      {
        "import": [
          "import_product_url_rewrite.observer.clear.url.rewrite",
          "import_product.observer.clear.product"
        ]
      }
    ]
  }
  ...
}
```

> It is necessary that the observer is added **BEFORE**, because if not, the SKU => entity ID relation is not longer availble which results in dead rows.

### New Observer to prepare the URL key (only EE)

When using the EE, the `import_product.observer.url.key` in the `import_product_ee.subject.bunch` configuration of the`replace` and `add-update` operations has to be replaced with the new `import_product_ee.observer.url.key` like

```json
{
  ...
  {
    "id": "import_product.subject.bunch",
    "identifier": "files",
    "prefix": "product-import",
    "observers": [
      {
        "pre-import": [
          "import_product.observer.pre.load.entity.id",
          "import_product_url_rewrite.observer.clear.url.rewrite",
          "import_product.observer.clear.product",
          "import.observer.attribute.set",
          "import.observer.additional.attribute",
          "import_product_ee.observer.url.key",
          "import_product.observer.quality.and.stock.status"
        ]
      }
    ]
  }
  ...
}
```

> This observer now makes sure, that each product has a unique URL key

### New Observer to create the URL rewrite artefacts.

The third new observer `import_product_url_rewrite.observer.product.url.rewrite` prepares new CSV files with the prefix `url-rewrite` that contains the information, which is necessary to create the URL rewrites by the new subject later. This observer replaces the existing `import_product.observer.url.rewrite` in the `import_product.subject.bunch` (`import_product_ee.subject.bunch` when using EE) configuration of the`replace` and `add-update` operations **AFTER** the `import_product.observer.product` observer, like

```json
{
  ...
  {
    "id": "import_product.subject.bunch",
    "identifier": "files",
    "prefix": "product-import",
    "observers": [
      {
        "import": [
          ...
          "import_product.observer.product.inventory",
          "import_product_url_rewrite.observer.product.url.rewrite",
          ...
        ]
      }
    ]
  }
  ...
}
```

### Additional Subject to persist URL rewrites

Finally, a new subject has be added to the`delete` and `replace` operations **AFTER** The the `import_product.subject.bunch` (`import_product_ee.subject.bunch` when using EE) configuration like

```json
{
  ...

  {
    "id": "import_product_url_rewrite.subject.url.rewrite",
    "prefix": "url-rewrite",
    "observers": [
      {
        "import": [
          "import_product_url_rewrite.observer.url.rewrite"
        ]
      }
    ]
  }
  ...
}
```

This subject parses the previously created CSV files prefixed with `url-rewrite` and creates URL rewrites foreach store view and category found in a row, assumed the store view is enabled and the product is visible.
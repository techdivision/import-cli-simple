# Upgrade from 1.0.0-beta35 to 1.0.0-beta36

## ProductUpdateObserver + EeProductUpdateObserver

Due to [invalid creation of product entities in a multi-store environment with replace operation](https://github.com/techdivision/import-product/issues/75) the 
`ProductUpdateObserver` as well as the `EeProductUpdateObserver` has been removed and replaced with the `ProductObserver` and `EeProductObserver`. Additionally,
the symfony DI configuration has been removed together with renaming the related IDs in the `etc/techdivision-import.json` files in the apropriate libraries
`techdivision/import-product` and `techdivision/import-product-ee`.

If you have a custom `techdivision-import.json` in your project, do not forget to update it from

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
          ...
        ]
      },
     {
        "import": [
          "import_product.observer.product.update",
          ...
        ]
      }
    ]
  }
  ...
}
```

to

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
          ...
        ]
      },
     {
        "import": [
          "import_product.observer.product",
          ...
        ]
      }
    ]
  }
  ...
}
```

When using Enterprise Edition, the new observer ID for the `add-update operation` is `import_product_ee.observer.product`.

## Header Mappings

Header mappings are necessary when the column names in the CSV file does not match the attribute code. By default, the necessary header mappings
are available. If addional header mappings are necessary, e. g. when new columns are added to the CSV file and the column names doesn't match the
attribute code, theses header mappings can be added to the configuration file on root level like

```json
{
  ...
  "header-mappings" : [
    {
      "mouse_over_image" : "mouse_over",
      "mouse_over_image_label" : "mouse_over_label",
      ...
    }
  ]
  ...
}
```

## Image Types

Magento 2 lacks of the possiblity to add additional image types beside base, small, thumbnail and swatch images. To add addtional image types,
these have to be registered in the configuration on root level like

```json
{
  ...
  "image-types" : [
    {
      "mouse_over_image" : "mouse_over_image_label",
      ...
    }
  ]
  ...
}
```
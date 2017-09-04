# Upgrade from 1.0.0-beta49 to 1.0.0-beta50

## Product Configuration

### Image Upload Functionality

The params for the image upload configuration has to be moved from the subject with the ID `import_product_media.subject.media` to the one with the ID `import_product.subject.bunch`, as the files has to be copied before the product attributes are created. Additionally the new observer with the ID `import_product.observer.file.upload` has to be added to this subject, as well as the old `import_product_media.observer.file.upload` has to be removed from the subject with the ID `import_product_media.subject.media`. This has to be configured for the `add-update` as well as the `replace` operation.

```json
{
  {
    ...
    {
      "id": "import_product.subject.bunch",
      "identifier": "files",
      "prefix": "product-import",
      "params" : [
        {
          "copy-images" : false
        }
      ],
      "observers": [
        {
          "pre-import": [
            ...
            "import_product.observer.url.key",
            "import_product.observer.file.upload"
          }
        }
      ],
      ...
    }
  }
}
```

### Remove Attribute Values when Column in CSV file is empty (only add-update operation)

The new functionality to remove product/category attribute values when the column in the CSV file is empty, can be configured by simply add the column names to the parameter `clean-up-empty-columns` to the parameters for the subject with the ID `import_product_media.subject.media`.

```json
{
  {
    ...
    {
      "id": "import_product.subject.bunch",
      "identifier": "files",
      "prefix": "product-import",
      "params" : [
        {
          "copy-images" : false,
          "clean-up-empty-columns" : [ "base_image", "small_image", "swatch_image", "thumbnail_image" ]
        }
      ],
      "observers": [
        {
          "pre-import": [
            ...
            "import_product.observer.url.key",
            "import_product.observer.file.upload"
          }
        }
      ],
      ...
    }
  }
}
```

### Remove Category Relations when not longer in CSV file (only add-update operation)

The new functionality to remove product category relations when they have been removed from the CSV file, can be configured by simply add the parameter `clean-up-category-product-relations` with the value `true` to the parameters for the subject with the ID `import_product_media.subject.media`.

```json
{
  {
    ...
    {
      "id": "import_product.subject.bunch",
      "identifier": "files",
      "prefix": "product-import",
      "params" : [
        {
          "copy-images" : false,
          "clean-up-category-product-relations" : true
        }
      ],
      "observers": [
        {
          "pre-import": [
            ...
            "import_product.observer.url.key",
            "import_product.observer.file.upload"
          }
        }
      ],
      ...
    }
  }
}
```

### Remove Images and Images Tags when not in CSV file (only add-update operation)

The new functionality to remove images when they have been removed from the CSV file, can be configured by simply add the parameter `clean-up-media-gallery` with the value `true`, as well as adding the new observer `import_product_media.observer.clear.media.gallery`, to the parameters for the subject with the ID `import_product_media.subject.media`

```json
{
  {
    ...
    {
      "id": "import_product.subject.bunch",
      "identifier": "files",
      "prefix": "product-import",
      "params" : [
        {
          "copy-images" : false,
          "clean-up-media-gallery" : true
        }
      ],
      "observers": [
        {
          "import": [
            ...
            "import_product_media.observer.product.media",
            "import_product_media.observer.clear.media.gallery",
            "import_product_link.observer.product.link"
          }
        }
      ],
      ...
    }
  }
}
```

## Category Configuration

### Image Upload Functionality

The params for the image upload configuration has to be moved from the subject with the ID `import_category.subject.media` to the one with the ID `import_category.subject.bunch`, as the files has to be copied before the category attributes are created. Additionally the observer with the ID `import_category.observer.file.upload` has to be moved to this subject as the existing observer with the ID `import_category.observer.category.image` has to be removed. IN addition, the complete subject `import_category.subject.media` has to be removed. This has to be configured for the `add-update` as well as the `replace` operation.

```json
{
  {
    ...
    {
      "id": "import_category.subject.bunch",
      "identifier": "files",
      "prefix": "category-create",
      "params" : [
        {
          "copy-images" : false
        }
      ],
      "observers": [
        {
          "pre-import": [
            ...
            "import_category.observer.file.upload"
          ]
        },
        {
          "import": [
            "import_category.observer.category",
            "import_category.observer.category.attribute",
            "import_category.observer.url.rewrite"
          ]
        },
        {
          "post-import": [
            "import_category.observer.clean.up"
          ]
        }
      ]
    },
    ...
  }
}
```

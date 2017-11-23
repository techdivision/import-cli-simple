# Upgrade from 1.0.0-beta63 to 1.0.0-beta64

### Remove Image Attribute Values when Column in CSV file is empty (only add-update operation)

The new functionality to remove product image attribute values when the column in the CSV file is empty, can be configured by simply add the column names to the parameter `clean-up-empty-image-columns` to the parameters for the subject with the ID `import_product_media.subject.media`.

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
          "clean-up-empty-image-columns" : true,
          "clean-up-empty-columns" : [ "special_price" ]
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
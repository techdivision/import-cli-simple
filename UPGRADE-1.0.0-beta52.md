# Upgrade from 1.0.0-beta51 to 1.0.0-beta52

## Replace Observer

First a new observer that creates URL rewrite artefacts for each store view has to be added to the `import_category.subject.bunch` (`import_category_ee.subject.bunch` when using EE) configuration of the`delete` and `replace` operations **AFTER** the `import_category.observer.category.attribute` and `import_category.observer.category.attribute.update` observer, like

```json
{
  ...
  "observers": [
    {
      "pre-import": [
        "import_category.observer.url.key.and.path",
        "import.observer.attribute.set",
        "import.observer.additional.attribute",
        "import_category.observer.file.upload"
      ]
    },
    {
      "import": [
        "import_category.observer.category",
        "import_category.observer.category.attribute",
        "import_category.observer.category.url.rewrite"
      ]
    }
  ...
}
```

The old `import_category.observer.url.rewrite` has to be moved to a new subject.

## New Subject to Process the Artefacts

Additionally a new subject with the `import_category.observer.url.rewrite` observer that creates/updates the URL rewrites has to be added like

```json
{
  ...

  {
    "id": "import_category_ee.subject.bunch",
    "identifier": "files",
    "prefix": "url-rewrite",
    "observers": [
      {
        "import": [
          "import_category.observer.url.rewrite"
        ]
      }
    ]
  }
  ...
}
```

> It is necessary that the observer is added **AFTER** the subject that contains the `import_category_ee.observer.category.path`, the URL rewrites needs the category paths to be created.
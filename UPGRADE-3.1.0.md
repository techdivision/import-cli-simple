# Upgrade from 3.0.1 to 3.1.0

## Configuration

### Date + Number Converter

The date + number conversion from a source date/number to the expected Magento 2 target format has been refactored.
Therefore a DateConverter and a NumberConverter class has been introduced which replaces the `source-date-format` 
option in the configuration file.

source and number conversion has to be configured on subject level, e. g.

```json
"subjects" : [
  {
    "id": "import.subject.move.files",
    "identifier": "move-files",
    "prefix": "product-import",
    "ok-file-needed": true,,
    "number-converter": {
      "locale": "de_DE"
    },
    "date-converter": {
      "source-date-format": "Y-m-d H:i:s"
    }
  }
]
```

> Whith overriding the ID values of each node, it is possible to replace the converter implementation.

### File Resolver

To make configuration of import file + OK file handling more flexible, the new FileResolver class has been added.

Instead of defining the pattern of the files that has to be imported as well as their appropriate OK file on the
subject level, each subject now has a `file-resolver` node that accepts the following new configuration options

* prefix
* filename
* counter
* suffix
* ok-file-suffix
* element-separator
* pattern-elements

These options allows a very flexible configuration for the pattern which decides which files has to be imported 
or not. You can find more detailed information in the documentation on the M2IF [website](http://www.m2if.com).

It is important that the `prefix` option for all subjects which, until version < 3.1.*, looks like 

```json
"subjects" : [
  {
    "id": "import.subject.move.files",
    "identifier": "move-files",
    "prefix": "product-import",
    "ok-file-needed": true
  }
]
```

has to be replaced with 

```json
"subjects" : [
  {
    "id": "import.subject.move.files",
    "identifier": "move-files",
    "file-resolver": {
      "prefix": "product-import"
    },
    "ok-file-needed": true
  }
]
```

A correct subject configuration up from version 3.1.* will look like 

```json
{
  ...
  "operations" : [
    {
      "name" : "delete",
      "plugins" : [
        {
          "id": "import.plugin.global.data"
        },
        {
          "id": "import.plugin.subject",
          "subjects" : [
            {
              "id": "import.subject.move.files",
              "identifier": "move-files",
              "file-resolver": {
                "prefix": "product-import"
              },
              "ok-file-needed": true
            },
            ...
          ]
        }
      ]
    }
  ]
  ...
}
```
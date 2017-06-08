# M2IF - Simple Console Tool

[![Latest Stable Version](https://img.shields.io/packagist/v/techdivision/import-cli-simple.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-cli-simple) 
 [![Total Downloads](https://img.shields.io/packagist/dt/techdivision/import-cli-simple.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-cli-simple)
 [![License](https://img.shields.io/packagist/l/techdivision/import-cli-simple.svg?style=flat-square)](https://packagist.org/packages/techdivision/import-cli-simple)
 [![Build Status](https://img.shields.io/travis/techdivision/import-cli-simple/master.svg?style=flat-square)](http://travis-ci.org/techdivision/import-cli-simple)
 [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/techdivision/import-cli-simple/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/techdivision/import-cli-simple/?branch=master) 
 [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/techdivision/import-cli-simple/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/techdivision/import-cli-simple/?branch=master)

 
## Installation

To install the Magnento 2 Import Framework, composer is necessary. The framework itself is a set of components 
that provide import functionality for Magento 2. This repository, based on Symfony Console, uses the the package 
[M2IF](https://github.com/techdivision/import) and provides a command line tool with import functionality for 
Magento 2 standard CSV files.

### Install as Composer Project

To install the package as a new project, assuming composer is available, open a console and enter

```sh
$ composer create-project techdivision/import-cli-simple --no-dev --stability=alpha
```

This will clone the repository from the internal Gitlab and install the M2IF, that's all.

### Install as Composer Library

The second option will be the installation as a Composer library. For example, if you want to deliver it with
your Magento 2 project, simply add

```json
{
  "require": {
    "techdivision/import-cli-simple" : "1.0.0-beta3"
  }
}
```

to your Magento 2 composer.json file. Then run

```sh
$ composer update
```

from your Magento 2 root directory and your're all setup.

### Use as PHAR

The last, but for sure not the worst installation option, is to download the latest PHAR from our 
[Github](https://github.com/techdivision/import-cli-simple/releases) release page, e. g. with `wget`

```sh
$ wget https://github.com/techdivision/import-cli-simple/releases/download/1.0.0-alpha56/import-cli-simple.phar
```

To install globally put `import-cli-simple.phar` in `/usr/bin`, e. g.

```sh
$ sudo chmod +x import-cli-simple.phar && mv import-cli-simple.phar /usr/bin/import-cli-simple
```

Now you can use it just like `import-cli-simple`.

## Dependency Injection

M2IF uses the [Symonfy DI Container](http://symfony.com/doc/current/components/dependency_injection.html) to
compose the application. Therefore each library provides the necessary DI configuration files in the directory
`symfony/Resources/config/services.xml`. On application startup, the library files will be parsed and
depending on the used Magento Edition, the apropriate classes will be injected.

> For [configuration](#configuration) the symfony service IDs will be used instead of the real class names.

## Configuration

The necessary configuration has to be defined in a simple JSON file. An example that imports the Magento 2 
sample data in a Magento 2 CE version 2.1.2 can be found in our [examples](projects/sample-data/ce/212/conf/techdivision-import.json).

If **NO** configuration file (option `--configuration`) has been specified, the default one, defined by the
executed command will be used. In case of the `import:products`, the configuration file provided by the
library `techdivision/import-product` in the directory `etc/techdivision-import.json` will be used. This
default configuration contains **NO** database configuration and **NO** image directory.

The database configuration can be specified by the commandline options, but if images should be imported.

> If images should be imported, a custom configuration file with the paths to the image files has to be specified.

The import command itself supports a argument as well as several options.

### Arguments

The following configuration arguments are available:

| Argument             | Description                                                     | Default value |
|:---------------------|:----------------------------------------------------------------|:--------------|
| operation            | Specify the operation name to execute, either one of add-update, replace or delete | n/a |

### Options

The following configuration options are available:

| Option               | Description                                                     | Default value |
|:---------------------|:----------------------------------------------------------------|:--------------|
| --configuration      | Specify the pathname to the configuration file to use | `./vendor/techdivision/import-product/etc/techdivision-import.json` |
| --pid-filename       | The explicit PID filename to use | `<system-temp-dir>/importer.pid` |
| --system-name        | The system name to be used (will added to the mail subject, if mails are configured) | The hostname |
| --installation-dir   | The Magento installation directory to which the files has to be imported | The actual working directory |
| --entity-type-code   | The Magento entity type code, **MUST** be one of `catalog_product` or `catalog_category`  | n/a |
| --source-dir         | The directory that has to be watched for new files | n/a |
| --target-dir         | The target directory with the files that has been imported | n/a |
| --archive-dir        | The directory with the archived files that has been imported | n/a |
| --magento-edition    | The Magento edition to be used, either one of CE or EE | n/a |
| --magento-version    | The Magento version to be used, e. g. 2.1.2 | n/a |
| --source-date-format | The date format used in the CSV file(s) | n/a |
| --use-db-id          | The ID of the database to use, if not specified, the database with the default flag will be used | n/a |
| --db-pdo-dsn         | The DSN used to connect to the Magento database where the data has to be imported, e. g. `mysql:host=127.0.0.1;dbname=magento` | n/a |
| --db-username        | The username used to connect to the Magento database | n/a |
| --db-password        | The password used to connect to the Magento database | n/a |
| --debug-mode         | The flag to activate the debug mode | `false` |
| --log-level          | The log level to use (see Monolog documentation for further information) | `info` |

Beside the `configuration` option, all options can and **SHOULD** be defined in the configuration file. The 
commandline options should only be used to override these values in some circumstances.

If the `configuration` option has **NOT** been specified, the system tries to locate the Magento Edition, based
on the specified `installation-dir` option. If the `installation-dir` option **IS** specified explictly, and the
directory is a valid Magento root directory, the application tries to load database credentials from the 
`app/etc/env.php` script, so it is **NOT** necessary to specify a database configuration, nor in the configuration 
file or as commandline parameter.

### Configuration File

The configuration file **MUST** be in JSON format. Beside itself, all necessary configuration options/arguments
that can be passed on on the commandline, can and **SHOULD** be defined in the configuration file, instead.

The structure is separated into a general configuration section, the database configuration and the configuration
for the available operations.

```json
{
  "magento-edition": "CE",
  "magento-version": "2.1.2",
  "operation-name" : "replace",
  "installation-dir" : "/var/www/magento",
  "databases" : [ ... ],
  "loggers" : [ ... ],
  "operations" : { ... }
}
```
### Extend the M2IF - Simple Console Tool

In more complex projects, it'll we possible, that addional libraries are necessary. As the M2IF - Simple Console Tool
uses a Symfony DI container, it is necessary to register the additional library by adding it to the configuration
file. Depending on how the M2IF - Simple Console Tool has been installed, there a two options.

> Whenever you write an extension library do NOT forget to provide the Symfony DI configuration.

#### Extension Libraries

Assuming, that the M2IF - Simple Console Tool has been installed as Composer library, together with a Magento 2
installation, the simplest way to register an additional extension will be adding it as a extension library like

```json
"extension-libraries" : [
  "techdivision/import-product-magic360"
]
```

> This is only possible, if the additional library uses the same Composer autoloader as M2IF - Simple Console Tool does.

#### Additional Vendor Directories

Assuming, that the M2IF - Simple Console Tool PHAR archive will be used, it is necessary, that the Composer class loader 
of the additional library vendor directory will be added like

```json
"additional-vendor-dirs" : [
  {
    "vendor-dir" : "target/vendor",
    "libraries": [
      "techdivision/import-product-magic360"
    ]
  }
]
```

This register's the class loader of specified vendor directory and parse's the libraries for the necessary DI
configuration files.

### Database

The configuration allows the registration of multiple databases like

```json
"databases": [
  {
    "id": "local",
    "default": false,
    "pdo-dsn": "mysql:host=127.0.0.1;dbname=appserver_magento2_ee212",
    "username": "magento",
    "password": "eraZor"
  },
  {
    "id": "remote",
    "default": true,
    "pdo-dsn": "mysql:host=127.0.0.130;dbname=appserver_magento2_ee212",
    "username": "magento",
    "password": "eraZor"
  }
]
```

Depending whether the commandline option `--use-db-id` and the specified value, the database with the given ID will
be used. If the commandline options is **NOT** specified, the one with the  flag `"default": true` will be used, if
not found, the first configured database will be use.

If a value for the commandline option `--db-pdo-dsn` has been specified, the `--use-db-id` option will be ignored
and the given DSN value will be used for database connection instead. Additionally the credentials, by using the
`--db-username` and `--db-password` options also needs to be specified.

### Loggers

M2IF uses [Monolog](https://github.com/Seldaek/monolog) to provide the basic logging functionality. Therefore, at 
least one logger instance is necessary. By default, if no logger has been configured, a system logger will be 
instanciated, that writes log messages to the error log that has been configured in the `php.ini` file of the used
PHP installation.

To add additional loggers, or override the default one with name `system, the configuration file can be extended 
like

```json
"loggers": [
  {
    "name": "system",
    "channel-name": "logger/system",
    "type": "Monolog\\Logger",
    "handlers": [
      {
        "type": "Monolog\\Handler\\ErrorLogHandler",
        "params" : [
          {
            "message-type" : 0,
            "log-level" : "debug"
          }
        ]
      }
    ]
  },
  {
    "name": "mail",
    "channel-name" : "logger/mail",
    "type" : "Monolog\\Logger",
    "handlers" : [
      {
        "type": "Monolog\\Handler\\SwiftMailerHandler",
        "params" : [
          {
            "log-level" : "error",
            "bubble" : false
          }
        ],
        "swift-mailer" : {
          "factory" : "TechDivision\\Import\\Utils\\SwiftMailer\\SmtpTransportMailerFactory",
          "mailer-factory" : "\\Swift_Mailer",
          "params" : [
            {
              "to" : "info@my-domain.tld",
              "from" : "info@my-domain.tld",
              "subject": "Something Went Wrong",
              "content-type" : "text/plain"
            }
          ],
          "transport" : {
            "transport-factory" : "\\Swift_SmtpTransport",
            "params" : [
              {
                "smtp-host" : "my-domain.tld",
                "smtp-port" : 25,
                "smtp-security" : "tls",
                "smtp-auth-mode" : "LOGIN",
                "smtp-username" : "your-username",
                "smtp-password" : "your-password"
              }
            ]
          }
        }
      }
    ]
  }
]
```

This will override the system logger, as the name is `system`, and set the default log level to **debug**.

### Operations

A operation reflects an import command like the `delete` operation and combines the necessary functionality as as 
simple container, that allows to have a custom plugin configuration. Usually, most of the operations are at least 
build out of the tree plugins

* TechDivision\Import\Plugins\GlobalDataPlugin
* TechDivision\Import\Plugins\SubjectPlugin
* TechDivision\Import\Plugins\ArchivePlugin

Each plugin usually implements a specific functionality like the `GlobalDataPlugin` that pre-loads the global data 
like attributes or attribute sets. Other plugins like the `SubjectPlugin` are again only a container that can be 
configured with several subjects, that'll be executed synchronously for each matching file a subject can find.

The example above shows a short excerpt of a complete configuration file and should give a impression how the 
`delete` operation for products is configured by default.

The `GlobalDataPlugin` loads the global data that'll be needed in other plugins into the memory. This prevents
other plugins to load these data again and again and avoids unnecessary database traffic therfore. 

The next plugin, called `SubjectPlugin`, is configured with two subject. The `MoveFilesSubject` simply moves the CSV 
files into a temporary folder, where the `BunchSubject` starts to import them into the database.

Finally, the `ArchivePlugin` archives the imported files additionally artefacts into a ZIP archive and moves it to 
the configured archive directory.

```json
{
  "magento-edition": "CE",
  "magento-version": "2.1.2",
  "operation-name" : "replace",
  "installation-dir" : "/var/www/magento",
  "source-dir": "projects/sample-data/tmp",
  "target-dir": "projects/sample-data/tmp",
  "source-date-format": "n/d/y, g:i A",
  "archive-artefacts" : false,
  "archive-dir" : "archive",
  "debug-mode" : false,
  "ignore-pid" : false,
  "pid-filename" : "projects/sample-data/tmp/importer.pid",
  "databases" : [ ... ],
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
              "prefix": "magento-import",
              "ok-file-needed": true
            },
            {
              "id": "import_product.subject.bunch",
              "identifier": "files",
              "prefix": "magento-import",
              "observers": [
                {
                  "import": [
                    "import_product.observer.clear.product"
                  ]
                }
              ]
            }
          ]
        },
        {
          "id": "import.plugin.archive"
        }
      ]
    }
  ]
}
```

Most of the available configuration options has to be specified on the subject level, which is nested under the
plugins.

### Plugins

Plugins are usually used to implement the workflow itself and are not bound to a special context like subjects
that are invoked on each matching CSV file, found in the configured source directory. As shown in the example
above, the plugins can be used to implement functionality that has to be invoked before or after the data has
been imported from the CSV files.

```json
"plugins" : [
  {
    "id" : "import.plugin.subject",
    "subjects" : [ ... ]
  }
]
```

### Subjects

The `SubjectPlugin` is the plugin that provides the real import functionality. It can be configured with an
endless number of subjects which invokes the configured observers on each line of a CSV file, extracts the 
data and writes it to the database, if needed.

```json
"subjects" : [
  {
    "id" : "import_product.subject.bunch",
    "identifier" : "files",
    "prefix" : "magento-import",
    "observers" : [ ... ],
    "callbacks" : [ ... ]
  }
]
```

### Observers

Observers provides the functionality of a subject **ON ROW LEVEL** and need to be registered for a subject by 
a adding them to the list of observers like

```json
"observers" : [
  {
    "import" : [
      "import_product.observer.clear.product"
    ]
  }
]
```

### Callbacks

Callbacks can be used to transform values, found in the CSV file into the necessary types that needs to be stored
into the database. For example, the default Magento 2 CSV format allows the values

* `Catalog`
* `Search`
* `Catalog, Search`
* `Not Visible Individually`

for the column `visibility`. These values can not be stored in the appropriate database column, as this expects
integer values. Therefore, a callback can be use to transform the string into the correct integer value, in this
case the class `TechDivision\\Import\\Product\\Callbacks\\VisibilityCallback`.

By default, the necessary callbacks to transform the Magento 2 standard attributes found in the CSV file are 
already defined. When a new, user defined attribute will be added, e. g. with a setup script, the M2IF tries to
find the best matching callback, depending on the `frontend_input` value of the attribute. Actually M2IF comes
whith callbacks for

* `select`
* `multiselect`
* `boolean`

`frontend_input` types. Callbacks for other input types will be part of upcoming versions, but can always be
implemented by the developers using M2IF in their project. To register a custom callback, it has to be added
to the array with the callbacks of a subject, like

```json
"callbacks": [
  {
    "visibility": [
      "import_product.callback.visibility"
    ]
  }
]
```

> Please be aware, that a custom callback will **REPLACE** the default callback and will **NOT** be appended!

## Operations

As well as the Magento 2 standard import functionality, M2IF will provide 3 different import operations:

| Operation                 | Description
|:--------------------------|:-----------------------------------------------------------------------------------|
| add-update                | New product data is added to the existing product data for the existing entries in the database. All fields except sku can be updated. New tax classes that are specified in the import data are created automatically. New SKUs that are specified in the import file are created automatically. |
| replace                   | The existing product data is replaced with new data. If a SKU in the import data matches the SKU of an existing entity, all fields, including the SKU are deleted, and a new record is created using the CSV data. An error occurs if the CSV file references a SKU that does not exist in the database. |
| delete                    | Any entities in the import data that already exist in the database are deleted from the database. Delete ignores all columns in the import data, except for SKU. You can disregard all other attributes in the data. An error occurs if the CSV file references a SKU that does not exist in the database. |

> Exercise caution when replacing data because the existing product data will be completely cleared and all 
> references in the system will be lost.

## Preparation

To run the example import, it is necessary to get a copy of the Magento 2 sample data, that can be cloned 
from Github, assumed you're in the root folder of this repository, by invoking

```sh
$ git clone https://github.com/magento/magento2-sample-data.git projects/sample-data/magento2-sample-data
```

on the command line.

## Bunches

The import is able to handle bunches. In general this is a functionality that will only make sense in a
multithreaded or multiprocessed environment where the bunches can be imported in parallel. In this case,
it should only give the developer an idea, on how a multiprocessed functionality can be implemented.

A bunch is a CSV file which is only a part of a complete import. It doesn't matter, what a kind of data a
bunch contains, as the importer handles the data in the necessary order. This means, that the first step 
is to import all simple products found in a bunch. After that, information like the created entity IDs 
related with the imported SKUs, which is necessary to import all other product data (Bunches, Configurables, 
Images, Related etc.) will be shared, so it'll be possible to import these data step-by-step, but each 
step also in parallel.

To split a import into multiple bunches, the bunched files **MUST** follow these pattern:

1. The prefix has to equal, e. g. `magento-product-import`
2. The prefix has to be followed by an underscore `_`
3. A random number of alphanumeric charaters has to follow
4. These characters has also to be followed by an underscore `_`
5. Finally, each bunch **MUST** have a sequential number, followed by `.csv`

For example, the following files will be imported as a bunch:

* `tmp/magento-import_20170203-1234_01.csv`
* `tmp/magento-import_20170203-1234_02.csv`
* `tmp/magento-import_20170203-1234_03.csv`
* `tmp/magento-import_20170203-1234_04.csv`

When starting the import process by invoking the apropriate command, these files will be imported like one
file. It is **NOT** necessary to invoke the importer four times.

## Running the Import

The command doesn't implement any directory clean-up or archiving functionality, what means that the files
have to copied to the source directory specified for the subjects. Assuming a Magento 2 CE 2.1.2 instance, 
with sample data installed, is available under `/var/www/magento` the configuration file, as well as the
CSV files, can be found under `projects/sample-data/ce/212`.

The command to re-import the sample data including the images, would look like this:

```sh
$ sudo rm -rf projects/sample-data/tmp \ 
    && sudo mkdir projects/sample-data/tmp \
    && sudo cp projects/sample-data/ce/212/data/replace/*.csv projects/sample-data/tmp \
    && sudo bin/import-simple import:products \
       --installation-dir=/var/www/magento \
       --configuration=projects/sample-data/ce/212/conf/techdivision-import.json
```

To make sure, that all old import files will be removed, we'll delete and re-create the directory that contains
the import files `projects/sample-data/tmp`, before.

The import process only starts, when an OK flagfile is available in the same directory where the CSV files are 
located. The naming convention for the OK flagfile **MUST** follow one of these naming conventions

* `<IMPORT-DIRECTORY>/<PREFIX>.ok`
* `<IMPORT-DIRECTORY>/<PREFIX>_<FILENAME>.ok`
* `<IMPORT-DIRECTORY>/<PREFIX>_<FILENAME>_<COUNTER>.ok`

which results in one of

* `projects/sample-data/tmp/magento-import.ok`
* `projects/sample-data/tmp/magento-import_20170203.ok`
* `projects/sample-data/tmp/magento-import_20170203_01.ok`

The flagfile **MUST** contain the name of the CSV files that have to be imported within the next iterations. If
the flagfile would be named `projects/sample-data/tmp/magento-import_20170203-1234.ok` for example and contains
the following lines

```sh
magento-import_20170203-1234_01.csv
magento-import_20170203-1234_02.csv
magento-import_20170203-1234_03.csv
magento-import_20170203-1234_04.csv
```

the importer has to be invoked four times (because the example above is **NO** bunch), whereas on each invovation, 
the next file will be imported and removed from the flagfile.

Have a look in subdirectories of `project/sample-data/*` for a working example.

## Debug Mode

The debug mode provides a more detailed logging output, by automatically setting the Monolog log level to 
`LogLevel::DEBUG` if **NOT** overwritten with the commandline option `--log-level`. Additionally it ignores

* product category relations to categories that not exists 
* product links (related, upsell, crosssell, etc.) for SKUs which are **NOT** available
* configurable products for SKUs which are **NOT** available or available more than one time

but logs these issues as warnings to the console.

When the debug mode has been enabled, missing attribute option values will **NOT** throw an exception, instead
they will logged and put on an internal stack. If the [MissingOptionValuesPlugin](https://github.com/techdivision/import#missing-option-values) 
has been enabled, a CSV file with the missing option values will be created in the temporary import folder. If 
a Swift Mailer has been enabled by the plugin configuration, the CSV file will be sent to the given mail addresses.

This will help developers to test imports with partially invalid CSV files which do **NOT** break data consistency.

## Running Parallel Imports

To avoid unwanted behaviour, only one import process can be started at a time. To make sure, that only one
process is running, a PID file in the system's temporary directory (`sys_get_temp_dir()`) is created which
contains the UUID of the actual import process. After the import process has been finished, the file will
be deleted and a new process can be started.

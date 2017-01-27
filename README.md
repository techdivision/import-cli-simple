# M2IF - Simple Console

## Installation

To install the Magnento 2 Import Framework, composer is necessary. The framework itself is a set of components 
that provide import functionality for Magento 2. This repository, based on Symfony Console, uses the the package 
[M2IF](https://github.com/techdivision/import) and provides a command line tool with import functionality for 
Magento 2 standard CSV files.

To install the package, assuming composer is available, open a console and enter

```sh
$ composer create-project techdivision/import-cli-simple --no-dev --stability=alpha
```

This will clone the repository from the internal Gitlab and install the M2IF, that's all.

## Configuration

The necessary configuration has to be defined in a simple JSON file. An example that imports the Magento 2 
sample data in a Magento 2 CE version 2.1.2 can be found in `example/ce/212/conf/techdivision-import.json`

The configuration file (option `--configuration`) **MUST** to be specified whenever data has to be imported,
as it contains the operations, subjects and callbacks configuration.

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
| --configuration      | Specify the pathname to the configuration file to use | ./techdivision-import.json |
| --installation-dir   | The Magento installation directory to which the files has to be imported | n/a |
| --source-dir         | The directory that has to be watched for new files | n/a |
| --target-dir         | The target directory with the files that has been imported | n/a |
| --utility-class-name | The utility class name with the Magento edition/version specific SQL statements | n/a |
| --magento-edition    | The Magento edition to be used, either one of CE or EE | n/a |
| --magento-version    | The Magento version to be used, e. g. 2.1.2 | n/a |
| --source-date-format | The date format used in the CSV file(s) | n/a |
| --db-pdo-dsn         | The DSN used to connect to the Magento database where the data has to be imported, e. g. mysql:host=127.0.0.1;dbname=magento | n/a |
| --db-username        | The username used to connect to the Magento database | n/a |
| --db-password        | The password used to connect to the Magento database | n/a |
| --debug-mode         | The flag to activate the debug mode | false |
| --log-level          | The log level to use (see Monolog documentation for further information) | info |

All values can and **SHOULD** be defined in the configuration file. The commandline options should only be 
used to override these values in some circumstances.

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
  "utility-class-name" : "TechDivision\\Import\\Utils\\SqlStatements",
  "database" : { ... },
  "operations" : { ... }
}
```

The operations are simply a container, that allows to have a custom subject configuration for each operation.
This makes sense, as the `delete` operation only needs the `ClearProductObserver`, that removes the product
with the SKU found in each row of the CSV file as well as all it's relations. The `delete` operation therefore
is small a configuration variation out of the available subjects, observers and callbacks and looks like this

```json
{
  "magento-edition": "CE",
  "magento-version": "2.1.2",
  "operation-name" : "replace",
  "installation-dir" : "/var/www/magento",
  "utility-class-name" : "TechDivision\\Import\\Utils\\SqlStatements",
  "database" : { ... },
  "operations" : [
    {
      "name" : "delete",
      "subjects" : [
        {
          "class-name": "TechDivision\\Import\\Product\\Ee\\Subjects\\EeBunchSubject",
          "processor-factory" : "TechDivision\\Import\\Cli\\Services\\EeProductBunchProcessorFactory",
          "utility-class-name" : "TechDivision\\Import\\Product\\Ee\\Utils\\SqlStatements",
          "identifier": "files",
          "source-date-format": "n/d/y, g:i A",
          "source-dir": "projects/sample-data/tmp",
          "target-dir": "projects/sample-data/tmp",
          "prefix": "magento-import",
          "observers": [
            {
              "import": [
                "TechDivision\\Import\\Product\\Observers\\ClearProductObserver"
              ]
            }
          ]
        }
      ]
    }
  ]
}
```

Most of the available configuration options can be specified on the subjects itself, which are nested under the
operations.

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

## Debug Mode

The debug mode provides a more detailed logging output, by automatically setting the Monolog log level to 
`LogLevel::DEBUG` if **NOT** overwritten with the commandline option `--log-level`. Additionally it ignores

* product category relations to categories that not exists 
* product links (related, upsell, crosssell, etc.) for SKUs which are **NOT** available

but logs these issues as warnings to the console. This will help developers to test imports with partially
invalid CSV files which do **NOT** break data consistency.
# M2IF - Simple Console

## Installation

To install the Magnento 2 Import Framework, composer is necessary. The framework itself is a set of components
that provide import functionality for Magento 2. The package `techdivision/import-cli-simple`, based on Symfony 
Console, uses de M2IF and provides a command line tool with import functionality for Magento 2 standard CSV 
files.

To install the package, assuming composer is available, open a console and enter

```sh
$ composer create-project techdivision/import-cli-simple --no-dev --stability=alpha
```

This will clone the repository from the internal Gitlab and install the M2IF, that's all.

## Configuration

The necessary configuration has to be defined in a simple JSON file. An example that imports the Magento 2 
sample data in a Magento 2 CE version 2.1.2 can be found in `example/ce/212/conf/techdivision-import.json`

The configuration file (option `--configuration`) **MUST** to be specified whenever data has to be imported.

The import command allows arguments (actually only one) as well as options.

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
| --utility-class-name | The utility class name with the Magento edition/version specific SQL statements | n/a |
| --magento-edition    | The Magento edition to be used, either one of CE or EE | n/a |
| --magento-version    | The Magento version to be used, e. g. 2.1.2 | n/a |
| --source-date-format | The date format used in the CSV file(s) | n/a |
| --db-pdo-dsn         | The DSN used to connect to the Magento database where the data has to be imported, e. g. mysql:host=127.0.0.1;dbname=magento | n/a |
| --db-username        | The username used to connect to the Magento database | n/a |
| --db-password        | The password used to connect to the Magento database | n/a |

All values can and **SHOULD** be defined in the configuration file. The commandline options should only be 
used to override these values in some circumstances.

## Operations

As well as the Magento 2 standard import functionality, M2IF will provide 3 different import operations:

| Operation                 | Description
|:--------------------------|:-----------------------------------------------------------------------------------|
| add-update (**NOT YET**)  | New product data is added to the existing product data for the existing entries in 
the database. All fields except sku can be updated. New tax classes that are specified in the import data are 
created automatically. New SKUs that are specified in the import file are created automatically. |
| replace                   | The existing product data is replaced with new data. If a SKU in the import data 
matches the SKU of an existing entity, all fields, including the SKU are deleted, and a new record is created 
using the CSV data. An error occurs if the CSV file references a SKU that does not exist in the database. |
| delete                    | Any entities in the import data that already exist in the database are deleted from the 
database. Delete ignores all columns in the import data, except for SKU. You can disregard all other attributes 
in the data. An error occurs if the CSV file references a SKU that does not exist in the database. |

> Exercise caution when replacing data because the existing product data will be completely cleared and all 
> references in the system will be lost.

## Preparation

To run the example import, it is necessary to get a copy of the Magento 2 sample data, that can be cloned 
from Github, assumed you're in the root folder of this repository, by invoking

```sh
$ git clone https://github.com/magento/magento2-sample-data.git example/magento2-sample-data
```

on the command line.

## Running the Import

The command doesn't implement any directory clean-up or archiving functionality, what means that the files
have to copied to the source directory specified for the subjects. When running the example, this will be
`example/tmp`. To make sure, that all old import files will be removed, we'll delete and re-create the 
directory before.

```sh
$ sudo rm -rf example/tmp \ 
    && sudo mkdir example/tmp \
    && sudo cp example/ce/212/data/*.csv example/tmp \
    && sudo bin/import-simple import:products \
       --installation-dir=/var/www/magento \
       --configuration=example/ce/212/conf/techdivision-import.json
```
# M2IF - Simple Console

## Installation

To install the Magnento 2 Import Framework, composer is necessary. The framework itself is a set of components
that provide import functionality for Magento 2. The package `techdivision/import-cli-simple`, based on Symfony 
Console, uses de M2IF and provides a command line tool with import functionality for Magento 2 standard CSV 
files.

To install the package, assuming composer is available, open a console and enter

```sh
$ git clone https://github.com/techdivision/import-cli-simple.git
$ cd import-cli-simple
$ composer install
```

This will clone the repository from the internal Gitlab and install the M2IF, that's all.

## Configuration

The necessary configuration has to be defined in a simple JSON file. An example that imports the Magento 2 
sample data in a Magento 2 CE version 2.1.2 can be found in `example/ce/212/conf/techdivision-import.json`

The configuration file **MUST** to be specified whenever data has to be imported.

The following configuration options are available:

| Option               | Description                                                     | Default value |
|:---------------------|:----------------------------------------------------------------|:--------------|
| --configuration      | Specify the pathname to the configuration file to use | example/ce/212/conf/techdivision-import.json |
| --installation-dir   | The Magento installation directory to which the files has to be imported | n/a |
| --utility-class-name | The utility class name with the Magento edition/version specific SQL statements | n/a |
| --magento-edition    | The Magento edition to be used, either one of CE or EE | n/a |
| --magento-version    | The Magento version to be used, e. g. 2.1.2 | n/a |
| --source-date-format | The date format used in the CSV file(s) | n/a |
| --db-pdo-dsn         | The DSN used to connect to the Magento database where the data has to be imported, e. g. mysql:host=127.0.0.1;dbname=magento |
| --db-username        | The username used to connect to the Magento database | n/a |
| --db-password        | The password used to connect to the Magento database | n/a |

All values can and **SHOULD** be defined in the configuration file. The commandline options should only be 
used to override these values in some circumstances.

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
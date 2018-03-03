<?php

/**
 * TechDivision\Import\Cli\FileMerger
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */

namespace TechDivision\Import\Cli;

/**
 * Prototype for a class that merges multiple CSV files.
 *
 * This class SHOULD only be used for testing purposes, as it reads the content of all files
 * into the memory and merges them. This results in huge memory consumption and has to be
 * refactored.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class FileMerger
{

    /**
     * The columns that has to be ignored when merging the files
     *
     * @var array
     */
    protected $ignoreColumns = array(
        'base_image',
        'base_image_label',
        'small_image',
        'small_image_label',
        'thumbnail_image',
        'thumbnail_image_label',
        'swatch_image',
        'swatch_image_label',
        'additional_images',
        'additional_image_label'
    );

    /**
     * The array with the headers.
     *
     * @var array
     */
    protected $headers = array();

    /**
     * The array with the rows.
     *
     * @var array
     */
    protected $rows = array();

    /**
     * Initialize the the merger instance.
     *
     * @param string $srcDir       The source directory
     * @param string $destFilename The target filename
     *
     * @return void
     */
    public function merge($srcDir, $destFilename)
    {

        // load the CSV files from the source directory
        $srcFilenames = glob(sprintf('%s/*.csv', $srcDir));

        // iterate over all found CSV files
        foreach ($srcFilenames as $srcFilename) {
            // open the CSV file
            if ($fh = fopen($srcFilename, 'r')) {
                // log a message
                error_log(sprintf('Now open file "%s"', $srcFilename));
                // initialize the row counter
                $rowCounter = 0;

                // read the lines
                while (($data = fgetcsv($fh, 0, ",")) !== false) {
                    // raise the row counter
                    $rowCounter++;

                    // initialize the headers, if we're on the first line
                    if ($rowCounter === 1) {
                        $this->headers = array_flip($data);
                        continue;
                    }

                    // iterate over the headers
                    foreach ($this->headers as $headerName => $headerValue) {
                        // set a default price of 1, if the column has NO price
                        if ($headerName === 'price' && $data[$headerValue] !== '') {
                            $data[$headerValue] = 1;
                        }

                        // query whether or not the field has to be ignored
                        if (in_array($headerName, $this->ignoreColumns)) {
                            $this->rows[$data[$this->headers['sku']]][$data[$this->headers['store_view_code']]][$headerValue] = null;
                        } elseif ($data[$headerValue] !== '') {
                            $this->rows[$data[$this->headers['sku']]][$data[$this->headers['store_view_code']]][$headerValue] = $data[$headerValue];
                        } elseif ($data[$headerValue] === '' && !isset($this->rows[$data[$this->headers['sku']]][$data[$this->headers['store_view_code']]][$headerValue])) {
                            $this->rows[$data[$this->headers['sku']]][$data[$this->headers['store_view_code']]][$headerValue] = $data[$headerValue];
                        } else {
                            // do not override with empty value
                        }
                    }

                    // log a message with the number of rows processed
                    error_log(sprintf('Successfully processed line "%s", "%d"', $srcFilename, $rowCounter));
                }

                // close the file finally
                fclose($fh);
            }
        }

        // open the destination file
        $fh = fopen(sprintf('%s/%s', $srcDir, $destFilename, 'w'));

        // write the headers
        fputcsv($fh, array_keys($this->headers));

        // write the rows to the target file
        foreach ($this->rows as $skus) {
            foreach ($skus as $storeViewCode) {
                fputcsv($fh, $storeViewCode);
            }
        }

        // close the destination file
        fclose($fh);
    }
}

// Example how to invoke the file merger:
// php -f src/FileMerger.php projects/sample-data/data/products/add-update import-products_20171220-1234_01.csv

// initialize the source directory
$srcDir = __DIR__;

// query whether or not a source directoy has been passed
if (isset($argv[1]) && is_dir($argv[1])) {
    $srcDir = $argv[1];
} else {
    throw new \Exception(sprintf('Please specify a source directory as first argument'));
}

// query whether or not a target filename has been passed
if (!isset($argv[2])) {
    throw new \Exception(sprintf('Please specify a target filename as second argument'));
}

// intialize the instance and merge the files
$merger = new FileMerger();
$merger->merge($srcDir, argv[2]);

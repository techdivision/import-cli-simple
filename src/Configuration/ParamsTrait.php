<?php

/**
 * TechDivision\Import\Cli\Configuration\ParamsTrait
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

namespace TechDivision\Import\Cli\Configuration;

use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\SerializedName;

/**
 * A trait implementation that provides parameter handling.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
trait ParamsTrait
{

    /**
     * The array with the formatter's params.
     *
     * @var array
     * @Type("array")
     */
    protected $params = array();

    /**
     * The delimiter for the params that should be converted into an array.
     *
     * @var string
     * @Type("string")
     * @SerializedName("param-delimiter")
     */
    protected $paramDelimiter = ',';

    /**
     * Return's the parameter delimiter.
     *
     * @return string The delimiter
     */
    public function getParamDelimiter()
    {
        return $this->paramDelimiter;
    }

    /**
     * Return's the array with the params.
     *
     * @return array The params
     */
    public function getParams()
    {

        // initialize the array for the params
        $params = array();

        // prepare the params, e. g. explode them into an array
        if ($paramsAvailable = reset($this->params)) {
            foreach ($paramsAvailable as $paramKey => $paramValue) {
                $params[$paramKey] = $this->getParam($paramKey);
            }
        }

        // return the params
        return $params;
    }

    /**
     * Query whether or not the param with the passed name exists.
     *
     * @param string $name The name of the param to be queried
     *
     * @return boolean TRUE if the requested param exists, else FALSE
     */
    public function hasParam($name)
    {
        return array_key_exists($name, $this->getParams());
    }

    /**
     * Return's the param with the passed name.
     *
     * @param string $name         The name of the param to return
     * @param mixed  $defaultValue The default value if the param doesn't exists
     *
     * @return string The requested param
     * @throws \Exception Is thrown, if the requested param is not available
     */
    public function getParam($name, $defaultValue = null)
    {

        // load the params
        $params = reset($this->params);

        // query whether or not, the param with the passed name is set
        if (is_array($params) && isset($params[$name])) {
            // load the value from the array
            $value = $params[$name];
            // query whether or not, the value contains a comma
            // => if yes, we explode it into an array
            if (stripos($value, $delimiter = $this->getParamDelimiter())) {
                $value = explode($delimiter, $value);
            }

            // return the found value
            return $value;
        }

        // if not, query we query if a default value has been passed
        if ($defaultValue != null) {
            return $defaultValue;
        }

        // throw an exception if neither the param exists or a default value has been passed
        throw new \Exception(sprintf('Requested param %s not available', $name));
    }
}

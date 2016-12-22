<?php

/**
 * TechDivision\Import\Cli\Configuration\Operation
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
use TechDivision\Import\Configuration\OperationInterface;

/**
 * The configuration implementation for the options.
 *
 * @author    Tim Wagner <t.wagner@techdivision.com>
 * @copyright 2016 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/techdivision/import-cli-simple
 * @link      http://www.techdivision.com
 */
class Operation implements OperationInterface
{

    /**
     * The operation's name.
     *
     * @var string
     * @Type("string")
     */
    protected $name;

    /**
     * ArrayCollection with the information of the configured subjects.
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @Type("ArrayCollection<TechDivision\Import\Cli\Configuration\Subject>")
     */
    protected $subjects;

    /**
     * Initialize the operation with the passed name.
     *
     * @param string|null $name The operation name
     */
    public function __construct($name = null)
    {
        if ($name != null) {
            $this->name = $name;
        }
    }

    /**
     * Query's whether or not the passed operation equals this instance.
     *
     * @param \TechDivision\Import\Cli\Configuration\Operation $operation The operation to query
     *
     * @return boolean TRUE if the operations are equal, else FALSE
     */
    public function equals(OperationInterface $operation)
    {
        return strcasecmp($this->getName(), $operation->getName()) === 0;
    }

    /**
     * Return's the operation's name.
     *
     * @return string The operation's class name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return's the ArrayCollection with the operation's subjects.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection The ArrayCollection with the operation's subjects
     */
    public function getSubjects()
    {
        return $this->subjects;
    }

    /**
     * String representation of the operation (the name).
     *
     * @return string The operation name
     */
    public function __toString()
    {
        return $this->getName();
    }
}

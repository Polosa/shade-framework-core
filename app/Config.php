<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Config
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Config
{
    /**
     * Children
     *
     * @var \Shade\Config[]
     */
    protected $children = array();

    /**
     * Value
     *
     * @var mixed
     */
    protected $value = null;

    /**
     * Constructor
     *
     * @param mixed $value
     */
    public function __construct($value = null)
    {
        $this->setValue($value);
    }

    /**
     * Set value
     *
     * @param mixed $value
     *
     * @return \Shade\Config
     */
    public function setValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $childKey => $childValue) {
                $this->children[$childKey] = new self($childValue);
            }
            $this->value = null;
        } else {
            $this->value = $value;
        }
        return $this;
    }

    /**
     * Get value
     *
     * @return mixed
     */
    public function getValue()
    {
        if ($this->children) {
            $value = array();
            foreach ($this->children as $childKey => $child) {
                $childValue = $child->getValue();
                if (isset($childValue)) {
                    $value[$childKey] = $child->getValue();
                }
            }
            return $value ? $value : null;
        } else {
            return $this->value;
        }
    }

    /**
     * Get child
     *
     * @param string $name
     *
     * @return \Shade\Config
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->children)) {
            $this->children[$name] = new self();
        }
        return $this->children[$name];
    }

    /**
     * Set child value
     *
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $this->children[$name] = new self($value);
        $this->value = null;
    }

    /**
     * Check is child set
     *
     * @param string $name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->children[$name]);
    }

    /**
     * Unset child
     *
     * @param string $name
     */
    public function __unset($name)
    {
        unset($this->children[$name]);
    }
}
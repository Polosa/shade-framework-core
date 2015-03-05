<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Nested container
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class NestedContainer
{
    /**
     * Children
     *
     * @var self[]
     */
    protected $children = array();

    /**
     * Value
     *
     * @var mixed
     */
    protected $value;

    /**
     * Constructor
     *
     * @param mixed $value Value
     */
    public function __construct($value = null)
    {
        $this->setValue($value);
    }

    /**
     * Set value
     *
     * @param mixed $value Value
     *
     * @return self
     */
    public function setValue($value)
    {
        if (is_array($value)) {
            foreach ($value as $childKey => $childValue) {
                $this->children[$childKey] = new static($childValue);
            }
            $this->value = null;
        } else {
            $this->value = $value;
            $this->children = array();
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
                    $value[$childKey] = $childValue;
                }
            }
        }
        return !empty($value) ? $value : $this->value;
    }

    /**
     * Get child
     *
     * @param string $name Child name
     *
     * @return self
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->children)) {
            $this->children[$name] = new static();
        }
        return $this->children[$name];
    }

    /**
     * Set child value
     *
     * @param string $name  Child name
     * @param mixed  $value Value
     */
    public function __set($name, $value)
    {
        $this->children[$name] = new static($value);
        $this->value = null;
    }

    /**
     * Check if child set
     *
     * @param string $name Child name
     *
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->children[$name]) && !is_null($this->children[$name]->getValue());
    }

    /**
     * Unset child
     *
     * @param string $name Child name
     */
    public function __unset($name)
    {
        unset($this->children[$name]);
    }

    /**
     * Merge with Container
     *
     * @param self $container Container to merge with
     *
     * @return self
     */
    public function merge($container)
    {
        $resultContainer = clone $this;
        $resultContainer->overwrite($container);
        return $resultContainer;
    }

    /**
     * Overwrite Container's data
     *
     * @param self $container Container to overwrite by
     *
     * @return self
     */
    public function overwrite($container)
    {
        $originalValue = $this->getValue();
        $newValue = $container->getValue();

        if (is_null($newValue)) {
            $resultValue = $originalValue;
        } elseif (is_array($newValue) && is_array($originalValue)) {
            $resultValue = array_replace_recursive($originalValue, $newValue);
        } else {
            $resultValue = $newValue;
        }

        $this->setValue($resultValue);

        return $this;
    }
}
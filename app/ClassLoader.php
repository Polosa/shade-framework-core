<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Class Loader
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class ClassLoader
{
    /**
     * File extension
     */
    const FILE_EXTENSION = '.php';

    /**
     * Namespace of classes
     *
     * @var string
     */
    protected $namespace;

    /**
     * Length of the namespace
     *
     * @var int
     */
    protected $namespaceLength;

    /**
     * Root directory to load classes from
     *
     * @var string
     */
    protected $loadDir;

    /**
     * Load directory consists a subdirectory named as a namespace
     *
     * @var bool
     */
    protected $nsSubdir = false;

    /**
     * Constructor
     *
     * @param string $namespace Namespace of classes
     * @param string $loadDir   Root directory to load classes from
     * @param bool   $nsSubdir  Load directory consists a subdirectory named as a namespace
     */
    public function __construct($namespace, $loadDir, $nsSubdir = false)
    {
        $this->loadDir = $loadDir;
        $this->namespace = $namespace;
        $this->namespaceLength = strlen($namespace);
        $this->nsSubdir = $nsSubdir;
    }

    /**
     * Load class/interface
     *
     * @param string $name Class or interface name
     *
     * @throws \Shade\ClassLoader\Exception
     *
     * @return bool
     */
    public function load($name)
    {
        if (substr($name, 0, $this->namespaceLength + 1) !== $this->namespace.'\\') {
            return false;
        } else {
            $file = $this->getClassFilePath($name);

            if (is_readable($file)) {
                include_once $file;
                if (!class_exists($name, false) && !interface_exists($name, false)) {
                    throw new ClassLoader\Exception(
                        "\"$name\" not found in \"$file\""
                    );
                }
            } else {
                throw new ClassLoader\Exception(
                    "File \"$file\" to load \"$name\" doesn't exist"
                );
            }

            return true;
        }
    }

    /**
     * Get class/interface file path
     *
     * @param $name Class/Interface name
     *
     * @return string
     */
    public function getClassFilePath($name)
    {
        $relativeName = $this->nsSubdir ? $name : substr($name, $this->namespaceLength + 1);
        $lastNsSeparatorPosition = strrpos($relativeName, '\\');
        if ($lastNsSeparatorPosition) {
            $namespace = substr($relativeName, 0, $lastNsSeparatorPosition);
            $className = substr($relativeName, $lastNsSeparatorPosition + 1);
        } else {
            $namespace = '';
            $className = substr($relativeName, $lastNsSeparatorPosition);
        }

        $file = $this->loadDir.'/'.($namespace ? str_replace('\\', '/', $namespace).'/' : '').str_replace('_', '/', $className).self::FILE_EXTENSION;

        return $file;
    }

    /**
     * Register autoload
     *
     * @return bool
     */
    public function registerAutoload()
    {
        return spl_autoload_register(array($this, 'load'), true, true);
    }
}

require_once 'ClassLoader/Exception.php';

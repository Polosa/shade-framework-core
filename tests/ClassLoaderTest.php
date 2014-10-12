<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Class Loader Test
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class ClassLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetClassFilePath()
    {
        $classLoader = new ClassLoader('MyTestApp', '/my/test_app/Dir');

        $file1Path = $classLoader->getClassFilePath('MyTestApp\MyTestClass');
        $this->assertEquals('/my/test_app/Dir/MyTestClass.php', $file1Path);

        $file2Path = $classLoader->getClassFilePath('MyTestApp\MyTestNamespace\MyTestClass');
        $this->assertEquals('/my/test_app/Dir/MyTestNamespace/MyTestClass.php', $file2Path);

        $file3Path = $classLoader->getClassFilePath('MyTestApp\My_Test_Namespace\My_Test_Class');
        $this->assertEquals('/my/test_app/Dir/My_Test_Namespace/My/Test/Class.php', $file3Path);
    }
}

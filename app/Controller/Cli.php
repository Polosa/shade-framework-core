<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\Controller;

use Shade\Request\Virtual as VirtualRequest;
use Shade\Response;

/**
 * Controller "Cli"
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Cli extends \Shade\Controller
{

    /**
     * Index Action
     */
    public function indexAction()
    {
        return $this->render('system/cli/index.phtml');
    }

    /**
     * New Action
     */
    public function newAction()
    {
        $args = $this->getRequest()->getArgv();
        $actionArgs = array_slice($args, 2);
        $config = array(
            'applicationName' => array(
                'value' => 'ShadeApp',
                'description' => 'Specify your application name, e.g. MyApp'
            ),
            'applicationRootPath' => array(
                'value' => './ShadeApp',
                'description' => 'Set path to store your application files'
            ),
        );
        $argIndex = 0;
        $interactiveMode = count($actionArgs) < count($config);
        foreach ($config as $configKey => &$configEntry) {
            if (isset($actionArgs[$argIndex])) {
                $configEntry['value'] = $actionArgs[$argIndex];
            } elseif ($configKey == 'applicationRootPath') {
                $configEntry['value'] = "./{$config['applicationName']['value']}";
            }
            if ($interactiveMode) {
                echo "{$configEntry['description']} or press Enter to use '{$configEntry['value']}': ";
                $userInput = trim(fgets(STDIN));
                if ($userInput) {
                    $configEntry['value'] = $userInput;
                }
            }
            $argIndex++;
        }

        $applicationName = $config['applicationName']['value'];
        $applicationRootPath = $config['applicationRootPath']['value'];
        $response = new Response();

        $appDir = $this->serviceProvider()->getApp()->getAppDir();
        $skeletonTemplatesDir = $appDir.'/skeleton';
        $viewReplace = $this->serviceProvider()->getView('\Shade\View\Replace');
        $classLoaderReflection = new \ReflectionClass('\Composer\Autoload\ClassLoader');
        $autoloadPath = dirname(dirname($classLoaderReflection->getFileName())).'/autoload.php';
        $replaces = array(
            'ShadeApp' => $applicationName,
            '%ShadePath%' => $appDir,
            '%autoloadPath%' => $autoloadPath
        );
        if (file_exists($applicationRootPath)) {
            if (!is_dir($applicationRootPath)) {
                return $response->setContent("'$applicationRootPath' exists and it is not a directory\n");
            }

            if (count(scandir($applicationRootPath)) !== 2) {
                return $response->setContent("Directory '$applicationRootPath' is not empty\n");
            }
        } else {
            if (!mkdir($applicationRootPath, 0775, true)) {
                return $response->setContent("Can't create application directory $applicationRootPath\n");
            }
        }

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($skeletonTemplatesDir, \FilesystemIterator::SKIP_DOTS)) as $fileInfo) {
            $filePath = $fileInfo->getPathname();
            $content = $viewReplace->render($filePath, $replaces);
            $destinationPath = $applicationRootPath.str_replace($skeletonTemplatesDir, '', $filePath);
            $destinationDir = dirname($destinationPath);
            if (!is_dir($destinationDir)) {
                mkdir($destinationDir, 0775, true);
            }
            file_put_contents($destinationPath, $content);
        }

        return $response->setContent("Skeleton application '$applicationName' generated and stored under '$applicationRootPath'");
    }

    /**
     * Run Action
     */
    public function runAction()
    {
        $args = $this->getRequest()->getArgv();
        if (!isset($args[2], $args[3])) {
            $response = new Response();
            $response->setContent("Usage: run MyController myAction [ arg1 arg2 ...]\n");
            return $response;
        }
        $actionArgs = array_slice($args, 4);
        $serviceProvider = $this->serviceProvider();
        $request = new VirtualRequest($serviceProvider, $args[2], $args[3], $actionArgs);
        $response = $this->serviceProvider()->getApp()->execute($request);
        if ($response->getCode() == 404) {
            $response->setContent("Wrong arguments provided\n");
        }
        return $response;
    }
}

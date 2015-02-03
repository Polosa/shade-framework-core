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
use Shade\View\Replace as ViewReplace;

/**
 * Controller "Cli"
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class Cli extends \Shade\Controller
{
    /**
     * Help information
     *
     * @var array
     */
    protected static $help = array(
        'new' => array(
            'description' => 'Generate new Skeleton Application',
            'usage' => 'new [applicationName] [applicationRootPath]',
            'arguments' => array(
                'applicationName' => 'Will be used as a namespace for the new application',
                'applicationRootPath' => 'Path to the new application root',
            ),
        ),
        'run' => array(
            'description' => "Run Controller Action in CLI mode",
            'usage' => 'run controllerClassName actionMethodName [arg1] ... [argN]',
            'arguments' => array(
                'controllerClassName' => 'Fully qualified class name',
                'actionMethodName' => 'Action name',
            ),
        ),
        'help' => array(
            'description' => "Display help for a command",
            'usage' => 'help [command]',
            'arguments' => array(
                'command' => 'CLI command name',
            ),
        ),
    );

    /**
     * Fallback help
     *
     * @var array
     */
    protected static $fallbackHelp = array(
        'description' => 'Shade Framework CLI',
        'usage' => 'command [arguments]',
        'arguments' => array(
            'new' => 'Generate new Skeleton Application',
            'help' => 'Display help for a command',
            'run' => "Run Controller Action in CLI mode",
        ),
    );

    /**
     * Index Action
     *
     * @return \Shade\Response
     */
    public function indexAction()
    {
        return $this->help();
    }

    /**
     * Help Action
     *
     * @return \Shade\Response
     */
    public function helpAction()
    {
        $args = $this->getRequest()->getArgv();
        $command = !empty($args[2]) ? $args[2] : 'help';

        return $this->help($command);
    }

    /**
     * New Action
     *
     * @param string      $appDir      Application directory
     * @param ViewReplace $viewReplace View "Replace"
     *
     * @return \Shade\Response
     */
    public function newAction($appDir, ViewReplace $viewReplace)
    {
        $args = $this->getRequest()->getArgv();
        $actionArgs = array_slice($args, 2);
        $config = array(
            'applicationName' => array(
                'value' => 'ShadeApp',
                'description' => 'Specify your application name, e.g. MyApp',
            ),
            'applicationRootPath' => array(
                'value' => './ShadeApp',
                'description' => 'Set path to store your application files',
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

        $skeletonTemplatesDir = $appDir.'/skeleton';
        $classLoaderReflection = new \ReflectionClass('\Composer\Autoload\ClassLoader');
        $autoloadPath = dirname(dirname($classLoaderReflection->getFileName())).'/autoload.php';
        $replaces = array(
            'ShadeApp' => $applicationName,
            '%ShadePath%' => $appDir,
            '%autoloadPath%' => $autoloadPath,
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

        return $response->setContent("Skeleton application '$applicationName' generated and stored under '$applicationRootPath'\n");
    }

    /**
     * Run Action
     *
     * @return \Shade\Response
     */
    public function runAction()
    {
        $args = $this->getRequest()->getArgv();
        if (!isset($args[2], $args[3])) {
            return $this->help('run');
        }
        $actionArgs = array_slice($args, 4);
        $request = new VirtualRequest($args[2], $args[3], $actionArgs);
        $response = $this->dispatch($request);
        if ($response->getCode() !== 200) {
            $response->setContent("Wrong arguments provided\n");
        }

        return $response;
    }

    /**
     * Help
     *
     * @param string|null $command
     *
     * @return \Shade\Response
     */
    protected function help($command = null)
    {
        if ($command && isset(self::$help[$command])) {
            $data = self::$help[$command];
        } else {
            $data = self::$fallbackHelp;
        }

        return $this->render('system/cli/help.phtml', $data);
    }
}

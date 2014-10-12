<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade\Controller;

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
        $args = $this->getRequest()->getArgv();
        if (isset($args[1]) && $args[1] == 'new') {
            $skeletonPathIsExistingFile = false;
            $errorWhenCreatingDir = false;
            if (count($args) == 4) {
                $appDir = $this->serviceProvider()->getApp()->getAppDir();
                $skeletonTemplatesDir = $appDir.'/skeleton';
                $skeletonName = $args[2];
                $skeletonPath = $args[3];
                $viewReplace = $this->serviceProvider()->getView('\Shade\View\Replace');
                $classLoaderReflection = new \ReflectionClass('\Composer\Autoload\ClassLoader');
                $autoloadPath = dirname(dirname($classLoaderReflection->getFileName())).'/autoload.php';
                $replaces = array(
                    'ShadeApp' => $skeletonName,
                    '%ShadePath%' => $appDir,
                    '%autoloadPath%' => $autoloadPath
                );
                if (file_exists($skeletonPath)) {
                    $skeletonPathIsExistingFile = !is_dir($skeletonPath);
                } else {
                    $errorWhenCreatingDir = !mkdir($skeletonPath, 0775, true);
                }

                if (!$skeletonPathIsExistingFile && !$errorWhenCreatingDir) {
                    foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($skeletonTemplatesDir, \FilesystemIterator::SKIP_DOTS)) as $fileInfo) {
                        $filePath = $fileInfo->getPathname();
                        $content = $viewReplace->render($filePath, $replaces);
                        $destinationPath = $skeletonPath.str_replace($skeletonTemplatesDir, '', $filePath);
                        $destinationDir = dirname($destinationPath);
                        if (!is_dir($destinationDir)) {
                            mkdir($destinationDir, 0775, true);
                        }
                        file_put_contents($destinationPath, $content);
                    }
                }
            } else {
                $wrongArgs = true;
            }

            return $this->render('system/cli/new.phtml', array(
                'skeletonName' => !empty($skeletonName) ? $skeletonName : null,
                'skeletonPath' => !empty($skeletonPath) ? $skeletonPath : null,
                'skeletonPathIsExistingFile' => $skeletonPathIsExistingFile,
                'errorWhenCreatingDir' => $errorWhenCreatingDir,
                'wrongArgs' => !empty($wrongArgs),
                'cliFileName' => $args[0],
            ));
        } else {
            return $this->render('system/cli/cli_layout.phtml');
        }
    }
}

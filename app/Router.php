<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Router
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
abstract class Router
{
    /**
     * Default Web Controller
     */
    const DEFAULT_WEB_CONTROLLER = 'Index';

    /**
     * Default Cli Controller
     */
    const DEFAULT_CLI_CONTROLLER = 'Cli';

    /**
     * Base Controller
     */
    const BASE_CONTROLLER = 'Controller';

    /**
     * Default Action
     */
    const DEFAULT_ACTION = 'indexAction';

    /**
     * Action suffix
     */
    const ACTION_SUFFIX = 'Action';

    /**
     * Service Provider
     *
     * @var \Shade\ServiceProvider
     */
    protected $serviceProvider;

    /**
     * Base controller class
     *
     * @var string
     */
    protected $baseControllerClass;

    /**
     * Controller path
     *
     * @var string
     */
    protected $baseControllerPath;

    /**
     * Default controller
     *
     * @var string
     */
    protected $defaultController;

    /**
     * Clean URLs
     *
     * @var bool
     */
    protected $cleanUrlsEnabled = false;

    /**
     * Constructor
     *
     * @param \Shade\ServiceProvider $serviceProvider
     *
     * @throws \Shade\Exception
     */
    public function __construct(ServiceProvider $serviceProvider)
    {
        if (!$serviceProvider->inProgress()) {
            throw new Exception('Router can be only requested from ServiceProvider');
        }
        $this->serviceProvider = $serviceProvider;
        $app = $serviceProvider->getApp();
        $this->baseControllerClass = '\\'.$app->getAppNamespace().'\\'.self::BASE_CONTROLLER;
        try {
            $baseControllerReflection = new \ReflectionClass($this->baseControllerClass);
        } catch (\Exception $e) {
            throw new Exception("Can not find base controller '{$this->baseControllerClass}'");
        }
        $this->baseControllerPath = dirname($baseControllerReflection->getFileName()).'/'.self::BASE_CONTROLLER.'/';
    }

    /**
     * Build route
     *
     * @param \Shade\Request $request Request
     *
     * @throws \Shade\Exception
     *
     * @return \Shade\Route
     */
    public function route(Request $request)
    {
        if ($request instanceof Request\Web) {
            $server = $request->getServer();
            if (!isset($server['REQUEST_URI'])) {
                throw new Exception('REQUEST_URI is not set');
            }
            $urlComponents = explode('?', $server['REQUEST_URI']);
            $destination = reset($urlComponents);
            if ($destination && (strpos($destination, $request::SCRIPT_NAME) === 0)) {
                $destination = substr($destination, strlen($request::SCRIPT_NAME));
            }
            if (empty($destination) || $destination === '/') {
                return new Route($this->baseControllerClass.'\\'.self::DEFAULT_WEB_CONTROLLER, self::DEFAULT_ACTION);
            }
            $this->defaultController = self::DEFAULT_WEB_CONTROLLER;
        } elseif ($request instanceof Request\Cli) {
            $argv = $request->getArgv();
            if (!isset($argv[1])) {
                return new Route($this->baseControllerClass.'\\'.self::DEFAULT_CLI_CONTROLLER, self::DEFAULT_ACTION);
            }
            $this->defaultController = self::DEFAULT_CLI_CONTROLLER;
            $destination = $argv[1];
        } elseif ($request instanceof Request\Virtual) {
            if (!method_exists($request->getController(), $request->getAction())) {
                throw new Exception("Method {$request->getAction()} does not exists in class {$request->getAction()}");
            }
            $this->validateActionArguments($request->getController(), $request->getAction(), $request->getActionArgs());
            return new Route($request->getController(), $request->getAction(), $request->getActionArgs());
        } else {
            throw new Exception('Request type not supported');
        }

        return $this->getRoute(trim($destination, '/'));
    }

    /**
     * Get Route for given destination
     *
     * @param $destination
     *
     * @return Route
     */
    abstract protected function getRoute($destination);

    /**
     * Build URL
     *
     * @param string $controller Controller name
     * @param string $action     Action name
     * @param array  $args       Action arguments
     * @param array  $get        GET-parameters
     *
     * @return string
     */
    public function buildUrl($controller, $action, array $args = array(), array $get = array())
    {
        $url = $this->buildDestination($controller, $action, $args);
        if (!$this->cleanUrlsEnabled()) {
            $url = Request\Web::SCRIPT_NAME.$url;
        }
        if ($get) {
            $url .= '?'.http_build_query($get);
        }

        return $url;
    }

    /**
     * Check that requested URL is clean
     *
     * @param \Shade\Request\Web $request Request
     *
     * @throws \Shade\Exception
     *
     * @return bool
     */
    public function isRequestedUrlClean(Request\Web $request)
    {
        $server = $request->getServer();
        if (!isset($server['REQUEST_URI'])) {
            throw new Exception('REQUEST_URI is not set');
        }

        return strpos($server['REQUEST_URI'], $request::SCRIPT_NAME) !== 0;
    }

    /**
     * Enable clean URLs
     */
    public function enableCleanUrls()
    {
        $this->cleanUrlsEnabled = true;
    }

    /**
     * Disable clean URLs
     */
    public function disableCleanUrls()
    {
        $this->cleanUrlsEnabled = false;
    }

    /**
     * Check that clean URLs enabled
     *
     * @return boolean
     */
    protected function cleanUrlsEnabled()
    {
        return $this->cleanUrlsEnabled;
    }

    /**
     * Build Destination
     *
     * @param string $controller Controller name
     * @param string $action     Action name
     * @param array  $args       Action arguments
     *
     * @throws \Shade\Exception
     *
     * @return string
     */
    protected function buildDestination($controller, $action, array $args = array())
    {
        $baseClassLength = strlen($this->baseControllerClass);
        if (substr($controller, 0, $baseClassLength + 1) !== $this->baseControllerClass.'\\') {
            throw new Exception("Provided controller name '$controller' does not contain application Controller namespace '{$this->baseControllerClass}'");
        } else {
            $destination = '/'.str_replace('\\', '/', substr($controller, $baseClassLength + 1)).'/';
        }
        $actionSuffixLength = strlen(self::ACTION_SUFFIX);
        if (substr($action, -$actionSuffixLength) !== self::ACTION_SUFFIX) {
            throw new Exception('Provided action name does not contain application action suffix'.self::ACTION_SUFFIX);
        } else {
            if ($action !== self::DEFAULT_ACTION) {
                $destination .= substr($action, 0, -$actionSuffixLength).'/';
            }
        }
        $destination = strtolower($destination);

        if ($args) {
            $destination .= implode('/', $args).'/';
        }

        return $destination;
    }

    /**
     * Validate number of arguments
     *
     * @param string $controller Controller class name
     * @param string $action     Action name
     * @param array  $args       Arguments
     *
     * @throws Exception
     */
    protected function validateActionArguments($controller, $action, $args)
    {
        $actionArgsNum = count($args);
        $reflectionAction = new \ReflectionMethod($controller, $action);
        $reflectionActionArgsNum = $reflectionAction->getNumberOfParameters();
        $reflectionActionReqArgsNum = $reflectionAction->getNumberOfRequiredParameters();

        if ($actionArgsNum < $reflectionActionReqArgsNum || $actionArgsNum > $reflectionActionArgsNum) {
            throw new Exception('Wrong number of arguments provided for '.$controller.'::'.$action);
        }
    }
}

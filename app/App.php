<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

//TODO CLI: interactive skeleton generation
//TODO CLI: generate apache, nginx, fastcgi configs, hosts
//TODO CLI: dev - ./phpcs -n --standard=PSR2 ...
//TODO CLI: dev - ./php-cs-fixer fix ...
//TODO realise plain routing; think about "smart" routing; may be the last one is not necessary anymore
//TODO logging
//TODO class to hold application config?
//TODO remove ClassLoader and it's Exception class or extend from composer's implementation
//TODO PHPDoc test?
//TODO View "Replace": multiple templates rendering?

namespace Shade;

/**
 * Application
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class App
{
    /**
     * Application modes
     */
    const
        MODE_WEB = 'web',
        MODE_CLI = 'cli';

    /**
     * Instance of Application was created
     *
     * @var bool
     */
    private static $init = false;

    /**
     * Application start time
     *
     * @var float
     */
    private $startTime;

    /**
     * Framework root directory
     *
     * @var string
     */
    private $frameworkDir;

    /**
     * Application root directory
     *
     * @var string
     */
    private $appDir;

    /**
     * Application namespace
     *
     * @var string
     */
    private $appNamespace;

    /**
     * Application configuration
     *
     * @var array
     */
    private $config;

    /**
     * Service Provider
     *
     * @var \Shade\ServiceProvider
     */
    protected $serviceProvider;

    /**
     * Constructor
     *
     * @param string|array|null $config Configuration data or path to configuration file (ini format)
     *
     * @throws \Shade\Exception
     */
    public function __construct($config = null)
    {
        if (self::$init) {
            throw new Exception('Only one instance of Application can be created');
        }
        self::$init = true;
        $this->startTime = microtime(true);
        $this->frameworkDir = dirname(__DIR__);
        $appClass = get_class($this);
        $appReflection = new \ReflectionClass($appClass);
        $this->appDir = dirname(dirname($appReflection->getFileName()));
        $this->appNamespace = substr($appClass, 0, strpos($appClass, '\\'));
        $this->addIncludePath(array($this->frameworkDir, $this->appDir));
        $defaults = $this->parseConfig('config/app.defaults.ini');
        if (is_string($config)) {
            $config = $this->parseConfig($config);
            $this->config = array_replace_recursive($defaults, $config);
        } else {
            $this->config = $defaults;
        }
        $this->setErrorReporting();
        $this->setupDebugging();
        $this->serviceProvider = new ServiceProvider($this);
    }

    /**
     * App can't be cloned
     */
    private function __clone()
    {
    }

    /**
     * Run Application and output content
     *
     * @throws \Shade\Exception
     */
    public function run()
    {
        $appMode = $this->getMode();
        if ($appMode == self::MODE_WEB) {
            $request = Request\Web::makeFromGlobals($this->serviceProvider);
            $this->setupRouter($request);
        } elseif ($appMode == self::MODE_CLI) {
            $request = Request\Cli::makeFromGlobals($this->serviceProvider);
        } else {
            throw new Exception('Mode does not supported');
        }
        $response = $this->execute($request);
        $this->output($response);
    }

    /**
     * Execute Application and return Response
     *
     * @param \Shade\Request $request Request
     *
     * @throws \Shade\Exception
     *
     * @return \Shade\Response
     */
    public function execute(Request $request)
    {
        $router = $this->serviceProvider->getRouter();
        try {
            $route = $router->route($request);
        } catch (Exception $e) {
            $response = new Response();
            $response->setCode(404);

            return $response;
        }
        $controllerClass = $route->controller();
        $controller = new $controllerClass();
        $controller->setServiceProvider($this->serviceProvider);
        $controller->setRequest($request);

        /**
         * @var Response $response
         */
        $response = call_user_func_array(array($controller, $route->action()), $route->args());
        if (!$response instanceof Response) {
            throw new Exception(
                "Executed controller hasn't returned instance of \\Shade\\Response. "
                .ucfirst(gettype($response)).' has been returned.'
            );
        }
        if (!$response->getCode()) {
            $response->setCode(200);
        }

        return $response;
    }

    /**
     * Output content of Response
     *
     * @param \Shade\Response $response Response
     */
    public function output(Response $response)
    {
        $response->output();
    }

    /**
     * Get Application start time
     *
     * @return float
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Get Application configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get Application root directory
     *
     * @return string
     */
    public function getAppDir()
    {
        return $this->appDir;
    }

    /**
     * Get Application namespace
     *
     * @return string
     */
    public function getAppNamespace()
    {
        return $this->appNamespace;
    }

    /**
     * Get Shade root directory
     *
     * @return string
     */
    public function getFrameworkDir()
    {
        return $this->frameworkDir;
    }

    /**
     * Get Application mode
     *
     * @return string
     */
    public function getMode()
    {
        return PHP_SAPI == "cli" ? self::MODE_CLI : self::MODE_WEB;
    }

    /**
     * Setup Router
     *
     * @param Request $request
     */
    protected function setupRouter(Request $request)
    {
        if ($request instanceof Request\Web) {
            $router = $this->serviceProvider->getRouter();
            if ($router->isRequestedUrlClean($request)) {
                $router->enableCleanUrls();
            }
        }
    }

    /**
     * Add paths to "include path"
     *
     * @param string|array $paths Path or paths
     */
    private function addIncludePath($paths)
    {
        set_include_path(get_include_path().PATH_SEPARATOR.implode(PATH_SEPARATOR, (array) $paths));
    }

    /**
     * Parse config from ini-file
     *
     * @param string $path Path to file
     *
     * @return array
     */
    private function parseConfig($path)
    {
        return parse_ini_file($path, true);
    }

    /**
     * Set error reporting and logging
     */
    private function setErrorReporting()
    {
        error_reporting(E_ALL | E_STRICT);
        if (!empty($this->config['debug']['debug_mode'])) {
            ini_set('display_errors', 'On');
            ini_set('log_errors', 'On');
            if (!empty($this->config['debug']['error_log_path'])) {
                ini_set('error_log', $this->config['debug']['error_log_path']);
            }
        } else {
            ini_set('display_errors', 'Off');
            ini_set('log_errors', 'Off');
        }
    }

    /**
     * Setup Debugging
     */
    private function setupDebugging()
    {
        if (!empty($this->config['debug']['debug_mode'])) {
            /**
             * Print variables in preformatted "print_r" style)
             *
             * @return string
             */
            function p()
            {
                $args = func_get_args();
                if ($args) {
                    $result = '<pre>';
                    foreach ($args as $arg) {
                        $result .= htmlspecialchars(print_r($arg, true)).PHP_EOL;
                    }
                    $result .= '</pre>';
                } else {
                    $result = '';
                }
                echo $result;

                return $result;
            }

            /**
             * Print variables in preformatted "var_dump" style)
             *
             * @return string
             */
            function v()
            {
                $args = func_get_args();
                ob_start();
                foreach ($args as $arg) {
                    var_dump($arg);
                    echo PHP_EOL;
                }
                $result = ob_get_clean();
                if ($result && !ini_get('xdebug.overload_var_dump')) {
                    $result = '<pre>'.htmlspecialchars($result).'</pre>';
                }
                echo $result;

                return $result;
            }
        } else {
            function p() {}
            function v() {}
        }
    }
}

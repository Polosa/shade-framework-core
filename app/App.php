<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

//TODO components in separate bundles
//TODO CLI: generate apache, nginx, fastcgi configs, hosts
//TODO CLI: dev - ./phpcs -n --standard=PSR2 ...
//TODO CLI: dev - ./php-cs-fixer fix ... (or phpcbf?)
//TODO PHPDoc test?
//TODO tests

namespace Shade;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Application
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class App
{
    /**
     * Application run modes
     */
    const
        MODE_WEB = 'web',
        MODE_CLI = 'cli';

    const
        DEFAULT_CONFIG_PATH = 'config/app_defaults.php';

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
     * @var \Shade\Config
     */
    private $config;

    /**
     * Service Container
     *
     * @var \Shade\ServiceContainer
     */
    protected $serviceContainer;

    /**
     * Constructor
     *
     * @param \Shade\Config|array|null $config Configuration
     */
    public function __construct($config = null)
    {
        $this->startTime = microtime(true);
        $this->frameworkDir = dirname(__DIR__);
        $appClass = get_class($this);
        $appReflection = new \ReflectionClass($appClass);
        $this->appDir = dirname(dirname($appReflection->getFileName()));
        $this->appNamespace = substr($appClass, 0, strpos($appClass, '\\'));
        $this->addIncludePath([$this->frameworkDir, $this->appDir]);
        $this->config = new Config(require_once self::DEFAULT_CONFIG_PATH);
        if ($config instanceof Config) {
            $this->config->overwrite($config);
        } elseif (is_array($config)) {
            $this->config->overwrite(new Config($config));
        }
        $this->serviceContainer = new ServiceContainer();
        if (!$this->serviceContainer->isRegistered(ServiceContainer::SERVICE_CONTROLLER_DISPATCHER)) {
            $this->setService(ServiceContainer::SERVICE_CONTROLLER_DISPATCHER, new ControllerDispatcher($this->serviceContainer));
        }
    }

    /**
     * Initialize application before execution
     */
    public function init()
    {
        if (
            !$this->serviceContainer->isRegistered(ServiceContainer::SERVICE_LOGGER)
            || !($this->getLogger() instanceof LoggerInterface)
        ) {
            $this->setService(ServiceContainer::SERVICE_LOGGER, new NullLogger());
        }
        $this->getControllerDispatcher()->setLogger($this->getLogger());
    }

    /**
     * Run Application and output content
     *
     * @param \Shade\Request|null $request Request
     *
     * @throws \Shade\Exception
     */
    public function run(Request $request = null)
    {
        $this->init();
        $this->getLogger()->debug('Application launched');
        if (!($request instanceof Request)) {
            $appMode = $this->detectRunMode();
            if ($appMode == self::MODE_WEB) {
                $request = Request\Web::makeFromGlobals();
            } else {
                $request = Request\Cli::makeFromGlobals();
            }
        }
        $this->setupRouter($request);
        $response = $this->execute($request);
        $this->output($response);
        $this->getLogger()->debug('Application execution completed');
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
        return $this->getService(ServiceContainer::SERVICE_CONTROLLER_DISPATCHER)->dispatch($request);
    }

    /**
     * Output content of Response
     *
     * @param \Shade\Response $response Response
     */
    public function output(Response $response)
    {
        $this->getLogger()->debug('Response output');
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
     * @return \Shade\Config
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
     * Detect Application run mode
     *
     * @return string
     */
    protected function detectRunMode()
    {
        return PHP_SAPI == "cli" ? self::MODE_CLI : self::MODE_WEB;
    }

    /**
     * Register Service
     *
     * @param string                   $name                   Service name
     * @param ServiceProviderInterface $serviceProvider        Service Provider
     * @param bool                     $persistent             Register as persistent: all future attempts to get Service will retrieve the same instance
     * @param bool                     $instantiateImmediately Instantiate Service immediately
     *
     * @return App
     */
    public function registerService($name, ServiceProviderInterface $serviceProvider, $persistent = true, $instantiateImmediately = false)
    {
        $this->serviceContainer->registerService($name, $serviceProvider, $persistent, $instantiateImmediately);
        return $this;
    }

    /**
     * Set Service
     *
     * @param string $name    Service name
     * @param mixed  $service Service
     *
     * @return App
     */
    public function setService($name, $service)
    {
        $this->serviceContainer->setService($name, $service);
        return $this;
    }

    /**
     * Get Service
     *
     * @param string $name Service name
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function getService($name)
    {
        return $this->serviceContainer->getService($name);
    }

    /**
     * Get Controller Dispatcher
     *
     * @return ControllerDispatcher
     */
    public function getControllerDispatcher()
    {
        return $this->getService(ServiceContainer::SERVICE_CONTROLLER_DISPATCHER);
    }

    /**
     * Get Router
     *
     * @return \Shade\Router\RouterInterface
     */
    public function getRouter()
    {
        return $this->getService(ServiceContainer::SERVICE_ROUTER);
    }

    /**
     * Get Logger
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        return $this->getService(ServiceContainer::SERVICE_LOGGER);
    }

    /**
     * Setup Router
     *
     * @param Request $request
     *
     * @return \Shade\App
     */
    protected function setupRouter(Request $request)
    {
        if ($request instanceof Request\Web) {
            $router = $this->getRouter();
            if (
                $router instanceof Router\RouterCuInterface
                && $router->isRequestedUrlClean($request))
            {
                $router->enableCleanUrls();
            }
        }

        return $this;
    }

    /**
     * Add paths to "include path"
     *
     * @param string|array $paths Path or paths
     *
     * @return \Shade\App
     */
    private function addIncludePath($paths)
    {
        set_include_path(get_include_path().PATH_SEPARATOR.implode(PATH_SEPARATOR, (array) $paths));

        return $this;
    }
}

<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Controller Dispatcher
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class ControllerDispatcher
{
    /**
     * Service Container
     *
     * @var ServiceContainer
     */
    protected $serviceContainer;

    /**
     * Bindings
     *
     * @var array
     */
    protected $bindings = array();

    /**
     * Argument bindings
     *
     * @var array
     */
    protected $argumentBindings = array();

    /**
     * Primary Request
     *
     * @var \Shade\Request|null
     */
    protected $primaryRequest;

    /**
     * Primary Route
     *
     * @var \Shade\Route|null
     */
    protected $primaryRoute;

    /**
     * Constructor
     *
     * @param ServiceContainer $serviceContainer Service Container
     */
    public function __construct(ServiceContainer $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    /**
     * Dispatch
     *
     * @param Request $request
     *
     * @throws \Shade\Exception
     *
     * @return \Shade\Response
     */
    public function dispatch(Request $request)
    {
        try {
            /**
             * @var \Shade\Router\RouterInterface $router
             */
            $router = $this->serviceContainer->getService(ServiceContainer::SERVICE_ROUTER);
            $route = $router->route($request);
            if (!isset($this->primaryRequest)) {
                $this->primaryRequest = $request;
            }
            if (!isset($this->primaryRoute)) {
                $this->primaryRoute = $route;
            }
        } catch (Exception $e) {
            $response = new Response();
            $response->setCode(404);

            return $response;
        }

        $this->validateRoute($route);
        $controllerClass = $route->controller();
        $actionName = $route->action();
        $actionArguments = $this->getMethodArguments($controllerClass, $actionName);

        if (method_exists($controllerClass, '__construct')) {
            $constructorArguments = $this->getMethodArguments($controllerClass, '__construct');
        }

        if (empty($constructorArguments)) {
            $controller = new $controllerClass();
        } else {
            $controllerReflection = new \ReflectionClass($controllerClass);
            $controller = $controllerReflection->newInstanceArgs($constructorArguments);
        }

        if (!($controller instanceof Controller)) {
            throw new Exception(
                "'{$controllerClass}' must inherit '\\Shade\\Controller'."
            );
        }

        /**
         * @var \Shade\Controller $controller
         */
        $controller->setControllerDispatcher($this);
        $controller->setView($this->serviceContainer->getService(ServiceContainer::SERVICE_VIEW));
        $controller->setCurrentRequest($request);
        $controller->setCurrentRoute($route);
        $controller->setPrimaryRequest($request);
        $controller->setPrimaryRoute($route);

        /**
         * @var Response $response
         */
        $response = call_user_func_array(array($controller, $actionName), $actionArguments);
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
     * Bind Service to method argument
     *
     * @param string $controllerName Controller class name
     * @param string $methodName     Method name
     * @param string $argumentName   Argument name
     * @param string $serviceName    Registered Service name
     *
     * @return ControllerDispatcher
     */
    public function bindService($controllerName, $methodName, $argumentName, $serviceName)
    {
        $this->bindings[$controllerName][$methodName][$argumentName] = $serviceName;
        return $this;
    }

    /**
     * Set value of method argument
     *
     * @param string $controllerName Controller class name
     * @param string $methodName     Method name
     * @param string $argumentName   Argument name
     * @param string $value          Value
     *
     * @return ControllerDispatcher
     */
    public function setArgumentValue($controllerName, $methodName, $argumentName, $value)
    {
        $this->argumentBindings[$controllerName][$methodName][$argumentName] = $value;
        return $this;
    }

    /**
     * Validate Route
     *
     * @param Route $route Route
     *
     * @throws \Shade\Exception
     */
    protected function validateRoute(Route $route)
    {
        if (!class_exists($route->controller())) {
            throw new Exception("Controller '{$route->controller()}' not found");
        }

        if (!method_exists($route->controller(), $route->action())) {
            throw new Exception("Action '{$route->action()}' does not exist in controller '{$route->controller()}'");
        }
    }

    /**
     * Get arguments of Controller's Action or constructor
     *
     * @param string $controllerClass Controller class name
     * @param string $methodName      Method name
     *
     * @throws Exception
     *
     * @return array
     */
    protected function getMethodArguments($controllerClass, $methodName)
    {
        $actionArguments = array();

        $reflectionAction = new \ReflectionMethod($controllerClass, $methodName);
        $actionParametersReflections = $reflectionAction->getParameters();
        foreach ($actionParametersReflections as $actionParameterReflection) {
            $parameterName = $actionParameterReflection->getName();
            if (
                !empty($this->bindings[$controllerClass][$methodName])
                && array_key_exists($parameterName, $this->bindings[$controllerClass][$methodName])
            ) {
                $parameterValue = $this->serviceContainer->getService($this->bindings[$controllerClass][$methodName][$parameterName]);
                $actionArguments[$parameterName] = $parameterValue;
            } elseif (
                !empty($this->argumentBindings[$controllerClass][$methodName])
                && array_key_exists($parameterName, $this->argumentBindings[$controllerClass][$methodName])
            ) {
                $actionArguments[$parameterName] = $this->argumentBindings[$controllerClass][$methodName][$parameterName];
            } elseif ($actionParameterReflection->isOptional()) {
                $actionArguments[$parameterName] = $actionParameterReflection->getDefaultValue();
            } else {
                throw new Exception("No value provided for parameter '{$parameterName}' of '{$controllerClass}::{$methodName}'");
            }
        }
        return $actionArguments;
    }
}
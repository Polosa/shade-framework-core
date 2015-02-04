<?php

/**
 * Shade
 *
 * @version 0.1
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
     * Arguments
     *
     * @var array
     */
    protected $arguments = array();

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
        } catch (Exception $e) {
            $response = new Response();
            $response->setCode(404);

            return $response;
        }

        $this->validateRoute($route);
        $controllerClass = $route->controller();
        $actionName = $route->action();
        $actionArguments = array();

        $reflectionAction = new \ReflectionMethod($controllerClass, $actionName);
        $actionParametersReflections = $reflectionAction->getParameters();
        foreach ($actionParametersReflections as $actionParameterReflection) {
            $parameterName = $actionParameterReflection->getName();
            if (
                !empty($this->bindings[$controllerClass][$actionName])
                && array_key_exists($parameterName, $this->bindings[$controllerClass][$actionName]))
            {
                $parameterValue = $this->serviceContainer->getService($this->bindings[$controllerClass][$actionName][$parameterName]);
                $actionArguments[$parameterName] = $parameterValue;
            } elseif (
                !empty($this->arguments[$controllerClass][$actionName])
                && array_key_exists($parameterName, $this->arguments[$controllerClass][$actionName]))
            {
                $actionArguments[$parameterName] = $this->arguments[$controllerClass][$actionName][$parameterName];
            } elseif ($actionParameterReflection->isOptional()) {
                $actionArguments[$parameterName] = $actionParameterReflection->getDefaultValue();
            } else {
                throw new Exception("No value provided for parameter '{$parameterName}' of '{$controllerClass}::{$actionName}'");
            }
        }

        /**
         * @var \Shade\Controller $controller
         */
        //TODO implement binding of constructor arguments
        $controller = new $controllerClass();
        $controller->setControllerDispatcher($this);
        $controller->setView($this->serviceContainer->getService(ServiceContainer::SERVICE_VIEW));
        $controller->setRequest($request);

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
     * Bind Service to Action's argument
     *
     * @param string $controllerName Controller class name
     * @param string $actionName     Action name
     * @param string $argumentName   Argument name
     * @param string $serviceName    Registered Service name
     *
     * @return ControllerDispatcher
     */
    public function bindService($controllerName, $actionName, $argumentName, $serviceName)
    {
        $this->bindings[$controllerName][$actionName][$argumentName] = $serviceName;
        return $this;
    }

    //TODO better naming for bindService?

    /**
     * Set value of Action's argument
     *
     * @param string $controllerName Controller class name
     * @param string $actionName     Action name
     * @param string $argumentName   Argument name
     * @param string $value          Value
     *
     * @return ControllerDispatcher
     */
    public function setArgumentValue($controllerName, $actionName, $argumentName, $value)
    {
        $this->arguments[$controllerName][$actionName][$argumentName] = $value;
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
            throw new Exception("Controller '{$route->controller()}' not found}");
        }

        if (!method_exists($route->controller(), $route->action())) {
            throw new Exception("Action '{$route->action()}' does not exist in controller '{$route->controller()}'");
        }
    }
}

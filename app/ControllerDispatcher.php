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

        //TODO refactor
        $controllerClass = $route->controller();
        $actionName = $route->action();
        $routeArguments = $route->args();
        $actionArguments = array();

        $reflectionAction = new \ReflectionMethod($controllerClass, $route->action());
        $actionParametersReflections = $reflectionAction->getParameters();
        foreach ($actionParametersReflections as $actionParameterReflection) {
            $parameterName = $actionParameterReflection->getName();
            if (array_key_exists($parameterName, $routeArguments)) {
                $actionArguments[$parameterName] = $routeArguments[$parameterName];
            } elseif (
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
            }
        }
        //TODO arguments validation

        /**
         * @var \Shade\Controller $controller
         */
        $controller = new $controllerClass();
        $controller->setControllerDispatcher($this);
        $controller->setView($this->serviceContainer->getService(ServiceContainer::SERVICE_VIEW));
        $controller->setRequest($request);

        /**
         * @var Response $response
         */
        $response = call_user_func_array(array($controller, $route->action()), $actionArguments);
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
}

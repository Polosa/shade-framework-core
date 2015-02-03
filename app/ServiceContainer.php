<?php

/**
 * Shade
 *
 * @version 0.1
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Service Container
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class ServiceContainer
{

    /**
     * Service names
     */
    const
        SERVICE_VIEW = 'view',
        SERVICE_ROUTER = 'router';

    /**
     * Services
     *
     * @var array
     */
    protected $services = array();

    /**
     * Service Providers
     *
     * @var ServiceProviderInterface[]
     */
    protected $serviceProviders = array();

    /**
     * Register Service
     *
     * @param string                   $name                   Service name
     * @param ServiceProviderInterface $serviceProvider        Service Provider
     * @param bool                     $instantiateImmediately Instantiate Service immediately
     *
     * @return ServiceContainer
     */
    public function registerService($name, ServiceProviderInterface $serviceProvider, $instantiateImmediately = false)
    {
        $this->serviceProviders[$name] = $serviceProvider;
        if ($instantiateImmediately) {
            $this->setService($name, $serviceProvider->instantiate());
        }
        return $this;
    }

    /**
     * Set Service
     *
     * @param string $name    Service name
     * @param mixed  $service Service
     *
     * @return ServiceContainer
     */
    public function setService($name, $service)
    {
        $this->services[$name] = $service;
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
        if (array_key_exists($name, $this->services)) {
            return $this->services[$name];
        } elseif (isset($this->serviceProviders[$name]) && $this->serviceProviders[$name] instanceof ServiceProviderInterface) {
            $service = $this->serviceProviders[$name]->instantiate();
            $this->setService($name, $service);
            return $service;
        } else {
            throw new Exception("Requested service {$name} is not registered");
        }
    }

}

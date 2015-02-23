<?php

/**
 * Shade
 *
 * @version 1.0.0
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
     * Service configuration
     *
     * @var array
     */
    protected $serviceConfiguration = array();

    /**
     * Register Service
     *
     * @param string                   $name                   Service name
     * @param ServiceProviderInterface $serviceProvider        Service Provider
     * @param bool                     $persistent             Register as persistent: all future attempts to get Service will retrieve the same instance
     * @param bool                     $instantiateImmediately Instantiate Service immediately
     *
     * @return ServiceContainer
     */
    public function registerService($name, ServiceProviderInterface $serviceProvider, $persistent = true, $instantiateImmediately = false)
    {
        $this->serviceConfiguration[$name]['persistent'] = $persistent;
        $this->serviceProviders[$name] = $serviceProvider;

        if ($instantiateImmediately) {
            $service = $serviceProvider->instantiate();
            if ($persistent) {
                $this->setService($name, $service);
            }
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
        $this->serviceConfiguration[$name]['persistent'] = true;
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
            if (!empty($this->serviceConfiguration[$name]['persistent'])) {
                $this->setService($name, $service);
            }
            return $service;
        } else {
            throw new Exception("Requested service {$name} is not registered");
        }
    }

}

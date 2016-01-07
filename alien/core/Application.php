<?php

namespace Alien;

use Alien\Di\ServiceLocator;
use Alien\Routing\HttpRequest;
use Alien\Routing\RequestInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

abstract class Application
{

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ServiceLocator
     */
    private $serviceLocator;

    /**
     * @var HttpRequest
     */
    private $request;

    /**
     * Initialize application. Should be run only once.
     * @param Configuration $configuration
     */
    public function bootstrap(Configuration $configuration = null)
    {

        $this->request = HttpRequest::createFromServer();

        $this->configuration = $configuration;

        $timeZone = $this->getConfiguration()->get('timezone');
        if ($timeZone) {
            date_default_timezone_set($timeZone);
        }

        if ($this->getConfiguration()->get('autoload')) {
            foreach ($this->getConfiguration()->get('autoload') as $dir) {
                $files = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );
                foreach ($files as $fileinfo) {
                    require_once $fileinfo->getBasename();
                }
            }
        }

        $sl = new ServiceLocator($this->getConfiguration());
        $this->serviceLocator = $sl;
        $sl->register($configuration);

    }

    /**
     * Returns configuration object
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Sets configuration
     * @param Configuration $configuration
     */
    public function setConfiguration(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Runs application MVC and renders output into string
     *
     * @return string HTML output
     * @todo toto takto zostat nemoze, privela zavislosti
     */
    abstract public function run();

    /**
     * Returns initialized ServiceLocator object
     *
     * @return ServiceLocator
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Returns HTTP request
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }
}

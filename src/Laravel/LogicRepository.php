<?php

namespace Riario\Logic\Laravel;

use Riario\Logic\Contracts\LogicInterface;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Container\Container;
use Illuminate\Foundation\ProviderRepository;

class LogicRepository implements LogicInterface
{
    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;
    /**
     * @var Filesystem
     */
    private $files;
    /**
     * The module path.
     *
     * @var string|null
     */
    protected $path;

    /**
     * The constructor.
     * @param Container $app
     * @param string|null $path
     */
    public function __construct(Container $app, $path = null)
    {
        $this->app = $app;
        $this->files = $app['files'];
        $this->path = $path ?: config('logic.paths.logic').'/*';
    }
    /**
     * get logics path
     */
    public function getPath()
    {
        return $this->path;
    }
    /**
     * get logic path for json file scan
     */
    public function getScanPath()
    {
        return $this->path.'/*';
    }
    /**
     * register all main providers in logics
     */
    public function register()
    {
        foreach ($this->getAllLogics() as $manifest) {
            $attributes = $this->decodeContents($manifest);
            $this->registerProviders($attributes['providers']);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function registerProviders($providers): void
    {
        (new ProviderRepository($this->app, new Filesystem(), $this->app->getCachedServicesPath()))
            ->load($providers);
    }
    /**
     * get all logic's path into array
     * @return array
     */
    public function getAllLogics()
    {
        return $this->files->glob("{$this->getScanPath()}/logic.json");
    }

    /**
     *  Decode contents as array.
     *
     * @return array
     * @throws InvalidJsonException
     */
    public function decodeContents($path)
    {
        $attributes =  json_decode($this->getContents($path), 1);

        // any JSON parsing errors should throw an exception
        if (json_last_error() > 0) {
            throw new \Exception('Error processing file: ' . $path . '. Error: ' . json_last_error_msg());
        }

        return $attributes;
    }

     /**
     * Get file content.
     *
     * @return string
     */
    public function getContents($path)
    {
        return $this->files->get($path);
    }
}

<?php

namespace Riario\Logic\Generator;

use Illuminate\Config\Repository as Config;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Console\Command as Console;
use Illuminate\Support\Str;
use Riario\Logic\Support\Stub;

class LogicGenerator
{
    /**
     * The logic name will created.
     *
     * @var string
     */
    protected $name;

    /**
     * The laravel config instance.
     *
     * @var Config
     */
    protected $config;

    /**
     * The laravel filesystem instance.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * The laravel console instance.
     *
     * @var Console
     */
    protected $console;
    /**
     * The laravel component Factory instance.
     *
     * @var \Illuminate\Console\View\Components\Factory
     */
    protected $component;

    /**
     * The constructor.
     * @param $name
     * @param Config     $config
     * @param Filesystem $filesystem
     * @param Console    $console
     */
    public function __construct(
        $name,
        Config $config = null,
        Filesystem $filesystem = null,
        Console $console = null,
    ) {
        $this->name = $name;
        $this->config = $config;
        $this->filesystem = $filesystem;
        $this->console = $console;
    }
    /**
     * Set the laravel console instance.
     *
     * @param Console $console
     *
     * @return $this
     */
    public function setConsole($console)
    {
        $this->console = $console;

        return $this;
    }
    /**
     * @param \Illuminate\Console\View\Components\Factory $component
     */
    public function setComponent(\Illuminate\Console\View\Components\Factory $component): self
    {
        $this->component = $component;
        return $this;
    }
    /**
     * Get the name of module will created. By default in studly case.
     *
     * @return string
     */
    public function getName()
    {
        return Str::studly($this->name);
    }
    /**
     * Set the laravel filesystem instance.
     *
     * @param Filesystem $filesystem
     *
     * @return $this
     */
    public function setFilesystem($filesystem)
    {
        $this->filesystem = $filesystem;

        return $this;
    }
    /**
     * Generate the folders.
     */
    public function generateFolders()
    {
        foreach ($this->getFolders() as $key => $folder) {
            $path = config('logic.paths.logic') . '/' .$this->getName() . '/' . $folder['path'];

            $this->filesystem->makeDirectory($path, 0755, true);
        }
        return $this;
    }
    /**
     * Generate the files.
     * @return $this
     */
    public function generateFiles()
    {
        foreach ($this->getFiles() as $stub => $file) {
            $path = config('logic.paths.logic') . '/' .$this->getName() . '/' . $file;

            $this->component->task("Generating file {$path}",function () use ($stub, $path) {
                
                if (!$this->filesystem->isDirectory($dir = dirname($path))) {
                    $this->filesystem->makeDirectory($dir, 0775, true);
                }
    
                $this->filesystem->put($path, $this->getStubContents($stub));
            });

        }
        return $this;
    }

    /**
     * genereate default files via command line
     * @return $this
     */
    public function generateResources()
    {
        $this->console->call('logic:make-provider', [
            'name' => $this->getName() . 'ServiceProvider',
            'logic' => $this->getName(),
        ]);
        $this->console->call('logic:route-provider', [
            'logic' => $this->getName(),
        ]);
        return $this;
    }
    /**
     * generate json file
     */
    public function generateJson()
    {
        $path = config('logic.paths.logic') . '/' .$this->getName() . '/' . 'logic.json';

        $this->component->task("Generating file $path",function () use ($path) {
            if (!$this->filesystem->isDirectory($dir = dirname($path))) {
                $this->filesystem->makeDirectory($dir, 0775, true);
            }

            $this->filesystem->put($path, $this->getStubContents('json'));
        });
    }

    /**
     * Get the list of folders will created.
     *
     * @return array
     */
    public function getFolders()
    {
        return config('logic.paths.generator');
    }
    /**
     * Get the list of files will created.
     *
     * @return array
     */
    public function getFiles()
    {
        return config('logic.stubs.files');
    }
    /**
     * Get the contents of the specified stub file by given stub name.
     *
     * @param $stub
     *
     * @return string
     */
    protected function getStubContents($stub)
    {
        return (new Stub(
            config('logic.stubs.path').'/' . $stub . '.stub',
            $this->getReplacement($stub)
        )
        )->render();
    }
    /**
     * Get array replacement for the specified stub.
     *
     * @param $stub
     *
     * @return array
     */
    protected function getReplacement($stub)
    {
        $replacements = config('logic.stubs.replacements');

        if (!isset($replacements[$stub])) {
            return [];
        }

        $keys = $replacements[$stub];

        $replaces = [];
        
        if ($stub === 'json') {
            if (in_array('PROVIDER_NAMESPACE', $keys, true) === false) {
                $keys[] = 'PROVIDER_NAMESPACE';
            }
        }
     
        foreach ($keys as $key) {
            if (method_exists($this, $method = 'get' . ucfirst(Str::studly(strtolower($key))) . 'Replacement')) {
                $replaces[$key] = $this->$method();
            } else {
                $replaces[$key] = null;
            }
        }
      
        return $replaces;
    }

    /**
     * Get the module name in lower case.
     *
     * @return string
     */
    protected function getLowerNameReplacement()
    {
        return strtolower($this->getName());
    }

    /**
     * Get the module name in studly case.
     *
     * @return string
     */
    protected function getStudlyNameReplacement()
    {
        return $this->getName();
    }


    /**
     * Get replacement for $MODULE_NAMESPACE$.
     *
     * @return string
     */
    protected function getModuleNamespaceReplacement()
    {
        return str_replace('\\', '\\\\', config('logic.namespace'));
    }


    protected function getProviderNamespaceReplacement(): string
    {
        return str_replace('\\', '\\\\', config('logic.paths.generator.provider.path'));
    }
}
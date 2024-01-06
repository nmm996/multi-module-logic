<?php

namespace Riario\Logic\Commands;

use Riario\Logic\Support\Stub;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Support\Str;
use Riario\Logic\Commands\Base\LogicCommand;
use Riario\Logic\Generator\FileGenerator;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ProviderMakeCommand extends LogicCommand
{
    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'name';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'logic:make-provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new provider class for the specified logic.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the policy class.'],
            ['logic', InputArgument::OPTIONAL, 'The name of logic will be used.'],
        ];
    }
    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $module = $this->getLogicName();

        return (new Stub( config('logic.stubs.path') . '/provider.stub', [
            'NAMESPACE' => $this->getClassNamespace($module),
            'CLASS'     => $this->getClass(),
        ]))->render();
    }
    /**
     * Get the module name.
     *
     * @return string
     */
    public function getLogicName()
    {
        return Str::studly($this->argument('logic'));
    }
    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }
     /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        return config('logic.paths.logic') . '/'.$this->getLogicName(). '/'. $this->getDefaultNamespace(). '/' . $this->getFileName(). '.php';
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = str_replace('\\', '/', $this->getDestinationFilePath());

            if (!$this->laravel['files']->isDirectory($dir = dirname($path))) {
                $this->laravel['files']->makeDirectory($dir, 0777, true);
            }

            $contents = $this->getTemplateContents();

            try {
                $this->components->task("Generating file {$path}",function () use ($path,$contents) {
                    (new FileGenerator($path, $contents))->generate();
                });

            } catch (FileException $e) {
                $this->components->error("File : {$path} already exists.");

                return E_ERROR;
            }

        return 0;
    }

    /**
     * get default namespace
     */
    public function getDefaultNamespace()
    {
        return config('logic.paths.generator.provider.path');
    }
    /**
     * Get class name.
     *
     * @return string
     */
    public function getClass()
    {
        return class_basename($this->argument($this->argumentName));
    }
}

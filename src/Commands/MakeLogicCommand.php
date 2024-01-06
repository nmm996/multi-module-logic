<?php

namespace Riario\Logic\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Riario\Logic\Generator\LogicGenerator;
use Symfony\Component\Console\Input\InputArgument;

class MakeLogicCommand extends Command
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
    protected $name = 'logic:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $path = str_replace('\\', '/', $this->getDestinationFolderPath());
       
        if (!$this->laravel['files']->isDirectory($dir = dirname($path))) {
            $this->laravel['files']->makeDirectory($dir, 0777, true);
        }
        
        $name = $this->getLogicName();

        $this->components->info("Creating module: [$name]");
        
        (new LogicGenerator($name))
            ->setFilesystem($this->laravel['files'])
            ->setConsole($this)
            ->setComponent($this->components)
            ->generateFolders()
            ->generateFiles()
            ->generateResources()
            ->generateJson();
            
    }
    /**
     * @return string
     */
    private function getLogicName()
    {
        return Str::studly($this->argument('name'));
    }
    /**
     * @return mixed
     */
    protected function getDestinationFolderPath()
    {
        return config('logic.paths.logic') . '/' . $this->getLogicName();
    }
    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the logic.']
        ];
    }
    
}

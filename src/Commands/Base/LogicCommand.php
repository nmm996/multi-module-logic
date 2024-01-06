<?php

namespace Riario\Logic\Commands\Base;

use Illuminate\Console\Command;

abstract class LogicCommand extends Command
{
    /**
     * The name of 'name' argument.
     *
     * @var string
     */
    protected $argumentName = '';

    /**
     * Get template contents.
     *
     * @return string
     */
    abstract protected function getTemplateContents();

    /**
     * Get the destination file path.
     *
     * @return string
     */
    abstract protected function getDestinationFilePath();

    /**
     * default namespace for generating file
     */
    abstract public function getDefaultNamespace();
    /**
     * Get class namespace.
     *
     * @return string
     */
    public function getClassNamespace($module)
    {
        $extra = str_replace($this->getClass(), '', $this->argument($this->argumentName));

        $extra = str_replace('/', '\\', $extra);

        $namespace = $this->getLogicNamespace();

        $namespace .= '\\' . $module;

        $namespace .= '\\' . $this->getDefaultNamespace();

        $namespace .= '\\' . $extra;

        $namespace = str_replace('/', '\\', $namespace);

        return trim($namespace, '\\');
    }
    /**
     * get logic namespace
     */
    public function getLogicNamespace()
    {
        return config('logic.namespace');
    }
    /**
     * Get name in lower case.
     *
     * @return string
     */
    public function getLowerName($name): string
    {
        return strtolower($name);
    }
}
<?php
namespace LazyOptionCommand\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use LazyOptionCommand\Input\InteractiveInput;

class Command extends SymfonyCommand
{
    public function run(InputInterface $input, OutputInterface $output)
    {
        $input = new InteractiveInput($input, $output, $this->getHelperSet()->get('question'));
        return parent::run($input, $output);
    }

    public function addOption($name, $shortcut = null, $mode = null, $description = '', $default = null)
    {
        if (is_object($name)) {
            $this->getDefinition()->addOption($name);
            return $this;
        }

        if (is_array($name)) {
            return parent::addOption(
                $name['name'],
                $name['shortcut'],
                $name['mode'],
                $name['description'],
                isset($name['default']) ? $name['default'] : null
            );
        }

        return parent::addOption($name, $shortcut, $mode, $description, $default);
    }
}

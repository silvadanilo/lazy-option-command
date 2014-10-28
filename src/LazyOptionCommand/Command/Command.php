<?php
namespace LazyOptionCommand\Command;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use LazyOptionCommand\Input\InteractiveInput;

class Command extends SymfonyCommand
{
    public function run(InputInterface $input, OutputInterface $output)
    {
        $input = new InteractiveInput($input, $output, $this->getHelperSet()->get('question'));
        return parent::run($input, $output);
    }

    public function addRawOption(InputOption $option)
    {
        $this->getDefinition()->addOption($option);

        return $this;
    }
}

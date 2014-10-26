<?php
namespace LazyOptionCommand\Input;

use LazyOptionCommand\Input\LazyInputOption;
use Symfony\Component\Console\Input\Input;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\QuestionHelper;

/**
 * @inheritdoc
 *
 * @author Danilo Silva <silva.danilo82@gmail.com>
 */
class InteractiveInput implements InputInterface
{
    private $input;
    private $output;
    private $helper;
    private $definition;
    private $optionEnricher;

    public function __construct(InputInterface $input, OutputInterface $output, QuestionHelper $helper)
    {
        $this->input  = $input;
        $this->helper = $helper;
        $this->optionEnricher = new OptionsEnricher($this, $output, $helper);
    }

    /**
     * Returns the option value for a given option name.
     * If value is not setted and the option is lazy, ask to the user interactively
     *
     * @param string $name The option name
     *
     * @return mixed The option value
     *
     * @throws \InvalidArgumentException When option given doesn't exist
     */
    public function getOption($name)
    {
        $value = $this->input->getOption($name);
        if (!is_null($value)) {
            return $value;
        }

        $option = $this->definition->getOption($name);
        if ($option instanceof LazyInputOption && $option->isLazy()) {
            return $this->optionEnricher->enrich($option->getName(), $option->availableValues($this));
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function bind(InputDefinition $definition)
    {
        $this->definition = $definition;
        return $this->input->bind($definition);
    }

    /**
     * @inheritdoc
     */
    protected function parse()
    {
        return $this->input->parse();
    }

    /**
     * @inheritdoc
     */
    public function getFirstArgument()
    {
        return $this->input->getFirstArgument();
    }

    /**
     * @inheritdoc
     */
    public function hasParameterOption($values)
    {
        return $this->input->hasParameterOption($values);
    }

    /**
     * @inheritdoc
     */
    public function getParameterOption($values, $default = false)
    {
        return $this->input->getParameterOption($values, $default);
    }

    /**
     * @inheritdoc
     */
    public function validate()
    {
        return $this->input->validate();
    }

    /**
     * @inheritdoc
     */
    public function getArguments()
    {
        return $this->input->getArguments();
    }

    /**
     * @inheritdoc
     */
    public function getArgument($name)
    {
        return $this->input->getArgument($name);
    }

    /**
     * @inheritdoc
     */
    public function setArgument($name, $value)
    {
        return $this->input->setArgument($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function hasArgument($name)
    {
        return $this->input->hasArgument($name);
    }

    /**
     * @inheritdoc
     */
    public function getOptions()
    {
        return $this->input->getOptions();
    }

    /**
     * @inheritdoc
     */
    public function setOption($name, $value)
    {
        return $this->input->setOption($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function hasOption($name)
    {
        return $this->input->hasOption($name);
    }

    /**
     * @inheritdoc
     */
    public function isInteractive()
    {
        return $this->input->isInteractive();
    }

    /**
     * @inheritdoc
     */
    public function setInteractive($interactive)
    {
        return $this->input->setInteractive($interactive);
    }
}

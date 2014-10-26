<?php
namespace LazyOptionCommand\Input;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Helper\QuestionHelper;

class OptionsEnricher
{
    private $input;
    private $output;
    private $questionHelper;

    public function __construct(InputInterface $input, OutputInterface $output, QuestionHelper $questionHelper)
    {
        $this->input = $input;
        $this->output = $output;
        $this->questionHelper = $questionHelper;
    }

    public function enrich($name, $availableValues)
    {
        if (empty($availableValues)) {
            $value = $this->askForAFreeResponse($name);
        }
        elseif (is_array($availableValues)) {
            $value = $this->askForArray($name, $availableValues);
        } else {
            throw new \RuntimeException('Value type: `'.gettype($availableValues).'`' . ' is not handled');
        }

        $this->input->setOption($name, $value);

        return $value;
    }

    public function askForAFreeResponse($name)
    {
        $question = new Question("The {$name} option is mandatory; Please enter a value for it");
        return $this->questionHelper->ask($this->input, $this->output, $question);
    }

    public function askForArray($name, array $availableValues)
    {
        if (0 === count($availableValues)) {
            return null;
        }

        if (1 === count($availableValues)) {
            return key($availableValues);
        }

        $question = new ChoiceQuestion(
            "{$name} option is mandatory, choose between:",
            $availableValues,
            array_keys($availableValues)[0]
        );
        $question->setMaxAttempts(5);
        $question->setErrorMessage("The {$name} `%s` is not valid value.");

        $valueLabel = $this->questionHelper->ask($this->input, $this->output, $question);

        return array_search($valueLabel, $availableValues);
    }
}

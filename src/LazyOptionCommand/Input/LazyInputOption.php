<?php
namespace LazyOptionCommand\Input;

use LazyOptionCommand\Question\KeyChoiceQuestion;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Helper\QuestionHelper;

class LazyInputOption extends InputOption
{
    private $name;
    private $inputOption;
    private $question;

    public function __construct($name, $shortcut = null, $mode = null, $description = '', $default = null, $question = null)
    {
        parent::__construct($name, $shortcut, $mode, $description, $default);
        $this->name = $name;
        $this->question = $question;
    }

    public function question(InputInterface $input)
    {
        $availableValues = $this->availableValues($input);

        if (is_null($availableValues) || empty($availableValues)) {
            return $this->stringQuestion();
        }

        if (is_array($availableValues)) {
            return $this->choiceQuestion($availableValues);
        }

        if (is_object($availableValues) && $availableValues instanceof Question) {
            return $availableValues;
        }

        throw new \RuntimeException('Value type: `'.gettype($availableValues).'`' . ' is not handled');
    }

    private function stringQuestion()
    {
        return new Question("The {$this->name} option is mandatory; Please enter a value for it");
    }

    private function choiceQuestion(array $availableValues)
    {
        if (0 === count($availableValues)) {
            return null;
        }

        if (1 === count($availableValues)) {
            return key($availableValues);
        }

        $name = $this->name;
        $question = new KeyChoiceQuestion(
            "{$name} option is mandatory, choose between:",
            $availableValues,
            array_shift(array_keys($availableValues))
        );
        $question->setMaxAttempts(5);

        return $question;
    }

    private function availableValues(InputInterface $input)
    {
        $availableValues = null;

        if ($this->question) {
            if (is_callable($this->question)) {
                $availableValues = call_user_func($this->question, $input);
            } else {
                $availableValues = $this->question;
            }
        }

        return $availableValues;
    }
}

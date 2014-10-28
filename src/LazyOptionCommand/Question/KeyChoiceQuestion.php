<?php
namespace LazyOptionCommand\Question;

use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

/**
 * Represents a choice question.
 *
 * @author Danilo Silva <silva.danilo82@gmail.com>
 */
class KeyChoiceQuestion extends ChoiceQuestion
{
    private $choices;

    public function __construct($question, array $choices, $default = null)
    {
        $this->choices = $choices;
        parent::__construct($question, $choices, $default);
    }

    public function getValidator()
    {
        return function ($selected) {
            $validator = parent::getValidator();

            return array_search($validator($selected), $this->choices);
        };
    }
}

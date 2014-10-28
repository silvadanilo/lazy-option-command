<?php
namespace LazyOptionCommand\Tests\Input;

use LazyOptionCommand\Input\LazyInputOption;
use LazyOptionCommand\Question\KeyChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputOption;

class LazyInputOptionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->input = $this->getMock('Symfony\Component\Console\Input\InputInterface');
    }

    public function testReturnsAKeyChoiceQuestionWhenAvailableValuesIsACallbackThatReturnsAnArray()
    {
        $availableValues = array(
            'foo' => 'bar',
            'bar' => 'foo'
        );

        $option = new LazyInputOption(
            'sample',
            'o',
            InputOption::VALUE_OPTIONAL,
            'sample option',
            null,
            function () use ($availableValues) {
                return $availableValues;
            }
        );

        $expectedQuestion = new KeyChoiceQuestion(
            "sample option is mandatory, choose between:",
            $availableValues,
            array_shift(array_keys($availableValues))
        );
        $expectedQuestion->setMaxAttempts(5);

        $this->assertEquals($expectedQuestion, $option->question($this->input));
    }

    public function testReturnsAValueWhenAvailableValuesIsACallbackThatReturnsASingleValue()
    {
        $availableValues = array(
            'foo' => 'bar'
        );

        $option = new LazyInputOption(
            'option',
            'o',
            InputOption::VALUE_OPTIONAL,
            'sample option',
            null,
            $availableValues
        );

        $this->assertEquals('foo', $option->question($this->input));
    }

    public function testReturnsAQuestionWhenAvailableValuesIsNullOrAnEmptyArray()
    {
        $availableValues = array();

        $option = new LazyInputOption(
            'sample',
            'o',
            InputOption::VALUE_OPTIONAL,
            'sample option',
            null,
            function () use ($availableValues) {
                return $availableValues;
            }
        );

        $expectedQuestion = new Question(
            "The sample option is mandatory; Please enter a value for it"
        );
        $this->assertEquals($expectedQuestion, $option->question($this->input));
    }

    public function testIfAvailableValuesIsAnInstanceOfQuestionItIsReturnedDirectly()
    {
        $availableValues = new KeyChoiceQuestion('choose: ', array(
            'foo' => 'bar'
        ));

        $option = new LazyInputOption(
            'option',
            'o',
            InputOption::VALUE_OPTIONAL,
            'sample option',
            null,
            $availableValues
        );

        $this->assertEquals($availableValues, $option->question($this->input));
    }
}

<?php
namespace LazyOptionCommand\Tests\Input;

use Symfony\Component\Console\Input\InputOption;
use LazyOptionCommand\Input\LazyInputOption;

class LazyInputOptionTest extends \PHPUnit_Framework_TestCase
{
    public function testCreationWithLazyModeDontCauseAnError()
    {
        $option = new LazyInputOption(
            'option',
            'o',
            InputOption::VALUE_OPTIONAL | LazyInputOption::OPTION_IS_LAZY
        );

        $this->assertTrue($option->isLazy());
    }

    public function testAvailableValuesExecuteCallback()
    {
        $availableValues = [
            'foo' => 'bar'
        ];

        $option = new LazyInputOption(
            'option',
            'o',
            InputOption::VALUE_OPTIONAL | LazyInputOption::OPTION_IS_LAZY,
            'sample option',
            null,
            function () use ($availableValues) {
                return $availableValues;
            }
        );

        $input = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $this->assertEquals($availableValues, $option->availableValues($input));
    }

    public function testAvailableValuesRetunrsLazyValueIfItIsNotCallable()
    {
        $availableValues = [
            'foo' => 'bar'
        ];

        $option = new LazyInputOption(
            'option',
            'o',
            InputOption::VALUE_OPTIONAL | LazyInputOption::OPTION_IS_LAZY,
            'sample option',
            null,
            $availableValues
        );

        $input = $this->getMock('Symfony\Component\Console\Input\InputInterface');
        $this->assertEquals($availableValues, $option->availableValues($input));
    }
}

<?php
namespace LazyOptionCommand\Tests\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\OutputInterface;
use LazyOptionCommand\Command\Command;
use LazyOptionCommand\Input\LazyInputOption;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->application = new Application();
    }

    public function testAskForMandatoryOptionsIfThemAreMissingAndEnrichOptions()
    {
        $command = new SampleCommand([
            'foo' => 'bar',
            'bar' => 'foo',
        ]);
        $this->application->add($command);

        $this->runtimeInputIs($command, "foo");

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        $this->assertEquals('foo', $commandTester->getInput()->getOption('option'));
        $this->assertRegExp("/foo.*bar\n.*bar.*foo/", $commandTester->getDisplay());
    }

    public function testEnrichOptionsWithoutAskingForAMissingMandatoryOptionsWithOnlyOneValue()
    {
        $command = new SampleCommand([
            'key' => 'key should not be showed',
        ]);
        $this->application->add($command);

        $this->runtimeInputIsNotNecessary();

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        $this->assertEquals('key', $commandTester->getInput()->getOption('option'));
        $this->assertNotRegExp("/key/", $commandTester->getDisplay());
    }

    public function testAskingForAFreeResponseForAMissingMandatoryOptionsWithoutAvailableValues()
    {
        $command = new SampleCommand(null);
        $this->application->add($command);

        $this->runtimeInputIs($command, "free response");

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
        ]);

        $this->assertEquals('free response', $commandTester->getInput()->getOption('option'));
    }

    private function runtimeInputIsNotNecessary()
    {
    }

    private function runtimeInputIs($command, $input)
    {
        $dialog = $command->getHelper('question');
        $dialog->setInputStream($this->getInputStream($input));
    }

    protected function getInputStream($input)
    {
        $stream = fopen('php://memory', 'r+', false);
        fputs($stream, $input);
        rewind($stream);

        return $stream;
    }
}

class SampleCommand extends Command
{
    private $availableValues;

    public function __construct($availableValues)
    {
        parent::__construct();
        $this->availableValues = $availableValues;
    }

    protected function configure()
    {
        parent::configure();

        $this->setName('sample:command')
            ->addOption([
                'name'        => 'sample',
                'shortcut'    => 's',
                'mode'        => InputOption::VALUE_REQUIRED,
                'description' => 'sample',
                'default'     => null
            ])
            ->addOption(new LazyInputOption(
                'option',
                'o',
                InputOption::VALUE_OPTIONAL | LazyInputOption::OPTION_IS_LAZY,
                'sample option',
                null,
                function () {
                    return $this->availableValues;
                }
            ))
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $input->getOption('option');
    }
}

<?php
namespace LazyOptionCommand\Input;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Helper\QuestionHelper;

class LazyInputOption extends InputOption
{
    const OPTION_IS_LAZY = 16;

    private $inputOption;
    private $isLazy = false;
    private $lazyValue;

    public function __construct($name, $shortcut = null, $mode = null, $description = '', $default = null, $lazyValue = null)
    {
        parent::__construct($name, $shortcut, $this->normalizeMode($mode), $description, $default);
        $this->lazyValue = $lazyValue;
    }

    public function isLazy()
    {
        return $this->isLazy;
    }

    public function availableValues(InputInterface $input)
    {
        $availableValues = null;

        if ($this->lazyValue) {
            if (is_callable($this->lazyValue)) {
                $availableValues = call_user_func($this->lazyValue, $input);
            } else {
                $availableValues = $this->lazyValue;
            }
        }

        return $availableValues;
    }

    private function normalizeMode($mode)
    {
        if ($mode < self::OPTION_IS_LAZY) {
            return $mode;
        }

        $this->isLazy = true;

        return $mode ^ self::OPTION_IS_LAZY;
    }
}

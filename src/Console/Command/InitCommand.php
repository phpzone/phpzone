<?php

namespace PhpZone\PhpZone\Console\Command;

use PhpZone\PhpZone\Exception\Config\ConfigAlreadyExistsException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InitCommand extends Command
{
    public function __construct()
    {
        parent::__construct('phpzone:init');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $destinationFile = 'phpzone.yml';
        $filePath = realpath($destinationFile);

        if (file_exists($destinationFile)) {
            throw new ConfigAlreadyExistsException(
                sprintf('Configuration file "%s" already exists in "%s"', $destinationFile, $filePath),
                1
            );
        }

        copy('src/Config/templates/phpzone.example.yml', $destinationFile);

        $style = new OutputFormatterStyle(null, null, array('bold'));
        $output->getFormatter()->setStyle('bold', $style);

        $output->writeln(<<<EOF
<info>
Configuration file <bold>{$destinationFile}</bold> has been successfully created in <bold>{$filePath}</bold>
</info>
EOF
        );
    }
}

<?php

namespace PhpZone\PhpZone\Console;

use PhpZone\PhpZone\Application;
use Symfony\Component\Console\Shell as BaseShell;

class Shell extends BaseShell
{
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;

        parent::__construct($application);
    }

    /**
     * @return string
     */
    protected function getHeader()
    {
        $version = $this->application->getVersion();

        return <<<EOF
<options=bold>      ____  _           _____
     |  _ \| |         |___  |
     | |_) | |__  ____    / /  ___  _ __   ___
     |  __/|  _ \|  _ \  / /  / _ \| '_ \ / _ \
     | |   | | | | |_) |/ /__| (_) | | | |  ___|
     |_|   |_| |_|  __//_____|\___/|_| |_|\___|    <fg=yellow;options=bold>version {$version}</fg=yellow;options=bold>
                 |_|</options=bold>

At the prompt, type <comment>help</comment> for some help,
or <comment>list</comment> (or press ENTER) to get a list of available commands.

To exit the shell, type <comment>^D</comment>.

<fg=cyan>Tip: The shell environment supports an autocomplete and a command history.</fg=cyan>

EOF;
    }

    /**
     * @return string The prompt
     */
    protected function getPrompt()
    {
        // using the formatter here is required when using readline
        return $this->getOutput()->getFormatter()->format(
            '<fg=green;options=bold>' . $this->application->getName() . '</fg=green;options=bold> > '
        );
    }
}

Basic Commands
==============

List of commands
----------------

There is a command to display all available commands:

.. code-block:: bash

    $ vendor/bin/phpzone
    # or
    $ vendor/bin/phpzone list

Initialize config file
----------------------

In case of new project or new implementation it can be useful to let PhpZone generate the configuration file by:

.. code-block:: bash

    $ vendor/bin/phpzone --init

.. note::
    If the ``phpzone.yml`` already exists, it will **not** be overwritten.

Custom config path
------------------

There is also provided an option for the custom path for the configuration file. You can just basically use:

.. code-block:: bash

    $ vendor/bin/phpzone --config path/to/config.yml
    # or
    $ vendor/bin/phpzone -c path/to/config.yml

Shell environment
-----------------

Shell environment provides an interactive environment with full support of history and auto-complete commands.
Very useful when there are more defined commands and the developer often switches between them.

.. code-block:: bash

    $ vendor/bin/phpzone --shell
    # or
    $ vendor/bin/phpzone -s

Help
----

Help can be called for general application or for specific command. It will show all available arguments, options
or help description of command if defined.

.. code-block:: bash

    $ vendor/bin/phpzone help <COMMAND>
    # or
    $ vendor/bin/phpzone -h <COMMAND>

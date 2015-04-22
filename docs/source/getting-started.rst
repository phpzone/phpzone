Getting Started
===============

Requirements
------------

PhpZone requires PHP 5.3 or higher.

Installation
------------

Installation is provided via `Composer`_, if you don't have it, do install:

.. code-block:: bash

    $ curl -s https://getcomposer.org/installer | php

then PhpZone can be added into your dependencies by:

.. code-block:: bash

    $ composer require --dev phpzone/phpzone 0.2.*

or add it manually into your ``composer.json``:

.. code-block:: json

    {
        "required-dev": {
            "phpzone/phpzone": "0.2.*"
        }
    }

Configuration file
------------------

The configuration file ``phpzone.yml`` is the alpha and omega of this tool and its format is `YAML`_.

Creating the configuration file
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

Default location of the config file is a root of a project where PhpZone should be used.

You can create ``phpzone.yml`` manually or run:

.. code-block:: bash

    $ vendor/bin/phpzone --init

which would automatically create the ``phpzone.yml`` in the project folder.

.. note::
    If the ``phpzone.yml`` already exists, it will **not** be overwritten.

Custom path
^^^^^^^^^^^

There is also provided an option for the custom path. You can just basically use:

.. code-block:: bash

    $ vendor/bin/phpzone --config path/to/config.yml

Definitions
^^^^^^^^^^^

The file can contain none or each of the following definitions:

========== ======== ==================================================================
imports    Optional Including another files.
extensions Optional Register all required extensions and their configurations included
========== ======== ==================================================================

One example rules them all:

.. code-block:: yaml

    imports:
        - { resource: relative/path/to/file_1.yml } # key "resource" is required!
        - { resource: relative/path/to/another/file_2.yml }
    extensions:
        Namespace\Foo\ClassFoo: ~ # simple registration of an extension without any value
        Namespace\Bar\ClassBar:
            some_key: some_value
        Namespace\Baz\ClassBaz:
            - value 1 of an array
            - value 2 of an array

.. important::
    Every extension has it's own configuration values and their structure depends on the specification of the extension.
    For more details follow instructions according the extension.

.. _Composer: https://getcomposer.org
.. _YAML: http://symfony.com/doc/current/components/yaml/yaml_format.html

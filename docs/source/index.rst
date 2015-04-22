PhpZone
=======

.. toctree::
    :hidden:

    getting-started
    basic-commands
    official-extensions
    creating-an-extension


PhpZone is a generic tool for the easy creation of `YAML`_ configured console applications. Its primary purpose is to
provide a centralized automation tool for developers to simplify development workflow.

.. note::
    As it's built on `Symfony components <http://symfony.com/components>`_ without rapid custom modifications,
    it can be used as an application skeleton for any individual commands.

.. attention::
    Its power is based on simplicity of centralized configuration via `YAML`_ and main value comes from extensions.

Basic Usage
-----------

One example is more than hundreds words so let's go to show one.

Create a ``phpzone.yml`` file in the root of a project:

.. code-block:: yaml

    extensions:
        PhpZone\Shell\Shell: # register an extension with a configuration
            tests:
                - vendor/bin/behat
                - vendor/bin/phpunit
                - vendor/bin/phpspec

and run:

.. code-block:: bash

    $ vendor/bin/phpzone tests

As you would expect, the configuration contains the definition for the command ``tests`` and when you run it, all
defined sub-commands will be executed.

.. important::
    The ``PhpZone\Shell\Shell`` extension is not a part of the ``phpzone/phpzone`` package, but an aside project
    based on PhpZone. More info in a chapter dedicated to official extensions.


.. _YAML: http://symfony.com/doc/current/components/yaml/yaml_format.html

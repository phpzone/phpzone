PhpZone
=======

.. toctree::
    :hidden:
    :caption: PhpZone
    :numbered:

    getting-started
    basic-commands
    official-extensions
    creating-an-extension

.. toctree::
    :hidden:
    :caption: Links

    PhpZone Docker <http://docs.phpzone.org/projects/phpzone-docker>
    PhpZone Shell <http://docs.phpzone.org/projects/phpzone-shell>


PhpZone is a generic tool for the easy creation of `YAML`_ configured console applications. Its primary purpose is to
provide a centralized automation tool for developers to simplify development workflow. **Basically it is a wrapper
around commands to provide a unified command line tool.**

.. note::
    As it's built on `Symfony components <http://symfony.com/components>`_ without rapid custom modifications,
    it can be used as an application skeleton for any individual commands.

.. attention::
    Its power is based on simplicity of centralized configuration via `YAML`_ and main value comes from extensions.

Basic Usage
-----------

An example speaks a hundred words so let’s go through one.

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
    based on PhpZone. More info in a chapter dedicated to `official extensions <official-extensions.html>`_.


.. _YAML: http://symfony.com/doc/current/components/yaml/yaml_format.html

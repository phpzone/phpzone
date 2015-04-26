# PhpZone

[![Build Status](https://travis-ci.org/phpzone/phpzone.svg?branch=master)](https://travis-ci.org/phpzone/phpzone)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phpzone/phpzone/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phpzone/phpzone/?branch=master)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/e4eca535-7714-4ae2-901a-99a735dd9915/mini.png)](https://insight.sensiolabs.com/projects/e4eca535-7714-4ae2-901a-99a735dd9915)

[![Latest Stable Version](https://poser.pugx.org/phpzone/phpzone/v/stable.png)](https://packagist.org/packages/phpzone/phpzone)
[![Total Downloads](https://poser.pugx.org/phpzone/phpzone/downloads.png)](https://packagist.org/packages/phpzone/phpzone)
[![License](https://poser.pugx.org/phpzone/phpzone/license.png)](https://packagist.org/packages/phpzone/phpzone)

PhpZone is a generic tool for the easy creation of [YAML] configured console applications. Its primary purpose is to
provide a centralized automation tool for developers to simplify development workflow.

As it's built on [Symfony components] without rapid custom modifications,
it can be used as an application skeleton for any individual commands.

**Its power is based on simplicity of centralized configuration via [YAML] and main value comes from extensions.**

## Basic Usage

An example speaks a hundred words so let’s go through one.

Create a `phpzone.yml` file in the root of a project:

```yaml
extensions:
    PhpZone\Shell\Shell: # register an extension with a configuration
        tests:
            - vendor/bin/behat
            - vendor/bin/phpunit
            - vendor/bin/phpspec
```

and run:

```bash
$ vendor/bin/phpzone tests
```

As you would expect, the configuration contains the definition for the command `tests` and when you run it, all
defined sub-commands will be executed.

The `PhpZone\Shell\Shell` extension is not a part of the `phpzone/phpzone` package, but an aside project
based on PhpZone. More info [PhpZone Shell].

## Documentation

For more details visit [PhpZone documentation].


[YAML]: http://symfony.com/doc/current/components/yaml/yaml_format.html
[Symfony components]: http://symfony.com/components
[PhpZone Shell]: https://github.com/phpzone/shell
[PhpZone documentation]: http://docs.phpzone.org

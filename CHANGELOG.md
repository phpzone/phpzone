# Change Log
All notable changes to this project will be documented in this file.

## [Unreleased][unreleased]
### Added
- Command for automatic initialization of the config file called by the `--init` option.
- Shell environment activated by the `--shell` option.
- Automatic registration of services with the tag `event_subscriber` as an event subscriber.
- Automatic registration of services with the tag `event_listener` as an event listener.
- Implementation of [Symfony Event Dispatcher].
- Support for importing resources in the config file via the `imports:` definition.
- Implementation of [Symfony Debug] for better error and 
exception handling.

### Changed
- Force ANSI color by the `--colors` options and no ANSI color by the `--no-colors` option.
- Registration of extensions via [Symfony DependencyInjection Extension] system.

## 0.1.0 - 2015-04-07
### Added
- Automatic registration of services with the `command` tag as a command.
- Custom path for the config file defined by the `--config` option.
- Loading extensions defined in the [YAML] config file via
the `extensions:` definition.
- Implementation of [Symfony DependencyInjection].
- CLI application based on [Symfony Console].

[unreleased]: https://github.com/phpzone/phpzone/compare/0.1.0...HEAD

[Symfony Event Dispatcher]: http://symfony.com/doc/current/components/event_dispatcher/index.html
[Symfony Debug]: http://symfony.com/doc/current/components/debug/index.html
[Symfony DependencyInjection Extension]: http://symfony.com/doc/current/components/dependency_injection/compilation.html
[YAML]: http://symfony.com/doc/current/components/yaml/index.html
[Symfony DependencyInjection]: http://symfony.com/doc/current/components/dependency_injection/index.html
[Symfony Console]: http://symfony.com/doc/current/components/console/index.html

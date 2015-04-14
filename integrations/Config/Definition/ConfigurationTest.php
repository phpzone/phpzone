<?php

namespace PhpZone\PhpZone\Integration\Config\Definition;

use PhpZone\PhpZone\Config\Definition\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_properly_parse_configuration()
    {
        $configTest = array(
            'imports' => array(
                array(
                    'resource' => 'resource.yml',
                ),
            ),
            'extensions' => array(
                'Path\To\Extension1' => null,
                'Path\To\Extension2' => 'value 2',
                'Path\To\Extension3' => array('value 3'),
                'Path\To\Extension4' => array('key 4' => 'value 4')
            ),
        );

        $configs = array($configTest);

        $processor = new Processor();
        $configuration = new Configuration();
        $processedConfiguration = $processor->processConfiguration($configuration, $configs);

        expect($processedConfiguration)->shouldBeLike(array(
            'imports' => array(
                array(
                    'resource' => 'resource.yml',
                ),
            ),
            'extensions' => array(
                'Path\To\Extension1' => array(),
                'Path\To\Extension2' => array('value 2'),
                'Path\To\Extension3' => array('value 3'),
                'Path\To\Extension4' => array('key 4' => 'value 4')
            ),
        ));
    }

    public function test_it_should_properly_parse_when_options_are_null()
    {
        $configTest = array(
            'imports' => null,
            'extensions' => null,
        );

        $configs = array($configTest);

        $processor = new Processor();
        $configuration = new Configuration();
        $processedConfiguration = $processor->processConfiguration($configuration, $configs);

        expect($processedConfiguration)->shouldBeLike(array(
            'imports' => array(),
            'extensions' => array(),
        ));
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function test_it_should_fail_when_string_in_extensions_given()
    {
        $configTest = array(
            'exceptions' => 'exception',
        );

        $configs = array($configTest);

        $processor = new Processor();
        $configuration = new Configuration();
        $processor->processConfiguration($configuration, $configs);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function test_it_should_fail_when_list_in_extensions_given()
    {
        $configTest = array(
            'exceptions' => array('exception'),
        );

        $configs = array($configTest);

        $processor = new Processor();
        $configuration = new Configuration();
        $processor->processConfiguration($configuration, $configs);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function test_it_should_fail_when_unexpected_base_option_given()
    {
        $configTest = array(
            'unexpected' => 'option',
        );

        $configs = array($configTest);

        $processor = new Processor();
        $configuration = new Configuration();
        $processor->processConfiguration($configuration, $configs);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function test_it_should_fail_when_string_in_imports_given()
    {
        $configTest = array(
            'imports' => 'resource.yml',
        );

        $configs = array($configTest);

        $processor = new Processor();
        $configuration = new Configuration();
        $processor->processConfiguration($configuration, $configs);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function test_it_should_fail_when_no_resource_option_in_imports_given()
    {
        $configTest = array(
            'imports' => array(
                array('resource.yml'),
            ),
        );

        $configs = array($configTest);

        $processor = new Processor();
        $configuration = new Configuration();
        $processor->processConfiguration($configuration, $configs);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function test_it_should_fail_when_empty_resource_option_in_imports_given()
    {
        $configTest = array(
            'imports' => array(
                array('resource' => null),
            ),
        );

        $configs = array($configTest);

        $processor = new Processor();
        $configuration = new Configuration();
        $processor->processConfiguration($configuration, $configs);
    }
}

<?php

/**
 * Shade
 *
 * @version 1.0.0
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */

namespace Shade;

/**
 * Configuration test
 *
 * @package Shade
 * @author  Denis Shapkin <i@denis-shapkin.ru>
 */
class ConfigTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Config CRUD
     */
    public function testConfigCRUD()
    {
        $originalConfigValue = [
            'key1' => 'key1Value',
            'key2' => 10,
            'key3' => $key3SubConfigValue = [
                'key4' => 'key4Value',
                'key5' => $key5SubConfigValue = [
                    'key6' => $testObjectValue = new \stdClass()
                ]
            ]
        ];

        $testObjectValue->testProperty = 'test';

        $config = new Config($originalConfigValue);

        $this->assertEquals($originalConfigValue, $config->getValue());

        $config->key7 = 'key7Value';
        $config->key8->key9 = 'key9Value';
        $config->key10 = [
            'key11' => 'key11Value',
            'key12' => [
                'key13' => 'key13Value'
            ]
        ];

        $this->assertInstanceOf('\Shade\Config', $config->key1);
        $this->assertEquals('key1Value', $config->key1->getValue());
        $this->assertEquals($key3SubConfigValue, $config->key3->getValue());
        $this->assertEquals('key4Value', $config->key3->key4->getValue());
        $this->assertEquals($key5SubConfigValue, $config->key3->key5->getValue());
        $this->assertEquals('test', $config->key3->key5->key6->getValue()->testProperty);

        $this->assertEquals('key7Value', $config->key7->getValue());
        $this->assertEquals('key9Value', $config->key8->key9->getValue());

        $this->assertEquals('key11Value', $config->key10->key11->getValue());
        $this->assertEquals('key13Value', $config->key10->key12->key13->getValue());

        $this->assertFalse(isset($config->nonexistentKey));

        $this->assertInstanceOf('\Shade\Config', $config->nonexistentKey);
        $this->assertNull($config->nonexistentKey->getValue());

        $this->assertFalse(isset($config->nonexistentKey));

        $this->assertInstanceOf('\Shade\Config', $config->nonexistentKey->nonexistentKey);
        $this->assertNull($config->nonexistentKey->nonexistentKey->getValue());

        $this->assertFalse(isset($config->nonexistentKey));
        $this->assertFalse(isset($config->nonexistentKey->nonexistentKey));
        $this->assertFalse(isset($config->key1->nonexistentKey));
        $this->assertTrue(isset($config->key1));

        $this->assertInstanceOf('\Shade\Config', $config->key2->nonexistentKey);
        $this->assertEquals(10, $config->key2->getValue());

        $config->key2->setValue(20);
        $this->assertEquals(20, $config->key2->getValue());

        $config->key2 = 30;
        $this->assertEquals(30, $config->key2->getValue());

        $config->key2->setValue($key2SubConfigValue = ['key14' => 'key14Value']);
        $this->assertEquals($key2SubConfigValue, $config->key2->getValue());

        $config->key15 = [20];
        $this->assertEquals(20, $config->key15->{0}->getValue());

        $config->key16 = 10;
        $this->assertEquals(10, $config->key16->getValue());

        unset($config->key16);
        $this->assertFalse(isset($config->key16));
        $this->assertNull($config->key16->getValue());

        unset($config->nonexistentKey2->nonexistentKey3);

        $config->key17->setValue([1, 2]);
        $config->key17->setValue(40);

        $this->assertEquals(40, $config->key17->getValue());

        $this->assertEquals(
            [
                'key1' => 'key1Value',
                'key2' => [
                    'key14' => 'key14Value',
                ],
                'key3' => [
                    'key4' => 'key4Value',
                    'key5' => [
                        'key6' => $testObjectValue,
                    ],
                ],
                'key7' => 'key7Value',
                'key8' => [
                    'key9' => 'key9Value',
                ],
                'key10' => [
                    'key11' => 'key11Value',
                    'key12' => [
                        'key13' => 'key13Value',
                    ],
                ],
                'key15' => [20],
                'key17' => 40,
            ],
            $config->getValue()
        );
    }

    /**
     * Test Config merge
     */
    public function testMerge()
    {
        $config1 = new Config(
            $config1OriginalValue = [
                'key1' => 'config1key1Value',
                'key2' => 10,
                'key3' => [
                    'key4' => 'config1Key4Value',
                    'key5' => [
                        'key6' => 'config1Key6Value'
                    ]
                ]
            ]
        );
        $config2 = new Config(
            $config2OriginalValue = [
                'key1' => 'config2Key1Value',
                'key3' => [
                    'key5' => [
                        'key6' => 'config2Key6Value'
                    ]
                ]
            ]
        );

        $this->assertEquals(
            [
                'key1' => 'config2Key1Value',
                'key2' => 10,
                'key3' => [
                    'key4' => 'config1Key4Value',
                    'key5' => [
                        'key6' => 'config2Key6Value'
                    ]
                ]
            ],
            $config1->merge($config2)->getValue()
        );

        $this->assertEquals(
            $config1OriginalValue,
            $config1->getValue()
        );

        $this->assertEquals(
            $config2OriginalValue,
            $config2->getValue()
        );

        $config3 = new Config(10);
        $config4 = new Config(20);

        $this->assertEquals(
            20,
            $config3->merge($config4)->getValue()
        );

        $this->assertEquals(
            10,
            $config1->merge($config3)->getValue()
        );

        $this->assertEquals(
            $config1OriginalValue,
            $config3->merge($config1)->getValue()
        );

        $config5 = new Config();

        $this->assertEquals(
            $config1OriginalValue,
            $config1->merge($config5)->getValue()
        );

        $this->assertEquals(
            $config1OriginalValue,
            $config5->merge($config1)->getValue()
        );
    }

    /**
     * Test overwrite
     */
    public function testOverwrite()
    {
        $config1 = new Config(
            $config1OriginalValue = [
                'key1' => 'config1key1Value',
                'key2' => 10,
                'key3' => [
                    'key4' => 'config1Key4Value',
                    'key5' => [
                        'key6' => 'config1Key6Value'
                    ]
                ]
            ]
        );
        $config2 = new Config(
            $config2OriginalValue = [
                'key1' => 'config2Key1Value',
                'key3' => [
                    'key5' => [
                        'key6' => 'config2Key6Value'
                    ]
                ]
            ]
        );

        $config1->overwrite($config2);

        $this->assertEquals(
            [
                'key1' => 'config2Key1Value',
                'key2' => 10,
                'key3' => [
                    'key4' => 'config1Key4Value',
                    'key5' => [
                        'key6' => 'config2Key6Value'
                    ]
                ]
            ],
            $config1->getValue()
        );

        $this->assertEquals(
            $config2OriginalValue,
            $config2->getValue()
        );

        $config3 = new Config(10);
        $config4 = new Config(20);

        $config3->overwrite($config4);

        $this->assertEquals(
            20,
            $config3->getValue()
        );

        $config1->overwrite($config4);

        $this->assertEquals(
            20,
            $config1->getValue()
        );

        $config4->overwrite($config2);

        $this->assertEquals(
            $config2OriginalValue,
            $config4->getValue()
        );

        $config5 = new Config();

        $config2->overwrite($config5);

        $this->assertEquals(
            $config2OriginalValue,
            $config2->getValue()
        );

        $config5->overwrite($config2);

        $this->assertEquals(
            $config2OriginalValue,
            $config5->getValue()
        );
    }
}

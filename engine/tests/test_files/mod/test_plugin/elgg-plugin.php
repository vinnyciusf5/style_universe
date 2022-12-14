<?php

return [
	'plugin' => [
		'name' => 'Test Plugin',
		'version' => '1.9',
		'activate_on_install' => true,
	],
	'entities' => [
		[
			'type' => 'object',
			'subtype' => 'test_plugin',
			'class' => 'TestPluginObject',
			'capabilities' => [
				'searchable' => true,
			],
		],
	],
	'actions' => [
		'test_plugin/save' => [],
		'test_plugin/delete' => [],
	],
	'widgets' => [
		'test_plugin' => [
			'description' => elgg_echo('test_plugin:widget:description'),
			'context' => ['profile', 'dashboard'],
		],
	],
	'settings' => [
		'default1' => 'set1',
	],
	'user_settings' => [
		'user_default1' => 'set1',
	],
	'routes' => [
		'plugin:foo' => [
			'path' => '/plugin/{foo?}',
			'resource' => 'plugin/foo',
		],
	]
];

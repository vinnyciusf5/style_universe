<?php

namespace Elgg\Di;

use Laminas\Mail\Transport\InMemory;
use phpDocumentor\Reflection\DocBlock\Tag;
use Elgg\IntegrationTestCase;

class InternalContainerIntegrationTest extends IntegrationTestCase {

	/**
	 * @dataProvider servicesListProvider
	 */
	public function testPropertyType($name, $type) {
		$service = _elgg_services()->{$name};

		// support $type like "Foo\Bar|Baz|null"
		$passed = false;
		foreach (explode('|', $type) as $test_type) {
			if ($test_type === 'null') {
				if ($service === null) {
					$passed = true;
				}
			} elseif ($service instanceof $test_type) {
				$passed = true;
			}
		}
		$this->assertTrue($passed, "{$name} did not match type {$type}");
	}

	public function testListProvider() {
		$services = _elgg_services();

		$list = [];
		foreach (self::servicesListProvider() as $item) {
			$list[$item[0]] = $item[1];
		}

		$errors = [];
		foreach ($services->getKnownEntryNames() as $name) {
			if (isset($list[$name])) {
				continue;
			}
			
			if (class_exists($name) || interface_exists($name)) {
				// we only check alias names not full classes
				continue;
			}

			$errors[] = "$name is not present in data provider";
		}

		if ($errors) {
			$this->fail(implode(PHP_EOL, $errors));
		}
	}

	public static function servicesListProvider() {
		$class = new \ReflectionClass(InternalContainer::class);
		$factory  = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
		$phpdoc = $factory->create($class);
		
		$readonly_props = $phpdoc->getTagsByName('property-read');
		$sets = [];
		/* @var Tag[] $readonly_props */
		foreach ($readonly_props as $prop) {
			$name = $prop->getVariableName();
			$type = $prop->getType();

			// stuff set in PHPUnit bootstrap
			if ($name === 'mailer') {
				$type = InMemory::class;
			}

			$sets[] = [$name, $type];
		}

		return $sets;
	}
}

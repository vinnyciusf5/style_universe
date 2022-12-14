<?php

namespace Elgg\Integration;

use Elgg\IntegrationTestCase;

/**
 * Setup entities for getter tests
 */
abstract class ElggCoreGetEntitiesBaseTest extends IntegrationTestCase {

	/**
	 * @var \ElggUser
	 */
	protected $user;
	
	/**
	 * {@inheritDoc}
	 * @see \Elgg\BaseTestCase::up()
	 */
	public function up() {
		$this->user = $this->createUser();
		elgg()->session->setLoggedInUser($this->user);
	}
}

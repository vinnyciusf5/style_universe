<?php

namespace Elgg\Notifications;

/**
 * @group NotificationsService
 */
class NotificationsServiceElggRelationshipTest extends NotificationsServiceIntegrationTestCase {

	public function up() {
		$this->test_object_class = \ElggRelationship::class;
		parent::up();
	}
}

<?php

namespace Elgg\Notifications;

/**
 * @group NotificationsService
 */
class NotificationsServiceElggUserTest extends NotificationsServiceIntegrationTestCase {

	public function up() {
		$this->test_object_class = \ElggUser::class;
		parent::up();
	}
}

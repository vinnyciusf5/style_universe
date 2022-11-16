<?php

namespace Elgg\UserValidationByEmail\Menus;

use Elgg\Menu\MenuItems;

/**
 * Make changes to the admin:users:bulk menu
 *
 * @since 4.2
 */
class AdminUsersBulk {
	
	/**
	 * Add the bulk actions
	 *
	 * @param \Elgg\Hook $hook 'register' 'menu:admin:users:bulk'
	 *
	 * @return void|MenuItems
	 */
	public static function register(\Elgg\Hook $hook) {
		if (!elgg_is_admin_logged_in() || $hook->getParam('filter_value') !== 'unvalidated') {
			return;
		}
		
		/* @var $return MenuItems */
		$return = $hook->getValue();
		
		$return[] = \ElggMenuItem::factory([
			'name' => 'resend_validation',
			'icon' => 'envelope',
			'text' => elgg_echo('uservalidationbyemail:admin:resend_validation'),
			'href' => elgg_generate_action_url('uservalidationbyemail/resend_validation', [], false),
			'confirm' => elgg_echo('uservalidationbyemail:confirm_resend_validation_checked'),
			'priority' => 100,
		]);
		
		return $return;
	}
}

<?php

namespace Elgg\Discussions;

/**
 * Widget related functions
 */
class Widgets{

	/**
	 * Set the title URL for the discussions widget
	 *
	 * @param \Elgg\Hook $hook 'entity:url', 'object'
	 *
	 * @return void|string
	 */
	public static function widgetURL(\Elgg\Hook $hook) {
		
		$return_value = $hook->getValue();
		if (!empty($return_value)) {
			// someone already set an url
			return;
		}
		
		$widget = $hook->getEntityParam();
		if (!$widget instanceof \ElggWidget || $widget->handler !== 'discussions') {
			return;
		}
		
		$owner = $widget->getOwnerEntity();
		if ($owner instanceof \ElggUser) {
			if ($widget->context === 'dashboard') {
				return elgg_generate_url('collection:object:discussion:my_groups', ['username' => $owner->username]);
			}
			return elgg_generate_url('collection:object:discussion:owner', ['username' => $owner->username]);
		} elseif ($owner instanceof \ElggGroup) {
			return elgg_generate_url('collection:object:discussion:group', ['guid' => $owner->guid]);
		}
		
		return elgg_generate_url('collection:object:discussion:all');
	}
}

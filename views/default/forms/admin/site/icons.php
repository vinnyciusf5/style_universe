<?php

$site_icon = elgg_view('output/longtext', ['value' => elgg_echo('admin:site_icons:info')]);

$site_icon .= elgg_view('entity/edit/icon', [
	'entity' => elgg_get_site_entity(),
	'cropper_enabled' => true,
]);

echo elgg_view_module('info', elgg_echo('admin:site_icons:site_icon'), $site_icon);

if (extension_loaded('zip')) {
	$current_zip = elgg_get_config('font_awesome_zip');
	$fa = elgg_view_field([
		'#type' => 'file',
		'#label' => elgg_echo('admin:site_icons:font_awesome:zip'),
		'#help' => elgg_echo('admin:site_icons:font_awesome:zip:help'),
		'name' => 'font_awesome_zip',
		'value' => $current_zip,
		'accept' => '.zip',
	]);
	
	if ($current_zip) {
		$fa .= elgg_view_field([
			'#type' => 'checkbox',
			'#label' => elgg_echo('admin:site_icons:font_awesome:remove_zip'),
			'name' => 'remove_font_awesome_zip',
			'value' => 1,
			'switch' => true,
		]);
	}
	
	echo elgg_view_module('info', elgg_echo('admin:site_icons:font_awesome'), $fa);
}

$footer = elgg_view_field([
	'#type' => 'submit',
	'value' => elgg_echo('save'),
]);
elgg_set_form_footer($footer);

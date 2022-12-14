<?php
/**
 * Messages folder view (inbox, sent)
 *
 * Provides form body for mass deleting messages
 *
 * @uses $vars['list']   List of messages
 * @uses $vars['folder'] The folder currently looking at
 */

elgg_require_js('forms/messages/process');

$list = elgg_extract('list', $vars);

echo elgg_format_element('div', ['class' => 'messages-container'], $list);

// create footer
$buttons = [];
$buttons[] = [
	'#type' => 'submit',
	'value' => elgg_echo('delete'),
	'name' => 'delete',
	'class' => 'elgg-button-delete',
	'title' => elgg_echo('deleteconfirm:plural'),
	'data-confirm' => elgg_echo('deleteconfirm:plural'),
];

if (elgg_extract('folder', $vars) == 'inbox') {
	$buttons[] = [
		'#type' => 'submit',
		'value' => elgg_echo('messages:markread'),
		'name' => 'read',
	];
}

$buttons[] = [
	'#type' => 'button',
	'value' => elgg_echo('messages:toggle'),
	'class' => 'elgg-button-cancel',
	'id' => 'messages-toggle',
];

$footer = elgg_view('input/fieldset', [
	'align' => 'horizontal',
	'justify' => 'right',
	'fields' => $buttons,
]);

elgg_set_form_footer($footer);

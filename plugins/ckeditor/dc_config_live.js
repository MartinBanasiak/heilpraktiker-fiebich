/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	config.toolbar = [
		{ name: 'document', items: ['Cut', 'Copy', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo','-','Link', 'Unlink','-','Image', 'FontAwesome', 'Emojione'] },
		{ name: 'insert', items: [ 'Table', 'HorizontalRule', 'SpecialChar','CreateDiv','-','Maximize','ShowBlocks','Source'] },
		'/',
		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-' ] },
		{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
		{ name: 'styles', items: [ 'Format', 'RemoveFormat', 'Font', 'FontSize' ] },
	];

// Toolbar groups configuration.
config.toolbarGroups = [
	{ name: 'document', groups: [ 'document', 'clipboard','undo','links','other' ] },
	{ name: 'clipboard', groups: [ 'clipboard', 'undo','other' ] },
	{ name: 'insert', groups: ['insert','mode'] },
	{ name: 'editing', groups: [ 'find', 'spellchecker' ] },
	{ name: 'forms'},
	'/',
	{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
	{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi' ] },
	{ name: 'styles' },
	{ name: 'colors' },
	{ name: 'others' },
	{ name: 'about' }
];
	config.language_list =
		[
			'en:English', 'da:Danish', 'nl:Dutch',
			'fi:Finish', 'fr:French', 'de:German',
			'el:Greek', 'it:Italian', 'nb:Norwegian Bokmål',
			'pt:Portuguese', 'es:Spanish', 'sv:Swedish',
		];
config.extraPlugins = 'emojione,adv_link';
config.removePlugins = 'link';
config.allowedContent = true;
config.forcePasteAsPlainText = true;
config.enterMode = CKEDITOR.ENTER_BR;
config.skin = 'moono-lisa';
};
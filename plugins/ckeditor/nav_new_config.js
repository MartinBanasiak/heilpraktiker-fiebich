/**
 * @license Copyright (c) 2003-2014, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {

	config.toolbar = [
		{ name: 'document', items: ['Cut', 'Copy', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo','-','Link', 'Unlink', 'Anchor','-','Image', 'FontAwesome','Emojione'] },
		{ name: 'insert', items: [ 'Table', 'HorizontalRule', 'SpecialChar','Createdc_placeholder','CreateDiv','-','Maximize','ShowBlocks','Source'] },
		'/',
		{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-' ] },
		{ name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language' ] },
		{ name: 'styles', items: [ 'Format', 'RemoveFormat', 'Font', 'FontSize' ] },
	];

	/*
	config.toolbar = [
		{ name: 'top', items: [ 'Undo', 'Redo', '-', 'Cut', 'Copy', 'PasteText', '-', 'Link', 'Unlink', 'Anchor', '-', 'Image', 'Table', 'HorizontalRule', 'SpecialChar', 'CreatePlaceholder', '-', 'Source', 'ShowBlocks'] },
		'/',
		{ name: 'bottom', items: [ 'Bold', 'Italic', 'Underline', '-', 'RemoveFormat', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'Outdent', 'Indent', '-', 'NumberedList', 'BulletedList', '-', 'Format' ] },
	]
	*/

	/*config.toolbarGroups = [
		{ name: 'clipboard', groups: [ 'undo', 'clipboard' ] },
		{ name: 'links', groups: [ 'links' ] },
		{ name: 'insert', groups: [ 'insert' ] },
		{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
		{ name: 'tools', groups: [ 'tools' ] },
		'/',
		{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
		{ name: 'paragraph', groups: [ 'align', 'indent', 'list', 'blocks', 'bidi', 'paragraph' ] },
		{ name: 'styles', groups: [ 'styles' ] },
		{ name: 'forms', groups: [ 'forms' ] },
		{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
		'/',
		{ name: 'colors', groups: [ 'colors' ] },
		{ name: 'others', groups: [ 'others' ] },
		{ name: 'about', groups: [ 'about' ] }
	];*/

	config.language_list =
		[
			'en:English', 'da:Danish', 'nl:Dutch',
			'fi:Finish', 'fr:French', 'de:German',
			'el:Greek', 'it:Italian', 'nb:Norwegian Bokmål',
			'pt:Portuguese', 'es:Spanish', 'sv:Swedish',
		];

	config.extraPlugins = 'dc_placeholder,clipboard,emojione,pastefromword,widget,lineutils,colordialog,fontawesome,adv_link';
    config.removePlugins = 'link';
	config.removeButtons = 'Paste,Flash,Smiley,Iframe,PageBreak,Save,NewPage,Preview,Templates,Print,Maximize,Subscript,Superscript,Blockquote,BidiLtr,BidiRtl,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,Find,Replace,SelectAll,Scayt,TextColor,BGColor,About,Font,FontSize';
	config.forcePasteAsPlainText = true;
	config.enterMode = CKEDITOR.ENTER_BR;
    config.skin = 'moono-lisa';
};

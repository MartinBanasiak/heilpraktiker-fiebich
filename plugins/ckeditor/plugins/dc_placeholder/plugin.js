/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/**
 * @fileOverview The "dc_placeholder" plugin.
 *
 */

'use strict';

( function() {
	CKEDITOR.plugins.add( 'dc_placeholder', {
		requires: 'widget,dialog',
		lang: 'de,en', // %REMOVE_LINE_CORE%
		icons: 'dc_placeholder', // %REMOVE_LINE_CORE%
		hidpi: true, // %REMOVE_LINE_CORE%

		onLoad: function() {
			// Register styles for dc_placeholder widget frame.
			CKEDITOR.addCss( '.cke_dc_placeholder{background-color:#ff0}' );
		},

		init: function( editor ) {

			var lang = editor.lang.dc_placeholder;

			// Register dialog.
			CKEDITOR.dialog.add( 'dc_placeholder', this.path + 'dialogs/dc_placeholder.js' );

			// Put ur init code here.
			editor.widgets.add( 'dc_placeholder', {
				// Widget code.
				dialog: 'dc_placeholder',
				pathName: lang.pathName,
				// We need to have wrapping element, otherwise there are issues in
				// add dialog.
				template: '<span class="cke_dc_placeholder">%%</span>',

				downcast: function() {
					return new CKEDITOR.htmlParser.text( '%' + this.data.name + '%' );
				},

				init: function() {
					// Note that dc_placeholder markup characters are stripped for the name.
					this.setData( 'name', this.element.getText().slice( 1, -1 ) );
				},

				data: function() {
					this.element.setText( '%' + this.data.name + '%' );
				},

				getLabel: function() {
					return this.editor.lang.widget.label.replace( /%1/, this.data.name + ' ' + this.pathName );
				}
			} );

			editor.ui.addButton && editor.ui.addButton( 'Createdc_placeholder', {
				label: lang.toolbar,
				command: 'dc_placeholder',
				toolbar: 'insert,5',
				icon: 'dc_placeholder'
			} );
		},

		afterInit: function( editor ) {
			var dc_placeholderReplaceRegex = /%([^\[\]])+%/g;

			editor.dataProcessor.dataFilter.addRules( {
				text: function( text, node ) {
					var dtd = node.parent && CKEDITOR.dtd[ node.parent.name ];

					// Skip the case when dc_placeholder is in elements like <title> or <textarea>
					// but upcast dc_placeholder in custom elements (no DTD).
					if ( dtd && !dtd.span )
						return;

					return text.replace( dc_placeholderReplaceRegex, function( match ) {
						// Creating widget code.
						var widgetWrapper = null,
							innerElement = new CKEDITOR.htmlParser.element( 'span', {
								'class': 'cke_dc_placeholder'
							} );

						// Adds dc_placeholder identifier as innertext.
						innerElement.add( new CKEDITOR.htmlParser.text( match ) );
						widgetWrapper = editor.widgets.wrapElement( innerElement, 'dc_placeholder' );

						// Return outerhtml of widget wrapper so it will be placed
						// as replacement.
						return widgetWrapper.getOuterHtml();
					} );
				}
			} );
		}
	} );

} )();


/**
 * @license Copyright (c) 2003-2017, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

/**
 * @fileOverview Definition for placeholder plugin dialog.
 *
 */

'use strict';

CKEDITOR.dialog.add( 'placeholder', function( editor ) {
	var lang = editor.lang.placeholder,
		generalLabel = editor.lang.common.generalTab,
		validNameRegex = /^[^\[\]<>]+$/;

	return {
		title: lang.title,
		minWidth: 300,
		minHeight: 80,
		contents: [
			{
				id: 'info',
				label: generalLabel,
				title: generalLabel,
				elements: [
					// Dialog window UI elements.
					{
						id: 'name',
						type: 'select',
						style: 'width: 100%;',
						label: lang.name,
                        items : [
                        	[ lang.customerNo, 'customer_no' ],
							[ lang.customerName, 'customer_name' ],
							[ lang.userName, 'user_name' ],
							[ lang.login, 'login' ],
							[ lang.password, 'password' ],
							[ lang.passwordLink, 'password_link' ],
							[ lang.email, 'email' ],
							[ lang.orderNo, 'order_no' ],
							[ lang.trackingNo, 'tracking_no' ],
							[ lang.trackingLink, 'tracking_link' ],
							[ lang.invoiceNo, 'invoice_no' ],
							[ lang.invoiceAmount, 'invoice_amount' ],
							[ lang.shipmentNo, 'shipment_no' ],
							[ lang.crMemoNo, 'cr_memo_no' ],
							[ lang.shopName, 'name' ],
							[ lang.error, 'error' ] ],
						'default': '',
						required: true,
						validate: CKEDITOR.dialog.validate.regex( validNameRegex, lang.invalidName ),
						setup: function( widget ) {
							this.setValue( widget.data.name );
						},
						commit: function( widget ) {
							widget.setData( 'name', this.getValue() );
						}
					}
				]
			}
		]
	};
} );

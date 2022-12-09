var el = wp.element.createElement,
	registerBlockType = wp.blocks.registerBlockType,
	ServerSideRender = wp.components.ServerSideRender,
	TextControl = wp.components.TextControl,
	SelectControl = wp.components.SelectControl,
	InspectorControls = wp.editor.InspectorControls;

registerBlockType( 'ultimate-appointment-scheduling/ewd-uasp-display-booking-block', {
	title: 'Appointment Booking Form',
	icon: 'calendar-alt',
	category: 'ewd-uasp-blocks',
	attributes: {
		display_type: { type: 'string' },
		redirect_page: { type: 'string' },
	},

	edit: function( props ) {
		var returnString = [];
		returnString.push(
			el( InspectorControls, {},
				el( SelectControl, {
					label: 'Display Type',
					value: props.attributes.display_type,
					options: [ {value: 'Dropdown', label: 'Dropdown'}, {value: 'Calendar', label: 'Calendar'} ],
					onChange: ( value ) => { props.setAttributes( { display_type: value } ); },
				} ),
				el( TextControl, {
					label: 'Redirect Page',
					value: props.attributes.redirect_page,
					onChange: ( value ) => { props.setAttributes( { redirect_page: value } ); },
				} ),
			),
		);
		returnString.push( el( 'div', { class: 'ewd-uasp-admin-block ewd-uasp-admin-block-qppointment-form' }, 'Appointment Booking Form' ) );
		return returnString;
	},

	save: function() {
		return null;
	},
} );


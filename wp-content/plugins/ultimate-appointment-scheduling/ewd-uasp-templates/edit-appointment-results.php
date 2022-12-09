<div class='ewd-uasp-edit-appointment-table'>

	<table>

		<thead>
			<tr>
				<th><?php echo esc_html( $this->get_label( 'label-name' ) ); ?></th>
				<th><?php echo esc_html( $this->get_label( 'label-phone' ) ); ?></th>
				<th><?php echo esc_html( $this->get_label( 'label-email' ) ); ?></th>
				<th><?php echo esc_html( $this->get_label( 'label-appointment-date' ) ); ?></th>
				<th></th>
			</tr>
		</thead>
		
		<tbody>

			<?php foreach ( $this->edit_appointment_search_results as $appointment ) { ?>

				<form method='post' action='#'>
					<input type='hidden' name='ewd_uasp_appointment_id' value='<?php echo esc_attr( $appointment->id ); ?>' />
					<tr>
						<td><?php echo esc_html( $appointment->client_name ); ?></td>
						<td><?php echo esc_html( $appointment->client_phone ); ?></td>
						<td><?php echo esc_html( $appointment->client_email ); ?></td>
						<td><?php echo esc_html( $appointment->start ); ?></td>
						<td><input type='submit' name='ewd_uasp_edit_appointment' value='<?php echo esc_attr( __( 'Edit Appointment', 'ultimate-appointment-scheduling' ) ); ?>' /></td>
					</tr>
				</form>

			<?php } ?>

		</tbody>
	</table>
</div>
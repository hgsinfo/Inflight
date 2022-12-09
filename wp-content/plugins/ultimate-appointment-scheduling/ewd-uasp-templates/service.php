<div class='ewd-uasp-container ewd-uasp-service <?php echo ( $this->multi_step_booking ? 'ewd-uasp-hidden' : '' ); ?>'>

  	<div class='ewd-uasp-title ewd-uasp-service-label'>
  		<?php echo esc_html( $this->get_label( 'label-service-title' ) ); ?>
  	</div>

  	<div class='ewd-uasp-service-content'>

		<div class='ewd-uasp-field'>

			<div class='ewd-uasp-input-label'>
				<?php echo esc_html( $this->get_label( 'label-location' ) ); ?>
			</div>

			<div class='ewd-uasp-service-input'>
	
				<?php if ( sizeof( $this->locations ) == 1 ) { ?>

					<input type='hidden' id='ewd-uasp-location-id' name='ewd_uasp_location_id' value='<?php echo esc_attr( $this->locations[0]->ID ); ?>' /> <?php echo esc_html( $this->locations[0]->post_title ); ?>

				<?php } else { ?>
		
					<select id='ewd-uasp-location-id' name='ewd_uasp_location_id'>
						
						<?php foreach ( $this->locations as $location ) { ?>
							
							<option value='<?php echo esc_attr( $location->ID ); ?>' <?php echo ( $this->location == $location->ID ? 'selected' : '' ); ?> ><?php echo esc_html( $location->post_title ); ?></option>
						
						<?php } ?>

					</select>

				<?php } ?>

			</div>
	
		</div>
	
		<div class='clear'></div>

		<div class='ewd-uasp-field'>

			<div class='ewd-uasp-input-label'>
				<?php echo esc_html( $this->get_label( 'label-service' ) ); ?>
			</div>

			<div class='ewd-uasp-service-input'>
	
				<?php if ( sizeof( $this->services ) == 1 ) { ?>

					<input type='hidden' id='ewd-uasp-service-id' name='ewd_uasp_service_id' value='<?php echo esc_attr( $this->services[0]->ID ); ?>' /> <?php echo esc_html( $this->services[0]->post_title ); ?>

				<?php } else { ?>
		
					<select id='ewd-uasp-service-id' name='ewd_uasp_service_id'>
						
						<?php foreach ( $this->services as $service ) { ?>
							
							<option value='<?php echo esc_attr( $service->ID ); ?>' <?php echo ( $this->service == $service->ID ? 'selected' : '' ); ?> ><?php echo esc_html( $service->post_title ); ?></option>
						
						<?php } ?>

					</select>

				<?php } ?>

			</div>
	
		</div>

		<div class='clear'></div>

		<div class='ewd-uasp-field'>

			<div class='ewd-uasp-input-label'>
				<?php echo esc_html( $this->get_label( 'label-service-provider' ) ); ?>
			</div>

			<div class='ewd-uasp-service-input'>
	
				<?php if ( sizeof( $this->providers ) == 1 ) { ?>

					<input type='hidden' id='ewd-uasp-provider-id' name='ewd_uasp_provider_id' value='<?php echo esc_attr( $this->providers[0]->ID ); ?>' /> <?php echo esc_html( $this->providers[0]->post_title ); ?>

				<?php } else { ?>
		
					<select id='ewd-uasp-provider-id' name='ewd_uasp_provider_id'>

						<option value='all'><?php echo esc_html( $this->get_label( 'label-any' ) ); ?></option>
						
						<?php foreach ( $this->providers as $provider ) { ?>
							
							<option value='<?php echo esc_attr( $provider->ID ); ?>' <?php echo ( $this->provider == $provider->ID ? 'selected' : '' ); ?> ><?php echo esc_html( $provider->post_title ); ?></option>
						
						<?php } ?>

					</select>

				<?php } ?>

			</div>
	
		</div>

  	</div>

  	<div class='clear'></div>
  	
</div>

<div class='clear'></div>
<div class='ewd-uasp-calendar-container <?php echo ( $this->multi_step_booking ? 'ewd-uasp-hidden' : '' ); ?>'>

    <div id='ewd-uasp-calendar'></div>
    
    <input type='hidden' name='ewd_uasp_appointment_start' id='ewd-uasp-selected-appointment-time' />

    <div id='ewd-uasp-screen-background' class='ewd-uasp-hidden'></div>
    <div id='ewd-uasp-time-select' class='ewd-uasp-hidden'>

        <div id='ewd-uasp-time-location'></div>
        <div id='ewd-uasp-time-service'></div>
        <div id='ewd-uasp-time-service-provider'></div>

        <div id='ewd-uasp-time-select-input-div'>
            <select name='time-select-input'></select>
        </div>

        <div id='ewd-uasp-select-time-button'>
            <?php echo esc_html( $this->get_label( 'label-select-time' ) ); ?>
        </div>
    </div>

</div>
<?php
/**
 * Loop item more button
 */

if ( 'yes' !== $this->get_attr( 'show_more' ) ) {
	return;
}

if ( 'yes'  === $this->get_attr( 'open_new_tab' ) ) {
	$target = "_blank";
} else {
	$target = '';
}

jet_elements_post_tools()->get_post_button( array(
	'class' => 'btn btn-primary elementor-button elementor-size-md jet-more',
	'text'  => $this->get_attr( 'more_text' ),
	'icon'  => $this->html( $this->get_attr( 'more_icon' ), '<span class="jet-elements-icon jet-more-icon">%1$s</span>', array(), false ),
	'html'  => '<div class="jet-more-wrap"><a href="%1$s" %3$s target="' . $target .'"><span class="btn__text">%4$s</span>%5$s</a></div>',
	'echo'  => true,
) );

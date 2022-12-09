<?php
/**
 * Class: Jet_Woo_Widgets_Categories
 * Name: Categories Grid
 * Slug: woo-products-widgets-categories
 */

namespace Elementor;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;
use Elementor\Utils;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class Woo_Products_Widgets_Categories extends WPFE_Woo_Widgets_Base {

	public function get_name() {
		return 'woo-products-widgets-categories';
	}

	public function get_title() {
		return esc_html__( 'Woo Categories Grid', 'woo-product-widgets-for-elementor' );
	}

	public function get_icon() {
		return 'eicon-product-categories';
	}

	public function get_categories() {
		return array( 'woo-product-widgets-for-elementor' );
	}

	public function __shortcode() {
		return woo_elementor_products_widgets_shortocdes()->get_shortcode( $this->get_name() );
	}

	public function get_script_depends() {
		return array( 'woo-elementor-slick-js' );
	}

	public function get_style_depends() {
		return array( 'woo-elementor-slick' );
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_general',
			array(
				'label' => esc_html__( 'General', 'woo-product-widgets-for-elementor' ),
			)
		);

		$attributes = $this->__shortcode()->get_atts();

		foreach ( $attributes as $attr => $settings ) {

			if ( empty( $settings['type'] ) ) {
				continue;
			}

			if ( ! empty( $settings['responsive'] ) ) {
				$this->add_responsive_control( $attr, $settings );
			} else {
				$this->add_control( $attr, $settings );
			}

		}

		$this->end_controls_section();

		$css_scheme = apply_filters(
			'woo-product-widgets-elementor/woo-products-categories/css-scheme',
			array(
				'wrap'          => '.woo-products-categories',
				'column'        => '.woo-products-categories .woo-products-categories__item',
				'inner-box'     => '.woo-products-categories .woo-products-categories__inner-box',
				'thumb'         => '.woo-products-categories .woo-products-category-thumbnail',
				'content'       => '.woo-products-categories .woo-products-categories-content',
				'title-wrap'    => '.woo-products-categories .woo-products-categories-title__wrap',
				'title'         => '.woo-products-categories .woo-products-category-title',
				'count-wrap'    => '.woo-products-categories .woo-products-category-count__wrap',
				'count'         => '.woo-products-categories .woo-products-category-count',
				'excerpt'       => '.woo-products-categories .woo-products-category-excerpt',
				'overlay'       => '.woo-products-categories .woo-products-category-img-overlay',
				'overlay-hover' => '.woo-products-categories .woo-products-category-img-overlay__hover',
			)
		);

		$this->start_controls_section(
			'section_carousel',
			array(
				'label' => esc_html__( 'Carousel', 'woo-product-widgets-for-elementor' ),
			)
		);

		$this->add_control(
			'carousel_enabled',
			array(
				'label'        => esc_html__( 'Enable Carousel', 'woo-product-widgets-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'woo-product-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'woo-product-widgets-for-elementor' ),
				'return_value' => 'yes',
				'default'      => '',
			)
		);

		$this->add_responsive_control(
			'slides_min_height',
			array(
				'label'       => esc_html__( 'Slides Minimal Height', 'woo-product-widgets-for-elementor' ),
				'label_block' => true,
				'type'        => Controls_Manager::NUMBER,
				'default'     => '',
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['inner-box'] => 'min-height: {{VALUE}}px;',
				),

			)
		);

		$this->add_control(
			'slides_to_scroll',
			array(
				'label'     => esc_html__( 'Slides to Scroll', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => '1',
				'options'   => woo_product_widgets_elementor_tools()->get_select_range( 4 ),
				'condition' => array(
					'columns!' => '1',
				),
			)
		);

		$this->add_control(
			'arrows',
			array(
				'label'        => esc_html__( 'Show Arrows Navigation', 'woo-product-widgets-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'woo-product-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'woo-product-widgets-for-elementor' ),
				'return_value' => 'true',
				'default'      => 'true',
			)
		);

		$this->add_control(
			'prev_arrow',
			array(
				'label'     => esc_html__( 'Prev Arrow Icon', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fa fa-angle-left',
				'options'   => woo_product_widgets_elementor_tools()->get_available_prev_arrows_list(),
				'condition' => array(
					'arrows' => 'true',
				),
			)
		);

		$this->add_control(
			'next_arrow',
			array(
				'label'     => esc_html__( 'Next Arrow Icon', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'fa fa-angle-right',
				'options'   => woo_product_widgets_elementor_tools()->get_available_next_arrows_list(),
				'condition' => array(
					'arrows' => 'true',
				),
			)
		);

		$this->add_control(
			'dots',
			array(
				'label'        => esc_html__( 'Show Dots Navigation', 'woo-product-widgets-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'woo-product-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'woo-product-widgets-for-elementor' ),
				'return_value' => 'true',
				'default'      => '',
			)
		);

		$this->add_control(
			'pause_on_hover',
			array(
				'label'        => esc_html__( 'Pause on Hover', 'woo-product-widgets-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'woo-product-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'woo-product-widgets-for-elementor' ),
				'return_value' => 'true',
				'default'      => '',
			)
		);

		$this->add_control(
			'autoplay',
			array(
				'label'        => esc_html__( 'Autoplay', 'woo-product-widgets-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'woo-product-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'woo-product-widgets-for-elementor' ),
				'return_value' => 'true',
				'default'      => 'true',
			)
		);

		$this->add_control(
			'autoplay_speed',
			array(
				'label'     => esc_html__( 'Autoplay Speed', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => array(
					'autoplay' => 'true',
				),
			)
		);

		$this->add_control(
			'infinite',
			array(
				'label'        => esc_html__( 'Infinite Loop', 'woo-product-widgets-for-elementor' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Yes', 'woo-product-widgets-for-elementor' ),
				'label_off'    => esc_html__( 'No', 'woo-product-widgets-for-elementor' ),
				'return_value' => 'true',
				'default'      => 'true',
			)
		);

		$this->add_control(
			'effect',
			array(
				'label'     => esc_html__( 'Effect', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'slide',
				'options'   => array(
					'slide' => esc_html__( 'Slide', 'woo-product-widgets-for-elementor' ),
					'fade'  => esc_html__( 'Fade', 'woo-product-widgets-for-elementor' ),
				),
				'condition' => array(
					'columns' => '1',
				),
			)
		);

		$this->add_control(
			'speed',
			array(
				'label'   => esc_html__( 'Animation Speed', 'woo-product-widgets-for-elementor' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 500,
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_column_style',
			array(
				'label'      => esc_html__( 'Column', 'woo-product-widgets-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->add_control(
			'column_padding',
			array(
				'label'       => esc_html__( 'Column Padding', 'woo-product-widgets-for-elementor' ),
				'type'        => Controls_Manager::DIMENSIONS,
				'size_units'  => array( 'px' ),
				'render_type' => 'template',
				'selectors'   => array(
					'{{WRAPPER}} ' . $css_scheme['column'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} ' . $css_scheme['wrap']   => 'margin-right: -{{RIGHT}}{{UNIT}}; margin-left: -{{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_box_style',
			array(
				'label'      => esc_html__( 'Category Item', 'woo-product-widgets-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->controls_section_box( $css_scheme );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_box_hover_style',
			array(
				'label'      => esc_html__( 'Category Item (hover)', 'woo-product-widgets-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->controls_section_box_hover( $css_scheme );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_thumb_style',
			array(
				'label'      => esc_html__( 'Category Thumbnail', 'woo-product-widgets-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->controls_section_thumbnail( $css_scheme );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style',
			array(
				'label'      => esc_html__( 'Content', 'woo-product-widgets-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->controls_section_content( $css_scheme );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_title_style',
			array(
				'label'      => esc_html__( 'Title', 'woo-product-widgets-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->controls_section_title( $css_scheme );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_count_style',
			array(
				'label'      => esc_html__( 'Count', 'woo-product-widgets-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->controls_section_count( $css_scheme );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_excerpt_style',
			array(
				'label'      => esc_html__( 'Excerpt', 'woo-product-widgets-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->controls_section_excerpt( $css_scheme );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_overlay_style',
			array(
				'label'      => esc_html__( 'Overlay', 'woo-product-widgets-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
			)
		);

		$this->controls_section_overlay( $css_scheme );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_arrows_style',
			array(
				'label'      => esc_html__( 'Carousel Arrows', 'woo-product-widgets-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition'  => array(
					'carousel_enabled' => 'yes'
				)
			)
		);

		$this->controls_section_carousel_arrows();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_dots_style',
			array(
				'label'      => esc_html__( 'Carousel Dots', 'woo-product-widgets-for-elementor' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'show_label' => false,
				'condition'  => array(
					'carousel_enabled' => 'yes'
				)
			)
		);

		$this->controls_section_carousel_dots();

		$this->end_controls_section();

	}

	protected function render() {

		$this->__context = 'render';

		$this->__open_wrap();

		$attributes    = array();
		$settings      = $this->get_settings();
		$shortcode_obj = $this->__shortcode();

		foreach ( $shortcode_obj->get_atts() as $attr => $data ) {
			$attr_val            = $settings[ $attr ];
			$attr_val            = ! is_array( $attr_val ) ? $attr_val : implode( ',', $attr_val );
			$attributes[ $attr ] = $attr_val;
		}

		echo woo_product_widgets_elementor_tools()->get_carousel_wrapper_atts( $shortcode_obj->do_shortcode( $attributes ), $settings );

		$this->__close_wrap();
	}

	protected function controls_section_box( $css_scheme ) {

		$this->add_control(
			'box_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inner-box'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'box_border',
				'label'       => esc_html__( 'Border', 'woo-product-widgets-for-elementor' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['inner-box'],
				'separator'   => 'before'
			)
		);

		$this->add_responsive_control(
			'box_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['inner-box'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'inner_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['inner-box'],
			)
		);

		$this->add_responsive_control(
			'box_padding',
			array(
				'label'      => esc_html__( 'Padding', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['inner-box'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before'
			)
		);

	}

	protected function controls_section_box_hover( $css_scheme ) {

		$this->add_control(
			'box_hover_title',
			array(
				'label'     => esc_html__( 'Title', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'box_hover_title_color',
			array(
				'label'     => esc_html__( 'Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .woo-products-category-title' . ' a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'box_hover_excerpt',
			array(
				'label'     => esc_html__( 'Excerpt', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'box_hover_excerpt_color',
			array(
				'label'     => esc_html__( 'Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .woo-products-category-excerpt' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'box_hover_count',
			array(
				'label'     => esc_html__( 'Count', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'box_hover_count_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .woo-products-category-count' => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'box_hover_count_color',
			array(
				'label'     => esc_html__( 'Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .woo-products-category-count' => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'box_hover_content',
			array(
				'label'     => esc_html__( 'Content', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'box_hover_content_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .woo-products-categories-content' => 'background-color: {{VALUE}}',
				),
			)
		);

	}

	protected function controls_section_thumbnail( $css_scheme ) {

		$this->add_control(
			'thumb_background',
			array(
				'label'     => esc_html__( 'Background Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['thumb'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'thumb_border',
				'label'       => esc_html__( 'Border', 'woo-product-widgets-for-elementor' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['thumb'],
			)
		);

		$this->add_responsive_control(
			'thumb_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['thumb'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'thumb_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['thumb'],
			)
		);

		$this->add_responsive_control(
			'thumb_margin',
			array(
				'label'      => esc_html__( 'Margin', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['thumb'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before'
			)
		);

		$this->add_responsive_control(
			'thumb_padding',
			array(
				'label'      => esc_html__( 'Padding', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['thumb'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

	}

	protected function controls_section_content( $css_scheme ) {

		$this->add_control(
			'content_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['content'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'content_border',
				'label'       => esc_html__( 'Border', 'woo-product-widgets-for-elementor' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['content'],
				'separator'   => 'before'
			)
		);

		$this->add_control(
			'content_hover_border_color',
			array(
				'label'     => esc_html__( 'Hover Border Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .woo-products-categories-content' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'content_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['content'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'content_box_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['content'],
			)
		);

		$this->add_responsive_control(
			'content_margin',
			array(
				'label'      => esc_html__( 'Margin', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['content'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'separator'  => 'before'
			)
		);

		$this->add_responsive_control(
			'content_padding',
			array(
				'label'      => esc_html__( 'Padding', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['content'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

	}

	protected function controls_section_title( $css_scheme ) {

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['title'],
			)
		);

		$this->add_control(
			'title_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->start_controls_tabs( 'tabs_title_color' );

		$this->start_controls_tab(
			'tab_title_color_normal',
			array(
				'label' => esc_html__( 'Normal', 'woo-product-widgets-for-elementor' ),
			)
		);

		$this->add_control(
			'title_color',
			array(
				'label'     => esc_html__( 'Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title'] . ' a' => 'color: {{VALUE}}',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_title_color_hover',
			array(
				'label' => esc_html__( 'Hover', 'woo-product-widgets-for-elementor' ),
			)
		);

		$this->add_control(
			'title_color_hover',
			array(
				'label'     => esc_html__( 'Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title'] . ' a:hover' => 'color: {{VALUE}}!important',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'title_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'woo-product-widgets-for-elementor' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'woo-product-widgets-for-elementor' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'woo-product-widgets-for-elementor' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['title']      => 'text-align: {{VALUE}};',
					'{{WRAPPER}} ' . $css_scheme['title-wrap'] => 'text-align: {{VALUE}};',
				),
				'separator' => 'before'
			)
		);

		$this->add_responsive_control(
			'title_padding',
			array(
				'label'      => esc_html__( 'Padding', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'title_margin',
			array(
				'label'      => esc_html__( 'Margin', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['title'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

	}

	protected function controls_section_count( $css_scheme ) {

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'count_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['count'],
			)
		);

		$this->add_control(
			'count_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['count'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'count_color',
			array(
				'label'     => esc_html__( 'Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['count'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'        => 'count_border',
				'label'       => esc_html__( 'Border', 'woo-product-widgets-for-elementor' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} ' . $css_scheme['count'],
				'separator'   => 'before'
			)
		);

		$this->add_control(
			'count_hover_border_color',
			array(
				'label'     => esc_html__( 'Hover Border Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['inner-box'] . ':hover .woo-products-category-count' => 'border-color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'count_border_radius',
			array(
				'label'      => esc_html__( 'Border Radius', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['count'] => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			array(
				'name'     => 'inner_count_shadow',
				'selector' => '{{WRAPPER}} ' . $css_scheme['count'],
			)
		);

		$this->add_responsive_control(
			'count_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'woo-product-widgets-for-elementor' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'woo-product-widgets-for-elementor' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'woo-product-widgets-for-elementor' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['count-wrap'] => 'text-align: {{VALUE}};',
				),
				'condition' => array(
					'presets!' => array( 'preset-3' )
				),
				'separator' => 'before'
			)
		);

		$this->add_responsive_control(
			'count_padding',
			array(
				'label'      => esc_html__( 'Padding', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['count'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'count_margin',
			array(
				'label'      => esc_html__( 'Margin', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['count'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

	}

	protected function controls_section_excerpt( $css_scheme ) {
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'excerpt_typography',
				'selector' => '{{WRAPPER}} ' . $css_scheme['excerpt'],
			)
		);

		$this->add_control(
			'excerpt_bg',
			array(
				'label'     => esc_html__( 'Background Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['excerpt'] => 'background-color: {{VALUE}}',
				),
			)
		);

		$this->add_control(
			'excerpt_color',
			array(
				'label'     => esc_html__( 'Color', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['excerpt'] => 'color: {{VALUE}}',
				),
			)
		);

		$this->add_responsive_control(
			'excerpt_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
					'left'   => array(
						'title' => esc_html__( 'Left', 'woo-product-widgets-for-elementor' ),
						'icon'  => 'fa fa-align-left',
					),
					'center' => array(
						'title' => esc_html__( 'Center', 'woo-product-widgets-for-elementor' ),
						'icon'  => 'fa fa-align-center',
					),
					'right'  => array(
						'title' => esc_html__( 'Right', 'woo-product-widgets-for-elementor' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['excerpt'] => 'text-align: {{VALUE}};',
				),
				'separator' => 'before'
			)
		);

		$this->add_responsive_control(
			'excerpt_padding',
			array(
				'label'      => esc_html__( 'Padding', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['excerpt'] => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'excerpt_margin',
			array(
				'label'      => esc_html__( 'Margin', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['excerpt'] => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

	}

	protected function controls_section_overlay( $css_scheme ) {

		$this->start_controls_tabs( 'tabs_overlay_style' );

		$this->start_controls_tab(
			'tab_overlay_normal',
			array(
				'label' => esc_html__( 'Normal', 'woo-product-widgets-for-elementor' ),
			)
		);

		$this->add_control(
			'overlay_bg',
			array(
				'label'       => _x( 'Background Type', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => array(
					'color'    => array(
						'title' => _x( 'Classic', 'Background Control', 'woo-product-widgets-for-elementor' ),
						'icon'  => 'fa fa-paint-brush',
					),
					'gradient' => array(
						'title' => _x( 'Gradient', 'Background Control', 'woo-product-widgets-for-elementor' ),
						'icon'  => 'fa fa-barcode',
					),
				),
				'default'     => 'color',
				'label_block' => false,
				'render_type' => 'ui',
			)
		);

		$this->add_control(
			'overlay_bg_color',
			array(
				'label'     => _x( 'Color', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'title'     => _x( 'Background Color', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['overlay'] => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_bg_color_stop',
			array(
				'label'       => _x( 'Location', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( '%' ),
				'default'     => array(
					'unit' => '%',
					'size' => 0,
				),
				'render_type' => 'ui',
				'condition'   => array(
					'overlay_bg' => array( 'gradient' ),
				),
				'of_type'     => 'gradient',
			)
		);

		$this->add_control(
			'overlay_bg_color_b',
			array(
				'label'       => _x( 'Second Color', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#f2295b',
				'render_type' => 'ui',
				'condition'   => array(
					'overlay_bg' => array( 'gradient' ),
				),
				'of_type'     => 'gradient',
			)
		);

		$this->add_control(
			'overlay_bg_color_b_stop',
			array(
				'label'       => _x( 'Location', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( '%' ),
				'default'     => array(
					'unit' => '%',
					'size' => 100,
				),
				'render_type' => 'ui',
				'condition'   => array(
					'overlay_bg' => array( 'gradient' ),
				),
				'of_type'     => 'gradient',
			)
		);

		$this->add_control(
			'overlay_bg_gradient_type',
			array(
				'label'       => _x( 'Type', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'linear' => _x( 'Linear', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'radial' => _x( 'Radial', 'Background Control', 'woo-product-widgets-for-elementor' ),
				),
				'default'     => 'linear',
				'render_type' => 'ui',
				'condition'   => array(
					'overlay_bg' => array( 'gradient' ),
				),
				'of_type'     => 'gradient',
			)
		);

		$this->add_control(
			'overlay_bg_gradient_angle',
			array(
				'label'      => _x( 'Angle', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'default'    => array(
					'unit' => 'deg',
					'size' => 180,
				),
				'range'      => array(
					'deg' => array(
						'step' => 10,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['overlay'] => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{overlay_bg_color.VALUE}} {{overlay_bg_color_stop.SIZE}}{{overlay_bg_color_stop.UNIT}}, {{overlay_bg_color_b.VALUE}} {{overlay_bg_color_b_stop.SIZE}}{{overlay_bg_color_b_stop.UNIT}})',
				),
				'condition'  => array(
					'overlay_bg'               => array( 'gradient' ),
					'overlay_bg_gradient_type' => 'linear',
				),
				'of_type'    => 'gradient',
			)
		);

		$this->add_control(
			'overlay_bg_gradient_position',
			array(
				'label'     => _x( 'Position', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'center center' => _x( 'Center Center', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'center left'   => _x( 'Center Left', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'center right'  => _x( 'Center Right', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'top center'    => _x( 'Top Center', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'top left'      => _x( 'Top Left', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'top right'     => _x( 'Top Right', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'bottom center' => _x( 'Bottom Center', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'bottom left'   => _x( 'Bottom Left', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'bottom right'  => _x( 'Bottom Right', 'Background Control', 'woo-product-widgets-for-elementor' ),
				),
				'default'   => 'center center',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['overlay'] => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{overlay_bg_color.VALUE}} {{overlay_bg_color_stop.SIZE}}{{overlay_bg_color_stop.UNIT}}, {{overlay_bg_color_b.VALUE}} {{overlay_bg_color_b_stop.SIZE}}{{overlay_bg_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'overlay_bg'               => array( 'gradient' ),
					'overlay_bg_gradient_type' => 'radial',
				),
				'of_type'   => 'gradient',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_overlay_hover',
			array(
				'label' => esc_html__( 'Hover', 'woo-product-widgets-for-elementor' ),
			)
		);

		$this->add_control(
			'overlay_bg_hover',
			array(
				'label'       => _x( 'Background Type', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => array(
					'color'    => array(
						'title' => _x( 'Classic', 'Background Control', 'woo-product-widgets-for-elementor' ),
						'icon'  => 'fa fa-paint-brush',
					),
					'gradient' => array(
						'title' => _x( 'Gradient', 'Background Control', 'woo-product-widgets-for-elementor' ),
						'icon'  => 'fa fa-barcode',
					),
				),
				'default'     => 'color',
				'label_block' => false,
				'render_type' => 'ui',
			)
		);

		$this->add_control(
			'overlay_bg_hover_color',
			array(
				'label'     => _x( 'Color', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'transparent',
				'title'     => _x( 'Background Color', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['column'] . ':hover .woo-products-category-img-overlay__hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'overlay_bg_hover_color_stop',
			array(
				'label'       => _x( 'Location', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( '%' ),
				'default'     => array(
					'unit' => '%',
					'size' => 0,
				),
				'render_type' => 'ui',
				'condition'   => array(
					'overlay_bg_hover' => array( 'gradient' ),
				),
				'of_type'     => 'gradient',
			)
		);

		$this->add_control(
			'overlay_bg_hover_color_b',
			array(
				'label'       => _x( 'Second Color', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'type'        => Controls_Manager::COLOR,
				'default'     => '#f2295b',
				'render_type' => 'ui',
				'condition'   => array(
					'overlay_bg_hover' => array( 'gradient' ),
				),
				'of_type'     => 'gradient',
			)
		);

		$this->add_control(
			'overlay_bg_hover_color_b_stop',
			array(
				'label'       => _x( 'Location', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'type'        => Controls_Manager::SLIDER,
				'size_units'  => array( '%' ),
				'default'     => array(
					'unit' => '%',
					'size' => 100,
				),
				'render_type' => 'ui',
				'condition'   => array(
					'overlay_bg_hover' => array( 'gradient' ),
				),
				'of_type'     => 'gradient',
			)
		);

		$this->add_control(
			'overlay_bg_hover_gradient_type',
			array(
				'label'       => _x( 'Type', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => array(
					'linear' => _x( 'Linear', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'radial' => _x( 'Radial', 'Background Control', 'woo-product-widgets-for-elementor' ),
				),
				'default'     => 'linear',
				'render_type' => 'ui',
				'condition'   => array(
					'overlay_bg_hover' => array( 'gradient' ),
				),
				'of_type'     => 'gradient',
			)
		);

		$this->add_control(
			'overlay_bg_hover_gradient_angle',
			array(
				'label'      => _x( 'Angle', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'deg' ),
				'default'    => array(
					'unit' => 'deg',
					'size' => 180,
				),
				'range'      => array(
					'deg' => array(
						'step' => 10,
					),
				),
				'selectors'  => array(
					'{{WRAPPER}} ' . $css_scheme['column'] . ':hover .woo-products-category-img-overlay__hover' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{overlay_bg_hover_color.VALUE}} {{overlay_bg_hover_color_stop.SIZE}}{{overlay_bg_hover_color_stop.UNIT}}, {{overlay_bg_hover_color_b.VALUE}} {{overlay_bg_hover_color_b_stop.SIZE}}{{overlay_bg_hover_color_b_stop.UNIT}})',
				),
				'condition'  => array(
					'overlay_bg_hover'               => array( 'gradient' ),
					'overlay_bg_hover_gradient_type' => 'linear',
				),
				'of_type'    => 'gradient',
			)
		);

		$this->add_control(
			'overlay_bg_hover_gradient_position',
			array(
				'label'     => _x( 'Position', 'Background Control', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => array(
					'center center' => _x( 'Center Center', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'center left'   => _x( 'Center Left', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'center right'  => _x( 'Center Right', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'top center'    => _x( 'Top Center', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'top left'      => _x( 'Top Left', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'top right'     => _x( 'Top Right', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'bottom center' => _x( 'Bottom Center', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'bottom left'   => _x( 'Bottom Left', 'Background Control', 'woo-product-widgets-for-elementor' ),
					'bottom right'  => _x( 'Bottom Right', 'Background Control', 'woo-product-widgets-for-elementor' ),
				),
				'default'   => 'center center',
				'selectors' => array(
					'{{WRAPPER}} ' . $css_scheme['column'] . ':hover .woo-products-category-img-overlay__hover' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{overlay_bg_hover_color.VALUE}} {{overlay_bg_hover_color_stop.SIZE}}{{overlay_bg_hover_color_stop.UNIT}}, {{overlay_bg_hover_color_b.VALUE}} {{overlay_bg_hover_color_b_stop.SIZE}}{{overlay_bg_hover_color_b_stop.UNIT}})',
				),
				'condition' => array(
					'overlay_bg_hover'               => array( 'gradient' ),
					'overlay_bg_hover_gradient_type' => 'radial',
				),
				'of_type'   => 'gradient',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

	}

	protected function controls_section_carousel_arrows() {

		$this->start_controls_tabs( 'tabs_arrows_style' );

		$this->start_controls_tab(
			'tab_prev',
			array(
				'label' => esc_html__( 'Normal', 'woo-product-widgets-for-elementor' ),
			)
		);

		$this->add_group_control(
			\Woo_Product_Widgets_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'arrows_style',
				'selector'       => '{{WRAPPER}} .woo-products-categories .woo-products-arrow',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_next_hover',
			array(
				'label' => esc_html__( 'Hover', 'woo-product-widgets-for-elementor' ),
			)
		);

		$this->add_group_control(
			\Woo_Product_Widgets_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'arrows_hover_style',
				'selector'       => '{{WRAPPER}} .woo-products-categories .woo-products-arrow:hover',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'prev_arrow_position',
			array(
				'label'     => esc_html__( 'Prev Arrow Position', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'prev_vert_position',
			array(
				'label'   => esc_html__( 'Vertical Postition by', 'woo-product-widgets-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => array(
					'top'    => esc_html__( 'Top', 'woo-product-widgets-for-elementor' ),
					'bottom' => esc_html__( 'Bottom', 'woo-product-widgets-for-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'prev_top_position',
			array(
				'label'      => esc_html__( 'Top Indent', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => - 400,
						'max' => 400,
					),
					'%'  => array(
						'min' => - 100,
						'max' => 100,
					),
					'em' => array(
						'min' => - 50,
						'max' => 50,
					),
				),
				'condition'  => array(
					'prev_vert_position' => 'top',
				),
				'selectors'  => array(
					'{{WRAPPER}} .woo-products-categories .woo-products-arrow.prev-arrow' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'prev_bottom_position',
			array(
				'label'      => esc_html__( 'Bottom Indent', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => - 400,
						'max' => 400,
					),
					'%'  => array(
						'min' => - 100,
						'max' => 100,
					),
					'em' => array(
						'min' => - 50,
						'max' => 50,
					),
				),
				'condition'  => array(
					'prev_vert_position' => 'bottom',
				),
				'selectors'  => array(
					'{{WRAPPER}} .woo-products-categories .woo-products-arrow.prev-arrow' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
				),
			)
		);

		$this->add_control(
			'prev_hor_position',
			array(
				'label'   => esc_html__( 'Horizontal Position by', 'woo-product-widgets-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'left',
				'options' => array(
					'left'  => esc_html__( 'Left', 'woo-product-widgets-for-elementor' ),
					'right' => esc_html__( 'Right', 'woo-product-widgets-for-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'prev_left_position',
			array(
				'label'      => esc_html__( 'Left Indent', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => - 400,
						'max' => 400,
					),
					'%'  => array(
						'min' => - 100,
						'max' => 100,
					),
					'em' => array(
						'min' => - 50,
						'max' => 50,
					),
				),
				'condition'  => array(
					'prev_hor_position' => 'left',
				),
				'selectors'  => array(
					'{{WRAPPER}} .woo-products-categories .woo-products-arrow.prev-arrow' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'prev_right_position',
			array(
				'label'      => esc_html__( 'Right Indent', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => - 400,
						'max' => 400,
					),
					'%'  => array(
						'min' => - 100,
						'max' => 100,
					),
					'em' => array(
						'min' => - 50,
						'max' => 50,
					),
				),
				'condition'  => array(
					'prev_hor_position' => 'right',
				),
				'selectors'  => array(
					'{{WRAPPER}} .woo-products-categories .woo-products-arrow.prev-arrow' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
				),
			)
		);

		$this->add_control(
			'next_arrow_position',
			array(
				'label'     => esc_html__( 'Next Arrow Position', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_control(
			'next_vert_position',
			array(
				'label'   => esc_html__( 'Vertical Position by', 'woo-product-widgets-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'top',
				'options' => array(
					'top'    => esc_html__( 'Top', 'woo-product-widgets-for-elementor' ),
					'bottom' => esc_html__( 'Bottom', 'woo-product-widgets-for-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'next_top_position',
			array(
				'label'      => esc_html__( 'Top Indent', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => - 400,
						'max' => 400,
					),
					'%'  => array(
						'min' => - 100,
						'max' => 100,
					),
					'em' => array(
						'min' => - 50,
						'max' => 50,
					),
				),
				'condition'  => array(
					'next_vert_position' => 'top',
				),
				'selectors'  => array(
					'{{WRAPPER}} .woo-products-categories .woo-products-arrow.next-arrow' => 'top: {{SIZE}}{{UNIT}}; bottom: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'next_bottom_position',
			array(
				'label'      => esc_html__( 'Bottom Indent', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => - 400,
						'max' => 400,
					),
					'%'  => array(
						'min' => - 100,
						'max' => 100,
					),
					'em' => array(
						'min' => - 50,
						'max' => 50,
					),
				),
				'condition'  => array(
					'next_vert_position' => 'bottom',
				),
				'selectors'  => array(
					'{{WRAPPER}} .woo-products-categories .woo-products-arrow.next-arrow' => 'bottom: {{SIZE}}{{UNIT}}; top: auto;',
				),
			)
		);

		$this->add_control(
			'next_hor_position',
			array(
				'label'   => esc_html__( 'Horizontal Postition by', 'woo-product-widgets-for-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => array(
					'left'  => esc_html__( 'Left', 'woo-product-widgets-for-elementor' ),
					'right' => esc_html__( 'Right', 'woo-product-widgets-for-elementor' ),
				),
			)
		);

		$this->add_responsive_control(
			'next_left_position',
			array(
				'label'      => esc_html__( 'Left Indent', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => - 400,
						'max' => 400,
					),
					'%'  => array(
						'min' => - 100,
						'max' => 100,
					),
					'em' => array(
						'min' => - 50,
						'max' => 50,
					),
				),
				'condition'  => array(
					'next_hor_position' => 'left',
				),
				'selectors'  => array(
					'{{WRAPPER}} .woo-products-categories .woo-products-arrow.next-arrow' => 'left: {{SIZE}}{{UNIT}}; right: auto;',
				),
			)
		);

		$this->add_responsive_control(
			'next_right_position',
			array(
				'label'      => esc_html__( 'Right Indent', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => array( 'px', '%', 'em' ),
				'range'      => array(
					'px' => array(
						'min' => - 400,
						'max' => 400,
					),
					'%'  => array(
						'min' => - 100,
						'max' => 100,
					),
					'em' => array(
						'min' => - 50,
						'max' => 50,
					),
				),
				'condition'  => array(
					'next_hor_position' => 'right',
				),
				'selectors'  => array(
					'{{WRAPPER}} .woo-products-categories .woo-products-arrow.next-arrow' => 'right: {{SIZE}}{{UNIT}}; left: auto;',
				),
			)
		);

	}

	protected function controls_section_carousel_dots() {

		$this->start_controls_tabs( 'tabs_dots_style' );

		$this->start_controls_tab(
			'tab_dots_normal',
			array(
				'label' => esc_html__( 'Normal', 'woo-product-widgets-for-elementor' ),
			)
		);

		$this->add_group_control(
			\Woo_Product_Widgets_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'dots_style',
				'selector'       => '{{WRAPPER}} .woo-products-carousel .woo-products-slick-dots li span',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dots_hover',
			array(
				'label' => esc_html__( 'Hover', 'woo-product-widgets-for-elementor' ),
			)
		);

		$this->add_group_control(
			\Woo_Product_Widgets_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'dots_style_hover',
				'selector'       => '{{WRAPPER}} .woo-products-carousel .woo-products-slick-dots li span:hover',
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dots_active',
			array(
				'label' => esc_html__( 'Active', 'woo-product-widgets-for-elementor' ),
			)
		);

		$this->add_group_control(
			\Woo_Product_Widgets_Group_Control_Box_Style::get_type(),
			array(
				'name'           => 'dots_style_active',
				'selector'       => '{{WRAPPER}} .woo-products-carousel .woo-products-slick-dots li.slick-active span',
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'dots_gap',
			array(
				'label'     => esc_html__( 'Gap', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => array(
					'size' => 5,
					'unit' => 'px',
				),
				'range'     => array(
					'px' => array(
						'min' => 0,
						'max' => 50,
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .woo-products-carousel .woo-products-slick-dots li' => 'padding-left: {{SIZE}}{{UNIT}}; padding-right: {{SIZE}}{{UNIT}}',
				),
				'separator' => 'before',
			)
		);

		$this->add_control(
			'dots_margin',
			array(
				'label'      => esc_html__( 'Dots Box Margin', 'woo-product-widgets-for-elementor' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px', '%', 'em' ),
				'selectors'  => array(
					'{{WRAPPER}} .woo-products-carousel .woo-products-slick-dots' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'dots_alignment',
			array(
				'label'     => esc_html__( 'Alignment', 'woo-product-widgets-for-elementor' ),
				'type'      => Controls_Manager::CHOOSE,
				'default'   => 'center',
				'options'   => array(
					'flex-start' => array(
						'title' => esc_html__( 'Left', 'woo-product-widgets-for-elementor' ),
						'icon'  => 'fa fa-align-left',
					),
					'center'     => array(
						'title' => esc_html__( 'Center', 'woo-product-widgets-for-elementor' ),
						'icon'  => 'fa fa-align-center',
					),
					'flex-end'   => array(
						'title' => esc_html__( 'Right', 'woo-product-widgets-for-elementor' ),
						'icon'  => 'fa fa-align-right',
					),
				),
				'selectors' => array(
					'{{WRAPPER}} .woo-products-carousel .woo-products-slick-dots' => 'justify-content: {{VALUE}};',
				),
			)
		);

	}

	protected function content_template() {
	}
}

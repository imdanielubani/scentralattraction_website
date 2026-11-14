<?php
/**
 * Elementor widget: "Scentral Section" — drop any theme section into an Elementor layout.
 * Loaded only inside the elementor/widgets/register hook (Elementor guaranteed present).
 *
 * @package Scentral
 */

defined( 'ABSPATH' ) || exit;

/**
 * Scentral Section widget.
 */
class Scentral_Elementor_Section_Widget extends \Elementor\Widget_Base {

	/**
	 * Widget slug.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'scentral-section';
	}

	/**
	 * Widget title.
	 *
	 * @return string
	 */
	public function get_title() {
		return __( 'Scentral Section', 'scentral' );
	}

	/**
	 * Widget icon.
	 *
	 * @return string
	 */
	public function get_icon() {
		return 'eicon-section';
	}

	/**
	 * Widget categories.
	 *
	 * @return string[]
	 */
	public function get_categories() {
		return array( 'scentral' );
	}

	/**
	 * Search keywords.
	 *
	 * @return string[]
	 */
	public function get_keywords() {
		return array( 'scentral', 'hero', 'products', 'perfume', 'newsletter', 'collection', 'showcase' );
	}

	/**
	 * Controls.
	 */
	protected function register_controls() {
		$this->start_controls_section(
			'content',
			array( 'label' => __( 'Section', 'scentral' ) )
		);
		$this->add_control(
			'section',
			array(
				'label'   => __( 'Show', 'scentral' ),
				'type'    => \Elementor\Controls_Manager::SELECT,
				'default' => 'new_arrivals',
				'options' => scentral_section_choices(),
			)
		);
		$this->add_control(
			'note',
			array(
				'type'            => \Elementor\Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Texts, images and products for each section are edited in Appearance → Customize → Scentral Theme. Products always come live from WooCommerce.', 'scentral' ),
				'content_classes' => 'elementor-descriptor',
			)
		);
		$this->end_controls_section();
	}

	/**
	 * Render on the front end and in the editor.
	 */
	protected function render() {
		$settings = $this->get_settings_for_display();
		$section  = isset( $settings['section'] ) ? sanitize_key( $settings['section'] ) : '';
		echo '<div class="scentral-widget scentral-widget--' . esc_attr( $section ) . '">';
		scentral_render_section( $section );
		echo '</div>';
	}
}

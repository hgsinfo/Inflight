<?php

namespace SeriouslySimplePodcasting\Integrations\Blocks;

use SeriouslySimplePodcasting\Controllers\Controller;
use SeriouslySimplePodcasting\Handlers\Admin_Notifications_Handler;
use SeriouslySimplePodcasting\Player\Media_Player;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Blocks class, used to load blocks and any relevant block assets
 *
 * @author      Jonathan Bossenger
 * @category    Class
 * @package     SeriouslySimplePodcasting/Blocks
 * @since       2.0.4
 */
class Castos_Blocks extends Controller {

	/**
	 * @var array $asset_file
	 */
	protected $asset_file;

	protected $admin_notices_handler;

	/**
	 * Castos_Blocks constructor.
	 *
	 * @param $file
	 * @param $version
	 */
	public function __construct( $file, $version ) {
		parent::__construct( $file, $version );
		$this->bootstrap();
	}

	/**
	 * Dynamic Podcast List Block callback
	 *
	 * @param $attributes
	 *
	 * @return string
	 */
	public function podcast_list_render_callback( $attributes ) {
		return ssp_frontend_controller()->render_podcast_list_dynamic_block( $attributes );
	}

	/**
	 * Loads the asset file and the block registration
	 */
	protected function bootstrap() {
		if ( ! file_exists( SSP_PLUGIN_PATH . 'build/index.asset.php' ) ) {
			if ( is_admin() ) {
				$this->admin_notices_handler = new Admin_Notifications_Handler( SSP_CPT_PODCAST );
				add_action( 'admin_notices', array( $this->admin_notices_handler, 'blocks_error_notice' ) );
			}

			return;
		}
		$this->asset_file = include SSP_PLUGIN_PATH . 'build/index.asset.php';
		add_action( 'init', array( $this, 'register_castos_blocks' ) );
	}

	/**
	 * Registers the Castos Player Block
	 *
	 * @return void
	 */
	public function register_castos_blocks() {

		wp_register_script(
			'ssp-block-script',
			esc_url( SSP_PLUGIN_URL . 'build/index.js' ),
			$this->asset_file['dependencies'],
			$this->asset_file['version'],
			true
		);

		wp_register_style(
			'ssp-block-style',
			esc_url( SSP_PLUGIN_URL . 'assets/css/block-editor-styles.css' ),
			array(),
			$this->asset_file['version']
		);

		register_block_type(
			'seriously-simple-podcasting/castos-player',
			array(
				'editor_script' => 'ssp-block-script',
				'editor_style'  => 'ssp-castos-player',
			)
		);

		/**
		 * Is used for both rendering the block itself and preview in editor
		 * @since 2.8.2
		 * */
		register_block_type( 'seriously-simple-podcasting/castos-html-player', array(
			'editor_script'   => 'ssp-block-script',
			'editor_style'    => 'ssp-castos-player',
			'attributes'      => array(
				'episodeId' => array(
					'type' => 'string',
					'default' => '',
				),
			),
			'render_callback' => function ( $args ) {
				return ssp_frontend_controller()->players_controller->render_html_player( $args['episodeId'], false );
			}
		) );

		register_block_type(
			'seriously-simple-podcasting/audio-player',
			array(
				'editor_script' => 'ssp-block-script',
			)
		);

		register_block_type(
			'seriously-simple-podcasting/podcast-list',
			array(
				'editor_script'   => 'ssp-block-script',
				'editor_style'    => 'ssp-block-style',
				'attributes'      => array(
					'featuredImage' => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'featuredImageSize' => array(
						'type'    => 'string',
						'default' => 'full',
					),
					'excerpt' => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'player' => array(
						'type'    => 'boolean',
						'default' => false,
					),
					'playerBelowExcerpt' => array(
						'type'    => 'boolean',
						'default' => false,
					),
				),
				'render_callback' => array(
					$this,
					'podcast_list_render_callback',
				),
			)
		);
	}
}

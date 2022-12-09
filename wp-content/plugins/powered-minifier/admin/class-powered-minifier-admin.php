<?php
/**
 * The admin-specific functionality of the plugin
 *
 * @link https://neoslab.com
 * @since 1.0.0
 * @package Powered_Minifier
 * @subpackage Powered_Minifier/admin
*/

/**
 * Class `Powered_Minifier_Admin`
 * @package Powered_Minifier
 * @subpackage Powered_Minifier/admin
 * @author Neoslab <contact@neoslab.com>
*/
class Powered_Minifier_Admin
{
	/**
	 * The ID of this plugin
	 * @since 1.0.0
	 * @access private
	 * @var string $pluginName the ID of this plugin
	*/
	private $pluginName;

	/**
	 * The version of this plugin
	 * @since 1.0.0
	 * @access private
	 * @var string $version the current version of this plugin
	*/
	private $version;

	/**
	 * Initialize the class and set its properties
	 * @since 1.0.0
	 * @param string $pluginName the name of this plugin
	 * @param string $version the version of this plugin
	*/
	public function __construct($pluginName, $version)
	{
		$this->pluginName = $pluginName;
		$this->version = $version;
	}

	/**
	 * Register the stylesheets for the admin area
	 * @since 1.0.0
	*/
	public function enqueue_styles()
	{
		wp_register_style($this->pluginName.'-fontawesome', plugin_dir_url(__FILE__).'assets/fonts/font-awesome/css/all.min.css', array(), $this->version, 'all');
		wp_register_style($this->pluginName.'-dashboard', plugin_dir_url(__FILE__).'assets/styles/powered-minifier-admin.min.css', array(), $this->version, 'all');
		wp_enqueue_style($this->pluginName.'-fontawesome');
		wp_enqueue_style($this->pluginName.'-dashboard');
	}

	/**
	 * Register the JavaScript for the admin area
	 * @since 1.0.0
	*/
	public function enqueue_scripts()
	{
		wp_register_script($this->pluginName.'-script', plugin_dir_url(__FILE__).'assets/javascripts/powered-minifier-admin.min.js', array('jquery'), $this->version, false);
		wp_enqueue_script($this->pluginName.'-script');
	}

	/**
	 * Return the plugin header
	*/
	public function return_plugin_header()
	{
		$html = '<div class="header-plugin"><span class="header-icon"><i class="fas fa-sliders-h"></i></span> <span class="header-text">'.__('Powered Minifier', 'powered-minifier').'</span></div>';
		return $html;
	}

	/**
	 * Return the tabs menu
	*/
	public function return_tabs_menu($tab)
	{
		$link = admin_url('options-general.php');
		$list = array
		(
			array('tab1', 'powered-minifier-admin', 'fa-cogs', __('Settings', 'powered-minifier'))
		);

		$menu = null;
		foreach($list as $item => $value)
		{
			$html = array('div' => array('class' => array()), 'a' => array('href' => array()), 'i' => array('class' => array()), 'p' => array(), 'span' => array());
			$menu ='<div class="tab-label '.$value[0].' '.(($tab === $value[0]) ? 'active' : '').'"><a href="'.$link.'?page='.$value[1].'"><p><i class="fas '.$value[2].'"></i><span>'.$value[3].'</span></p></a></div>';
			echo wp_kses($menu, $html);
		}
	}

	/**
	 * Return minified source
	*/
	public function return_minified_source($source)
	{
		require_once plugin_dir_path(dirname(__FILE__)).'admin/class-powered-minifier-handler.php';
		$htmlTool = new Powered_Minifier_Source();

		$opts = get_option('_powered_minifier');
		$html = $source;
		if(substr(ltrim($html), 0, 5) == '<?xml')
		{
			return($html);
		}

		if(($opts['encoding'] == 'on') && (mb_detect_encoding($html, 'UTF-8', true)))
		{
			$mod = '/u';
		}
		else
		{
			$mod = '/s';
		}

		$html = str_replace(array(chr(13).chr(10), chr(9)), array(chr(10), ''), $html);
		$html = $htmlTool->return_clean_tags($html);
		$html = str_ireplace(array('<script', '/script>', '<pre', '/pre>', '<textarea', '/textarea>', '<style', '/style>'), array('::-vv-::<script', '/script>::-ss-::', '::-vv-::<pre', '/pre>::-ss-::', '::-vv-::<textarea', '/textarea>::-ss-::', '::-vv-::<style', '/style>::-ss-::'), $html);
		$load = explode('::-ss-::', $html);
		$html = '';

		for($i = 0; $i < count($load); $i++)
		{
			$s = strpos($load[$i], '::-vv-::');
			if($s !== false)
			{
				$proc = substr($load[$i], 0, $s);
				$asis = substr($load[$i], $s + 8);

				if(substr($asis, 0, 7) == '<script')
				{
					$part = explode(chr(10), $asis);
					$asis = '';

					for($r = 0; $r < count($part); $r ++)
					{
						if($part[$r])
						{
							$asis.= trim($part[$r]).chr(10);
						}

						if($opts['js'] == 'on')
						{
							if((strpos($part[$r], '//') !== false) && (substr(trim($part[$r]), -1) == ';'))
							{
								$asis.= chr(10);
							}
						}
					}

					if($asis)
					{
						$asis = substr($asis, 0, -1);
					}

					if($opts['comments'] == 'on')
					{
						$asis = $htmlTool->remove_comments($asis, $mod);
					}

					if($opts['js'] == 'on')
					{
						$asis = $htmlTool->return_minified_js($asis);
					}

				}
				elseif(substr($asis, 0, 6) == '<style')
				{
					$asis = $htmlTool->return_minified_tag($asis, $mod);

					if($opts['comments'] == 'on')
					{
						$asis = $htmlTool->remove_comments($asis, $mod);
					}

					$asis = $htmlTool->return_minified_css($asis);
				}
			}
			else
			{
				$proc = $load[$i];
				$asis = '';
			}

			$proc = $htmlTool->return_minified_tag($proc, $mod);

			if($opts['comments'] == 'on')
			{
				$proc = $htmlTool->remove_comments($proc, $mod);
			}

			$html.= $proc.$asis;
		}

		$html = str_replace(array(chr(10).'<script', chr(10).'<style', '*/'.chr(10), '::-vv-::'), array('<script', '<style', '*/', ''), $html);

		if(($opts['xhtml'] == 'on') && (strtolower(substr(ltrim($html), 0, 15)) == '<!doctype html>'))
		{
			$html = $htmlTool->return_minified_xhtml($html);
		}

		if($opts['domain'] == 'on')
		{
			$html = $htmlTool->return_minified_path($html);
		}

		if($opts['scheme'] == 'on')
		{
			$html = $htmlTool->return_minified_scheme($html);
		}

		if($opts['empty'] == 'on')
		{
			$html = $htmlTool->remove_empty_lines($html);
		}

		if($opts['level'] == 'hard')
		{
			$html = $htmlTool->remove_white_spaces($html);
			$html = $htmlTool->return_minified_hard($html);
		}

		if($opts['debug'] == 'on')
		{
			$html = $htmlTool->return_debug_header().$html;
		}

		if($opts['stats'] == 'on')
		{
			$html = $html.$htmlTool->return_loading_stats($source, $html);
		}

		if($opts['debug'] == 'on')
		{
			$html = $html.$htmlTool->return_debug_footer();
		}

		return ($html);
	}

	/**
	 * Start minified output
	*/
	public function return_minified_output()
	{
		$opts = get_option('_powered_minifier');

		if((isset($opts['source'])) && ($opts['source'] == 'on'))
		{
			ob_start(array($this, 'return_minified_source'));
		}
	}

	/**
	 * Update `Options` on form submit
	*/
	public function return_update_options()
	{
		if((isset($_POST['pwm-update-option'])) && ($_POST['pwm-update-option'] == 'true')
		&& check_admin_referer('pwm-referer-form', 'pwm-referer-option'))
		{
			$opts = array('source' => 'off', 'level' => 'soft', 'css' => 'off', 'js' => 'off', 'comments' => 'off', 'empty' => 'off', 'xhtml' => 'off', 'domain' => 'off', 'scheme' => 'off', 'encoding' => 'off', 'stats' => 'off', 'debug' => 'off', 'loadtime' => 'off');
			if(isset($_POST['_powered_minifier']['source']))
			{
				$opts['source'] = 'on';
			}

			$allowed = array('soft','hard');
			if((isset($_POST['_powered_minifier']['level'])) && (in_array($_POST['_powered_minifier']['level'], $allowed)))
			{
				$opts['level'] = sanitize_text_field($_POST['_powered_minifier']['level']);
			}

			if(isset($_POST['_powered_minifier']['css']))
			{
				$opts['css'] = 'on';
			}

			if(isset($_POST['_powered_minifier']['js']))
			{
				$opts['js'] = 'on';
			}

			if(isset($_POST['_powered_minifier']['comments']))
			{
				$opts['comments'] = 'on';
			}

			if(isset($_POST['_powered_minifier']['empty']))
			{
				$opts['empty'] = 'on';
			}

			if(isset($_POST['_powered_minifier']['xhtml']))
			{
				$opts['xhtml'] = 'on';
			}

			if(isset($_POST['_powered_minifier']['domain']))
			{
				$opts['domain'] = 'on';
			}

			if(isset($_POST['_powered_minifier']['scheme']))
			{
				$opts['scheme'] = 'on';
			}

			if(isset($_POST['_powered_minifier']['encoding']))
			{
				$opts['encoding'] = 'on';
			}

			if(isset($_POST['_powered_minifier']['stats']))
			{
				$opts['stats'] = 'on';
			}

			if(isset($_POST['_powered_minifier']['debug']))
			{
				$opts['debug'] = 'on';
			}

			if(isset($_POST['_powered_minifier']['loadtime']))
			{
				$opts['loadtime'] = 'on';
			}

			$data = update_option('_powered_minifier', $opts);
			header('location:'.admin_url('admin.php?page=powered-minifier-admin').'&output=updated');
			die();
		}
	}

	/**
	 * Return the `Options` page
	*/
	public function return_options_page()
	{
		$opts = get_option('_powered_minifier');
		require_once plugin_dir_path(__FILE__).'partials/powered-minifier-admin-options.php';
	}

	/**
	 * Return Backend Menu
	*/
	public function return_admin_menu()
	{
		add_options_page('Powered Minifier', 'Powered Minifier', 'manage_options', 'powered-minifier-admin', array($this, 'return_options_page'));
	}
}

?>
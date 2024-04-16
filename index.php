<?php
/**
 * ID: tgm-plugin-activation
 * Name: Plugin consigliati
 * Description: Una lista di plugin consigliati
 * Autoload: true
 * Icon: dashicons-admin-plugins
 * Version: 1.0
 * 
 */

 require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';

 class BCInstallPlugin {
	private $bc_install_plugin_options;

	public function __construct() {
		$this->bc_install_plugin_options = get_option( 'bc_install_plugin_option_name' ); 
		add_action( 'admin_menu', array( $this, 'bc_install_plugin_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'bc_install_plugin_page_init' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'load_enqueue' ) );
        add_action( 'tgmpa_register', array( $this, 'bc_register_required_plugins' ));
	}

	public function bc_install_plugin_add_plugin_page() {
		
        add_submenu_page(
            'bweb-component',
			'Plugin', // page_title
			'Plugin', // menu_title
			'manage_options', // capability
			'tgm-plugin-activation', // menu_slug
			array( $this, 'bc_install_plugin_create_admin_page' ) // function
		);
	}

	public function bc_install_plugin_create_admin_page() {
        ?>

		<div class="wrap" id="bc-install-plugin">
			<h2 class="wp-heading-inline">BC Install Plugin</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'bc_install_plugin_option_group' );
					do_settings_sections( 'bc-install-plugin-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function bc_install_plugin_page_init() {
		register_setting(
			'bc_install_plugin_option_group', // option_group
			'bc_install_plugin_option_name', // option_name
			array( $this, 'bc_install_plugin_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'bc_install_plugin_setting_section', // id
			'Plugin consigliati', // title
			array( $this, 'bc_install_plugin_section_info' ), // callback
			'bc-install-plugin-admin' // page
		);

		add_settings_field(
			'plugin', // id
			'', // title
			array( $this, 'plugin_callback' ), // callback
			'bc-install-plugin-admin', // page
			'bc_install_plugin_setting_section' // section
		);
	}

	public function bc_install_plugin_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['plugin'] ) ) {
			$sanitary_values['plugin'] = $input['plugin'];
		}

		return $sanitary_values;
	}

	public function bc_install_plugin_section_info() {
		
	}

	public function plugin_callback() {
        /*$list1 = array(
            array("name"=>"Yoast Seo","slug"=>"wordpress-seo","icon"=>"https://ps.w.org/wordpress-seo/assets/icon-128x128.png"),
            array("name"=>"Map Block Leaflet","slug"=>"map-block-leaflet","icon"=>"https://ps.w.org/map-block-leaflet/assets/icon-128x128.png"),
            array("name"=>"Contact Form 7","slug"=>"contact-form-7","icon"=>"https://ps.w.org/contact-form-7/assets/icon-128x128.png"),
            array("name"=>"Taxonomy images","slug"=>"taxonomy-images","icon"=>"https://ps.w.org/taxonomy-images/assets/icon-128x128.png"),
            array("name"=>"Complianz | GDPR/CCPA Cookie Consent","slug"=>"complianz-gdpr","icon"=>"https://ps.w.org/complianz-gdpr/assets/icon-128x128.png"),
            array("name"=>"Wp pagenavi","slug"=>"wp-pagenavi","icon"=>"https://ps.w.org/wp-pagenavi/assets/icon.svg"),
            array("name"=>"Quicklink","slug"=>"quicklink","icon"=>"https://ps.w.org/quicklink/assets/icon-128x128.png"),
            array("name"=>"Google Analytics Dashboard","slug"=>"google-analytics-dashboard-for-wp","icon"=>"https://ps.w.org/google-analytics-dashboard-for-wp/assets/icon-128x128.png"),
            array("name"=>"Site Kit by Google","slug"=>"google-site-kit","icon"=>"https://ps.w.org/google-site-kit/assets/icon-128x128.png"),
            array("name"=>"WP Mail SMTP","slug"=>"wp-mail-smtp","icon"=>"https://ps.w.org/wp-mail-smtp/assets/icon-128x128.png"),
            array("name"=>"Schema","slug"=>"schema","icon"=>"https://ps.w.org/schema/assets/icon-128x128.png"),
            array("name"=>"W3 Total Cache","slug"=>"w3-total-cache","icon"=>"https://ps.w.org/w3-total-cache/assets/icon-128x128.png"),
            array("name"=>"Polylang","slug"=>"polylang","icon"=>"https://ps.w.org/polylang/assets/icon-128x128.png"),
            array("name"=>"Wordfence","slug"=>"wordfence","icon"=>"https://ps.w.org/wordfence/assets/icon.svg"),
            array("name"=>"Mailchimp","slug"=>"mailchimp-for-wp","icon"=>"https://ps.w.org/mailchimp-for-wp/assets/icon-128x128.png"),
            array("name"=>"Tawkto.to Live Chat","slug"=>"tawkto-live-chat","icon"=>"https://ps.w.org/tawkto-live-chat/assets/icon-128x128.png"),
            array("name"=>"Email Address Encoder","slug"=>"email-address-encoder","icon"=>"https://ps.w.org/email-address-encoder/assets/icon-128x128.jpg"),
            array("name"=>"Webp Converter for Media","slug"=>"webp-converter-for-media","icon"=>"https://ps.w.org/webp-converter-for-media/assets/icon-128x128.png"),
            array("name"=>"Booking Calendar","slug"=>"booking","icon"=>"https://ps.w.org/booking/assets/icon-128x128.png"),
            array("name"=>"Woocommerce","slug"=>"woocommerce","icon"=>"https://ps.w.org/woocommerce/assets/icon-128x128.png"),
        );*/

        $list = json_decode( wp_remote_retrieve_body( wp_remote_get( "https://raw.githubusercontent.com/EdoardoDevelop/bweb_component_tgm-plugin-activation/master/plugin-wp.json" ) ), true );
        $plugin_active = array();
        foreach(get_option('active_plugins') as $p){
            array_push($plugin_active, dirname($p));
        }
        
        foreach($list as $arrayPlugin){
            
            printf(
                '<div class="list_plugin"><label><img src="%s"><div><input type="checkbox" name="bc_install_plugin_option_name[plugin][%s]" value="%s" %s %s> %s</div></label></div>',
                $arrayPlugin['icon'],
                $arrayPlugin['slug'],
                $arrayPlugin['name'],
                ( isset( $this->bc_install_plugin_options['plugin'][$arrayPlugin['slug']] ) && $this->bc_install_plugin_options['plugin'][$arrayPlugin['slug']] === $arrayPlugin['name'] ) ? 'checked' : '',
                ( in_array($arrayPlugin['slug'], $plugin_active) ) ? 'checked disabled' : '',
                $arrayPlugin['name']
            );
        }


        
	}


    public function bc_register_required_plugins(){
        $config = array(
            'id'           => 'BCInstallPlugin',                 // Unique ID for hashing notices for multiple instances of TGMPA.
            'default_path' => '',                      // Default absolute path to bundled plugins.
            'menu'         => 'tgm-plugin-activation', // Menu slug.
            'parent_slug'  => 'bweb-component',            // Parent menu slug.
            'capability'   => 'manage_options',    // Capability needed to view plugin install page, should be a capability associated with the parent menu used.
            'has_notices'  => true,                    // Show admin notices or not.
            'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
            'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
            'is_automatic' => true,                   // Automatically activate plugins after installation or not.
            'message'      => '',                      // Message to output right before the plugins table.
        );
        $plugins = array();

        if(isset($this->bc_install_plugin_options['plugin']) && is_array($this->bc_install_plugin_options['plugin'])){
            $sel_p = $this->bc_install_plugin_options['plugin'];

            foreach($sel_p as $slug=>$name){
                array_push($plugins, array(
                    'name'          => $name,
                    'slug'          => $slug
                ));
            }

            tgmpa( $plugins, $config );
        }

    }

    public function load_enqueue($hook){
        if($hook == 'bweb-component_page_tgm-plugin-activation'){
		    wp_enqueue_style( 'bc_install_plugin_css', plugin_dir_url( __FILE__ ).'assets/style.css');
        }
    }

}
if ( is_admin() )
	$bc_install_plugin = new BCInstallPlugin();

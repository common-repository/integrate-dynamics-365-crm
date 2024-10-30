<?php

/*
Plugin Name: Integrate Dynamics 365 CRM
Plugin URI: https://plugins.miniorange.com/
Description: This plugin will allow you to sync CRM Objects like contacts, accounts, leads, etc. between Dynamics 365 Sales and wordpress.
Version: 1.0.9
Author: miniOrange
License: MIT
*/

namespace MoDynamics365ObjectSync;
require_once __DIR__ . '/vendor/autoload.php';

use MoDynamics365ObjectSync\View\adminView;
use MoDynamics365ObjectSync\Controller\adminController;
use MoDynamics365ObjectSync\Observer\adminObserver;
use MoDynamics365ObjectSync\Observer\cf7dcrmObserver;
use MoDynamics365ObjectSync\View\feedbackForm;
use MoDynamics365ObjectSync\Wrappers\pluginConstants;
use MoDynamics365ObjectSync\Wrappers\wpWrapper;

define('MO_DCRM_PLUGIN_FILE',__FILE__);
define('MO_DCRM_PLUGIN_DIR',__DIR__.DIRECTORY_SEPARATOR);
define('PLUGIN_VERSION','1.0.9');

class MOdcrm{

    private static $instance;
    public static $version = PLUGIN_VERSION;

    public static function mo_dcrm_load_instance(){
        if(!isset(self::$instance)){
            $class = __CLASS__;
            self::$instance = new $class;
            self::$instance->mo_dcrm_load_hooks();
        }
        return self::$instance;
    }

    public function mo_dcrm_load_hooks(){
        add_action('admin_menu',[$this,'mo_dcrm_admin_menu']);
        add_action( 'admin_enqueue_scripts', [$this, 'mo_dcrm_settings_style' ] );
        add_action( 'admin_footer', [feedbackForm::getView() , 'mo_dcrm_display_feedback_form'] );
        add_action( 'admin_enqueue_scripts', [$this, 'mo_dcrm_settings_scripts' ] );
        add_action('admin_init',[adminController::getController(),'mo_dcrm_admin_controller']);
        add_action('init',[adminObserver::getObserver(),'mo_dcrm_admin_observer']);
        add_action('wp_ajax_mo_cf7dcrm_integrate',[cf7dcrmObserver::getObserver(),'mo_dcrm_cf7_support_api_handler']);
        add_filter('wpcf7_before_send_mail', [cf7dcrmObserver::getObserver(),'mo_dcrm_cf7_after_form_submit'],10,1);
        register_uninstall_hook(__FILE__, 'mo_dcrm_uninstall');
        add_action( 'admin_init', array ($this, 'mo_dcrm_redirect_after_activation') );
        register_activation_hook(__FILE__,array($this,'mo_dcrm_activate_plugin'));

    }

    public function mo_dcrm_admin_menu(){
        $page = add_menu_page(
            'miniOrange Dynamics 365 Integration ' .__('+ Sync'),
            'WP Dynamics 365 Integration',
            'administrator',
            'mo_dcrm',
            [adminView::getView(),'mo_dcrm_menu_display'],
            plugin_dir_url( __FILE__ ) . 'images/miniorange.png'
        );
    }

    public function mo_dcrm_activate_plugin(){
	    wpWrapper :: mo_dcrm_set_option(pluginConstants::ACTIVATION_REDIRECTION, true);
	}
    
    function mo_dcrm_redirect_after_activation() {
	    if (wpWrapper :: mo_dcrm_get_option(pluginConstants::ACTIVATION_REDIRECTION)) {
		    wpWrapper :: mo_dcrm_delete_option(pluginConstants::ACTIVATION_REDIRECTION);
		    if(!isset($_GET['activate-multi'])) {
			    wp_redirect(admin_url() . 'admin.php?page=mo_dcrm');
			    exit;
		    }
	    }
    }
    
    function mo_dcrm_settings_style($page){
        if( $page != 'toplevel_page_mo_dcrm'){
            return;
        }
        $css_url = plugins_url('includes/css/mo_dcrm_settings.css',__FILE__);
        $css_phone_url = plugins_url('includes/css/phone.css',__FILE__);
        $css_support = plugins_url('includes/css/support.css',__FILE__);
        $dcrm_alert_css_url= plugins_url('/includes/css/mo_dcrm_alert_css.css',__FILE__);
        
        wp_enqueue_style('mo_dcrm_css',$css_url,array(),self::$version);
        wp_enqueue_style('mo_dcrm_css',$css_phone_url,array(),self::$version);
        wp_enqueue_style('mo_dcrm_support_css',$css_support,array(),self::$version);
        wp_enqueue_style('mo_dcrm_alert_css',$dcrm_alert_css_url,array(),self::$version);


    }

    function mo_dcrm_settings_scripts($page){
        if( $page != 'toplevel_page_mo_dcrm'){
            return;
        }
        wp_enqueue_script('jquery');
        $setting_js_url = plugins_url('includes/js/mo_dcrm_settings.js',__FILE__);
		wp_enqueue_script('setting_js_url',$setting_js_url,array('jquery'),self::$version);
    }

}
MOdcrm::mo_dcrm_load_instance();
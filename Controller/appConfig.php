<?php

namespace MoDynamics365ObjectSync\Controller;

use MoDynamics365ObjectSync\Wrappers\pluginConstants;
use MoDynamics365ObjectSync\Wrappers\wpWrapper;

class appConfig{

    private static $instance;

    public static function getController(){
        if(!isset(self::$instance)){
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public function mo_dcrm_save_settings(){
        $option = sanitize_text_field($_POST['option']);
        switch ($option){
            case 'mo_dcrm_online_url_controller':{
                $this->mo_dcrm_online_url_controller();
                break;
            }
            case 'mo_dcrm_application_type_controller':{
                $this->mo_dcrm_application_type_controller();
                break;
            }
            case 'mo_dcrm_setup_connection_controller':{
                $this->mo_dcrm_setup_connection_controller();
                break;
            }
        }
    }

    private function mo_dcrm_check_for_empty_or_null(&$input,$arr){
        foreach ($arr as $key){
            if(!isset($_POST[$key]) || empty($_POST[$key])){
                return false;
            }
            $input[$key] = sanitize_text_field($_POST[$key]);
        }
        return $input;
    }

    private function mo_dcrm_online_url_controller(){
        check_admin_referer('mo_dcrm_online_url_controller');

        $input_arr = ['dcrm_org_endpoint'];
        $sanitized_arr = [];
        if(!$this->mo_dcrm_check_for_empty_or_null($sanitized_arr,$input_arr)){
            wpWrapper::mo_dcrm__show_error_notice(esc_html__("Input is empty or present in the incorrect format."));
            return;
        }

        $app = wpWrapper::mo_dcrm_get_option(pluginConstants::APP_CONFIG);
        if(empty($app))
            $app = [];

        $app['dcrm_org_endpoint'] = $sanitized_arr['dcrm_org_endpoint'];

        wpWrapper::mo_dcrm_set_option(pluginConstants::APP_CONFIG,$app);
        wpWrapper::mo_dcrm__show_success_notice(esc_html__("Settings Saved Successfully."));

    }

    private function mo_dcrm_application_type_controller(){
        check_admin_referer('mo_dcrm_application_type_controller');

        $input_arr = ['app_type'];
        $sanitized_arr = [];
        if(!$this->mo_dcrm_check_for_empty_or_null($sanitized_arr,$input_arr)){
            wpWrapper::mo_dcrm__show_error_notice(esc_html__("Input is empty or present in the incorrect format."));
            return;
        }

        $app = wpWrapper::mo_dcrm_get_option(pluginConstants::APP_CONFIG);
        if(empty($app))
            $app = [];

        $app['app_type'] = $sanitized_arr['app_type'];

        wpWrapper::mo_dcrm_set_option(pluginConstants::APP_CONFIG,$app);
        if($app['app_type'] == 'manual'){
            wpWrapper::mo_dcrm_delete_option(pluginConstants::APP_CONFIG_STATUS);
            wpWrapper::mo_dcrm_delete_option(pluginConstants::DCRMAUTHCODE);
        }
        wpWrapper::mo_dcrm__show_success_notice(esc_html__("Settings Saved Successfully."));

    }

    private function mo_dcrm_setup_connection_controller(){
        check_admin_referer('mo_dcrm_setup_connection_controller');

        $input_arr = ['client_id','client_secret','tenant_id'];
        $sanitized_arr = [];
        if(!$this->mo_dcrm_check_for_empty_or_null($sanitized_arr,$input_arr)){
            wpWrapper::mo_dcrm__show_error_notice(esc_html__("Input is empty or present in the incorrect format."));
            return;
        }

        $sanitized_arr['client_secret'] = wpWrapper::mo_dcrm_encrypt_data($sanitized_arr['client_secret'],hash("sha256",$sanitized_arr['client_id']));

        $app = wpWrapper::mo_dcrm_get_option(pluginConstants::APP_CONFIG);
        if(empty($app))
            $app = [];

        foreach($input_arr as $key)
            $app[$key] = $sanitized_arr[$key];

        wpWrapper::mo_dcrm_set_option(pluginConstants::APP_CONFIG,$app);
        wpWrapper::mo_dcrm__show_success_notice(esc_html__("Settings Saved Successfully."));
    }

}
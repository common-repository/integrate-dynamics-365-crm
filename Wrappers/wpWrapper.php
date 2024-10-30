<?php

namespace MoDynamics365ObjectSync\Wrappers;

class wpWrapper{

    private static $instance;

    public static function getWrapper(){
        if(!isset(self::$instance)){
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public static function mo_dcrm_set_option($key, $value){
        update_option($key,$value);
    }

    public static function mo_dcrm_get_option($key){
        return get_option($key);
    }

    public static function mo_dcrm_delete_option($key){
        return delete_option($key);
    }

    public static function mo_dcrm__show_error_notice($message){
        self::mo_dcrm_set_option(pluginConstants::notice_message,$message);
        $hook_name = 'admin_notices';
        remove_action($hook_name,[self::getWrapper(),'mo_dcrm_success_notice']);
        add_action($hook_name,[self::getWrapper(),'mo_dcrm_error_notice']);
    }

    public static function mo_dcrm__show_success_notice($message){
        self::mo_dcrm_set_option(pluginConstants::notice_message,$message);
        $hook_name = 'admin_notices';
        remove_action($hook_name,[self::getWrapper(),'mo_dcrm_error_notice']);
        add_action($hook_name,[self::getWrapper(),'mo_dcrm_success_notice']);
    }

    public function mo_dcrm_success_notice(){
        $class = "updated";
        $message = self::mo_dcrm_get_option(pluginConstants::notice_message);
        echo "<div style='margin:5px 0;width:95.8%' class='" . esc_attr($class) . "'> <p>" . esc_attr($message) . "</p></div>";
    }

    public function mo_dcrm_error_notice(){
        $class = "error";
        $message = self::mo_dcrm_get_option(pluginConstants::notice_message);
        echo "<div style='margin:5px 0' class='" . esc_attr($class) . "'> <p>" . esc_attr($message) . "</p></div>";
    }

    /**
     * @param string $data - the key=value pairs separated with &
     * @return string
     */
    public static function mo_dcrm_encrypt_data($data, $key) {
        $key    = openssl_digest($key, 'sha256');
        $method = 'aes-128-ecb';
        $strCrypt = openssl_encrypt ($data, $method, $key,OPENSSL_RAW_DATA||OPENSSL_ZERO_PADDING);
        return base64_encode($strCrypt);
    }

    public static function mo_dcrm_get_domain_from_url($url){

        $scheme = parse_url($url, PHP_URL_SCHEME);
        $domain = '';
        if($scheme == 'http'){
            $domain = str_replace('http://', '', $url);
        } else {
            $domain = str_replace('https://', '', $url);
        }
        $domain = rtrim($domain,'/');

        return $domain;
    }


    /**
     * @param string $data - crypt response from Sagepay
     * @return string
     */
    public static function mo_dcrm_decrypt_data($data, $key) {
        $strIn = base64_decode($data);
        $key    = openssl_digest($key, 'sha256');
        $method = 'AES-128-ECB';
        $ivSize = openssl_cipher_iv_length($method);
        $iv     = substr($strIn,0,$ivSize);
        $data   = substr($strIn,$ivSize);
        $clear  = openssl_decrypt ($data, $method, $key, OPENSSL_RAW_DATA||OPENSSL_ZERO_PADDING, $iv);

        return $clear;
    }

    public static function mo_dcrm_sanitize_array_map($array){
        $result=[];
        foreach($array as $key=>$value){
            if(!is_array($key)){
                $key=sanitize_text_field($key);
            }
            if(!is_array($value)){
                $value=sanitize_text_field($value);
            }
            $result[$key]=$value;
        }
        return $result;
    }

    public static function mo_dcrm_check_test_connection_status(){
        $test_connection_status=wpWrapper::mo_dcrm_get_option(pluginConstants::APP_CONFIG_STATUS);
        return $test_connection_status;
    }

    public static function mo_dcrm_check_cf7_status(){
        return is_plugin_active("contact-form-7/wp-contact-form-7.php");
    }

    public static function mo_dcrm_get_contact_form_fields($form_id){

        $ContactForm = \WPCF7_ContactForm::get_instance( $form_id );
        $form_fields = $ContactForm->scan_form_tags();

        $fields = []; 

        foreach($form_fields as $field){
            $type = $field->type;
            $name = $field->name;
            $label = ucwords(str_replace(['-','_']," ",$field->name));

            if($type == 'submit')   
                continue;

            $fields[$name] = ['name'=>$name,'label'=>$label];
        }

        return $fields;

    }

}
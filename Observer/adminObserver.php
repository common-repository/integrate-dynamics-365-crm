<?php

namespace MoDynamics365ObjectSync\Observer;

use MoDynamics365ObjectSync\API\Azure;
use MoDynamics365ObjectSync\API\CustomerMODCRM;
use MoDynamics365ObjectSync\Wrappers\dbWrapper;
use MoDynamics365ObjectSync\Wrappers\dcrmWrapper;
use MoDynamics365ObjectSync\Wrappers\pluginConstants;
use MoDynamics365ObjectSync\Wrappers\templateWrapper;
use MoDynamics365ObjectSync\Wrappers\wpWrapper;

class adminObserver{

    private static $obj;

    public static function getObserver(){
        if(!isset(self::$obj)){
            self::$obj = new adminObserver();
        }
        return self::$obj;
    }

    public function mo_dcrm_admin_observer(){

        if(isset($_REQUEST['code'])){
            $this->mo_dcrm_get_auth_code();
            return;
        }

        if(!isset($_REQUEST['option']))
            return;

        switch ($_REQUEST['option']) {
            case 'testdcrmapp':
                $this->mo_dcrm_test_app_connection();
                break;
            case 'testdcrmautomaticapp':
                $this->mo_dcrm_connect_to_dynamics();
                break;
            case 'dcrm_automatic_app_status':
                $this->mo_dcrm_reload_window_opener();
                $this->mo_dcrm_show_success_message();
                break;
            case 'mo_dcrm_contact_us_query_option':
                $this->mo_dcrm_contact_us_query_option();
                break;
            case 'mo_dcrm_feedback':
                $this->mo_dcrm_feedback();
                break;
        }
    }

private function mo_dcrm_contact_us_query_option(){
    $submited=$this->mo_dcrm_send_support_query();
    if(!is_null($submited)){
        if ( $submited == false ) {
            wpWrapper::mo_dcrm__show_error_notice(esc_html__("Your query could not be submitted. Please try again."));
        } else {
            wpWrapper::mo_dcrm__show_success_notice(esc_html__("Thanks for getting in touch! We shall get back to you shortly."));
        }
    }    
}
private function mo_dcrm_feedback(){
    $sent = isset($_REQUEST['miniorange_feedback_submit']);
    $skip = isset($_REQUEST['miniorange_skip_feedback']);
    $submited = $this->mo_dcrm_send_email_alert($skip,$sent);
    if( json_last_error() == JSON_ERROR_NONE) {
        if(is_array( $submited ) && array_key_exists( 'status', $submited ) && $submited['status'] == 'ERROR' ) {
            wpWrapper::mo_dcrm__show_error_notice(esc_html__($submited['message']));
        }
        else{
            if( $submited == false ){
                wpWrapper::mo_dcrm__show_error_notice(esc_html__("Error while submitting the query."));
            }
        }
    }

    include_once(ABSPATH . 'wp-admin/includes/plugin.php');

    deactivate_plugins( MO_DCRM_PLUGIN_FILE );
    wpWrapper::mo_dcrm__show_success_notice(esc_html__("Thank you for the feedback."));
}

private function mo_dcrm_send_email_alert($isSkipped = false,$isSend=false){
        $user = wp_get_current_user();
        $message = 'Plugin Deactivated';
        $deactivate_reasons=array_key_exists('dcrm_reason',$_POST)? sanitize_text_field($_POST['dcrm_reason']):[];
        
        $deactivate_reason_message = array_key_exists( 'query_feedback', $_POST ) ? htmlspecialchars(sanitize_text_field($_POST['query_feedback'])) : false;


        if($isSkipped && $deactivate_reason_message==false)
            $deactivate_reason_message = "skipped";
        if($isSend && $deactivate_reason_message==false)
            $deactivate_reason_message = "Send";

        $reply_required = '';
        if(isset($_POST['get_reply']))
            $reply_required = htmlspecialchars(sanitize_text_field($_POST['get_reply']));
        if(empty($reply_required)){
            $reply_required = "don't reply";
            $message.='<b style="color:red";> &nbsp; [Reply :'.$reply_required.']</b>';
        }else{
            $reply_required = "yes";
            $message.='[Reply :'.$reply_required.']';
        }

        if(is_multisite())
            $multisite_enabled = 'True';
        else
            $multisite_enabled = 'False';

        $message.= ', [Multisite enabled: ' . $multisite_enabled .']';
        
        $message.= ', Feedback : '.$deactivate_reason_message.'';
            
        $email = '';
        $reasons='';
        
        foreach($deactivate_reasons as $reason){
            $reasons.=$reason;
            $reasons.=',';
        }
        
        $reasons=substr($reasons, 0, -1);
        $message.= ', [Reasons :'.$reasons.']';

        if (isset($_POST['query_mail']))
            $email = sanitize_email($_POST['query_mail']);

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $email = get_option('mo_dcrm_admin_email');
            if(empty($email))
                $email = $user->user_email;
        }
        $phone = get_option( 'mo_dcrm_admin_phone' );
        $feedback_reasons = new CustomerMODCRM();
        
        $response = json_decode( $feedback_reasons->mo_dcrm_send_email_alert( $email, $phone, $message ), true );
        return $response;
    }

    private function mo_dcrm_connect_to_dynamics(){
        $customer_tenant_id = 'common';
        $mo_client_id = (pluginConstants::CID);
        wp_redirect("https://login.microsoftonline.com/$customer_tenant_id/oauth2/authorize?response_type=code&client_id=$mo_client_id&scope=openid&redirect_uri=https://connect.xecurify.com/&state=".home_url()."");
        exit();
    }

    private function mo_dcrm_test_app_connection(){
        $config = wpWrapper::mo_dcrm_get_option(pluginConstants::APP_CONFIG);
        
        $apiHandler = Azure::getClient($config);
        $response = $apiHandler->mo_dcrm_get_all_entity_definations();

        if($response['status']){
            $this->mo_dcrm_reload_window_opener();

            $entity_objects = dcrmWrapper::mo_dcrm_process_entity_objects($response['data']);
            wpWrapper::mo_dcrm_set_option(pluginConstants::ENTITY_OBJECTS,$entity_objects);

            wpWrapper::mo_dcrm_set_option(pluginConstants::APP_CONFIG_STATUS,1);

            dbWrapper::mo_dcrm_create_cf7_object_map_table();

            $this->mo_dcrm_show_success_message_for_test_connection($entity_objects);

        }else{
            $this->mo_dcrm_reload_window_opener();
            wpWrapper::mo_dcrm_set_option(pluginConstants::APP_CONFIG_STATUS,0);
            $error_code = [
                "Error" => $response['data']['error'],
                "Description" => empty($response['data']['error'])?'':$response['data']['error_description']
            ];
            $this->mo_dcrm_display_error_message($error_code);
        }
    }

    private function mo_dcrm_get_auth_code(){
        wpWrapper::mo_dcrm_delete_option(pluginConstants::DCRM_RFTK);
        wpWrapper::mo_dcrm_set_option(pluginConstants::DCRMAUTHCODE,sanitize_text_field($_REQUEST['code']));
        
        wp_safe_redirect(admin_url('?option=dcrm_automatic_app_status'));
        exit();
    }

    private function mo_dcrm_reload_window_opener(){
        echo '<script>window.opener.reload_page_to_see_reflected_changes();</script>';
    }

    private function mo_dcrm_display_error_message($error_code){
        ?>
            <div class="dcrm_test_connection__error">
                
                <div class="dcrm_test_connection__error-heading">
                    Error
                </div>

                <table class="mo-ms-tab-content-app-config-table" style="border-collapse:collapse;width:90%">
                    <tr>
                        <td align="center" class="dcrm_test_connection__error-tableHeading" colspan="2"><h2><span>Test Configuration Failed</span></h2></td>
                    </tr>
                    <?php foreach ($error_code as $key => $value){
                       echo '<tr><td class="left-div dcrm_test_connection__error-table-colkey"><span style="margin-right:10px;"><b>'.esc_html($key).':</b></span></td>
                       <td class="right-div dcrm_test_connection__error-table-colvalue"><span>'.esc_html($value).'</span></td></tr>';
                    }?>
                </table>
                <h3 style="margin:20px;">
                    Contact us at <a style="color:#dc143c" href="mailto:samlsupport@xecurify.com">samlsupport@xecurify.com</a>
                </h3>
            </div>
        <?php
        $this->load_css();
        exit();
    }

    private function mo_dcrm_show_success_message(){
        echo '<div class="dcrm_test_connection__success">
            <div class="dcrm_test_connection__success-heading">
                Success
            </div>';
        $this->load_css();
        die();
    }

    private function mo_dcrm_show_success_message_for_test_connection($entity_objects){
        echo '<div class="dcrm_test_connection__success">
            <div class="dcrm_test_connection__success-heading">
                Success
            </div>
            <div class="dcrm_test_connection__success_test_connection-title"><img width="20px" height="20px" style="margin-right:10px;" src="'.esc_url_raw(templateWrapper::mo_dcrm_get_image_src('checked.png')).'" />'.count($entity_objects).' dynamics objects fetched successfully</div>
            <div class="dcrm_test_connection__success_test_connection-content">';
        foreach($entity_objects as $key=>$object){
            echo '<div class="dcrm_test_connection__success_test_connection-content-objects">'.esc_html($object['label']).'</div>';
        }
        echo '</div>';
        $this->load_css();
        die();
    }

    private function mo_dcrm_send_support_query(){
        $name     = htmlspecialchars(sanitize_text_field($_POST['mo_dcrm_contact_us_name']));
        $email    = sanitize_email($_POST['mo_dcrm_contact_us_email']);
        $subject    = htmlspecialchars(sanitize_text_field($_POST['mo_dcrm_contact_us_subject']));
        $query    = htmlspecialchars(sanitize_text_field($_POST['mo_dcrm_contact_us_query']));

        $query = '[Dynamics 365 CRM Integration] [ Subject : '.$subject.' ] '. $query;
                
        $handler = new CustomerMODCRM();

        $response = $handler->mo_dcrm_submit_contact_us($name,$email,$query);

        return $response;
    }

    private function load_css(){
        ?>
        <style>
            .test-container{
                width: 100%;
                background: #f1f1f1;
                margin-top: -30px;
            }

            .dcrm_test_connection__success_test_connection-title{
                display:flex;justify-content:flex-start;align-items:center;margin:10px;width:90%;
            }
            .dcrm_test_connection__success_test_connection-content{
                width:90%;display:flex;justify-content:flex-start;align-items:center;flex-wrap:wrap;height:400px;overflow-y:scroll;
            }
            .dcrm_test_connection__success_test_connection-content::-webkit-scrollbar {
                display: none;
            }
            .dcrm_test_connection__success_test_connection-content-objects{
                padding:10px;background-color:#eee;font-size:15px;margin:10px;border-radius:5px
            }

            .dcrm_test_connection__error{
                width:100%;display:flex;flex-direction:column;justify-content:center;align-items:center;font-size:15px;margin-top:10px;width:100%;
            }
            .dcrm_test_connection__error-heading{
                width:86%;padding: 15px;text-align: center;background-color:#f2dede;color:#a94442;border: 1px solid #E6B3B2;font-size: 18pt;margin-bottom:20px;
            }
            .dcrm_test_connection__error-tableHeading{
                padding: 30px 5px 30px 5px;border:1px solid #757575;
            }
            .dcrm_test_connection__error-table-colkey{
                padding: 30px 5px 30px 5px;border:1px solid #757575;
            }
            .dcrm_test_connection__error-table-colvalue{
                padding: 30px 5px 30px 5px;border:1px solid #757575;
            }
            .dcrm_test_connection__success{
                display:flex;justify-content:center;align-items:center;flex-direction:column;border:1px solid #eee;padding:10px;
            }
            .dcrm_test_connection__success-heading{
                width:90%;color: #3c763d;background-color: #dff0d8;padding: 2%;margin-bottom: 20px;text-align: center;border: 1px solid #AEDB9A;font-size: 18pt;
            }

            .mo-ms-tab-content-app-config-table{
                max-width: 1000px;
                background: white;
                padding: 1em 2em;
                margin: 2em auto;
                border-collapse:collapse;
                border-spacing:0;
                display:table;
                font-size:14pt;
            }

            .mo-ms-tab-content-app-config-table td.left-div{
                width: 40%;
                word-break: break-all;
                font-weight:bold;
                border:2px solid #949090;
                padding:2%;
            }
            .mo-ms-tab-content-app-config-table td.right-div{
                width: 40%;
                word-break: break-all;
                padding:2%;
                border:2px solid #949090;
                word-wrap:break-word;
            }

        </style>
        <?php
    }
}
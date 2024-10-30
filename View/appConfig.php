<?php

namespace MoDynamics365ObjectSync\View;

use MoDynamics365ObjectSync\Wrappers\pluginConstants;
use MoDynamics365ObjectSync\Wrappers\templateConstants;
use MoDynamics365ObjectSync\Wrappers\templateWrapper;
use MoDynamics365ObjectSync\Wrappers\wpWrapper;

class appConfig{

    private static $instance;

    public static function getView(){
        if(!isset(self::$instance)){
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public function mo_dcrm_display__tab_details(){
        $app = wpWrapper::mo_dcrm_get_option(pluginConstants::APP_CONFIG);

        ?>
        <div class="dcrm-tab-content">
            <div style="display:flex">
                <span><h1>Configure Microsoft Dynamics 365 CRM Application</h1></span>
            </div>
            <div style="border-top:4px solid #3F51B5;width:30%;margin-bottom:15px;border-radius:10px"></div>
            <div style="width: 100%">
                <div>
                    <?php

                    $app_status = wpWrapper::mo_dcrm_get_option(pluginConstants::APP_CONFIG_STATUS);
                    $has_code = wpWrapper::mo_dcrm_get_option(pluginConstants::DCRMAUTHCODE);
                    if(empty($app_status))
                        $app_status = 0;

                    $active_track = [
                        1,
                        (isset($app['dcrm_org_endpoint']) && !empty($app['dcrm_org_endpoint'])),
                        (isset($app['app_type']) && !empty($app['app_type'])),
                        (isset($app['app_type']) && $app['app_type'] == 'manual')?((isset($app['client_id']) && !empty($app['client_id'])) && (isset($app['client_secret']) && !empty($app['client_secret'])) && (isset($app['tenant_id']) && !empty($app['tenant_id']))):(!empty($has_code)),
                        $app_status
                    ];

                    foreach(templateConstants::app_config__tiles as $tile){
                        $contentFunction = $tile['content'];
                        $active = $active_track[$tile['index']-1];
                        $activeTile = $active_track[$tile['index']];
                        templateWrapper::mo_dcrm_load_tile('app_config',$tile['index'],$tile['title'],$this->$contentFunction($app,$active),$activeTile,false,$tile['description']);
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }

    private function mo_dcrm_online_url_settings($app,$active = true){
        $dcrm_org_endpoint = isset($app['dcrm_org_endpoint'])?$app['dcrm_org_endpoint']:'';
        $label_width = "20%";
        $input_width = "80%";
        $tab = 'app_config';
        $_nonce = 'mo_dcrm_online_url_controller';
        
        $form_inputs = [
            ['label'=>'Dynamics 365 Online URL','placeholder'=>'Enter Your Dynamics 365 Online URL','type'=>'url','name'=>'dcrm_org_endpoint','value'=>$dcrm_org_endpoint],
        ];
        
        return templateWrapper::mo_dcrm_load_basic_form($label_width,$input_width,$form_inputs,$tab,$_nonce,$active);
    }

    private function mo_dcrm_display_application_types($app,$active = true){

        $app_type = isset($app['app_type'])?$app['app_type']:'manual';

        $label_width = "100%";
        $input_width = "";
        $tab = 'app_config';
        $_nonce = 'mo_dcrm_application_type_controller';

        $form_inputs = [
            ['label'=>'Automatic App Connection ( Login with Dynamics 365 )','placeholder'=>'','type'=>'radio','name'=>'app_type','value'=>'auto','checked'=>$app_type == 'auto'],
            ['label'=>'Manual App Connection ( Use your custom own azure ad app )','placeholder'=>'','type'=>'radio','name'=>'app_type','value'=>'manual','checked'=>$app_type == 'manual'],
        ];
        
        return templateWrapper::mo_dcrm_load_basic_form($label_width,$input_width,$form_inputs,$tab,$_nonce,$active);

    }

    private function mo_dcrm_app_setup_connection($app,$active = true){
        $client_id = !empty($app['client_id'])?$app['client_id']:'';
        $tenant_id = !empty($app['tenant_id'])?$app['tenant_id']:'';
        $app_type = isset($app['app_type'])?$app['app_type']:'manual';
        $disabled = $active?'':'disabled';
   

        if(isset($app['client_secret']) && !empty($app['client_secret'])){
            $client_secret = wpWrapper::mo_dcrm_decrypt_data($app['client_secret'],hash("sha256",$client_id));
        }else{
            $client_secret = '';
        }

        $label_width = "20%";
        $input_width = "80%";
        $tab = 'app_config';
        $_nonce = 'mo_dcrm_setup_connection_controller';

        $form_inputs = [
            ['label'=>'Application ID','placeholder'=>'Enter Your Application (Client) ID','type'=>'text','name'=>'client_id','value'=>$client_id],
            ['label'=>'Client Secret','placeholder'=>'Enter Your Client Secret','type'=>'password','name'=>'client_secret','value'=>$client_secret],
            ['label'=>'Tenant ID','placeholder'=>'Enter Your Directory (Tenant) ID','type'=>'text','name'=>'tenant_id','value'=>$tenant_id],
        ];

        $content = '
        <table id="mo_dcrm_setup_connection_controller_auto" style="width:100%;margin:10px;display:'.($app_type == 'auto'?'block':'none').'">
            <colgroup>
                <col span="1" style="width: 30%;">
                <col span="2" style="width: 70%;">
            </colgroup>
            <tr>
                <td><span style="font-weight:600;">Click here to authorize the application:</span></td>
                <td>
                    <div style="display: flex;justify-content:flex-start;align-items:center;">
                        <div style="display: flex;margin:10px;">
                            <input '.$disabled.' style="height:30px;margin-top:10px" type="submit" id="view_attributes" class="dcrm_basic_form__button'.$disabled.'" onclick="loginWithDynamics()" value="Login with Dynamics 365">
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <script>
            function loginWithDynamics(e){
                var myWindow = window.open("'.esc_url_raw(admin_url('?option=testdcrmautomaticapp')).'", "Test Connection", "scrollbars=1 width=800, height=600");
            }
        </script>

        <div id="mo_dcrm_setup_connection_controller_manual" style="display:'.($app_type == 'manual'?'block':'none').'">'.templateWrapper::mo_dcrm_load_basic_form($label_width,$input_width,$form_inputs,$tab,$_nonce,$active).'</div>
    
        ';

        return $content;
    }

    private function mo_dcrm_app_test_connection($app,$active = true){

        $disabled = $active?'':'disabled';

        $content = '
        <table style="width:100%;margin:10px;">
            <colgroup>
                <col span="1" style="width: 30%;">
                <col span="2" style="width: 70%;">
            </colgroup>
            <tr>
                <td><span style="font-weight:600;">Click here to check your app connection:</span></td>
                <td>
                    <div style="display: flex;justify-content:flex-start;align-items:center;">
                        <div style="display: flex;margin:1px;">
                            <input '.$disabled.' style="height:30px;margin-top:10px" type="button" id="dcrm_test_connection" class="dcrm_basic_form__button'.$disabled.'" onclick="testAppConnection()" value="Test Connection">
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <script>
            function testAppConnection(e){
                var myWindow = window.open("'.esc_url_raw(admin_url('?option=testdcrmapp')).'", "Test Connection", "scrollbars=1 width=800, height=600");
            }
        </script>
        ';

        return $content;
    }
}
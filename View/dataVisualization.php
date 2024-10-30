<?php

namespace MoDynamics365ObjectSync\View;

use MoDynamics365ObjectSync\Wrappers\pluginConstants;
use MoDynamics365ObjectSync\Wrappers\templateConstants;
use MoDynamics365ObjectSync\Wrappers\templateWrapper;
use MoDynamics365ObjectSync\Wrappers\wpWrapper;

class dataVisualization
{

    private static $instance;

    public static function getView()
    {
        if (!isset(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public function mo_dcrm_display__tab_details()
    {
?>
        <div class="dcrm-tab-content">
            <h1>Embed Dynamics Enitities using shortcode</h1>
            <div style="border-top:4px solid #3F51B5;width:30%;margin-bottom:15px;border-radius:10px"></div>
            <div style="width: 100%">
                <div>
                    <?php
                    foreach (templateConstants::data_visualization__tiles as $tile) {
                        $contentFunction = $tile['content'];
                        templateWrapper::mo_dcrm_load_tile('data_visualization', $tile['index'], $tile['title'], $this->$contentFunction(), false, $tile['advert'],$tile['description']);
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php
    }

    private function mo_dcrm_generate_shortcode()
    {
        $entity_objects = wpWrapper::mo_dcrm_get_option(pluginConstants::ENTITY_OBJECTS);
        if(empty($entity_objects))
            $entity_objects = [];

        $content = '<div id="dcrm_section-tile-content_cf7_config_4" class="dcrm_section-tile-content" style="display: block;">
            <table style="width:100%;margin:10px;">
                <colgroup>
                    <col span="1" style="width: 20%;">
                    <col span="2" style="width: 80%;">
                </colgroup>

                <tbody>
                    <tr>
                        <td><span style="font-weight:600">Select Dynamics Object:</span></td>
                        <td><select placeholder="" style="width:95%" type="dropdown" id="dcrm_sc_dynamics_object" name="dcrm_sc_dynamics_object" value="">
                        <option disabled selected value="default">--- Select Dynamics Object to Embed ---</option>';
                                if(isset($entity_objects)) {
                                    foreach($entity_objects as $entity) {
                                        $content .= '<option>'.$entity['label'].'</option>';
                                    }
                                }
                                $content.='
                            </select></td>
                    </tr>
                    <tr>
                        <td>
                            <div style="display: flex;justify-content:flex-start;align-items:center;">
                                <div style="display: flex;margin:1px;">
                                    <input style="height:30px;margin-top:10px" disabled type="submit" class="dcrm_basic_form__button" value="Save">
                                </div>
                            </div>
                        </td>
                        <td>

                        </td>
                    </tr>
                </tbody>
            </table>
        </div>';
        return $content;
    }

    private function mo_dcrm_advance_settings()
    {
        $content = '
        <div id="dcrm_section-tile-content_cf7_config_4" class="dcrm_section-tile-content" style="display: block;">
            <div style="padding:10px;display:flex;justify-content:center;align-items:center;">
                <span style="display:none;" id="mo_dcrm_sc_config_mapping_loader"><img width="40px" height="40px" src="http://localhost/salesforce_testing/wp-content/plugins/integrate-dynamics-365-crm-premium/images/loader.gif"></span>
            </div>
            <form action="" method="post">
                <input type="hidden" name="option" id="app_config" value="mo_dcrm_select_sc_adv_settings_controller">
                <input type="hidden" name="mo_dcrm_tab" value="app_config"><input type="hidden" id="_wpnonce" name="_wpnonce" value="d12b60be8a"><input type="hidden" name="_wp_http_referer" value="/salesforce_testing/wp-admin/admin.php?page=mo_dcrm&amp;tab=entity_shortcode">
                <table style="width:100%;margin:10px;">
                    <colgroup>
                        <col span="1" style="width: 20%;">
                        <col span="2" style="width: 80%;">
                    </colgroup>

                    <tbody>
                        <tr>
                            <td><span style="font-weight:600">Select Columns:</span></td>
                            <td><select multiple="" disabled="" placeholder="" style="width:95%;height:200px" type="multidropdown" id="dcrm_sc_dynamics_object_fields" name="dcrm_sc_dynamics_object_fields" value="">
                                    <option disabled="" value="default">--- Select Columns For View ---</option>
                                </select></td>
                        </tr>
                        <tr>
                            <td><span style="font-weight:600">Enter query to filter records:</span></td>
                            <td><input disabled="" placeholder="" style="width:95%" type="text" name="dcrm_sc_filter" value=""></td>
                        </tr>
                        <tr>
                            <td>
                                <div style="display: flex;justify-content:flex-start;align-items:center;">
                                    <div style="display: flex;margin:1px;">
                                        <input disabled="" style="height:30px;margin-top:10px" type="submit" id="mo_dcrm_select_sc_adv_settings_controller_saveButton" class="dcrm_basic_form__buttondisabled" value="Save">
                                    </div>
                                </div>
                            </td>
                            <td>

                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>';
        return $content;
    }

    private function mo_dcrm_copy_shortcode()
    { 
        $content = '<div id="dcrm_section-tile-content_cf7_config_4" class="dcrm_section-tile-content" style="display: block;">
            <div style="padding:10px;display:flex;justify-content:center;align-items:center;">
                <span style="display:none;" id="mo_dcrm_sc_config_shortcode_loader"><img width="40px" height="40px" src="http://localhost/salesforce_testing/wp-content/plugins/integrate-dynamics-365-crm-premium/images/loader.gif"></span>
            </div>
            <div style="background-color:#eeee;display:flex;justify-content:space-between;align-items:center;padding:20px;margin:20px">
                <div style="color: transparent;text-shadow: 0 0 4px #000;pointer-events:none;" id="dcrm_text_to_copy">[MO_DYNAMICS_ENTITY entity="<b>Object_name</b>" columns="<b></b>" filter="<b></b>"]</div>
                <div class="dcrm_tooltip" name="dcrm_copy_icon" style="width:4%;cursor:pointer;">
                    <span id="dcrm_copy" style="background-color:rgba(0,0,0,0.1);border-radius:50%;height:30px;width:30px;display:flex;justify-content:center;align-items:center;font-size:14px;" class="dashicons dashicons-admin-page"></span>
                    <span id="dcrm_tooltip_text_copy" class="dcrm_tooltiptext"></span>
                </div>
            </div>
        </div>';
        return $content;
    }
}

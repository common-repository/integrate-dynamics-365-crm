<?php

namespace MoDynamics365ObjectSync\View;

use MoDynamics365ObjectSync\Wrappers\dbWrapper;
use MoDynamics365ObjectSync\Wrappers\pluginConstants;
use MoDynamics365ObjectSync\Wrappers\templateConstants;
use MoDynamics365ObjectSync\Wrappers\templateWrapper;
use MoDynamics365ObjectSync\Wrappers\wpWrapper;

class cf7Config
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

        if (!isset($_GET['id']) && empty($_GET['id'])) {
?>
            <div class="dcrm-tab-content">
                <h1>Contact Form 7 - Dynamics Objects Mapping</h1>
                <div id="mo_dcrm_main_div">
                    <img id="mo_dcrm_info" src="<?php echo esc_url_raw(templateWrapper::mo_dcrm_get_image_src('info.png')); ?>">
                    <p class="mo_dcrm_info_text"><a target="_blank" href="<?php echo pluginConstants::CF7_OBJECT_MAPPING_SETUP_GUIDE_LINK ; ?>" style="color:white;">know more</a> about Configuring object mapping</p>
                </div>
                <div style="border-top:4px solid #3F51B5;width:30%;margin-bottom:15px;border-radius:10px"></div>
                <?php
                if (!(wpWrapper::mo_dcrm_check_test_connection_status())) {
                    $this->mo_dcrm_show_test_connection_Waring();
                    $this->mo_dcrm_display_cf7_config_table(true);
                } else if (!wpWrapper::mo_dcrm_check_cf7_status()) {
                    $this->mo_dcrm_show_plugin_not_installed_Warning();
                    $this->mo_dcrm_display_cf7_config_table(true);
                } else {
                    $this->mo_dcrm_display_cf7_config_table();
                }
                $this->mo_dcrm_display_premium_features();
                ?>
            </div>
        <?php

            return;
        }

        $app = dbWrapper::mo_dcrm_get_cf7_object_map_table_row(sanitize_text_field($_GET['id']));

        $active_track = [
            1,
            (isset($app['cf7_form_id']) && !empty($app['cf7_form_id'])),
            (isset($app['dynamics_object_id']) && !empty($app['dynamics_object_id'])),
            (isset($app['field_map']) && !empty($app['field_map'])),
            0,
            0
        ];

        ?>
        <div class="dcrm-tab-content">
            <h1>Contact Form 7 - Dynamics Objects Mapping</h1>
            <div id="mo_dcrm_main_div">
                <img id="mo_dcrm_info" src="<?php echo esc_url_raw(templateWrapper::mo_dcrm_get_image_src('info.png')); ?>">
                <p class="mo_dcrm_info_text"><a target="_blank" href="<?php echo pluginConstants::CF7_OBJECT_MAPPING_SETUP_GUIDE_LINK ; ?>" style="color:white;">know more</a> about Configuring object mapping</p>
            </div>
            <div style="border-top:4px solid #3F51B5;width:30%;margin-bottom:15px;border-radius:10px"></div>
            <a href="<?php echo esc_url_raw(remove_query_arg(['id', 'option'])); ?>" style="padding:5px;text-align:center; border-radius:5px; border-color:#323d87; background:#323d87; width:150px; margin: 0px 0px 8px 0px;" class="button button-primary button-large"> Go Back </a>
            <div class="mo_dcrm_alert mo_dcrm_warning_alert" style="color:red;margin-bottom: 10px;display:none" id="cf7_config_notices"></div>
            <div style="width: 100%">
                <div>
                    <?php
                    foreach (templateConstants::cf7_config__tiles as $tile) {
                        $contentFunction = $tile['content'];
                        $active = $active_track[$tile['index'] - 1];
                        $activeTile = $active_track[$tile['index']];
                        templateWrapper::mo_dcrm_load_tile('cf7_config', $tile['index'], $tile['title'], $this->$contentFunction($app, $active), $activeTile, $tile['advert'], $tile['description']);
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php

        $cf7dcrm_integrate_js_url = plugins_url('../includes/js/mo_cf7dcrm_setting.js', __FILE__);
        wp_enqueue_script('mo_cf7dcrm_integrate_js', $cf7dcrm_integrate_js_url, array('jquery'));
        wp_localize_script('mo_cf7dcrm_integrate_js', 'cf7dcrmConfigs', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' =>  wp_create_nonce('mo_cf7dcrm_integrate__nonce'),
            'app' => $app,
            'id' => sanitize_text_field($_GET['id'])
        ]);
    }

    private function mo_dcrm_show_test_connection_Waring()
    {
        echo '<div class="mo_dcrm_alert mo_dcrm_warning_alert" style="color:red;margin-bottom: 10px;"> Please do configure the plugin correctly and test app connection in <a href="' . esc_url_raw(remove_query_arg('tab')) . '">Manage Application</a> tab.</div>';
    }

    private function mo_dcrm_show_plugin_not_installed_Warning()
    {
        echo '<div class="mo_dcrm_alert mo_dcrm_warning_alert" style="color:red;margin-bottom: 10px;"> Please install and activate <a href="https://wordpress.org/plugins/contact-form-7/">Contact Form 7 Plugin</a> to use this feature.</div>';
    }

    private function mo_dcrm_cf7_edit_config($id, $disabled = '')
    {
        $content = '<div class="flex-container">';
        $content = $content . '<a style="';
        if (!empty($disabled)) {
            $content = $content . 'pointer-events: none;';
        }
        $content = $content . ' text-decoration:none;" href="' . esc_url_raw(add_query_arg(['id' => $id, 'option' => 'edit'])) . '"> Edit </a> |';
        $content = $content . '
        <form hidden name="f" method="post" action="" id="mo_dcrm_delete_row_' . $id . '">
            ' . wp_nonce_field("mo_dcrm_delete_row") . '
            <input type="hidden" name="mo_dcrm_tab" value="cf7_config"/>
            <input type="hidden" name="option" value="mo_dcrm_delete_row"/>
            <input type="hidden" name="id" value="' . $id . '"/>            
        </form>
        <input type="button" style="border:none; background: transparent; color: #2271b1; padding:0px; box-shadow:none;';
        if (empty($disabled)) {
            $content = $content . 'cursor: pointer;';
        }
        $content = $content . '" onclick="document.getElementById(' . "'" . 'mo_dcrm_delete_row_' . $id . "'" . ').submit();"' . $disabled . ' placeholder="Delete" value="Delete"/>';
        $content = $content . '</div>';
        return $content;
    }

    private function mo_dcrm_add_new_cf7_config($disabled = '')
    {
        $id = dbWrapper::mo_dcrm_get_new_id();
        $content = '<a ' . $disabled . ' href="' . esc_url_raw(add_query_arg(['id' => $id, 'option' => 'addconfig'])) . '" style=" padding:5px; text-align:center; border-radius:5px; border-color:#323d87; background:#323d87; width:150px; margin: 0px 0px 13px 0px;" class="button button-primary button-large" > + Add New Mapping </a>';
        return $content;
    }

    private function mo_dcrm_display_cf7_config_table($disabled = false)
    {
        if ($disabled) {
            $disabled = 'disabled';
        } else {
            $disabled = '';
        }
        $dcrm_table_css_url = plugins_url('../includes/css/mo_dcrm_table_css.css', __FILE__);
        wp_enqueue_style('mo_dcrm_table_css', $dcrm_table_css_url, array(), PLUGIN_VERSION);
        $content = '';
        $content = $content . $this->mo_dcrm_add_new_cf7_config($disabled);
        $data = [];
        $data['head'] = ['Sr.No', 'CF7_Form', 'Dynamics Object', 'Field Mapping', 'Config'];
        $db_data = dbWrapper::mo_dcrm_get_all_cf7_object_entity_config();
        $entity_objects = wpWrapper::mo_dcrm_get_option(pluginConstants::ENTITY_OBJECTS);
        $index = 1;
        $rows = [];
        foreach ($db_data as $key => $config) {
            $config = (array) $config;
            $id = $config['id'];
            $CF7_form = '<div Style="color:red;font-weight:500">Pending</div>';
            if (!empty(get_the_title($config['cf7_form_id']))) {
                $CF7_form = get_the_title($config['cf7_form_id']);
            }
            $Dynamics_object = '<div Style="color:red;font-weight:500">Pending</div>';
            if (isset($config['dynamics_object_id']) && !empty($config['dynamics_object_id'])) {
                $Dynamics_object = $entity_objects[$config['dynamics_object_id']]['name'];
            }

            $field_map = '<div Style="color:red;font-weight:500">Pending</div>';
            if (isset($config['field_map']) && !empty($config['field_map']))
                $field_map = '<div Style="color:green;font-weight:500">Done</div>';

            array_push($rows, [$index, $CF7_form, $Dynamics_object, $field_map, $this->mo_dcrm_cf7_edit_config($id, $disabled)]);
            $index += 1;
        }
        $data['rows'] = $rows;
        $content = $content . templateWrapper::mo_dcrm_load_table($data);
        echo $content;
    }

    private function mo_dcrm_select_cf7_form($app, $active)
    {

        if ($active) {
            $cf7_all_forms = get_posts(array(
                'numberposts' => -1,
                'orderby' => 'ID',
                'order' => 'ASC',
                'post_type' => 'wpcf7_contact_form'
            ));

            $forms = [];
            foreach ($cf7_all_forms as $form_post) {
                array_push($forms, ['name' => $form_post->ID, 'label' => $form_post->post_title]);
            }
        }

        $value = isset($app['cf7_form_id']) ? $app['cf7_form_id'] : '';

        $label_width = "20%";
        $input_width = "80%";
        $tab = 'app_config';
        $_nonce = 'mo_dcrm_select_cf7_form_controller';

        $form_inputs = [
            ['label' => 'Select Contact Form 7 form', 'placeholder' => '', 'type' => 'dropdown', 'name' => 'dcrm_cf7_form_options', 'value' => $value, 'defaultValue' => '--- Select Contact 7 Form ---', 'options' => $forms],
        ];

        return templateWrapper::mo_dcrm_load_basic_form($label_width, $input_width, $form_inputs, $tab, $_nonce, $active);
    }

    private function mo_dcrm_select_cf7_dynamics_object($app, $active)
    {
        $entity_objects = [];
        if ($active)
            $entity_objects = wpWrapper::mo_dcrm_get_option(pluginConstants::ENTITY_OBJECTS);
        $value = isset($app['dynamics_object_id']) ? $app['dynamics_object_id'] : '';

        $label_width = "20%";
        $input_width = "80%";
        $tab = 'app_config';
        $_nonce = 'mo_dcrm_select_cf_dynamics_object_controller';

        $form_inputs = [
            ['label' => 'Select Dynamics Object', 'placeholder' => '', 'type' => 'dropdown', 'name' => 'dcrm_cf7_dynamics_object', 'value' => $value, 'defaultValue' => '--- Select Dynamics Entity Object ---', 'options' => $entity_objects],
        ];

        return templateWrapper::mo_dcrm_load_basic_form($label_width, $input_width, $form_inputs, $tab, $_nonce, $active);
    }

    private function mo_dcrm_select_cf7_dynamics_object_mapping($app, $active)
    {
        $disabled = $active ? '' : 'disabled';
        $form = isset($app['cf7_form_id']) ? $app['cf7_form_id'] : '';
        $object = isset($app['dynamics_object_id']) ? $app['dynamics_object_id'] : '';
        $attrs = isset($app['object_attrs']) ? json_decode($app['object_attrs'], true) : ['required' => [], 'optional' => []];
        $fields = isset($app['form_fields']) ? json_decode($app['form_fields'], true) : [];
        $field_map = isset($app['field_map']) ? json_decode($app['field_map'], true) : [];

        $content = '
            <div style="padding:10px;display:flex;justify-content:center;align-items:center;">
                <span style="display:none;" id="mo_dcrm_cf7_config_mapping_loader"><img width="40px" height="40px" src="' . templateWrapper::mo_dcrm_get_image_src('loader.gif') . '"/></span>
            </div>
        ';

        foreach ($attrs['required'] as $attr) {
            $content = $content . (templateWrapper::mo_dcrm_load_field_mapping($object, $form, $fields, $attr, $field_map, 1, 1));
        }

        foreach ($attrs['optional'] as $attr) {
            $content = $content . (templateWrapper::mo_dcrm_load_field_mapping($object, $form, $fields, $attr, $field_map, 0, 1));
        }

        $content = $content . '
        <div id="mo_dcrm_cf7_mapping_track_end"></div>
        <div style="padding:10px;display:flex;justify-content:flex;align-items:center;">';

        $inputHTML = '<select ' . $disabled . ' style="width:95%" id="mo_dcrm_object_form_mapping_deault_select" name="mo_dcrm_object_form_mapping_deault_select" value="">';

        if (!$active) {
            $inputHTML = $inputHTML . '<option>--- Select Object Field ---</option>';
        }

        foreach ($attrs['optional'] as $field) {
            $inputHTML = $inputHTML . '<option ' . (isset($field['is_added']) ? 'disabled' : '') . ' ' . ($field['label'] == '' ? '' : '') . ' value="' . $field['name'] . '">' . $field['label'] . '</option>';
        }
        $inputHTML = $inputHTML . '</select>';

        $content = $content . $inputHTML;

        $content = $content . '
            <input ' . $disabled . ' style="height:30px;margin:10px" type="button" id="dcrm_cf7_add_new_mapping_button" class="dcrm_basic_form__button' . $disabled . '" value="Add New Mapping">
            <div style="padding:10px;display:flex;justify-content:center;align-items:center;">
            <span style="display:none;" id="mo_dcrm_cf7_config_new_mapping_loader"><img width="40px" height="40px" src="' . templateWrapper::mo_dcrm_get_image_src('loader.gif') . '"/></span>
        </div>
        </div>
        <div>
        <input ' . $disabled . ' style="height:30px;margin:10px" type="button" id="dcrm_cf7_save_new_mapping_button" class="dcrm_basic_form__button' . $disabled . '" value="Save">
        </div>
        ';

        return $content;
    }

    private function mo_dcrm_restriction_on_dynamics_entry($app,$active)
    {
        $fields = isset($app['form_fields']) ? json_decode($app['form_fields'], true) : [];
        $content = '
        <div id="dcrm_section-tile-content_cf7_config_4" class="dcrm_section-tile-content" style="display: block;">
            <div class="mo_dcrm_div">
                <div class="mo_dcrm_group">
                    <div class="mo_dcrm_row">
                        <div class="mo_dcrm_col1">
                            <label for="crm_optin">
                                Choose a condition
                            </label>
                        </div>
                        <div class="mo_dcrm_col4">
                            <div>
                                <input disabled type="checkbox" id="switch" name="crm_encoding_enabled" class="mo-dcrm-switch" /><label class="mo-dcrm-switch-label" for="switch">Enable</label>
                            </div>
                        </div>
                    </div>
                    <div class="mo_dcrm_row">
                        <div class="mo_dcrm_col2">
                            <div style="clear: both;"></div>
                            <div id="crm_optin_div" style="margin-top: 16px;">
                                <div>
                                    <div>
                                        <div class="mo_dcrm_filter_div">
                                                <div class="mo_dcrm_filter_field mo_dcrm_filter_field1">
                                                    <select id="crm_optin_field" style="width:13rem">
                                                    <option disabled selected value="default">--- Select Form Field ---</option>';
                                                        foreach ($fields as $field) {
                                                            $content .= '<option>' . $field['label'] . '</option>';
                                                        }
                                                        $content .= '
                                                    </select>
                                                </div>
                                                <div class="mo_dcrm_filter_field mo_dcrm_filter_field2">
                                                    <select name="meta[filters][1][1][op]" style="width:13rem">
                                                        <option>Exactly Matches</option>
                                                        <option>Does Not Exactly Match</option>
                                                        <option>Contains</option>
                                                        <option>Does Not Contain</option>
                                                        <option>Is In</option>
                                                        <option>Is Not In</option>
                                                        <option>Starts With</option>
                                                        <option>Does Not Start With</option>
                                                        <option>Ends With</option>
                                                        <option>Does Not End With</option>
                                                        <option>Less Than</option>
                                                        <option>Greater Than</option>
                                                        <option>Less Than</option>
                                                        <option>Greater Than</option>
                                                        <option>Equals</option>
                                                        <option>Is Empty</option>
                                                        <option>Is Not Empty</option>
                                                    </select>
                                                </div>
                                                <div class="mo_dcrm_filter_field mo_dcrm_filter_field3">
                                                    <input type="text" placeholder="Value" value="" style="width:13rem">
                                                </div>

                                                

                                                <div style="clear: both;"></div>
                                        </div>
                                        <button disbaled class="dcrm_basic_form__button" style="margin-top:10px;">
                                                    Add New Condition </button>
                                    </div>
                                    <input style="height:30px;margin-top:10px;pointer-events:none;" disabled type="button" class="dcrm_basic_form__button" value="Save">
                                </div>
                            </div>
                        </div>
                        <div style="clear: both;"></div>
                    </div>

                </div>
            </div>
        </div>';

        return $content;
    }

    private function mo_dcrm_choose_primary_key($app,$active)
    {
        $attrs = isset($app['object_attrs']) ? json_decode($app['object_attrs'], true) : ['required' => [], 'optional' => []];
        $content = '<div id="dcrm_section-tile-content_cf7_config_4" class="dcrm_section-tile-content" style="display: block;">
                    <div class="mo_dcrm_div ">
                        <div class="mo_dcrm_group">
                            <div class="mo_dcrm_row">
                                <div class="mo_dcrm_col1">
                                    <label for="dcrm_primary_field">Select Primary Key</label>
                                </div>
                                <div class="mo_dcrm_col2">
                                    <select id="dcrm_primary_field" name="meta[primary_key]" autocomplete="off">
                                    <option disabled selected value="default">--- Select Object Field ---</option>';
                                    foreach($attrs['required'] as $req) $content .= '<option'.(isset($req['is_added']) ? 'disabled' : '') . ' ' . ($req['label'] == '' ? '' : '') . ' value="' . $req['name'] . '">' . $req['label'].'</option>';
                                    foreach($attrs['optional'] as $opt) $content.= '<option'.(isset($opt['is_added']) ? 'disabled' : '') . ' ' . ($opt['label'] == '' ? '' : '') . ' value="' . $opt['name'] . '">' . $opt['label'].'</option>';
                                    $content .= '</select>
                                </div>
                                <div class="clear"></div>
                            </div>
                            <div class="mo_dcrm_row">
                                <input style="height:30px;margin-top:10px;pointer-events:none;" disabled type="button" class="dcrm_basic_form__button" value="Save">
                            </div>

                        </div>

                    </div>
                </div>';
        return $content;
    }

    private function mo_dcrm_display_premium_features()
    {
        $check_icon = '<img style="width:1rem; height:1rem; vertical-align:middle;margin-right:0.3rem;" src="' . esc_url_raw(templateWrapper::mo_dcrm_get_image_src('check.png')) . '">';
    ?>
        <div class="mo_dcrm_random" style="position:relative;top:1rem;">
            <div>
                <img class="mo-dcrm-img-prem-feature" src="<?php echo esc_url_raw(templateWrapper::mo_dcrm_get_image_src('crown.png'));?>">
            </div>
            <div class="dcrm_section-main-tile" style="opacity:0.8;border: 6px solid #a3a4ff;border-radius: 4px;">
                <div class="dcrm_section-tile-nav">
                    <div class="dcrm_section-tile-nav__title">Contact Form 7 Integrations - Premium Features</div>
                </div>
                <div style="background-color:#eee; padding:10px; color:black;">
                    <p><?php echo $check_icon; ?>Support for syncing Custom Dynamics Objects</p>
                    <p><?php echo $check_icon; ?>Custom object fields support for syncing CRM fields like DateTime, Boolean, Money, Decimal, file attachments, etc.</p>
                    <p><?php echo $check_icon; ?>Assign one object to another, e.g assign a partiuclar contact object to account object.</p>
                    <p><?php echo $check_icon; ?>Option to set primary key to update already existing record of a particular dynamics object instead of creating new record each time.</p>
                    <p><?php echo $check_icon; ?>Sync form data based on the conditions/filters applied for particular mapping are matched with form submission data.</p>
                    <p>The plugin's premium version will provide you all the features you need to integrate your Dynamics 365 applications into WordPress specifically. Keep you dynamics crm data in sync with your WordPress site. Additionally, upgrading the licensed plugin will allow you to regular updates/compatibility patches for the plugin as well as team will be ready to help you with resolving issues or setup.</p>
                </div>
            </div>
        </div>
<?php }
}

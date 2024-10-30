<?php

namespace MoDynamics365ObjectSync\Observer;

use MoDynamics365ObjectSync\API\Azure;
use MoDynamics365ObjectSync\Wrappers\dbWrapper;
use MoDynamics365ObjectSync\Wrappers\dcrmWrapper;
use MoDynamics365ObjectSync\Wrappers\pluginConstants;
use MoDynamics365ObjectSync\Wrappers\templateWrapper;
use MoDynamics365ObjectSync\Wrappers\wpWrapper;

class cf7dcrmObserver{

    private static $obj;

    public static function getObserver(){
        if(!isset(self::$obj)){
            self::$obj = new cf7dcrmObserver();
        }
        return self::$obj;
    }

    public function mo_dcrm_cf7_support_api_handler(){

        if ( ! check_ajax_referer( 'mo_cf7dcrm_integrate__nonce','nonce', false ) ) {
			wp_send_json_error( array(
				'err' => 'Permission denied.',
			) );
			exit;
		}

        $task = sanitize_text_field($_POST['task']);
        $payload=wpWrapper::mo_dcrm_sanitize_array_map($_POST['payload']);
        switch ($task){
            case 'mo_dcrm_set_cf7_form_selection':{
                $this->mo_dcrm_set_cf7_form_selection($payload);
                break;
            }
            case 'mo_dcrm_set_cf7_object_selection':{
                $this->mo_dcrm_set_cf7_object_selection($payload);
                break;
            }
            case 'mo_dcrm_fetch_object_attributes':{
                $this->mo_dcrm_fetch_object_attributes($payload);
                break;
            }
            case 'mo_dcrm_fetch_form_fields':{
                $this->mo_dcrm_fetch_form_fields($payload);
                break;
            }
            case 'mo_dcrm_cf7_add_new_mapping':{
                $this->mo_dcrm_cf7_add_new_mapping($payload);
                break;
            }
            case 'mo_dcrm_cf7_save_mapping':{
                $this->mo_dcrm_cf7_save_mapping($payload);
                break;
            }
        }

    }

    public function mo_dcrm_cf7_after_form_submit( $contact_form ) {
		// to get form id.
		$form_id = $contact_form->id();
		// to get submission data $posted_data is asociative array.
		$submission = \WPCF7_Submission::get_instance();
		$form_data  = $submission->get_posted_data();

        $config = wpWrapper::mo_dcrm_get_option(pluginConstants::APP_CONFIG);

        $mappings = dbWrapper::mo_dcrm_get_cf7_object_map_table_rows_by_key('cf7_form_id',$form_id,'dynamics_object_id,field_map');

        if(empty($mappings))
            return $form_data;

        foreach($mappings as $map){
            if(!isset($map['field_map']))
                continue;

            if(empty($map['field_map']))
                continue;

            $data = [];
            $map['field_map'] = json_decode($map['field_map']);

            foreach ( $map['field_map'] as $object => $cf7field ) {
                if ( is_array( $form_data[ $cf7field ] ) && ! empty( $form_data[ $cf7field ][0] ) ) {
                    $data[ $object ] = $form_data[ $cf7field ][0];
                } else {
                    $data[ $object ] = $form_data[ $cf7field ];
                }
            }

            $apiHandler = Azure::getClient($config);
            $response = $apiHandler->mo_dcrm_add_row_for_object($map['dynamics_object_id'],$data);
        }

        return $form_data;
    }

    private function mo_dcrm_set_cf7_form_selection($payload){
        $val = $payload['val'];
        $id = $payload['id'];

        if($val == 'default')
            wp_send_json_error('Please select the form to proceed.');

        dbWrapper::mo_dcrm_update_cf7_object_map_table_row('cf7_form_id',$val,$id);
        
        wp_send_json_success('settings saved successfully');
    }

    private function mo_dcrm_set_cf7_object_selection($payload){
        $val = $payload['val'];
        $id = $payload['id'];

        if($val == 'default')
            wp_send_json_error('Please select the object to proceed.');

        dbWrapper::mo_dcrm_update_cf7_object_map_table_row('dynamics_object_id',$val,$id);
        dbWrapper::mo_dcrm_update_cf7_object_map_table_row('field_map',NULL,$id);

        wp_send_json_success('settings saved successfully');
    }

    private function mo_dcrm_fetch_object_attributes($payload){
        $LogicalName = $payload['logical_name'];
        $id = $payload['id'];

        $config = wpWrapper::mo_dcrm_get_option(pluginConstants::APP_CONFIG);
        
        $apiHandler = Azure::getClient($config);
        $response = $apiHandler->mo_dcrm_get_attributes_for_specific_object($LogicalName);

        if(!$response['status'])
            wp_send_json_error($response['data']['error_description']);

        $attrs = dcrmWrapper::mo_dcrm_process_attributes_of_entity_object($response['data']);

        dbWrapper::mo_dcrm_update_cf7_object_map_table_row('object_attrs',json_encode($attrs),$id);

        wp_send_json_success('settings saved successfully');
    }

    private function mo_dcrm_fetch_form_fields($payload){
        $cf7_form_id = $payload['val'];
        $id = $payload['id'];

        $fields = wpWrapper::mo_dcrm_get_contact_form_fields($cf7_form_id);

        dbWrapper::mo_dcrm_update_cf7_object_map_table_row('form_fields',json_encode($fields),$id);
        
        wp_send_json_success('settings saved successfully');
    }

    private function mo_dcrm_cf7_add_new_mapping($payload){
        $object_name = $payload['name'];
        $id = $payload['id'];

        $app = dbWrapper::mo_dcrm_get_cf7_object_map_table_rows_by_key('id',$id);
        if(!empty($app)){
            $app = $app[0];
        }

        $form = isset($app['cf7_form_id'])?$app['cf7_form_id']:'';
        $object = isset($app['dynamics_object_id'])?$app['dynamics_object_id']:'';
        $attrs = isset($app['object_attrs'])? json_decode($app['object_attrs'],true) :['required'=>[],'optional'=>[]];
        $fields = isset($app['form_fields'])?json_decode($app['form_fields'],true):[];
        $field_map = isset($app['field_map'])?json_decode($app['field_map'],true):[];

        $attr = $attrs['optional'][$object_name];

        $new_map_content = templateWrapper::mo_dcrm_load_field_mapping($object,$form,$fields,$attr,$field_map,0,1,1);

        wp_send_json_success($new_map_content);
    }

    private function mo_dcrm_cf7_save_mapping($payload){
        $field_map = $payload['field_map'];
        $id = $payload['id'];

        dbWrapper::mo_dcrm_update_cf7_object_map_table_row('field_map',json_encode($field_map),$id);

        wp_send_json_success('settings saved successfully');
        
    }

}
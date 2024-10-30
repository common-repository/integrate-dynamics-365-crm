<?php

namespace MoDynamics365ObjectSync\Wrappers;

use wpdb;

class dbWrapper{

    private static $instance;

    public static function getWrapper(){
        if(!isset(self::$instance)){
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public static function mo_dcrm_create_cf7_object_map_table(){
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . dbConstants::CF7_OBJECT_MAPPING_TABLE;

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            cf7_form_id varchar(200) NOT NULL,
            form_fields text NOT NULL,
            dynamics_object_id text NOT NULL,
            object_attrs longtext,
            field_map longtext, 
            PRIMARY KEY id (id),
            KEY cf7_form_id (cf7_form_id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        $status = dbDelta( $sql );
    }

    public static function mo_dcrm_update_cf7_object_map_table_row($key,$value,$id = 0){
        global $wpdb;
        $table = $wpdb->prefix.dbConstants::CF7_OBJECT_MAPPING_TABLE;

        $data = array($key => $value);
        $format = array('%s');

        $has_old_value = dbWrapper::mo_dcrm_get_cf7_object_map_table_row_id($id);

        if($has_old_value){
            $status = $wpdb->update( $table, $data, array('id'=>$has_old_value), $format );
        }else{
            $status = $wpdb->insert($table,$data,$format);
        }
    }

    public static function mo_dcrm_table_exists($table){
        global $wpdb;
        $result=$wpdb->get_results($wpdb->prepare("SHOW TABLES LIKE %s",$table),ARRAY_A);
        return !empty($result);
    }

    public static function mo_dcrm_get_new_id(){
        global $wpdb;
        $table= $wpdb->prefix.dbConstants::CF7_OBJECT_MAPPING_TABLE;

        if(!dbWrapper::mo_dcrm_table_exists($table))
            return 0;

        $db=$wpdb->dbname;
        $query = "SELECT id FROM $table ORDER BY id DESC LIMIT %d";
        $id = $wpdb->get_var($wpdb->prepare($query,1));
        $id = $id ? $id : 0;
        $query = "ALTER TABLE $table AUTO_INCREMENT = %d";
        $result = $wpdb->get_results($wpdb->prepare($query,$id), ARRAY_A);
            
        return $id+1;
    }

    public static function mo_dcrm_delete_row($id){
        global $wpdb;
        $wpdb->delete($wpdb->prefix.dbConstants::CF7_OBJECT_MAPPING_TABLE, array( 'id' => $id ));
        return;
    }

    public static function mo_dcrm_get_cf7_object_map_table_row_id($id){
        global $wpdb;
        $table = $wpdb->prefix.dbConstants::CF7_OBJECT_MAPPING_TABLE;
        $query = "SELECT id FROM $table where id = %d";
        $result = $wpdb->get_results( $wpdb->prepare($query,$id));
        if(empty($result))
            return false;

        return $result[0]->id;

    }

    public static function mo_dcrm_get_cf7_object_map_table_row($id){
        global $wpdb;
        $table = $wpdb->prefix.dbConstants::CF7_OBJECT_MAPPING_TABLE;
        $query = "SELECT * FROM $table where id = %d";
        $result = $wpdb->get_results($wpdb->prepare($query,$id), ARRAY_A );
        
        if(empty($result))
            return [];

        return $result[0];

    }

    public static function mo_dcrm_get_cf7_object_map_table_rows_by_key($key,$id,$select = '*'){
        global $wpdb;
        $table = $wpdb->prefix.dbConstants::CF7_OBJECT_MAPPING_TABLE;
        $query = "SELECT $select FROM $table where $key = %d";
        $result = $wpdb->get_results($wpdb->prepare($query,$id), ARRAY_A );
        if(empty($result))
            return [];

        return $result;
    }

     public static function mo_dcrm_get_all_cf7_object_entity_config(){
        global $wpdb;
        $table=$wpdb->prefix.dbConstants::CF7_OBJECT_MAPPING_TABLE;

        if(!dbWrapper::mo_dcrm_table_exists($table))
            return [];

        $result= $wpdb->get_results("SELECT id, cf7_form_id, dynamics_object_id, field_map from $table");
        return $result;
     }
}
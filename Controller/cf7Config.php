<?php

namespace MoDynamics365ObjectSync\Controller;

use MoDynamics365ObjectSync\Wrappers\dbWrapper;
use MoDynamics365ObjectSync\Wrappers\pluginConstants;
use MoDynamics365ObjectSync\Wrappers\wpWrapper;

class cf7Config{

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
            case 'mo_dcrm_delete_row':{
                $this->mo_dcrm_delete_row();
                break;
            }
        }
    }
    public function mo_dcrm_delete_row(){
        check_admin_referer('mo_dcrm_delete_row');
        $id=sanitize_text_field($_POST['id']);
        dbWrapper::mo_dcrm_delete_row($id);
    }

}
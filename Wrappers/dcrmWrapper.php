<?php

namespace MoDynamics365ObjectSync\Wrappers;

class dcrmWrapper{

    private static $instance;

    public static function getWrapper(){
        if(!isset(self::$instance)){
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public static function mo_dcrm_process_entity_objects($rawEntityObjects){
        $res = [];

        if(!isset($rawEntityObjects['value']))
            return [];

        foreach($rawEntityObjects['value'] as $object){
            if(( ($object['IsCustomEntity'] == false && $object['IsActivity'] == false && $object['IsChildEntity'] == false && $object['IsAvailableOffline'] == true )) && (isset($object['DisplayCollectionName']['UserLocalizedLabel']['Label']))){
                $label=$object['DisplayCollectionName']['UserLocalizedLabel']['Label'];
                $name = $object['LogicalCollectionName'];
                $logicalName = $object['LogicalName'];
                $key=$name;
                if(empty($name)){
                    $key=$label;
                }
                $res[$key]=['name'=>$name,'label'=>$label,'logical_name'=>$logicalName];
            }
        }

        return $res;
    }

    public static function mo_dcrm_process_attributes_of_entity_object($attributes){
        $res = ['required'=>[],'optional'=>[]];

        if(!isset($attributes['value']))
            return [];

        $filter_out = ['accountid','statecode','ownerid','customerid','customeridtype','pricelevelid','ispricelocked','msdyn_accountmanagerid'];
        $filter_out_required_fields = ['msdyn_psastatusreason','msdyn_psastate','msdyn_contractorganizationalunitid','msdyn_ordertype'];
        foreach($attributes['value'] as $attribute){
            if(isset($attribute['IsValidForUpdate']) && $attribute['IsValidForUpdate'] == true && !empty($attribute['DisplayName']['UserLocalizedLabel']['Label']) && !in_array($attribute['LogicalName'],$filter_out)){

                $name = $attribute['LogicalName'];
                $type = $attribute['AttributeType'];
                $label = $attribute['DisplayName']['UserLocalizedLabel']['Label'];
                $required = $attribute['RequiredLevel']['Value'] !== 'None'?1:0;

                if(!$required){
                    $res['optional'][$name]['name'] = $name;
                    $res['optional'][$name]['type'] = $type;
                    $res['optional'][$name]['label'] = $label;
                }else if(!in_array($name,$filter_out_required_fields)){
                    $res['required'][$name]['name'] = $name;
                    $res['required'][$name]['type'] = $type;
                    $res['required'][$name]['label'] = $label;
                }

            }
                
        }

        return $res;
    }

}
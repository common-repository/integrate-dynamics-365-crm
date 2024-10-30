<?php

namespace MoDynamics365ObjectSync\API;

class Azure{

    private static $obj;
    private $endpoints;
    private $config;
    private $scope = "https://graph.microsoft.com/.default";
    private $access_token;
    private $handler;
    private $args;

    private function __construct($config){
        $this->config = $config;
        $this->handler = Authorization::getController();
    }

    public static function getClient($config){
        if(!isset(self::$obj)){
            self::$obj = new Azure($config);
            self::$obj->setEndpoints();
        }
        return self::$obj;
    }

    private function setEndpoints(){
        $dcrm_api_endpoint = trailingslashit($this->config['dcrm_org_endpoint']).'api/data/v9.0/';
        if(isset($this->config['tenant_id']))
            $this->endpoints['dcrm_token'] = 'https://login.windows.net/'.$this->config['tenant_id'].'/oauth2/token';

        $this->endpoints['dcrm_api_endpoint'] = $dcrm_api_endpoint;
        $this->endpoints['dcrm_common_token'] =  'https://login.microsoftonline.com/common/oauth2/token';
        $this->endpoints['contacts'] = $dcrm_api_endpoint.'contacts/';
        $this->endpoints['EntityDefinitions'] = $dcrm_api_endpoint.'EntityDefinitions';
    }

    public function mo_dcrm_get_new_access_token(){

        $app_type = $this->config['app_type'];

        if($app_type == 'manual')
            $response = $this->handler->mo_dcrm_get_access_token_using_client_credentials($this->endpoints,$this->config,null);
        else
            $response = $this->handler->mo_dcrm_get_access_token_using_authorization_code($this->endpoints,$this->config,$this->scope);

        $this->access_token = $response;

        if($response['status']){
            $this->args = [
                'Authorization' => 'Bearer '.$this->access_token['data'],
            ];

            return $this->access_token['data'];
        }

        return false;
    }

    public function mo_dcrm_get_contacts_to_test_connection(){

        $access_token = $this->mo_dcrm_get_new_access_token();
        if(!$access_token){
            return $this->access_token;
        }

        $this->args = [
            'Authorization' => 'Bearer '.$access_token,
            'Prefer' => 'odata.maxpagesize=3'
        ];

        $contacts = $this->handler->mo_dcrm_get_request($this->endpoints['contacts'],$this->args);

        return $contacts;
    }

    public function mo_dcrm_get_all_entity_definations(){
        $access_token = $this->mo_dcrm_get_new_access_token();
        if(!$access_token){
            return $this->access_token;
        }

        $this->args = [
            'Authorization' => 'Bearer '.$access_token,
        ];

        $select = 'LogicalName,IsChildEntity,IsActivityParty,IsActivity,IsAIRUpdated,IsCustomizable,IsRenameable,IsAvailableOffline,IsManaged,IsPrivate,IsRenameable,IsLogicalEntity,IsCustomEntity,CanCreateForms,CanCreateAttributes,CanBeRelatedEntityInRelationship,IsCustomizable,DisplayCollectionName,LogicalCollectionName';
        $entityDefinitions = $this->handler->mo_dcrm_get_request($this->endpoints['EntityDefinitions'].'/?$select='.$select,$this->args);

        return $entityDefinitions;
    }

    public function mo_dcrm_get_attributes_for_specific_object($logicalname){
        $access_token = $this->mo_dcrm_get_new_access_token();
        if(!$access_token){
            return $this->access_token;
        }

        $this->args = [
            'Authorization' => 'Bearer '.$access_token,
        ];

        $entityAttributes = $this->handler->mo_dcrm_get_request($this->endpoints['EntityDefinitions']."(LogicalName='".$logicalname."')/Attributes",$this->args);

        return $entityAttributes;
    }

    public function mo_dcrm_add_row_for_object($object_name,$data){
        $access_token = $this->mo_dcrm_get_new_access_token();
        if(!$access_token){
            return $this->access_token;
        }

        $this->args = [
            'Authorization' => 'Bearer '.$access_token,
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept'=> 'application/json',
            'Prefer'=>'return=representation'
        ];

        $res = $this->handler->mo_dcrm_post_request($this->endpoints['dcrm_api_endpoint'].$object_name,$this->args,json_encode($data));

        return $res;

    }

}
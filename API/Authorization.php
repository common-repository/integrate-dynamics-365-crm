<?php

namespace MoDynamics365ObjectSync\API;

use MoDynamics365ObjectSync\Wrappers\pluginConstants;
use MoDynamics365ObjectSync\Wrappers\wpWrapper;

class Authorization{
    private static $instance;

    public static function getController(){
        if(!isset(self::$instance)){
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public function mo_dcrm_get_access_token_using_client_credentials($endpoints,$config,$scope){
        $client_secret = wpWrapper::mo_dcrm_decrypt_data($config['client_secret'],hash("sha256",$config['client_id']));
        $dcrm_org_endpoint = $config['dcrm_org_endpoint'];

        $args =  [
            'body' => [
                'grant_type' => 'client_credentials',
                'client_secret' => $client_secret,
                'client_id' => $config['client_id'],
                'resource' => $dcrm_org_endpoint
            ],
            'headers' => [
                'Content-type' => 'application/x-www-form-urlencoded'
            ]
        ];


        $response = wp_remote_post(esc_url_raw($endpoints['dcrm_token']),$args);
        if ( is_wp_error( $response ) ) {
            return ['status'=>false,'data'=>['error'=>'Request timeout','error_description'=>'Unexpected error occurred! Please check your internet connection and try again.']];
            $error_message = $response->get_error_message();
            wp_die("Error Occurred : ".esc_html($error_message));
        } else {
            $body= json_decode($response["body"],true);
            if(isset($body["access_token"])){
                return ['status'=>true,'data'=>$body["access_token"]];
            }else if(isset($body['error'])){
                 return ['status'=>false,'data'=>$body];
            }
        }
        return ['status'=>false,'data'=>['error'=>'Unexpected Error','error_description'=>'Check your configurations once again']];
    }

    public function mo_dcrm_get_access_token_using_authorization_code($endpoints,$config,$scope){

        $dcrm_org_endpoint = $config['dcrm_org_endpoint'];
        
        $mo_client_id = (pluginConstants::CID);
        $mo_client_secret = (pluginConstants::CSEC);
        $server_url = (pluginConstants::CONNECT_SERVER_URI);

        $refresh_token = wpWrapper::mo_dcrm_get_option(pluginConstants::DCRM_RFTK);


        if(empty($refresh_token)){
            $code = wpWrapper::mo_dcrm_get_option(pluginConstants::DCRMAUTHCODE);

            $args =  [
                'body' => [
                    'grant_type' => 'authorization_code',
                    'client_secret' => $mo_client_secret,
                    'client_id' => $mo_client_id,
                    'scope' => $scope." offline_access",
                    'code'=> $code,
                    'resource' => $dcrm_org_endpoint,
                    'redirect_uri' => $server_url
                ],
                'headers' => [
                    'Content-type' => 'application/x-www-form-urlencoded'
                ]
            ];
        }else{
            $args =  [
                'body' => [
                    'grant_type' => 'refresh_token',
                    'client_secret' => $mo_client_secret,
                    'client_id' => $mo_client_id,
                    'scope' => $scope." offline_access",
                    'resource' => $dcrm_org_endpoint,
                    'refresh_token'=>$refresh_token,
                ],
                'headers' => [
                    'Content-type' => 'application/x-www-form-urlencoded'
                ]
            ];
        }

        $response = wp_remote_post(esc_url_raw($endpoints['dcrm_common_token']),$args);


        if ( is_wp_error( $response ) ) {
            return ['status'=>false,'data'=>['error'=>'Request timeout','error_description'=>'Unexpected error occurred! Please check your internet connection and try again.']];
            $error_message = $response->get_error_message();
            wp_die("Error Occurred : ".esc_html($error_message));
        } else {
            $body= json_decode($response["body"],true);

            if(isset($body['refresh_token'])){
                wpWrapper::mo_dcrm_set_option(pluginConstants::DCRM_RFTK,$body['refresh_token']);
            }
            if(isset($body["access_token"])){
                return ['status'=>true,'data'=>$body["access_token"]];
            }else if(isset($body['error'])){
                 return ['status'=>false,'data'=>$body];
            }
        }

        return ['status'=>false,'data'=>['error'=>'Unexpected Error','error_description'=>'Check your configurations once again']];
    }

    public function mo_dcrm_get_request($url,$headers){
        $args = [
            'headers' => $headers
        ];
        $response = wp_remote_get(esc_url_raw($url),$args);
        if ( is_array( $response ) && ! is_wp_error( $response ) ) {
            $body = json_decode($response["body"],true);

            if(empty($body))
                return ['status'=>false,'data'=>['error'=>'Unauthorized','error_description'=>'Unexpected error occured']];
            else if(isset($body['error']))
                return ['status'=>false,'data'=>['error'=>$body['error']['code'],'error_description'=>$body['error']['message']]];

            return ['status'=>true,'data'=>$body];
        } else {
            return ['status'=>false,'data'=>['error'=>'Request timeout','error_description'=>'Unexpected error occurred! Please check your internet connection and try again.']];
            wp_die("Error occurred: ".esc_html($response->get_error_message()));
        }
    }

    public function mo_dcrm_post_request($url,$headers,$body){
        $args = [
            'body' => $body,
            'headers' => $headers
        ];
        $response = wp_remote_post(esc_url_raw($url),$args);
        if ( is_array( $response ) && ! is_wp_error( $response ) ) {
            $body = json_decode($response["body"],true);

            if(empty($body))
                return ['status'=>false,'data'=>['error'=>'Unauthorized','error_description'=>'Unexpected error occured']];
            else if(isset($body['error']))
                return ['status'=>false,'data'=>['error'=>$body['error']['code'],'error_description'=>$body['error']['message']]];

            return ['status'=>true,'data'=>$body];
        } else {
            return ['status'=>false,'data'=>['error'=>'Request timeout','error_description'=>'Unexpected error occurred! Please check your internet connection and try again.']];
            wp_die("Error occurred: ".esc_html($response->get_error_message()));
        }
        
    }
}
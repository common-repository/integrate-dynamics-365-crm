<?php


namespace MoDynamics365ObjectSync\View;

use MoDynamics365ObjectSync\View\supportForm;
use MoDynamics365ObjectSync\Wrappers\pluginConstants;
use MoDynamics365ObjectSync\Wrappers\templateConstants;
use MoDynamics365ObjectSync\Wrappers\templateWrapper;

class adminView{

    private static $instance;

    public static function getView(){
        if(!isset(self::$instance)){
            $class = __CLASS__;
            self::$instance = new $class;
        }

        return self::$instance;
    }

    public function mo_dcrm_menu_display(){
        if( isset( $_GET[ 'tab' ] ) ) {
            $active_tab = sanitize_text_field($_GET['tab']);
        }else{
            $active_tab = 'app_config';
        }
        $this->mo_dcrm_display_tabs($active_tab);
    }

    private function mo_dcrm_display_tabs($active_tab){
        $supportFormHandler= supportForm::getView();
        ?>

            <div class="dcrm_section-header">
                <?php 
                    $this->mo_dcrm_display__header_menu('miniorange.png',templateConstants::header__title);
                    $this->mo_dcrm_display__tabs($active_tab,templateConstants::tab__details);
                ?>
            </div>
            

            <div class="dcrm_container">
                <div class="dcrm_section-main">
                    <?php
                        $this->mo_dcrm_display__tab_content($active_tab);
                    ?>
                </div>
                <div class="dcrm_section-sidebar">
                <?php
                    $supportFormHandler->mo_dcrm_display_support_form();
                ?>
                </div>
                <div class="dcrm_section-footer"></div>
            </div>

        <?php

    }

    private function mo_dcrm_display__header_menu($imageName,$title){
       ?>
        <div class="dcrm_section-header_menu">
            <img class="dcrm_section-header__image" src="<?php echo esc_url_raw(templateWrapper::mo_dcrm_get_image_src($imageName));?>">
            <h1><label style="cursor:auto"><?php echo esc_html($title)?></label></h1>
            <h2 style="padding:0.4rem;margin-left:10px;border-left:3px solid #323d87;cursor:pointer;color:#1565C0;font-weight:bold;">
                  <a target="_blank" class="nonlink" href="<?php echo esc_url_raw(pluginConstants::SETUP_GUIDE_LINK);?>"><span class="dashicons dashicons-info-outline"></span>  <u>Documentation</u></a>
            </h2>
        </div>
        <?php
    }

    private function mo_dcrm_display__tabs($active_tab,$tab_details){
        ?>

        <div class="dcrm_section-header__tabs_container">
            <?php
                foreach($tab_details as $item){
                    ?>
                        <a href="<?php echo esc_url_raw(add_query_arg('tab',$item['tabName']));?>" class="<?php echo $active_tab == $item['tabName']?'dcrm_section-header__tab-active':'dcrm_section-header__tab'; ?>">
                            <img class="dcrm_section-header__tab_image" src="<?php echo esc_url_raw(templateWrapper::mo_dcrm_get_image_src($item['imageSrc']));?>">
                            <div ><?php echo esc_html($item['tabLabel']);?></div>
                        </a>
                    <?php
                }

            ?>            
        </div>
        <?php
    }

    private function mo_dcrm_display__tab_content($active_tab){
        $handler = self::getView();
        switch ($active_tab){
            case 'app_config':{
                $handler = appConfig::getView();
                break;
            }
            case 'cf7_config':{
                $handler = cf7Config::getView();
                break;
            }
            case 'data_visulization':{
                $handler = dataVisualization::getView();
                break;
            }
        }
        $handler->mo_dcrm_display__tab_details();
    }

    private function mo_dcrm_display__tab_details(){
       esc_html_e("Class missing. Please check if you've installed the plugin correctly.");
    }
}
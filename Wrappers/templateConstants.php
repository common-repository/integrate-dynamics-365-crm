<?php

namespace MoDynamics365ObjectSync\Wrappers;

class templateConstants{
    const header__title = 'Dynamics 365 CRM Integration';
    const tab__details = [
        ['tabName'=>'app_config','tabLabel'=>'Manage Application','imageSrc'=>'dynamics-crm.svg'],
        ['tabName'=>'cf7_config','tabLabel'=>'Contact Form 7','imageSrc'=>'cf7.svg'],
        ['tabName'=>'data_visulization','tabLabel'=>'Data Visulization','imageSrc'=>'shortcode.svg'],
    ];

    const app_config__tiles = [
        ['index'=>'1','title'=>'Enter Dynamics 365 Online URL','content'=>'mo_dcrm_online_url_settings','description'=>''],
        ['index'=>'2','title'=>'Select an application type','content'=>'mo_dcrm_display_application_types','description'=>''],
        ['index'=>'3','title'=>'Setup Connection','content'=>'mo_dcrm_app_setup_connection','description'=>''],
        ['index'=>'4','title'=>'Test / Manage Connection','content'=>'mo_dcrm_app_test_connection','description'=>''],
    ];

    const cf7_config__tiles = [
        ['index'=>'1','title'=>'Select Contact 7 Form','content'=>'mo_dcrm_select_cf7_form','advert'=>false,'description'=>'Select any cf7 form from the list to map with available dynamics objects.'],
        ['index'=>'2','title'=>'Select Dynamics Entity Object','content'=>'mo_dcrm_select_cf7_dynamics_object','advert'=>false,'description'=>'Select dynamics object for which you want to send the selected form data.'],
        ['index'=>'3','title'=>'Configure Mapping','content'=>'mo_dcrm_select_cf7_dynamics_object_mapping','advert'=>false,'description'=>'Add mapping between selected dynamics object fields and form fields. After form is submitted successfully from the WordPress page, data will be synced to the dynamics CRM based on the mapping which you have configured here.'],
        ['index'=>'4','title'=>'Choose Primary Key','content'=>'mo_dcrm_choose_primary_key','advert'=>true,'description'=>'A primary key can be set as an object field for a above selected dynamics object. This can be used to update the record on Dynamics CRM when the primary key value matches, rather than creating a new record each time.'],
        ['index'=>'5','title'=>'Conditional sync of objects','content'=>'mo_dcrm_restriction_on_dynamics_entry','advert'=>true,'description'=>'You can add multiple conditions that will determine whether or not the data from the submitted form should be synchronised to Dynamics 365.'],
    ];

    const data_visualization__tiles = [
        ['index'=>'1','title'=>'Generate Shortcode','content'=>'mo_dcrm_generate_shortcode','advert'=>true,'description'=>'Select dynamics object to generate a shortcode for embedding table view in pages/post.'],
        ['index'=>'2','title'=>'Advance Settings','content'=>'mo_dcrm_advance_settings','advert'=>true,'description'=>'Select objects fields as columns of table view for above selected dynamics object. Furthermore, you can filter some rows or records based on the conditions provided. For example, you can specify the condition to see only the contacts that are having First Name starting with A letter.'],
        ['index'=>'3','title'=>'Copy Shortcode','content'=>'mo_dcrm_copy_shortcode','advert'=>true,'description'=>'Use this shortcode to embed any dynamics object into pages/posts.'],
    ];

}
(function($){

    $(document).on('click','.dcrm_section-tile-nav',function(e){
        let suffix = e.target.getAttribute('dynamicID');
        $(`#dcrm_section-tile-nav__icon_up${suffix}`).toggle();
        $(`#dcrm_section-tile-nav__icon_down${suffix}`).toggle();
        $(`#dcrm_section-tile-content${suffix}`).slideToggle();
    });

    $(document).on('click','.dcrm_object_form_map_nav',function(e){
        let suffix = e.target.getAttribute('dynamicID');
        $(`#dcrm_object_form_map_nav__icon_up${suffix}`).toggle();
        $(`#dcrm_object_form_map_nav__icon_down${suffix}`).toggle();
        $(`#dcrm_object_form_map-content${suffix}`).slideToggle();
    });

    $(document).on('click','#dcrm_object_form_map_nav__icon_delete',function(e){
        let suffix = e.target.getAttribute('dynamicID');
        $(`#dcrm_object_form_map-tile${suffix}`).remove();
    });

    $(document).on('hover','.img-prem',function(e){
        $(`.mo_dynamic_prem_text`).style.display = block;
    });


    // $(document).on('click','input[name="app_type"]',function(e){
    //     let checked = e.target.value;
    //     if(checked == 'auto'){
    //         $('#mo_dcrm_setup_connection_controller_auto').show();
    //         $('#mo_dcrm_setup_connection_controller_manual').hide();
    //     }else{
    //         $('#mo_dcrm_setup_connection_controller_auto').hide();
    //         $('#mo_dcrm_setup_connection_controller_manual').show();
    //     }   
    // });

})(jQuery);

function show_green_tick_if_test_connection_successful(){
    document.getElementById('green_tick_app_config_4').style.display = "block";
}

function hide_green_tick_if_test_connection_successful(){
    document.getElementById('green_tick_app_config_4').style.display = "none";
}

function reload_page_to_see_reflected_changes(){
    window.location.reload();
}
(function($){

    eventHandlers();

    function eventHandlers(){


        $(document).on('click',"#mo_dcrm_select_cf7_form_controller_saveButton",function(e){
            e.preventDefault();
            val = $('#dcrm_cf7_form_options option:selected').val();
            mocf7dcrmHandleBackendCalls('mo_dcrm_set_cf7_form_selection',{'val':val,'id':cf7dcrmConfigs.id}).then((res)=>{
                // console.log(res);
                if(res.success){
                    mocf7dcrmHandleBackendCalls('mo_dcrm_fetch_form_fields',{'val':val,'id':cf7dcrmConfigs.id},'mo_dcrm_cf7_config_mapping_loader').then((res)=>{
                        // console.log(res);
                        window.location.reload();

                    });
                }else{
                    $('#cf7_config_notices').slideDown();
                    $('#cf7_config_notices').text(res.data);

                }
            });
        });

        $(document).on('click',"#mo_dcrm_select_cf_dynamics_object_controller_saveButton",function(e){
            e.preventDefault();
            let val = $('#dcrm_cf7_dynamics_object option:selected').val();
            let logical_name = $('#dcrm_cf7_dynamics_object option:selected').attr('logical_name');
            mocf7dcrmHandleBackendCalls('mo_dcrm_set_cf7_object_selection',{'val':val,'id':cf7dcrmConfigs.id}).then((res)=>{

                if(res.success){
                    mocf7dcrmHandleBackendCalls('mo_dcrm_fetch_object_attributes',{'logical_name':logical_name,'id':cf7dcrmConfigs.id},'mo_dcrm_cf7_config_mapping_loader').then((res)=>{
                        // console.log(res);

                        if(res.success){
                            window.location.reload();
                        }else{
                            $('#cf7_config_notices').slideDown();
                            $('#cf7_config_notices').text(res.data);
                        }


                    });
                }else{
                    $('#cf7_config_notices').slideDown();
                    $('#cf7_config_notices').text(res.data);

                }


            });
        });

        $(document).on('click',"#dcrm_cf7_add_new_mapping_button",function(e){
            e.preventDefault();
            let to_add = $('#mo_dcrm_object_form_mapping_deault_select option:selected').val();

            // console.log(to_add);
            
            let added = Array.from($('.dcrm_object_form_map_nav__title').map(function(index,item){return item.getAttribute('attributeName')}));
            // console.log(added);
            
            if(added.includes(to_add))
                return;

            mocf7dcrmHandleBackendCalls('mo_dcrm_cf7_add_new_mapping',{'name':to_add,'id':cf7dcrmConfigs.id},'mo_dcrm_cf7_config_new_mapping_loader').then((res)=>{
                // console.log(res);

                if(res.success)
                    $('#mo_dcrm_cf7_mapping_track_end').before(res.data);

            });


        });

        $(document).on('click','#dcrm_cf7_save_new_mapping_button',function(e){
            e.preventDefault();
            let fields_name_to_be_saved = Array.from($('.dcrm_object_form_map_nav__title').map(function(index,item){return item.getAttribute('attributeName')}));
            let app = cf7dcrmConfigs.app;
            let cf7_form_id = app.cf7_form_id;
            let dynamics_object_id = app.dynamics_object_id;

            let field_map = {};

            fields_name_to_be_saved.forEach(field => {
                let form_field = $(`#mo_dcrm_${dynamics_object_id}_${cf7_form_id}_${field}_dropdown option:selected`).val();
                field_map[field] = form_field; 
            });


            mocf7dcrmHandleBackendCalls('mo_dcrm_cf7_save_mapping',{'field_map':field_map,'id':cf7dcrmConfigs.id},'mo_dcrm_cf7_config_new_mapping_loader').then((res)=>{
                // console.log(res);
                window.location.reload();
            });


        });

    }


  



    function mocf7dcrmHandleBackendCalls(task,payload,loader_id=undefined){

        return $.ajax({
            url: `${cf7dcrmConfigs.ajax_url}?action=mo_cf7dcrm_integrate&nonce=${cf7dcrmConfigs.nonce}`,
            type: "POST",
            data: {
                task,
                payload
            },
            cache:false,
            beforeSend:function(){
                if(loader_id)
                    $(`#${loader_id}`).show();
            },
            success: function(data){
                setTimeout(() => {
                    if(loader_id)
                        $(`#${loader_id}`).hide();
                }, 0);
                return data;
            },
        });

    }


})(jQuery);
<?php

namespace MoDynamics365ObjectSync\View;

class feedbackForm{

    private static $instance;

    public static function getView(){
        if(!isset(self::$instance)){
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public function mo_dcrm_display_feedback_form(){

        if ( 'plugins.php' != basename(sanitize_text_field( $_SERVER['PHP_SELF']))) {
            return;
        }

        wp_enqueue_style( 'mo_dcrm_css_plugin', plugins_url('../includes/css/mo_dcrm_feedback.css', __FILE__) );

        ?>

        <div id="dcrm_feedback_modal" class="mo_modal" style="width:90%;margin-left:12%; margin-top:5%; text-align:center;">
            <div class="mo_modal-content" style="width:40%;padding:5px;">
                <h3 style="margin: 2%; text-align:center;"><b><?php esc_html_e('Your feedback','Integrate Dynamics 365 CRM plugin');?></b><span class="mo_close" style="cursor: pointer">&times;</span>
                </h3>
                <hr style="width:75%;">
                <form name="f" method="post" action="" id="mo_feedback">
                    <?php wp_nonce_field("mo_dcrm_feedback");?>
                    <input type="hidden" name="option" value="mo_dcrm_feedback"/>
                    <div>
                        <p style="margin:2%">
                        <h4 style="margin: 2%; text-align:center;"><?php esc_html_e('Please help us to improve our plugin by giving your opinion.','Integrate Dynamics 365 CRM plugin');?><br></h4>
                        
                        <div id="smi_rate" style="text-align:center">
                            <div style="text-align: left;padding:2% 20%;">
                                <input type="checkbox" name="dcrm_reason[]" value="Missing Features" id="dcrm_feature"/>
                                <label for="dcrm_feature" class="mo_dcrm_feedback_option" > Does not have the features I'm looking for</label>
                                <br>

                                <input type="checkbox" name="dcrm_reason[]" value="Costly" id="dcrm_costly" class="mo_dcrm_feedback_radio" />
                                <label for="dcrm_costly" class="mo_dcrm_feedback_option">Do not want to upgrade - Too costly</label>
                                <br>

                                <input type="checkbox" name="dcrm_reason[]" value="Confusing" id="dcrm_confusing" class="mo_dcrm_feedback_radio"/>
                                <label for="dcrm_confusing" class="mo_dcrm_feedback_option">Confusing Interface</label>
                                <br>

                                <input type="checkbox" name="dcrm_reason[]" value="Bugs" id="dcrm_bugs" class="mo_dcrm_feedback_radio"/>
                                <label for="dcrm_bugs" class="mo_dcrm_feedback_option">Bugs in the plugin</label>
                                <br>

                                <input type="checkbox" name="dcrm_reason[]" value="other" id="dcrm_other" class="mo_dcrm_feedback_radio"/>
                                <label for="dcrm_other" class="mo_dcrm_feedback_option">Other Reasons</label>
                            </div>
                        </div>
                        
                        <hr style="width:75%;">
                        <?php $email = get_option("mo_dcrm_admin_email");
                            if(empty($email)){
                                $user = wp_get_current_user();
                                $email = $user->user_email;
                            }
                            ?>
                        <div style="display:inline-block; width:60%;">
                            <input type="email" id="query_mail" name="query_mail" style="text-align:center; border:0px solid black; border-style:solid; background:#f0f3f7; width:20vw;border-radius: 6px;"
                                placeholder="<?php esc_html_e('Please enter your email address','Integrate Dynamics 365 CRM plugin');?>" required value="<?php echo esc_html($email); ?>" readonly="readonly"/>
                            
                            <input type="radio" name="edit" id="edit" onclick="editName()" value=""/>
                            <label for="edit"><img class="editable" src="<?php echo esc_url_raw(plugin_dir_url( __FILE__ )) . '../images/61456.png'; ?>" />
                            </label>
                            
                            </div>
                            
                        <div style="text-align:center;">    
                            <input type="checkbox" name="get_reply" value="reply" checked><?php esc_html_e('miniOrange representative will reach out to you at the email-address entered above.','Integrate Dynamics 365 CRM plugin');?></input>
                        </div>
                        <br>
                        
                        <div style="text-align:center;">
                            
                            <textarea id="query_feedback" name="query_feedback" rows="4" style="width: 60%"
                                placeholder="<?php esc_html_e('Tell us what happened!','Integrate Dynamics 365 CRM plugin');?>"></textarea>
                            <br><br>
                        </div>
                        <div class="mo-modal-footer" style="text-align: center;margin-bottom: 2%">
                            <input type="submit" name="miniorange_feedback_submit"
                                class="button button-primary button-large" value="<?php esc_html_e('Send','Integrate Dynamics 365 CRM plugin');?>"/>
                            <span width="30%">&nbsp;&nbsp;</span>
                            <input type="submit" name="miniorange_skip_feedback"
                                class="button button-primary button-large" value="<?php esc_html_e('Skip','Integrate Dynamics 365 CRM plugin');?>" onclick="document.getElementById('mo_feedback').submit();"/>
                        </div>
                    </div>
                </form>


            </div>

        </div>

        <script>
            jQuery('a[aria-label="Deactivate Integrate Dynamics 365 CRM"]').click(function () {

                var mo_modal = document.getElementById('dcrm_feedback_modal');

                var span = document.getElementsByClassName("mo_close")[0];

                mo_modal.style.display = "block";
                document.querySelector("#query_feedback").focus();
                span.onclick = function () {
                    mo_modal.style.display = "none";
                    jQuery('#mo_feedback_form_close').submit();
                };

                window.onclick = function (event) {
                    if (event.target === mo_modal) {
                        mo_modal.style.display = "none";
                    }
                };
                return false;

            });

            function editName(){

                document.querySelector('#query_mail').removeAttribute('readonly');
                document.querySelector('#query_mail').focus();
                return false;

            }
            
        </script><?php

    } 
}
<?php

namespace MoDynamics365ObjectSync\View;

use MoDynamics365ObjectSync\Wrappers\pluginConstants;

class supportForm{

    private static $instance;

    public static function getView(){
        if(!isset(self::$instance)){
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public function mo_dcrm_display_support_form(){
    ?>
        <style>

            .support_container{ 
                display: flex;
                justify-content: flex-start;
                align-items: center;
                margin: 72px 0px;
                flex-direction: column;
                background-color: #D7DBF6;
                padding: 15px 10px;
            }

        </style>


        <div style="width: 95%">
            <form method="post" action="">
                <input type="hidden" name="option" value="mo_dcrm_contact_us_query_option" />
                <div class="support_container" style="border-radius: 5px;" >
			        <?php  wp_nonce_field('mo_dcrm_contact_us_query_option'); ?>
                    <div style="color:#323d87; font-weight:bold; font-size:23px; padding: 15px 0px 0px 0px ">Contact Us</div>
                    
                    <div style="color:#323d87; font-weight:600; margin: 7px;" > Any question or remarks? Just write us a message!</div>
                    <div style="display:flex;justify-content:flex-start;align-items:center;width:90%;margin-top:14px;font-size:14px;font-weight:500;color:#323d87;">Name:</div>
                    <input style="padding:5px 10px;width:91%;border:none;margin-top:4px;background-color:#fff" type="text" required name="mo_dcrm_contact_us_name" placeholder="Name">
                    <div style="display:flex;justify-content:flex-start;align-items:center;width:90%;margin-top:14px;font-size:14px;font-weight:500;color:#323d87;">Email:</div>
                    <input style="padding:5px 10px;width:91%;border:none;margin-top:4px;background-color:#fff" type="email" required name="mo_dcrm_contact_us_email" value="<?php echo ( esc_html(get_option( 'mo_dcrm_admin_email' )) == '' ) ? esc_html(get_option( 'admin_email' )) : esc_html(get_option( 'mo_dcrm_admin_email' )); ?>" placeholder="Email">
                    <div style="display:flex;justify-content:flex-start;align-items:center;width:90%;margin-top:14px;font-size:14px;font-weight:500;color:#323d87;">Subject:</div>
                    <input style="padding:5px 10px;width:91%;border:none;margin-top:4px;background-color:#fff" type="text" required name="mo_dcrm_contact_us_subject" placeholder="Subject">
                    <div style="display:flex;justify-content:flex-start;align-items:center;width:90%;margin-top:14px;font-size:14px;font-weight:500;color:#323d87;">How can we help you?</div>
                    <textarea style="padding:5px 10px;width:91%;height:80px;border:none;margin-top:5px;background-color:#fff" onkeypress="mo_dcrm_valid_query(this)" onkeyup="mo_dcrm_valid_query(this)" onblur="mo_dcrm_valid_query(this)" required name="mo_dcrm_contact_us_query" rows="2" style="resize: vertical;" placeholder="You will get reply via email"></textarea>
                    <div style="text-align:center; margin:20px 20px 4px">
                        <input type="submit" value="Send a Message" name="submit" style="padding:5px; border-radius:7px; border-color:#323d87; background:#323d87; width:150px;margin:8px;" class="button button-primary button-large"/>
                    </div>
                </div>
            </form>
        </div>

        <script>
            function mo_dcrm_valid_query(f) {
            !(/^[a-zA-Z?,.\(\)\/@ 0-9]*$/).test(f.value) ? f.value = f.value.replace(
                /[^a-zA-Z?,.\(\)\/@ 0-9]/, '') : null;
            }

        </script>
    <?php
    }
}
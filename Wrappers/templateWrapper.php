<?php

namespace MoDynamics365ObjectSync\Wrappers;

class templateWrapper
{

    private static $instance;

    public static function getWrapper()
    {
        if (!isset(self::$instance)) {
            $class = __CLASS__;
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public static function mo_dcrm_get_image_src($imageName)
    {
        return esc_url(plugin_dir_url(MO_DCRM_PLUGIN_FILE) . 'images/' . $imageName);
    }

    public static function mo_dcrm_load_tile($tab, $index, $title, $content, $active, $advert, $description)
    {
        $suffix = '_' . $tab . '_' . $index;
        if (!$advert) {
?>
            <div class="dcrm_section-main-tile">
                <div dynamicID="<?php echo esc_html($suffix); ?>" class="dcrm_section-tile-nav">
                    <div dynamicID="<?php echo esc_html($suffix); ?>" class="dcrm_section-tile-nav__index"><?php echo esc_html($index); ?>.</div>
                    <div dynamicID="<?php echo esc_html($suffix); ?>" class="dcrm_section-tile-nav__title"><?php echo esc_html($title); ?><img id="green_tick<?php echo esc_html($suffix); ?>" style="margin:10px;display:<?php echo esc_html($active) ? 'block' : 'none'; ?>" width="18px" height="18px" src="<?php echo esc_url_raw(templateWrapper::mo_dcrm_get_image_src('checked.png')); ?>" /></div>
                    <div dynamicID="<?php echo esc_html($suffix); ?>" class="dcrm_section-tile-nav__icon">
                        <span dynamicID="<?php echo esc_html($suffix); ?>" id="dcrm_section-tile-nav__icon_up<?php echo esc_html($suffix); ?>" style="display:<?php echo esc_html($active) ? 'block' : 'none'; ?>" class="dashicons dashicons-arrow-right"></span>
                        <span dynamicID="<?php echo esc_html($suffix); ?>" id="dcrm_section-tile-nav__icon_down<?php echo esc_html($suffix); ?>" style="display:<?php echo esc_html($active) ? 'none' : 'block'; ?>" class="dashicons dashicons-arrow-down"></span>
                    </div>
                </div>
                <div id="dcrm_section-tile-content<?php echo esc_html($suffix); ?>" class="dcrm_section-tile-content" style="display:<?php echo esc_html($active) ? 'none' : 'block'; ?>">
                    <?php if ($description != '') { ?>
                        <div id="basic_attr_access_desc" class="mo_dcrm_help_desc" style="margin-bottom:20px;">
                            <span><?php echo "<b>Note: </b>".$description; ?></span>
                        </div>
                    <?php } ?>
                    <?php echo $content; ?>
                </div>
            </div>
        <?php
        } else {
        ?>
            <div class="mo_dcrm_random" style="position:relative">
                <div>
                    <img class="mo-dcrm-img-prem" src="<?php echo esc_url_raw(templateWrapper::mo_dcrm_get_image_src('crown.png')); ?>">
                    <p class="mo_dcrm_prem_text">Available in premium plugin.</p>
                </div>
                <div class="dcrm_section-main-tile" style="opacity:0.8;border: 6px solid #a3a4ff;border-radius: 4px;">
                    <div class="dcrm_section-tile-nav">
                        <div class="dcrm_section-tile-nav__index"><?php echo esc_html($index); ?>.</div>
                        <div class="dcrm_section-tile-nav__title"><?php echo esc_html($title); ?><img id="green_tick<?php echo esc_html($suffix); ?>" style="margin:10px;display:<?php echo esc_html($active) ? 'block' : 'none'; ?>" width="18px" height="18px" src="<?php echo esc_url_raw(templateWrapper::mo_dcrm_get_image_src('checked.png')); ?>" /></div>
                    </div>
                    <div class="dcrm_section-tile-content" style="display:<?php echo esc_html($active) ? 'none' : 'block'; ?>">
                        <?php if ($description != '') { ?>
                            <div id="basic_attr_access_desc" class="mo_dcrm_help_desc" style="margin-bottom:20px;font-weight:500;">
                                <span><?php echo "<b style='color:#000;font-weight:500;'>Note: </b>".$description; ?></span>
                            </div>
                        <?php } ?>
                        <?php echo $content; ?>
                    </div>
                </div>
            </div>
<?php
        }
    }

    public static function mo_dcrm_load_field_mapping($object, $form, $fields, $attr, $field_map, $isReq = 0, $active = true, $added = false)
    {
        $label = $attr['label'];
        $name = $attr['name'];
        $type = $attr['type'];
        $suffix = '_' . $object . '_' . $form . '_' . $name;
        $requiredMark = $isReq ? '<sup style="color:red;font-weight:bold;font-size:15px;">*</sup>' : '';
        $content = '';
        $deleteButton = $isReq ? '' : '<div dynamicID="' . $suffix . '" class="dcrm_object_form_map_nav__icon"><button type="button" dynamicID="' . $suffix . '" id="dcrm_object_form_map_nav__icon_delete" style="background:transparent;border:none;"><span dynamicID="' . $suffix . '" style="display: block" class="dashicons dashicons-trash"></span></button></div>';

        $selected_form_field = '';
        if (isset($field_map[$name]))
            $selected_form_field = $field_map[$name];

        if (!$added && (!$isReq && !isset($field_map[$name])))
            return $content;

        $content =  '
            <div dynamicID="' . $suffix . '" id="dcrm_object_form_map-tile' . $suffix . '" class="dcrm_object_form_map-tile" style="margin:10px;">
                <div dynamicID="' . $suffix . '" class="dcrm_object_form_map_nav">
                    <div dynamicID="' . $suffix . '" attributeName="' . $name . '" class="dcrm_object_form_map_nav__title">' . $label . $requiredMark . '&nbsp; ( ' . $name . ' )</div>' . $deleteButton . '
                    <div dynamicID="' . $suffix . '" class="dcrm_object_form_map_nav__icon">
                        <span dynamicID="' . $suffix . '" id="dcrm_object_form_map_nav__icon_up' . $suffix . '" style="display:none;" class="dashicons dashicons-arrow-right"></span>
                        <span dynamicID="' . $suffix . '" id="dcrm_object_form_map_nav__icon_down' . $suffix . '" style="display: block" class="dashicons dashicons-arrow-down"></span>
                    </div>
                </div>
                <div id="dcrm_object_form_map-content' . $suffix . '" class="dcrm_object_form_map-content">
                <table style="width:100%;margin:10px;">
                    <colgroup>
                        <col span="1" style="width: 20%;">
                        <col span="2" style="width: 80%;">
                    </colgroup>
                    <tr>
                        <td><span style="font-weight:500">Type:</span></td>
                        <td>' . $type . '</td>
                    </tr>
              
                    <tr>
                        <td><span style="font-weight:500">Select Form Field:</span></td>
                        <td>
                <div>';


        $inputHTML = '<select style="width:95%" id="mo_dcrm_' . $object . '_' . $form . '_' . $name . '_dropdown" name="mo_dcrm_' . $object . '_' . $form . '_' . $name . '_dropdown" value="">
                    ';
        foreach ($fields as $field) {
            $inputHTML = $inputHTML . '<option ' . ($selected_form_field == $field['name'] ? 'selected' : '') . ' value="' . $field['name'] . '">' . $field['label'] . '</option>';
        }
        $inputHTML = $inputHTML . '</select>';

        $content = $content . $inputHTML;
        $content = $content . '
                                </select>
                            </div>
                        </td>
                    </tr>
                </table>
                </div>
            </div>
        ';

        return $content;
    }

    public static function mo_dcrm_load_basic_form($label_width, $input_width, $form_inputs, $tab, $_nonce, $active = true)
    {
        $disabled = $active ? '' : 'disabled';
        $content = '
        <form action="" method="post">
        <input type="hidden" name="option" id="' . $tab . '" value="' . $_nonce . '">
        <input type="hidden" name="mo_dcrm_tab" value="' . $tab . '">' . wp_nonce_field($_nonce) . '
        <table style="width:100%;margin:10px;">
            <colgroup>
                <col span="1" style="width: ' . $label_width . ';">
                <col span="2" style="width: ' . $input_width . ';">
            </colgroup>
        ';
        foreach ($form_inputs as $row) {

            switch ($row['type']) {
                case 'text':
                    $inputHTML = '<input ' . $disabled . ' placeholder="' . $row['placeholder'] . '" style="width:95%" type="' . $row['type'] . '" name="' . $row['name'] . '" value="' . esc_html($row['value']) . '">';
                    $cols = '<td><span style="font-weight:600">' . $row['label'] . ':</span></td><td>' . $inputHTML . '</td>';
                    break;

                case 'radio':
                    $inputHTML = '<input style="border:1px solid #1da;" ' . $disabled . ' ' . ($row['checked'] ? 'checked' : '') . ' type="' . $row['type'] . '" name="' . $row['name'] . '" value="' . esc_html($row['value']) . '">';
                    $cols = '<td colspan="2"><div style="display:flex;justify-content:flex-start;align-items:center;"><div >' . $inputHTML . '</div><div style="margin:5px;font-weight:600;">' . $row['label'] . '</div></div></td><td></td>';
                    break;

                case 'dropdown':
                    $inputHTML = '<select ' . $disabled . ' placeholder="' . $row['placeholder'] . '" style="width:95%" type="' . $row['type'] . '" id="' . $row['name'] . '" name="' . $row['name'] . '" value="' . esc_html($row['value']) . '">
                    <option ' . (empty($row['value']) ? 'selected' : '') . ' disabled value="default">' . $row['defaultValue'] . '</option>';
                    foreach ($row['options'] as $key => $option) {
                        $name = $option['name'];
                        $logical_name = '';
                        if (isset($option['logical_name']))
                            $logical_name = $option['logical_name'];

                        if (empty($name))
                            $name = $logical_name;


                        $inputHTML = $inputHTML . '<option ' . (!empty($row['value'] && $row['value'] == $name) ? 'selected' : '') . ' logical_name="' . $logical_name . '" value="' . $name . '">' . $option['label'] . '</option>';
                    }
                    $inputHTML = $inputHTML . '</select>';
                    $cols = '<td><span style="font-weight:600">' . $row['label'] . ':</span></td><td>' . $inputHTML . '</td>';
                    break;

                case 'multidropdown':
                    $inputHTML = '<select multiple ' . $disabled . ' placeholder="' . $row['placeholder'] . '" style="width:95%;height:200px" type="' . $row['type'] . '" id="' . $row['name'] . '" name="' . $row['name'] . '" value="' . '' . '">
                    <option disabled value="default">' . $row['defaultValue'] . '</option>';
                    foreach ($row['options'] as $option) {
                        $name = $option['name'];
                        $logical_name = '';
                        if (isset($option['logical_name']))
                            $logical_name = $option['logical_name'];

                        if (empty($name))
                            $name = $logical_name;


                        $inputHTML = $inputHTML . '<option ' . (!empty($row['value'] && in_array($name, $row['value'])) ? 'selected' : '') . ' logical_name="' . $logical_name . '" value="' . $name . '">' . $option['label'] . '</option>';
                    }
                    $inputHTML = $inputHTML . '</select>';
                    $cols = '<td><span style="font-weight:600">' . $row['label'] . ':</span></td><td>' . $inputHTML . '</td>';
                    break;

                default:
                    $inputHTML = '<input ' . $disabled . ' placeholder="' . $row['placeholder'] . '" autocomplete="new-password" style="width:95%" type="' . $row['type'] . '" name="' . $row['name'] . '" value="' . esc_html($row['value']) . '">';
                    $cols = '<td><span style="font-weight:600">' . $row['label'] . ':</span></td><td>' . $inputHTML . '</td>';
                    break;
            }



            $content = $content . '
            <tr>' . $cols . '</tr>';
        }


        $content = $content . '
        <tr>
            <td>
            <div style="display: flex;justify-content:flex-start;align-items:center;">
                <div style="display: flex;margin:1px;">
                    <input ' . $disabled . ' style="height:30px;margin-top:10px" type="submit" id="' . $_nonce . '_saveButton" class="dcrm_basic_form__button' . $disabled . '" value="Save">
                </div>
            </div>
            </td>
            <td>
                
            </td>
        </tr>
        </table>
        </form>
        ';

        return $content;
    }

    public static function mo_dcrm_load_table($data, $disabled = false)
    {
        $content = '<div class="mo_dcrm_table_container"> <div class="mo_dcrm_cf7_config_table"> <div class="mo_dcrm_cf7_config_table_header">';
        foreach ($data['head'] as $key => $heading) {
            $content = $content . '<div class="mo_dcrm_header__item">' . $heading . '</div>';
        }
        $content = $content . '</div> <div class="mo_dcrm_cf7_config_table_content">';
        if (empty($data['rows'])) {
            $content = $content . '<div class="mo_dcrm_cf7_config_table_row">';
            $content = $content . '<div class="mo_dcrm_cf7_config_table_data" style="font-size: 15px;">You don' . "'" . 't have any mapping configured yet.</div>';
            $content = $content . '</div>';
        }
        foreach ($data['rows'] as $row) {
            $content = $content . '<div class="mo_dcrm_cf7_config_table_row">';
            foreach ($row as $key => $cell) {
                $content = $content . '<div class="mo_dcrm_cf7_config_table_data">' . $cell . '</div>';
            }
            $content = $content . '</div>';
        }
        $content = $content . '</div></div></div>';
        return $content;
    }
}

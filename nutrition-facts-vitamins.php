<?php
/***
 * Plugin Name:    Nutrition Facts Vitamins
 * Plugin URI:     https://dandelionwebdesign.com/downloads/nutrition-facts-label-vitamins/
 * Description:    Version 3.0 changes the Nutrition Label to the NEW FDA format. Adds a custom post type "Labels" to generate a Nutrition Facts Label. Includes nutrients D, Calcium, Iron and Potassium. Also supports user generated additional vitamins or "Not a Significant source of" text for blank fields. Use shortcode [nutrition-label id=XXX]to add the label to any page or post.
 * Version:       4.0
 * Author:        Dandelion Web Design Inc. modified by Heartland Business Systems
 * Author URI:    https://dandelionwebdesign.com/
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * ( at your option ) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/* ADDS */
add_shortcode('nutrition-label', 'nutr_label_shortcode');
add_action('wp_head', 'nutr_style');
add_action('init', 'nutr_init');
add_filter('manage_edit-nutrition-label_columns', 'nutr_modify_nutritional_label_table');
add_filter('manage_posts_custom_column', 'nutr_modify_nutritional_label_table_row', 10, 2);

add_action('add_meta_boxes', 'nutr_create_metaboxes');
add_action('save_post', 'nutr_save_meta', 1, 2);

//plugin update warning
add_action('in_plugin_update_message-' . plugin_basename(__FILE__), 'update_notice');

function update_notice()
{
    $info = __('CAUTION version 3.0 of this plugin changes the Nutrition Label to the NEW FDA format. Only update the plugin if you want this new format. 
                Includes nutrients D, Calcium, Iron and Potassium. See ' .
               '<a href="http://www.fda.gov/downloads/Food/NewsEvents/WorkshopsMeetingsConferences/UCM508389.pdf" target="_blank">http://www.fda.gov/downloads/Food/NewsEvents/WorkshopsMeetingsConferences/UCM508389.pdf</a>'
               . ' for a detailed description of what\'s changed.',
            'nutrition-facts-vitamins');
    echo '<div><p style="color:red; padding:7px 0;">' . strip_tags($info, '<a><b><i><span>');
}


/* RDA SETTINGS */
$rda = [
        'totalfat' => 80,
        'satfat' => 15,
        'cholesterol' => 300,
        'sodium' => 2300,
        'carbohydrates' => 300,
        'fiber' => 25,
        'protein' => 50,
        'sugar' => 50,
        'vitamin_d' => 100,
        'calcium' => 100,
        'iron' => 100,
        'potassium' => 100
];

/* BASE NUTRIIONAL FIELDS */
$nutritional_fields = [
        'servingsize' => __('Serving Size', 'nutrition-facts-vitamins'),
        'servings' => __('Servings', 'nutrition-facts-vitamins'),
        'calories' => __('Calories', 'nutrition-facts-vitamins'),
        'totalfat' => __('Total Fat', 'nutrition-facts-vitamins'),
        'satfat' => __('Saturated Fat', 'nutrition-facts-vitamins'),
        'transfat' => __('Trans. Fat', 'nutrition-facts-vitamins'),
        'polyunsaturatedfat' => __('Polyunsaturated Fat', 'nutrition-facts-vitamins'),
        'monounsaturatedfat' => __('Monounsaturated Fat', 'nutrition-facts-vitamins'),
        'cholesterol' => __('Cholesterol', 'nutrition-facts-vitamins'),
        'sodium' => __('Sodium', 'nutrition-facts-vitamins'),
        'carbohydrates' => __('Carbohydrates', 'nutrition-facts-vitamins'),
        'fiber' => __('Fiber', 'nutrition-facts-vitamins'),
        'sugars' => __('Sugars', 'nutrition-facts-vitamins'),
        'added_sugars' => __('Added Sugars', 'nutrition-facts-vitamins'),
        'protein' => __('Protein', 'nutrition-facts-vitamins'),
        'calcium' => __('Calcium', 'nutrition-facts-vitamins'),
        'iron' => __('Iron', 'nutrition-facts-vitamins'),
        'vitamin_d' => __('Vitamin D', 'nutrition-facts-vitamins'),
        'potassium' => __('Potassium', 'nutrition-facts-vitamins'),
];


/*
 * Init
 */
function nutr_init()
{
    load_plugin_textdomain('nutrition-facts-vitamins', false, basename(dirname(__FILE__)) . '/languages');

    $labels = [
            'name' => __('Nutritional Labels', 'nutrition-facts-vitamins'),
            'singular_name' => __('Label', 'nutrition-facts-vitamins'),
            'add_new' => __('Add New', 'nutrition-facts-vitamins'),
            'add_new_item' => __('Add New Label', 'nutrition-facts-vitamins'),
            'edit_item' => __('Edit Label', 'nutrition-facts-vitamins'),
            'new_item' => __('New Label', 'nutrition-facts-vitamins'),
            'all_items' => __('All Labels', 'nutrition-facts-vitamins'),
            'view_item' => __('View Label', 'nutrition-facts-vitamins'),
            'search_items' => __('Search Labels', 'nutrition-facts-vitamins'),
            'not_found' => __('No labels found', 'nutrition-facts-vitamins'),
            'not_found_in_trash' => __('No labels found in Trash', 'nutrition-facts-vitamins'),
            'parent_item_colon' => '',
            'menu_name' => __('Labels', 'nutrition-facts-vitamins'),
    ];

    $args = [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => false,
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => null,
            'menu_icon' => plugins_url('img/facts-menu-icon.png', __FILE__),
            'supports' => ['title'],
    ];
    register_post_type('nutrition-label', $args);

    //enqueue script
    wp_enqueue_script('scripts-nutrition-facts-vitamins', plugins_url('/js/nutrition-facts-vitamins.js', __FILE__),
            ['jquery']);

    //enqueue style
    wp_enqueue_style('nutrition-facts-vitamins', plugins_url('/css/nutrition-facts-vitamins.css', __FILE__),
            []);
}


/*
 * Meta Box with Data
 */
function nutr_create_metaboxes()
{
    add_meta_box('nutr_create_metabox_1', 'Nutritional Label Options', 'nutr_create_metabox_1', 'nutrition-label',
            'normal', 'default');
}

function nutr_create_metabox_1()
{
    global $post, $nutritional_fields;
    $meta_values = get_post_meta($post->ID);

    $pages = get_posts(['post_type' => 'page', 'numberposts' => -1]);
    $posts = get_posts(['numberposts' => -1]);

    $selected_page_id = isset($meta_values['_pageid']) ? $meta_values['_pageid'][0] : 0;
    ?>

    <div class="nutritionPluginWrap">
        <div class="pageSelectWrap">
            <div class="label">
                <?php _e('Page', 'nutrition-facts-vitamins'); ?>
            </div>
            <select name="pageid" class="left">
                <option value=""><?php _e('Select a Page...', 'nutrition-facts-vitamins'); ?></option>
                <optgroup label="<?php _e('Pages', 'nutrition-facts-vitamins'); ?>">
                    <?php foreach ($pages as $page) { ?>
                        <option value="<?php echo $page->ID ?>"<?php if ($selected_page_id == $page->ID) {
                            echo " SELECTED";
                        } ?>><?php echo $page->post_title ?></option>
                    <?php } ?>
                </optgroup>
                <optgroup label="<?php _e('Posts', 'nutrition-facts-vitamins'); ?>">
                    <?php foreach ($posts as $post) { ?>
                        <option value="<?php echo $post->ID ?>"<?php if ($selected_page_id == $post->ID) {
                            echo " SELECTED";
                        } ?>><?php echo $post->post_title ?></option>
                    <?php } ?>
                </optgroup>
            </select>
            <div style="clear:both;"></div>
        </div>
        <hr/>
        <div class="nutritionFieldsWrap">
            <?php
            foreach ($nutritional_fields as $name => $nutrient_fields) { ?>
                <div class='nutritionField' id='<?php echo $name ?>'>
                    <div class='label'>
                        <?php echo $nutrient_fields ?>
                    </div>
                    <input type="text" name="<?php echo $name ?>"
                            value="<?php if (isset($meta_values['_' . $name])) {
                                echo esc_attr($meta_values['_' . $name][0]);
                            } ?>"/>

                    <div class="clear"></div>
                </div>
            <?php } ?>

            <?php
            /**
             * Print extra vitamins
             */
            if (isset($meta_values['_extra_vitamins'])):
                $vitamins = unserialize(current($meta_values['_extra_vitamins']));
                if (!empty($vitamins)):
                    $dataId = 1;
                    foreach ($vitamins as $name => $vitamin):
                        ?>

                        <div class='nutritionField dynamic' id='<?php echo $name ?>' data-id='<?php echo $dataId++ ?>'>
                            <div class='label editable' title="Click to edit">
                                <label><?php echo $name ?></label>
                            </div>
                            <input type="hidden" name="extra_vitamin_label[]" class="extraVitaminLabel"
                                    value="<?php echo $name ?>">
                            <input type="text" name="extra_vitamin[]" value="<?php echo $vitamin ?>"/>
                            <a title="Remove this label" href="#" class="remove"></a>
                            <div class="clear"></div>
                        </div>

                        <?php
                    endforeach;
                endif;
            endif;

            //Add a nonce field
            wp_nonce_field(plugin_basename(__FILE__), 'nutrition-facts-nonce');
            ?>
            <a class="addNewVitamin" href="javascript:void(0)"><?php _e('Add New Vitamin',
                        'nutrition-facts-vitamins'); ?></a>

        </div>
    </div>
    <?php
}

function nutr_save_meta($post_id, $post)
{
    global $nutritional_fields;

    //Check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    /**
     * Check for nonce before saving
     */
    if (isset($_POST['nutrition-facts-nonce']) &&
        !wp_verify_nonce($_POST['nutrition-facts-nonce'], plugin_basename(__FILE__))
    ) {
        return;
    }

    /**
     * Allow to save if the current user have permission to edit posts
     */
    if (current_user_can('edit_posts', $post_id)) {

        foreach ($nutritional_fields as $name => $nutri_fields) {
            if (isset($_POST[$name])) {
                update_post_meta($post_id, '_' . $name, strip_tags($_POST[$name]));
            }
        }

        /**
         * Save extra meta data, if any
         */
        if (isset($_POST['extra_vitamin']) && !empty($_POST['extra_vitamin'])) {
            $vitamins = array_combine($_POST['extra_vitamin_label'], $_POST['extra_vitamin']);
            update_post_meta($post_id, '_extra_vitamins', $vitamins);
        } else {
            delete_post_meta($post_id, '_extra_vitamins');
        }

        if (isset($_POST['pageid'])) {
            update_post_meta($post_id, '_pageid', strip_tags($_POST['pageid']));
        }

    }

}

/*
 * Add Column to WordPress Admin
 * Displays the shortcode needed to show label
 *
 * 2 Functions
 */
function nutr_modify_nutritional_label_table($column)
{
    $columns = [
            'cb' => '<input type="checkbox" />',
            'title' => 'Title',
            'nutr_shortcode' => 'Shortcode',
            'nutr_page' => 'Page',
            'date' => 'Date',
    ];

    return $columns;
}

function nutr_modify_nutritional_label_table_row($column_name, $post_id)
{
    if ($column_name == "nutr_shortcode") {
        echo "[nutrition-label id={$post_id}]";
    }

    if ($column_name == "nutr_page") {
        echo get_the_title(get_post_meta($post_id, "_pageid", true));
    }

}

/*
 * output our style sheet at the head of the file
 * because it's brief, we just embed it rather than force an extra http fetch
 *
 * @return void
 */
function nutr_style()
{
    ?>
    <style type='text/css'>
        .wp-nutrition-label {
            border: 2px solid #111;
            font-family: 'Source Sans Pro', sans-serif, arial, helvetica;
            font-size: .9em;
            max-width: 280px;
            padding: .35em;
            line-height: 1em;
            margin: 1em;
            background: #fff;
            color: #000;
        }

        .wp-nutrition-label hr {
            height: 8px;
            background: #111;
            margin: 3px 0;
        }

        .wp-nutrition-label .heading {
            font-size: 39px;
            font-weight: 900;
            margin: 0;
            line-height: 1em;
            text-justify: auto;
            border-bottom: 2px solid #111;
        }

        .wp-nutrition-label .indent {
            margin-left: 1em;
        }

        .wp-nutrition-label .double-indent {
            margin-left: 2em;
        }

        .wp-nutrition-label .small {
            font-size: .8em;
            line-height: 1em;
        }

        .wp-nutrition-label .item_row {
            border-top: solid 1px #111;
            padding: 3px 0;
        }

        .item_row strong {
            font-size: 15px;
            font-weight: bold;
        }

        .wp-nutrition-label .amount-per {
            padding: 0 0 8px 0;
            font-weight: bold;
            font-size: 15px;
        }

        .wp-nutrition-label .daily-value {
            padding: 4px;
            font-weight: bold;
            text-align: right;
            border-top: solid 4px #111;
        }

        .wp-nutrition-label .f-left {
            float: left;
        }

        .wp-nutrition-label .f-right {
            float: right;
        }

        .wp-nutrition-label .noborder {
            border: none;
        }

        .footnote:before {
            content: "* ";
            margin-left: -9px;
        }

        .footnote {
            font-size: 0.813em;
            padding: 0 0 0 9px;
        }

        .wp-nutrition-label .amount {
            font-weight: 700;
            padding: 0;
            line-height: 1em;
        }

        .cf:before, .cf:after {
            content: " ";
            display: table;
        }

        .cf:after {
            clear: both;
        }

        .cf {
            *zoom: 1;
        }
    </style>
    <?php
}


/*
 *
 * @param array $atts
 * @return string
 */
function nutr_label_shortcode($atts)
{
    $id    = (int)isset($atts['id']) ? $atts['id'] : false;
    $width = (int)isset($atts['width']) ? $atts['width'] : 22;

    if ($id) {
        return nutr_label_generate($id, $width);
    }
    {
        global $post;

        $label = get_posts([
                'post_type' => 'nutrition-label',
                'meta_key' => '_pageid',
                'meta_value' => $post->ID,
        ]);

        if ($label) {
            $label = reset($label);

            return nutr_label_generate($label->ID, $width);
        }
    }
}


/*
 * @param integer $contains
 * @param integer $reference
 * @return integer
 */
function nutr_percentage($contains, $reference)
{
    if ($reference == 0) {
        return 0;
    }

    return ceil($contains / $reference * 100);
}


/*
 * @param array $args
 * @return string
 */
function nutr_label_generate($id, $width = 22)
{
    global $rda, $nutritional_fields;

    $label = get_post_meta($id);

    $insufficient = []; //holds insufficient vitamins data

    if (!$label) {
        return false;
    }

    // GET VARIABLES
    foreach ($nutritional_fields as $name => $value) {
        $$name = $label['_' . $name][0];
    }

    // BUILD CALORIES IF WE DON'T HAVE ANY
    if ($calories == 0) {
        $calories = (($protein + $carbohydrates) * 4) + ($totalfat * 10);
    }

    // WIDTH THE LABEL
    $style = '';
    if ($width != 22) {
        $style = " style='width: " . $width . "em; font-size: " . (($width / 22) * .75) . "em;'";
    }

    $servingsizeounces = $servingsize . ' oz';
    $rtn = "";
    $rtn .= "<div class='wp-nutrition-label' id='wp-nutrition-label-$id' " . ($style ? $style : "") . ">\n";

    $rtn .= "	<div class='heading'>" . __('Nutrition Facts', 'nutrition-facts-vitamins') . "</div>\n";

    $rtn .= "	<div style='font-size: 18px; padding-top: 3px; padding-bottom: 5px'>" . $servings . " " . __('',
                    'nutrition-facts-vitamins') . "</div>\n";
    $rtn .= "	<div style='font-weight: 900; font-size: 1.250em'><span class='f-left' style='padding-bottom: 5px'>" . __('Serving size',
                    'nutrition-facts-vitamins') . "</span> <span style='font-weight: 900; float: right'>" . $servingsizeounces . "</span></div>\n";
    $rtn .= "	<hr style='clear: both; height: 14px' />\n";
    $rtn .= "	<div class='amount-per small item_row noborder'>" . __('Amount per serving',
                    'nutrition-facts-vitamins') . "</div>\n";

    $rtn .= "	<div class='item_row cf noborder' style='font-size: 2.313em; font-weight: bold'>\n";
    $rtn .= "		<span class='f-left' style='font-weight: 900; letter-spacing:0.03em; padding-bottom: 10px'>" . __('Calories',
                    'nutrition-facts-vitamins') . "</span>\n";
    $rtn .= "		<span class='f-right' style='font-size: 1.313em;font-weight: 900;'>" . $calories . "</span>\n";
    /* $rtn .= "		<span class='f-right'>" . __( 'Calories from Fat ',
					 'nutrition-facts-vitamins' ) . ( $totalfat * 9 ) . "</span>\n";*/
    $rtn .= "	</div>\n";

    $rtn .= "	<div class='item_row daily-value small'>% " . __('Daily Value',
                    'nutrition-facts-vitamins') . "*</div>\n";

    $rtn .= "	<div class='item_row cf'>\n";
    $rtn .= "		<span class='f-left'><strong>" . __('Total Fat',
                    'nutrition-facts-vitamins') . "</strong> " . $totalfat . "g</span>\n";
    $rtn .= "		<span class='f-right'>" . nutr_percentage($totalfat, $rda['totalfat']) . "%</span>\n";
    $rtn .= "	</div>\n";

    $rtn .= "	<div class='indent item_row cf'>\n";
    $rtn .= "		<span class='f-left'>" . __('Saturated Fat',
                    'nutrition-facts-vitamins') . " " . $satfat . "g</span>\n";
    $rtn .= "		<span class='f-right'>" . nutr_percentage($satfat, $rda['satfat']) . "%</span>\n";
    $rtn .= "	</div>\n";

    $rtn .= "	<div class='indent item_row cf'>\n";
    $rtn .= "		<span><em>" . __('Trans', 'nutrition-facts-vitamins') . '</em> ' . __('Fat',
                    'nutrition-facts-vitamins') . " " . $transfat . "g</span>";
    $rtn .= "	</div>\n";

    $rtn .= "	<div class='indent item_row cf'>\n";
    $rtn .= "		<span class='f-left'>" . __('Polyunsaturated Fat', 'nutrition-facts-vitamins') . " " . $polyunsaturatedfat . "g</span>";
    $rtn .= "	</div>\n";

    $rtn .= "	<div class='indent item_row cf'>\n";
    $rtn .= "		<span class='f-left'>" . __('Monounsaturated Fat', 'nutrition-facts-vitamins') . " " . $monounsaturatedfat . "g</span>";
    $rtn .= "	</div>\n";

    $rtn .= "	<div class='item_row cf'>\n";
    $rtn .= "		<span class='f-left'><strong>" . __('Cholesterol',
                    'nutrition-facts-vitamins') . "</strong> " . $cholesterol . "mg</span>\n";
    $rtn .= "		<span class='f-right'>" . nutr_percentage($cholesterol, $rda['cholesterol']) . "%</span>\n";
    $rtn .= "	</div>\n";

    $rtn .= "	<div class='item_row cf'>\n";
    $rtn .= "		<span class='f-left'><strong>" . __('Sodium',
                    'nutrition-facts-vitamins') . "</strong> " . $sodium . "mg</span>\n";
    $rtn .= "		<span class='f-right'>" . nutr_percentage($sodium, $rda['sodium']) . "%</span>\n";
    $rtn .= "	</div>\n";

    $rtn .= "	<div class='item_row cf'>\n";
    $rtn .= "		<span class='f-left'><strong>" . __('Total Carbohydrate',
                    'nutrition-facts-vitamins') . "</strong> " . $carbohydrates . "g</span>\n";
    $rtn .= "		<span class='f-right'>" . nutr_percentage($carbohydrates, $rda['carbohydrates']) . "%</span>\n";
    $rtn .= "	</div>\n";

    $rtn .= "	<div class='indent item_row cf'>\n";
    $rtn .= "		<span class='f-left'>" . __('Dietary Fiber',
                    'nutrition-facts-vitamins') . " " . $fiber . "g</span>\n";
    $rtn .= "		<span class='f-right'>" . nutr_percentage($fiber, $rda['fiber']) . "%</span>\n";
    $rtn .= "	</div>\n";

    $rtn .= "	<div class='indent item_row cf'>\n";
    $rtn .= "		<span>" . __('Total Sugars', 'nutrition-facts-vitamins') . " " . $sugars . "g</span>";
    $rtn .= "	</div>\n";

    $rtn .= "	<div class='double-indent item_row cf'>\n";
    $rtn .= "		<span>" . __('Includes',
                    'nutrition-facts-vitamins') . " " . $added_sugars . 'g ' . __('Added Sugars') . "</span>";
    $rtn .= "		<span class='f-right'>" . nutr_percentage($added_sugars, $rda['sugar']) . "%</span>\n";
    $rtn .= "	</div>\n";

    $rtn .= "	<div class='item_row cf'>\n";
    $rtn .= "		<span class='f-left'><strong>" . __('Protein',
                    'nutrition-facts-vitamins') . "</strong> " . $protein . "g</span>\n";
    $rtn .= "		<span class='f-right'></span>\n";
    $rtn .= "	</div>\n";

    $rtn .= "	<hr style='height: 14px; background-color: #111; margin-top: 0' />\n";

    if (isset($vitamin_d) || ($vitamin_d === "0")) {
        $rtn .= "	<div class='item_row noborder cf'>\n";
        $rtn .= "		<span class='f-left'>Vitamin D</span>\n";
        $rtn .= "		<span class='f-right'>" . nutr_percentage($vitamin_d, $rda['vitamin_d']) . "%</span>\n";
        $rtn .= "	</div>\n";
    } else {
        $insufficient[] = 'vitamin D';
    }

    if ($calcium || ($calcium === "0")) {
        $rtn .= "	<div class='item_row cf'>\n";
        $rtn .= "		<span class='f-left'>Calcium</span>\n";
        $rtn .= "		<span class='f-right'>" . nutr_percentage($calcium, $rda['calcium']) . "%</span>\n";
        $rtn .= "	</div>\n";
    } else {
        $insufficient[] = 'calcium';
    }

    if ($iron || ($iron === "0")) {
        $rtn .= "	<div class='item_row cf'>\n";
        $rtn .= "		<span class='f-left'>Iron</span>\n";
        $rtn .= "		<span class='f-right'>" . nutr_percentage($iron, $rda['iron']) . "%</span>\n";
        $rtn .= "	</div>\n";
    } else {
        $insufficient[] = 'iron';
    }

    if (isset($potassium) || ($potassium === "0")) {
        $rtn .= "	<div class='item_row cf'>\n";
        $rtn .= "		<span class='f-left'>Potassium</span>\n";
        $rtn .= "		<span class='f-right'>" . nutr_percentage($potassium, $rda['potassium']) . "%</span>\n";
        $rtn .= "	</div>\n";
    } else {
        $insufficient[] = 'potassium';
    }

    /*
		* Extra vitamins
	 */
    if (isset($label['_extra_vitamins']) && !empty($label['_extra_vitamins'])) {
        $extraVitamins = unserialize(current($label['_extra_vitamins']));

        $sufficient = [];
        foreach ($extraVitamins as $key => $vitamin) {
            if ($vitamin || $vitamin === '0') {
                $sufficient[$key] = $vitamin;
            } else {
                $insufficient[] = strtolower($key);
            }
        }

        if (!empty($sufficient)) {
            foreach ($sufficient as $extraLabel => $extraVit) {
                $rtn .= "	<div class='item_row cf'>\n";
                $rtn .= "		<span class='f-left'>" . $extraLabel . "</span>\n";
                $rtn .= "		<span class='f-right'>" . $extraVit . "%</span>\n";
                $rtn .= "	</div>\n";
            }
        }

    }
    if (!empty($insufficient)) {
        $last = "";
        if (count($insufficient) > 1) {
            $last = array_pop($insufficient);
            $last = ", or " . $last;
        }

        $rtn .= "	<div class='item_row cf'>\n";
        $rtn .= __("Not a significant source of ") . implode(', ', $insufficient);
        $rtn .= $last . ".\n";
        $rtn .= "	</div>";
    }
    $rtn .= "<hr />";
    $rtn .= "	<div class='item_row cf noborder'>\n";
    $rtn .= "	<div class='footnote'>" . __('The % Daily Value (DV) tells you how much a nutrient in a serving of 
        food contributes to a daily diet. 2,000 calories a day is used for general nutrition advice.') . "</div>\n";
    $rtn .= "	</div>\n";

    $rtn .= "</div> <!-- /wp-nutrition-label -->\n\n";

    return $rtn;
}

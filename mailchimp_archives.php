<?php
/*
Plugin Name: MailChimp Archives
Plugin URI: http://wordpress.org/extend/plugins/mail-chimp-archives/
Description: Add your MailChimp archives to any page
Version:  3.2
Author:  Mark Parolisi
Author URI: http://markparolisi.com
*/

/*  Copyright 2009  Mark Parolisi  (email : mark@markparolisi.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

//TODO allow option to pull campaign from only certain lists

define('MCA_API_OPT', "mca_api");
define('MCA_LISTS_OPT', "mca_lists");
define('MCA_INT_OPT', "mca_interval_option");
define('MCA_TARGET_OPT', "mca_target");
define('MCA_JS_OPT', "mca_js");
define('MCA_SAVE_OPT', "mca_save"); 
define('MCA_CAT_OPT', "mca_cat");
define('MCA_AUTHOR_OPT', "mca_author");
define('MCA_STATUS_OPT', "mca_status");
define('MCA_TYPE_OPT', "mca_type");
define('MCA_CAMPDATA_OPT', "mca_campsdata");
define('MCA_POSTMETA', "mca_id");

register_activation_hook(__FILE__, 'mca_activation');
add_action('admin_menu', 'add_mca_admin_page');
add_action('admin_notices', 'mca_activation_notice');


function mca_activation() {
    add_option(MCA_API_OPT, 'api_key');
    add_option(MCA_LISTS_OPT, array());
    add_option(MCA_INT_OPT, 'year');
    add_option(MCA_TARGET_OPT, 'off');
    add_option(MCA_JS_OPT, 'off');
    add_option(MCA_SAVE_OPT, 'off');
    add_option(MCA_CAT_OPT, '0');
    add_option(MCA_AUTHOR_OPT, '1');
    add_option(MCA_STATUS_OPT, 'draft');
    add_option(MCA_TYPE_OPT, 'post');
    add_option(MCA_CAMPDATA_OPT, '0');
    add_action('mca_savedata_event', 'mca_save_archives');
//Save just the ID's and titles for the live fetch
    wp_schedule_event(time(), 'daily', 'mca_savedata_event');


}

//Notify new install to input API key
function mca_activation_notice() {
    $current_mca_api = get_option(MCA_API_OPT);
    if ($current_mca_api == 'api_key') {
        echo '<div class="error fade"><p><strong>Please add your Mail Chimp API key to the <a href="'. get_option('siteurl') . '/wp-admin/options-general.php?page=mail-chimp-archives/mailchimp_archives.php">admin page</a> of this plugin located under the Tools category</strong></p></div>';
    }
}

//Bring in the MC API wrapper class and our two main functions
if (!class_exists('MCAPI')) {
    require_once 'MCAPI.class.php';
}

//saves general info about archives for later retrieval
function mca_save_archives() {
    global $mca_api;
    $camps_data         = array();
    $current_mca_lists  = get_option(MCA_LISTS);
    if(is_array($current_mca_lists)) {
        foreach($current_mca_lists as $mca_list) {
            $filters['status']  = 'sent';
            $filters['list_id'] = $mca_list;
            $retval             = $mca_api->campaigns($filters, 0, 1000);
            if($retval) {
                foreach ($retval as $c) {
                    $tmp_array      = array();
                    $camp_id        = $c['id'];
                    $camp_subject   = $c['subject'];
                    $camp_time      = $c['create_time'];
                    $tmp_array['id']          = $camp_id;
                    $tmp_array['subject']     = $camp_subject;
                    $tmp_array['create_time'] = $camp_time;
                    array_push($camps_data, $tmp_array);
                }
            }
        }
    }

    update_option(MCA_CAMPDATA_OPT, $camps_data);
}


require_once 'mca_livefetch.php';
require_once 'mca_savefetch.php';

$mca_api_key = get_option(MCA_API_OPT);
$mca_api     = new MCAPI($mca_api_key);
if ($mca_api->errorCode) {
    //echo "Unable to Pull list of Campaigns";
    //echo "\n\tCode=".$api->errorCode;
    //echo "\n\tMsg=".$api->errorMessage."\n";
}


//Enable Saving of Archives as Posts
if (get_option(MCA_SAVE_OPT) == 'on') {
    if (!has_action('mca_savepost_event')) {
        add_action('mca_savepost_event', 'mca_insert_function');
    }
    wp_schedule_event(time(), 'twicedaily', 'mca_savepost_event');
}else {
    if (has_action('mca_savepost_event')) {
        wp_clear_scheduled_hook('mca_savepost_event');
    }
}

function add_mca_admin_page() {
    add_submenu_page('options-general.php', 'MailChimp Archives', 'MailChimp Archives', 8, __FILE__, 'mca_admin_page');
}
//Admin Panel
function mca_admin_page() {
    global $mca_api;

    if (isset($_POST["submit"])) {
        $mca_api_key    = trim($_POST["mca_api"]);
        $mca_lists      = $_POST['mca_lists'];
        $mca_interval   = trim($_POST["mca_interval"]);
        $mca_target     = $_POST["mca_target"];
        $mca_js         = $_POST["mca_js"];
        $mca_save       = $_POST["mca_save"];
        $mca_cat        = $_POST["cat"];
        $mca_author     = $_POST["user"];
        $mca_status     = $_POST["mca_status"];
        $mca_type       = $_POST["mca_type"];

        update_option(MCA_API_OPT, $mca_api_key);
        update_option(MCA_LISTS_OPT, $mca_lists);
        update_option(MCA_INT_OPT, $mca_interval);
        update_option(MCA_TARGET_OPT, $mca_target);
        update_option(MCA_JS_OPT, $mca_js);
        update_option(MCA_SAVE_OPT, $mca_save);
        update_option(MCA_CAT_OPT, $mca_cat);
        update_option(MCA_AUTHOR_OPT, $mca_author);
        update_option(MCA_STATUS_OPT, $mca_status);
        update_option(MCA_TYPE_OPT, $mca_type);
        remove_action('admin_notices', 'mca_activation_notice');
        mca_save_archives();
        echo "<p><div id='message' class='updated'>
			<p><strong>Your settings have been updated</strong></p>
			</div></p>";
    }
    if(isset($_POST["update_records"])) {
        if (get_option(MCA_SAVE_OPT) == 'on') {
            mca_save_archives();
            mca_insert_function();
            echo "<p><div id='message' class='updated'>
			<p><strong>Updated</strong></p>
			</div></p>";
        }
    }
    $current_mca_api        = get_option(MCA_API_OPT);
    $current_mca_lists      = get_option(MCA_LISTS);
    $current_time_interval  = get_option(MCA_INT_OPT);
    $current_target         = get_option(MCA_TARGET_OPT);
    $current_js             = get_option(MCA_JS_OPT);
    $current_save           = get_option(MCA_SAVE_OPT);
    $current_cat            = get_option(MCA_CAT_OPT);
    $current_author         = get_option(MCA_AUTHOR_OPT);
    $current_status         = get_option(MCA_STATUS_OPT);
    $current_type           = get_option(MCA_TYPE_OPT);
    $mca_plugin_location    = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));
    echo '<script type="text/javascript">
        jQuery(document).ready(function($){

            $("#checkall").change(function(){
                if($(this).is(":checked")){
                    $(".list_check").attr("checked", "checked");
                }else{
                    $(".list_check").attr("checked", "");
                }
            });
        });
        </script>';
    echo '<style type="text/css">
	#icon-options-general{background:url("'.$mca_plugin_location.'logo.jpg") no-repeat scroll 0 0 transparent; }
        #mca-admin h3 {margin-bottom: 3px; }
        #mca-api {display: block; clear: both;}
        #mca-admin fieldset {width:525px; margin: 10px 0; padding: 5px; background: #F1F1F1; border: 2px solid #ddd; -moz-border-radius: 5px;
            -webkit-border-radius: 5px; border-radius: 5px;}
        #mca-admin fieldset label {width: 200px; display: block; float: left; margin: 0 0 5px 5px;}
        #mca-admin fieldset label.title-label {font-size: 1em; font-weight: bold; width: auto; display: inline; float: none;}
        #mca-admin fieldset h4 {margin-top: 0; margin-bottom: 5px;}
        #mca-admin #checkallp {margin: 0;}
        #mca-admin p.checkbx {width: 225px; float: left; margin: 5px 0;}
        #save-list, #shortcode-list {margin: 10px; display: block; clear: both;}
        #save-list li, #shortcode-list li {clear: both; margin: 5px 0;}
	#mca-admin #submit {padding-left: 12px; padding-right: 12px; margin-bottom: 15px;}
        #credits {font-style: italic; margin-top: 20px;}
	</style>';
    echo '<div class="wrap" id="mca-admin">';
    echo '<div id="icon-options-general" class="icon32"></div>';
    echo '<h2>MailChimp Archives Settings</h2>';
    echo '<form method="post" action="">';
    echo '<h3>Add your MailChimp API key</h3>';
    echo '<input type="text" size="60" name="mca_api" id="mca_api" value="'.$current_mca_api.'" />';
    echo '<fieldset>';
    echo '<h4>Which list(s) would you like to use?</h4>';
    echo '<p id="checkallp">Check All <input type="checkbox" name="checkall" id="checkall" value="" /></p>';
    $lists = $mca_api->lists();
    if($lists) {
        foreach($lists as $list) {
            echo '<p class="checkbx"><input type="checkbox" name="mca_lists[]" class="list_check" value="'.$list['id'].'"';
            if(is_array($current_mca_lists)) {
                if(in_array($list['id'], $current_mca_lists)) {
                    echo ' checked="checked" ';
                }
            }
            echo '/>'.$list['name'].'</p>';
        }
    } else {
        echo '<p>Please enter your API key and update your settings to see your lists</p>';
    }
    echo '</fieldset>';
    echo '<fieldset>';
    echo '<label class="title-label">Would you like to save your archives to this WordPress site? </label>';
    echo '<input type="checkbox" name="mca_save"';
    if ($current_save == "on") {
        echo ' checked="checked"';
    }
    echo '/>';
    echo '<ul id="save-list">';
    echo '<li><label>Save to which category? </label>';
    wp_dropdown_categories('hide_empty=0&selected='.$current_cat);
    echo '</li>';
    echo '<li><label>Assign to which author? </label>';
    wp_dropdown_users('selected='.$current_author);
    echo '</li>';
    echo '<li><label>Save as which status? </label>';
    echo '<select name="mca_status">';
    echo '<option value="'.$current_status.'">'.$current_status.'</option>';
    echo '<option value="draft">draft</option>';
    echo '<option value="pending">pending</option>';
    echo '<option value="publish">publish</option>';
    echo '</select>';
    echo '</li>';
    echo '<li><label>Save as which type? </label>';
    echo '<select name="mca_type">';
    echo '<option value="'.$current_type.'">'.$current_type.'</option>';
    echo '<option value="post">post</option>';
    echo '<option value="page">page</option>';
    echo '</select>';
    echo '</li></ul>';
    echo '</fieldset>';
    echo '<fieldset>';
    echo '<h4>Or you can always just display your archives by adding the shortcode [mc_archives] to any page.</h4>';
    echo '<ul id="shortcode-list">';
    echo '<li><label>Time interval you would like your archive list to be grouped.</label>';
    echo '<select name="mca_interval">';
    if ($current_time_interval == 'month') {
        echo '<option value="month">Current - Month</option>';
    }else {
        echo '<option value="year">Current - Year</option>';
    }
    echo '<option value="year">Year</option>';
    echo '<option value="month">Month</option>';
    echo '</select>';
    echo '</li>';
    echo '<li><label>Open archives in a new window (target="_blank")? </label>';
    echo '<input type="checkbox" name="mca_target"';
    if ($current_target == "on") {
        echo ' checked="checked"';
    }
    echo '/>';
    echo '</li>';
    echo '<li><label>Show/Hide Toggle on Lists? </label>';
    echo '<input type="checkbox" name="mca_js"';
    if ($current_js == "on") {
        echo ' checked="checked" ';
    }
    echo '/>';
    echo '</li></ul>';
    echo '</fieldset>';
    echo '<input type="submit" name="submit" id="submit" value="update settings" class="button-primary" />';
    echo '</form>';
    echo '<form method="POST" action="">';
    echo '<input type="submit" name="update_records" value="update campaigns" class="button-secondary" />';
    echo '</form>';
    echo '<p id="credits">Developed by <a href="http://markparolisi.com" target="_blank">Mark Parolisi</a> -- Sponsored by <a href="http://cloudburstconsulting.com/" target="_blank">CloudBurst Consulting</a></p>';
    echo '</div>';
}

//enable JS show/hide
function add_mca_js() {
    wp_enqueue_script( 'mca_js', WP_PLUGIN_URL . '/mail-chimp-archives/mca.js', array('jquery'), '1.0', 'true' );
}

$mca_js = get_option(MCA_JS_OPT);
if ($mca_js == 'on') {
    add_action('template_redirect', 'add_mca_js');
}

//Add shortcode support
add_shortcode('mc_archives', 'mca_show_the_archives');

//Deactivate
register_deactivation_hook(__FILE__, 'mca_deactivation');

function mca_deactivation() {
    wp_clear_scheduled_hook('mca_savedata_event');
}
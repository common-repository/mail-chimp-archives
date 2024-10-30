<?php
function mca_insert_function() {
    global $wpdb;
    global $mca_api;
    $current_cat    = get_option(MCA_CAT_OPT);
    $current_author = get_option(MCA_AUTHOR_OPT);
    $current_status = get_option(MCA_STATUS_OPT);
    $current_type   = get_option(MCA_TYPE_OPT);
    //Get the array of all of the Camp ID's we have in the system already.
    $camps_data     = get_option(MCA_CAMPDATA_OPT);
    //Create an array of the posts that have our Camp ID postmeta (kinda loopy--needs update to use the the DB returned nested array)
    $camp_meta_r    = array();
    $mca_postmetas  = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key = '".MCA_POSTMETA."'");
    if($mca_postmetas) {
        foreach($mca_postmetas as $mca_postmeta) {
            $mca_id = $mca_postmeta->meta_value;
            array_push($camp_meta_r, $mca_id);
        }
        //This is just for testing
        update_option("mca_postmeta", $mca_postmetas);
    }

    //loop through our systems Camp ID's and make posts when needed
    if($camps_data) {
        foreach ($camps_data as $camp) {
            $camp_id = $camp['id'];
            if(!in_array($camp_id, $camp_meta_r, false)) {
                $camp_title = $camp['subject'];
                $send_time  = $camp['create_time'];
                $retval     = $mca_api->campaignContent($camp_id);
                $html       = $retval['html'];
                //cut out the <head> section of the email
                /*$html_start = strpos($html, '</title>');
				$html_l = strlen($html);
				if ($html_start) { //make sure there is a <head> section
				$html = substr($html, $html_start, $html_l);
				}*/
                //$post_content =  '<frame src="'.$html.'"</frame>';
                $post_content           = $html;
                $post                   = array();
                $post['post_title']     = $camp_title;
                $post['post_content']   = $post_content;
                $post['post_status']    = $current_status;
                $post['post_author']    = $current_author;
                $post['post_date']      = $send_time;
                $post['post_type']      = $current_type;
                $post['post_category']  = array($current_cat);
                $postID                 = wp_insert_post($post);
                //adding a unique custom meta field to check against duplicates
                add_post_meta($postID, MCA_POSTMETA, $camp_id);
            }
        }
    }
}
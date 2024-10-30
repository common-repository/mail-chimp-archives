<?php
//Fetch and Display the HTML from Mail Chimp
function get_mca_archive() {
    global $mca_api;
    $cid    = $_GET['mc_cid'];
    $retval = $mca_api->campaignContent($cid);
    $html   = $retval['html'];
    echo $html;
    exit;
}

if (isset($_GET['mc_cid'])) { //if the user has selected an archive, go get it
    add_action('template_redirect', 'get_mca_archive');
}

//List the entire Archive
function mca_show_the_archives() {
    //List Archives
    $current_time_interval = get_option(MCA_INT_OPT);
    $target                = get_option(MCA_TARGET_OPT);
    $site_url              = get_option('siteurl');
    $return = '';
    //define our target option
    if ($target == 'on') {
        $target = 'target="_blank"';
    }else {
        $target = '';
    }
    $time_group = array();
    $camps_data = get_option(MCA_CAMPDATA_OPT);
    if(is_array($camps_data)) {
        foreach ($camps_data as $c) {
            //parse out the creation date in different formats
            $create_time        = $c['create_time'];
            $create_date_r      = explode(" ", $create_time);
            $create_day         = $create_date_r[0];
            $create_day_r       = explode("-", $create_day);
            $create_month       = $create_day_r[1];
            $create_year        = $create_day_r[0];
            $create_monthyear   = "".$create_month."-".$create_year."";
            //get user specified time interval
            $time_interval_option = get_option(MCA_INT_OPT);
            if ($current_time_interval == "month") {
                $time_interval = $create_monthyear;
            }else {
                $time_interval = $create_year;
            }
            if (!in_array($time_interval, $time_group)) {
                $return .= '</ul>';
                $return .= "<h3 class='time_interval' id='title_".$c['id']."'>$time_interval</h3>\n";
                $return .= "<ul class='mailing_group' id='group_".$c['id']."'>";
                //add this date to our time interval group
                array_push($time_group, $time_interval);
            }
            //list the link to the archive entry
            $return .= "<li class='archive_entry' id='".$c['id']."'><a href='". $site_url ."?mc_cid=".$c['id']."' ".$target." />".$c['subject']."</a></li>\n";
        }
        return $return;
    }
}
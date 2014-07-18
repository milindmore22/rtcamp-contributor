<?php
/*
Plugin Name: rtcamp-contribuitor
Plugin URI: http://milindmore22.blogspot.com/
Description: the plugin will add metabox for each post to add contributors
Author: Milind More
Version: 1.0
Author URI : http://milindmore22.blogspot.com/
Licence: GLP V2
*/

/**
 * if is admin then require
 */
if(is_admin()){
	require_once plugin_dir_path(__FILE__).'rtcamp-contributor-admin.php';
}

/**
 * Content filter hook to appends new div for contributors 
 */
add_filter("the_content", "add_contributors");

function add_contributors($content){
	$rtcamp_contributors=get_post_meta(get_the_ID(),"rtcamp_contributor",true);
	$rtcamp_contributor_count=get_post_meta(get_the_ID(),"rtcamp_contributor_count",true);
	$rtcamp_contributor_total=get_post_meta(get_the_ID(),"total_count",true);
	
	//var_dump($rtcamp_contributors);
	//var_dump($rtcamp_contributor_count);
	if($rtcamp_contributors){
	$contributor_box="
	<div class='clear'>
			<h3>Contributors</h3>
			";
			
				foreach ($rtcamp_contributors as  $user_id){
					$user=get_user_by("id", $user_id);
					$contributor_box.="<div class='contributor_box'>";
					$contributor_box.= "<a href='".get_author_link(false, $user_id)."'>".get_avatar( $user_id, $size = '44')."<span>".$user->user_nicename."</span></a>";
					$usercount=$rtcamp_contributor_count[$user_id];
					
					//echo $rtcamp_contributor_total;
					if($rtcamp_contributor_total!=0){
						$percentage_contribution= $usercount / $rtcamp_contributor_total * 100;
					}
					$contributor_box.="Contributed : ".round($percentage_contribution,2) ." %";
					$contributor_box.="</div>";
				}
					
	$contributor_box.="</div>";
	
	return $content.$contributor_box;
	}else{
		return $content;
	}
}

/**
 *  enque script and style in header 
 */
add_action("wp_head", "load_contributor_header_files");

function load_contributor_header_files(){
	wp_register_style("contributor-css", plugin_dir_url(__FILE__)."/css/rtcamp-contributor.css");
	wp_enqueue_style("contributor-css");
}

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
if(is_admin()){
	require_once plugin_dir_path(__FILE__).'rtcamp-contributor-admin.php';
}


add_filter("the_content", "add_contributors");

function add_contributors($content){
	$rtcamp_contributors=get_post_meta(get_the_ID(),"rtcamp_contributor",true);
	if($rtcamp_contributors){
	$contributor_box="
	<div class='clear'>
			<h3>Contributors</h3>
			";
			
				foreach ($rtcamp_contributors as  $user_id){
					$user=get_user_by("id", $user_id);
					$contributor_box.="<div class='contributor_box'>";
					$contributor_box.= "<a href='".get_author_link(false, $user_id)."'>".get_avatar( $user_id, $size = '44')."<span>".$user->user_nicename."</span></a>";
					$contributor_box.="</div>";
				}
					
	$contributor_box.="</div>";
	
	return $content.$contributor_box;
	}else{
		return $content;
	}
}

add_action("wp_head", "load_contributor_header_files");

function load_contributor_header_files(){
	wp_register_style("contributor-css", plugin_dir_url(__FILE__)."/css/rtcamp-contributor.css");
	wp_enqueue_style("contributor-css");
}
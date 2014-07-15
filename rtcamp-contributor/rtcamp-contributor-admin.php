<?php
if ( is_admin() ){
/**
 * Add meta box
 */
add_action( 'admin_init', 'post_contribuitor_metabox' );

function post_contribuitor_metabox(){
	add_meta_box("contributors", "Contributors", "display_contributors_box","post","normal","high");

}

/**
 *  Call back for metabox
 */
function display_contributors_box($post){
	//retrive presaved contributors
	$rtcamp_contributors=get_post_meta($post->ID,"rtcamp_contributor",true);

	$args = array(
			'blog_id'      => $GLOBALS['blog_id'],
			'role'         => 'author',
			'meta_key'     => '',
			'meta_value'   => '',
			'meta_compare' => '',
			'meta_query'   => array(),
			'include'      => array(),
			'exclude'      => array(),
			'orderby'      => 'login',
			'order'        => 'ASC',
			'offset'       => '',
			'search'       => '',
			'number'       => '',
			'count_total'  => false,
			'fields'       => 'all',
			'who'          => ''
	);
	$users=get_users( $args );

	foreach ($users as $user){
		?>
			<input type="checkbox" name="rtcamp_contributor[]" value="<?php echo $user->ID;?>" <?php if($rtcamp_contributors){if(in_array($user->ID, $rtcamp_contributors)){echo "checked='checked'";}}?>/><?php echo $user->user_nicename;?>
		<?php 
	}
}

/**
 * Saving POSTMETA with save_post action
 */
 
add_action("save_post", "save_post_contributors",10,2);

function save_post_contributors($post_id,$post){
	if($post->post_type=="post"){ // you can add diffrent post type in case you registered post type here
		if(isset($_POST['rtcamp_contributor']) && $_POST['rtcamp_contributor']!=''){
			update_post_meta($post_id, "rtcamp_contributor", $_POST['rtcamp_contributor']);
		}else{
			delete_post_meta($post_id, "rtcamp_contributor");
		}
	}
}
}
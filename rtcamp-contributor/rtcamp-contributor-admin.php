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
	$post_count_pre=get_post_meta($post->ID,"post_count_pre",true);
	?>
	<div id="contributor_metabox">
			<input type="text" name="tag" value="" class="tag" />
			<div class="tagcloud">
				<?php
				if($rtcamp_contributors){
					foreach ($rtcamp_contributors as $user_id){
						$user=get_user_by("id", $user_id);
						?>
						<div class='tagval'><?php echo $user->user_login;?><span class='removebox'></span><input type='hidden' name='rtcamp_contributor[]' value='<?php echo $user_id;?>'></div>
			<?php } 
				}
				?>
			</div>
			<input type="hidden" name="post_count_pre" value="<?php echo $post_count_pre;?>" />
	</div>
	<?php 
}

/**
 * Content Edit pre action hook to get previous world count
 */
add_action("content_edit_pre", "edit_pre_hook",10,1);
function edit_pre_hook($post_content_pre){
	$precount=str_word_count($post_content_pre);
	update_post_meta(get_the_ID(),"post_count_pre", $precount);
	return $post_content_pre;
}

/**
 * Saving POSTMETA with save_post action
 */
 
add_action("save_post", "save_post_contributors",10,2);

function save_post_contributors($post_id,$post){
	
	if($post->post_type=="post"){ // you can add diffrent post type in case you registered post type here
		
		$post_count_pre=get_post_meta($post->ID,"post_count_pre",true); // previous word count
		$rtcamp_contributor_count=get_post_meta($post->ID,"rtcamp_contributor_count",true); // authorwise word count in array
		$user_id=get_current_user_id(); // current author id
		$post_count_new=str_word_count($post->post_content); // new word count
		$authorCount=$post_count_new - $post_count_pre; // author word count
		
		/**
		 * check if user is present in list
		 * created array of word count with $array[$user_id]=>$author_word_count
		 */
			if($rtcamp_contributor_count && array_key_exists($user_id,$rtcamp_contributor_count)){
				// if user is present will just add his new word count and proceed further
				$rtcamp_contributor_count[$user_id]=$rtcamp_contributor_count[$user_id] + $authorCount;
			}else{
				//else will add new array element
				$rtcamp_contributor_count[$user_id]=$authorCount;
			}
		
		if(isset($rtcamp_contributor_count) && $rtcamp_contributor_count!=''){
			update_post_meta($post->ID, "rtcamp_contributor_count", $rtcamp_contributor_count);
		}else{
			delete_post_meta($post->ID, "rtcamp_contributor_count");
		}
	
		
		if(isset($post_count_new) && $post_count_new!=''){
			update_post_meta($post->ID, "total_count", $post_count_new);
		}else{
			delete_post_meta($post->ID, "total_count");
		}
		
		// add automatically user to contribution if his not present
		if($_POST['rtcamp_contributor']){
			if(!in_array($user_id, $_POST['rtcamp_contributor'])){
				array_push($_POST['rtcamp_contributor'], $user_id);
			}
		}else{
			$_POST['rtcamp_contributor']=array();
			array_push($_POST['rtcamp_contributor'], $user_id);
			
		}
		
		if(isset($_POST['rtcamp_contributor']) && $_POST['rtcamp_contributor']!=''){
			
			update_post_meta($post_id, "rtcamp_contributor", $_POST['rtcamp_contributor']);
		}else{
			delete_post_meta($post_id, "rtcamp_contributor");
		}
	 }
	}	
}

/**
 * Add other capability to edit other users post
 */
add_action( 'admin_init', 'add_author_edit_post_caps');
function add_author_edit_post_caps() {
	// gets the author role
	$role = get_role( 'author' );

	// This only works, because it accesses the class instance.
	// would allow the author to edit others' posts
	$role->add_cap( 'edit_others_posts' );
}

/**
 * load scripts and styl nessary for backend 
*/
add_action("admin_head", "rtcamp_contributor_load_header");

function rtcamp_contributor_load_header(){
		
	wp_register_style("jquery-tagedit-css", plugin_dir_url(__FILE__)."css/jquery.tagedit.css");
	
	wp_enqueue_script("jquery");
	wp_enqueue_script("jquery-ui-core");
	wp_enqueue_script("jquery-ui-widge");
	wp_enqueue_script("jquery-ui-autocomplete");
	
	
	wp_enqueue_style("jquery-tagedit-css");
	
	// Wordpress Admin Ajax
	wp_enqueue_script( 'rtcamp_contributor_autocomplete', plugin_dir_url(__FILE__)."js/rtcamp-contributor-admin.js", array( 'jquery', 'jquery-form', 'json2' ), false, true );
	wp_localize_script(
	'rtcamp_contributor_autocomplete',
	'rtcamp_contributor_object',
	array(
	'ajaxurl' => admin_url( 'admin-ajax.php' ),
	'myajax_nonce' => wp_create_nonce( 'rtcamp_contributor_nonce' ),
	'action' => 'rtcamp_contributor_submit'
	)
	);
}
/**
 *  Action handles ajax request
 */
// Callback
function get_authors_list() {
	global $wpdb;
	$prefix=$wpdb->prefix;
	// This function should query the database and get results as an array of rows:
	// GET the recieved data: 'term' (what has been typed by the user)
	$term = $_GET['term'];
	$query="SELECT u.id as id, u.user_login as term, u.user_login as value FROM ".$prefix."users u WHERE user_login LIKE '%".$term."%' AND u.id!=".get_current_user_id();
	
	$result=$wpdb->get_results($query,ARRAY_A);
	
	// echo JSON to page  and exit.
	$response = $_GET["callback"]."(". json_encode($result) .")";
	echo $response;
	exit;
}
add_action( 'wp_ajax_rtcamp_contributor_submit', 'get_authors_list' );
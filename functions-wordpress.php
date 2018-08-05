<?php
/* II.- PLUGINS */
/* Contact Form 7 CF7 */

// 1. - Control de envio de formulario , agregar nuevos correos
add_action('wpcf7_before_send_mail','project_name_dynamic_addcc');
function project_name_dynamic_addcc($WPCF7_ContactForm){
		$form_id='00000'; /* ID de formulario */
	    if ($form_id == $WPCF7_ContactForm->id()) {
        $currentformInstance  = WPCF7_ContactForm::get_current();
        $contactformsubmition = WPCF7_Submission::get_instance();
        if ($contactformsubmition) {
            $cc_email = array();
            $data = $contactformsubmition->get_posted_data();
            /* -------------- */
            /*
            $post_id = $data['ID']; <-- name of input field
            */
			$email1='ejemplo@ejemplo.com';
			$email2='ejemplo2@ejemplo2.com';
			if ($email1){array_push($cc_email, $email1);}
			if ($email2){array_push($cc_email, $email2);}

			$cclist = implode(', ',$cc_email);
            if (empty($data)){ return; }

            $mail = $currentformInstance->prop('mail');

            if(!empty($cclist)){
                $mail['additional_headers'] = "Cc: $cclist";
            }

            $currentformInstance->set_properties(array(
                "mail" => $mail
            ));

            // return current cf7 instance
            return $currentformInstance;
        }
	}
	return true;
}

// 2. - Control de envio de formulario , Guardo en la BD
add_action('wpcf7_before_send_mail','project_name_catch_beforme_send');
function project_name_catch_beforme_send($WPCF7_ContactForm){
		 $log_file='loginsert';
		$form_id='00000'; /* ID de formulario */
	    if ($form_id == $WPCF7_ContactForm->id()) {
        $currentformInstance  = WPCF7_ContactForm::get_current();
        $contactformsubmition = WPCF7_Submission::get_instance();
        if ($contactformsubmition) {
            $cc_email = array();
            $data = $contactformsubmition->get_posted_data();
            /* -------------- */
            /*
            $post_id = $data['ID']; <-- name of input field
            */
			try {

				global $wpdb;

				$wpdb->insert( 
					'vista_cotizaciones', 
					array( 
						'rut' => $data['rut'],
						'fecha' => DATE('Y-m-d H:i:s')
					), 
					array( 
						'%s','%s'
					) 
				);
				
			} catch (Exception $e) {

				$out = fopen(get_template_directory()."/".$log_file.".log", "w");
				$file_data = '$e' ;
				fwrite($out, $file_data);
				fclose($out);
				
			}
            return $currentformInstance;
        }
	}
	return true;
}

function project_name_getCustomPosts($post_name, $limit){
	if (!$post_name){ return false; }
	if (!$limit){ $limit = -1; }
	$query = array(
		'post_type' => $post_name,
		'post_status' => 'publish',
		'posts_per_page' => $limit,
		'order_by' => 'date',
		'order' => 'DESC'
	);
}

function project_name_theContent($content){
	if( !$content ){ return false; }
	echo apply_filters( 'the_content', $content );
}


function project_name_getCustomPostsFull($post_name,$offset, $category, $category_name,$include, $exclude, $meta_key, $meta_value,$limit,$post_mime_type,$post_parent,$author,$author_name,$suppress_filters, $fields,$post_status,$orderby,$order){
	if (!$post_name){ $post_name = 'post'; }
	if (!$offset){ $offset = 0; }
	if (!$category){ $category = ''; }
	if (!$category_name){ $category_name = ''; }
	if (!$include){ $include = ''; }
	if (!$exclude){ $exclude = ''; }
	if (!$meta_key){ $meta_key = ''; }
	if (!$meta_value){ $meta_value = ''; }
	if (!$post_mime_type){ $post_mime_type = ''; }
	if (!$post_parent){ $post_parent = ''; }
	if (!$author){ $author = ''; }
	if (!$author_name){ $author_name = ''; }
	if (!$suppress_filters){ $suppress_filters = true; }
	if (!$post_status){ $post_status = 'publish'; }
	if (!$orderby){ $orderby = 'date'; }
	if (!$order){ $order = 'DESC'; }
	if (!$fields){ $fields = ''; }
	if (!$limit){ $limit = -1; }
	$query = array(
		'posts_per_page'   => $limit,
		'offset'           => $offset,
		'category'         => $category,
		'category_name'    => $category_name,
		'orderby'          => $orderby,
		'order'            => $order,
		'include'          => $include,
		'exclude'          => $exclude,
		'meta_key'         => $meta_key,
		'meta_value'       => $meta_value,
		'post_type'        => $post_name,
		'post_mime_type'   => $post_mime_type,
		'post_parent'      => $post_parent,
		'author'	   => $author,
		'author_name'	   => $author_name,
		'post_status'      => $post_status,
		'suppress_filters' => $suppress_filters,
		'fields'           => $fields,
	);
}

/*carga jquery*/
wp_enqueue_script("jquery");

/*Remueve la version de wordpress*/
function project_name_no_generator() { return ''; } 
add_filter( 'the_generator', 'project_name_no_generator' );

/*Contador de vistas*/
function project_name_getPostViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 View";
    }
    return $count.' Views';
}

// function to count views.
function project_name_setPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}


// Add it to a column in WP-Admin
add_filter('manage_posts_columns', 'project_name_posts_column_views');
add_action('manage_posts_custom_column', 'project_name_posts_custom_column_views',5,2);
function project_name_posts_column_views($defaults){
    $defaults&#91;'post_views'&#93; = __('Views');
    return $defaults;
}
function project_name_posts_custom_column_views($column_name, $id){
	if($column_name === 'post_views'){
        echo project_name_getPostViews(get_the_ID());
    }
}

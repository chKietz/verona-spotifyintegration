<?php 
/*	Plugin Name: Verona - SpotifyIntegration
	Author: Christian Kietzmann
	Author URI:   mailto:chris.kietzmann@gmx.de
	Description: Einbindung von Spotify in der Marketingplattform - Abschlussarbeit Bachelor
	Version: 1.0
*/
register_activation_hook( __FILE__, 'register_settings' );
register_activation_hook( __FILE__, 'create_callback');
register_activation_hook( __FILE__, 'vspot_create_table');

define('IMGURL', plugin_dir_url(__FILE__) . 'assets/Spotify_Icon.png'); 
define('LOADER', plugin_dir_url(__FILE__) . 'assets/ajax-loader.gif');
define('VSPOTIFY_TABLE_NAME', 'spotify-ba-user-reads');
define('VSPOTIFY_API_URL', 'https://ajec5zkbxf.execute-api.eu-west-1.amazonaws.com/ba_dev/table');
define('VSPOTIFY_OPTION_GROUP', 'vspotify_option_group');

add_action( 'wp_ajax_show_ukv_tops', 'show_ukv_tops' ); 
add_action( 'wp_ajax_nopriv_show_ukv_tops', 'show_ukv_tops' ); 

include_once(plugin_dir_path( __FILE__ ) . '/api.php');

register_deactivation_hook( __FILE__, 'vspot_deactivation' );

if (!defined('ABSPATH')) die();

function enqueue_scripts(){
	wp_enqueue_script('jquery');
	wp_enqueue_script( 'spotify_ba-script', plugin_dir_url( __FILE__ ) . 'js/script.js');
	wp_register_style( 'spotify_ba-css', plugin_dir_url( __FILE__ ) . 'css/style.css');
	wp_enqueue_style( 'spotify_ba-css');
	wp_localize_script( 'spotify_ba-script', 'updater', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'callback' => get_option('vspotify_callback'),
		'client_id' => get_option('vspotify_client'),
		'loader_url' => LOADER
    ));
}

function register_settings(){
	$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';
	$host = $_SERVER['HTTP_HOST'];
	register_setting( VSPOTIFY_OPTION_GROUP, "vspotify_client");
	add_option( 'vspotify_client', '24e3b411414f4fb597b6f383722fd9ee');
	register_setting( VSPOTIFY_OPTION_GROUP, "vspotify_api-key");
	add_option( 'vspotify_api-key', 'Q55fpB8aPx5L9WcnlPXaIpWzPNP4fEl2R0j8exL4');
}

function create_callback(){
	$callback = get_page_by_title("spotifycallback");
	if(!$callback){
	 $my_post = array(
          'post_title'    => 'spotifycallback',
          'post_content'  => 'Sofern Sie sich angemeldet haben, werden Ihre Daten nun verarbeitet. Das Fenster schließt sich automatisch.',
          'post_status'   => 'publish',
          'post_author'   => '',
          'post_category' => '',
          'post_type'     => 'page'
          );
		  wp_insert_post( $my_post );
		  sleep(2);
	}else{
		$id = $callback->ID;
		$callback->post_status = 'publish';
        $id = wp_update_post( $callback );
	}

}

function vspot_deactivation(){
	$post = get_page_by_title( 'spotifycallback', OBJECT, 'page' );
	$id = $post->ID;
	wp_delete_post($id);
	$body = array(
		"table-name" => VSPOTIFY_TABLE_NAME,
	);
	$headers = array(
		"x-api-key" => get_option('vspotify_api-key'),
		"Content-Type" => "application/json"
	);
	sApiCustom(VSPOTIFY_API_URL, 'DELETE', $headers, $body);
	delete_option('vspotify_client');
	delete_option('vspotify_api-key');
	delete_option('vspotify_callback');
	
}

function plug_init(){
	if(get_page_by_title("spotifycallback") == NULL){
		create_callback();
	}
	if(is_home() || is_page() || is_single()){
		if(!wp_is_mobile() && is_user_logged_in()){
			include_once plugin_dir_path(__FILE__) . 'html/spotify_html.php';
			enqueue_scripts();
			$uri = $_SERVER['REQUEST_URI'];
			if (strpos($uri, '/spotifycallback/?access_token=') !== false && strpos($uri, '&token_type=Bearer') !== false){
				add_filter('the_content', 'vspotify_login_success');
				check_token();
			}
		}
	}      
};
add_action( 'wp_enqueue_scripts', 'plug_init' );
                                                                               
function check_token(){
	global $post;
	$id = $post->ID;
	parse_str(str_replace('/spotifycallback/?', '', $_SERVER['REQUEST_URI']), $token);
	get_user_top_read($token);
	return True;
	wp_die();
}

function get_user_top_read($token){
	$token = $token;
	$headers = array("Authorization" => $token['token_type'] . " " . $token['access_token']);
	$q = sApiCustom('https://api.spotify.com/v1/me/top/tracks?limit=3&time_range=short_term', 'GET', $headers);
	$body = array();
	foreach($q->items as $item){
		$body[] = array(
			'table-name' => VSPOTIFY_TABLE_NAME,
			'artist-name' => $item->artists[0]->name,
			'track-title' => $item->name,
			'album-title' => $item->album->name,
			'song-url' => $item->external_urls->spotify,
			'album-img' => $item->album->images[0]->url,
			'user-id' => base64_encode(get_current_user_id())
		);
	}

	put_user_top_reads($body);
	sleep(5);
}

function put_user_top_reads($body){
	$headers = array(
		"x-api-key" => get_option('vspotify_api-key'),
		"Content-Type" => "application/json"
	);
	$table = check_ddb_table($body, $headers);
	if($table != True){
		echo "<script type='text/javascript'>window.alert('Die Datenbank ist nicht erreichbar - bitte kontaktieren Sie ckietzmann@oev.de');</script>";
		wp_die();
	}else{
		$body = $body;
		$q = sApiCustom(VSPOTIFY_API_URL, 'PUT', $headers, $body);
		sleep(5);
		echo "<script type='text/javascript'>window.close();</script>";
	}
	wp_die();
}

function check_ddb_table($body, $headers){
	$headers = $headers;
	$body = $body;
	$q = sApiCustom(VSPOTIFY_API_URL, 'POST', $headers, $body);
	if ($q == $body[0]['table-name'] . " vorhanden" || $q == $body[0]['table-name'] . " erstellt"){
		return True;
	}else{
		return False;
	}
	wp_die();
}

function vspot_create_table(){
	$body[0]['table-name'] = VSPOTIFY_TABLE_NAME;
	$headers = array(
		"x-api-key" => get_option('vspotify_api-key'),
		"Content-Type" => "application/json"
	);
	$q = sApiCustom(VSPOTIFY_API_URL, 'POST', $headers, $body);
}

function show_ukv_tops(){
	$headers = array(
		"x-api-key" => get_option('vspotify_api-key'),
		"Content-Type" => "application/json"
	);
	$body = array(
			'table-name' => VSPOTIFY_TABLE_NAME,
	);
	$q = sApiCustom(VSPOTIFY_API_URL, 'GET', $headers, $body);
	sleep(3);
	$resArr = array();
	if($q != NULL){
		foreach ($q as $item){
			$resArr[] = array(
				"artistName" => $item->artistName,
				"trackTitle" => $item->trackTitle,
				"albumTitle" => $item->albumTitle,
				"songUrl" => $item->songUrl,
				"albumImg" => $item->albumImg
			);
		}
	}else{
		$resArr[0] = array(
			"artistName" => "",
			"trackTitle" => "<div style='color: rgb(13, 87, 166); text-align: center;'>Keine Datenbankeinträge.</div>",
			"albumTitle" => "",
			"songUrl" => "",
			"albumImg" => ""
		);
	}
	ob_start();
	include( plugin_dir_path(__FILE__) . '/html/tops_content.php' );
	$output = ob_get_contents();
	ob_end_clean();
	echo $output;
	wp_die();
}

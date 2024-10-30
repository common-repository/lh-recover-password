<?php 
/*
Plugin Name: LH Recover Password
Plugin URI: https://lhero.org/portfolio/lh-recover-password/
Description: Creates a front end recover password form
Version: 1.13
Author: Peter Shaw
Author URI: https://shawfactor.com/

== Changelog ==


License:
Released under the GPL license
http://www.gnu.org/copyleft/gpl.html

Copyright 2013  Peter Shaw  (email : pete@localhero.biz)


This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published bythe Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class LH_recover_password_plugin {

var $filename;
var $options;
var $opt_name = 'lh_recover_password-options';
var $page_id_field = 'lh_recover_password-page_id';
var $email_title_field = 'lh_recover_password-email_title';
var $email_message_field = 'lh_recover_password-email_message';
var $namespace = 'lh_recover_password';
var $hidden_field_name = 'lh_recover_password-hidden_field_name';

//this function will only create a login page if one is not already set in options

private function create_page() {

$options = get_option($this->opt_name);

if (!$page = get_page($options[$this->page_id_field])){


$page['post_type']    = 'page';
$page['post_content'] = '[lh_recover_password_form]';
$page['post_status']  = 'publish';
$page['post_title']   = 'Recover Password';

if ($pageid = wp_insert_post($page)){



$options[$this->page_id_field] = $pageid;

$options[$this->email_title_field] = "Password reset email";

$options[$this->email_message_field] = "Hello [lh_personalised_content]%display_name%[/lh_personalised_content],

Someone requested that the password be reset for the following account:

".get_site_url()."

Username: [lh_personalised_content]%user_login%[/lh_personalised_content]

If this was a mistake, just ignore this email and nothing will happen.

To reset your password, visit the following address:

[lh_personalised_content]%reset_link%[/lh_personalised_content]";



if (update_option($this->opt_name, $options )){


}

}
}
}




function lh_recover_password_form_shortcode_output(){
ob_start();

if ( is_user_logged_in() ) {

?>

<p>Your are already logged in, there is no need to reset your password</p>

<?php


} else {

if (($GLOBALS['lh_recover_password-result']) and (is_email($GLOBALS['lh_recover_password-result']))){

echo "<strong>";

_e('A password reset email has been sent to ');

echo $GLOBALS['lh_recover_password-result'];

echo "</strong>";

}


?>

<form name="lh_recover_password-front_end-form" id="lh_recover_password-front_end-form" action="" method="post" data-lh_recover_password-front_end-nonce="<?php echo wp_create_nonce( 'lh_recover_password-front_end-form-nonce');  ?>">

<p>
<!--[if lt IE 10]><label for="lh_recover_password-email"><?php _e('Your email') ?></label><br/><![endif]-->
<input type="email" name="lh_recover_password-email" id="lh_recover_password-email"\ class="input" value="" size="20" placeholder="yourname@email.com" required="required" />
</p>
<p>
<input type="submit" name="lh_recover_password-front_end-submit" id="lh_recover_password-front_end-submit" class="button-primary" value="<?php esc_attr_e('Recover Password'); ?>" />
</p>
<span id="lh_recover_password-confirm_message" class="confirmMessage">

<?php

if ( is_wp_error( $GLOBALS['lh_recover_password-result'] ) ) {

echo '<strong>ERROR</strong>:';

 foreach ( $GLOBALS['lh_recover_password-result']->get_error_messages() as $error ) {


            echo $error . '<br/>';

}


}


?>


</span>

<input name="lh_recover_password-front_end-form-nonce" id="lh_recover_password-front_end-form-nonce" value="" type="hidden" />
</form>
<?php

wp_enqueue_script('lh_recover_password_script', plugins_url( '/assets/lh-recover-password.js' , __FILE__ ), array(), '1.0', true  );

}



$output = ob_get_contents();
ob_end_clean();
return $output;

}


public function register_shortcodes(){

add_shortcode('lh_recover_password_form', array($this,"lh_recover_password_form_shortcode_output"));

}


public function action_recovery(){



if ( (wp_verify_nonce( $_POST['lh_recover_password-front_end-form-nonce'], "lh_recover_password-front_end-form-nonce")) and ($_POST['lh_recover_password-front_end-submit']) and ($_POST['lh_recover_password-email']) and ( !is_user_logged_in() )) {

if ($user = get_user_by( 'email', sanitize_user($_POST['lh_recover_password-email']) )){

update_user_meta( $user->ID, "lh_recover_password-flag", "yes");

wp_schedule_single_event(time(), 'lh_recover_password-sendemail');

$GLOBALS['lh_recover_password-result'] = $user->user_email;


} else {


$error = new WP_Error;

$error->add( 'unknown_email', 'The email was not recognised' );

$GLOBALS['lh_recover_password-result'] = $error;


}

}

}

public function send_email(){

 $args = array(
'meta_key'    => 'lh_recover_password-flag',
'number'       => '1',
);

$users = get_users( $args );

$user = empty ( $users[0] ) ? null : $users[0];

if ($user){

$headers = array('Content-Type: text/html; charset=UTF-8');

$message  = "<html><body>".wpautop($this->options[ $this->email_message_field ])."</body></html>";

wp_mail( $user->user_email, $this->options[ $this->email_title_field ], $message, $headers);

delete_user_meta( $user->ID, 'lh_recover_password-flag');


}


}

public function plugin_menu() {
add_options_page('LH Recover Password Options', 'Recover Password', 'manage_options', $this->filename, array($this,"plugin_options"));

}

public function plugin_options() {

if (!current_user_can('manage_options'))  {
		wp_die( __('You do not have sufficient permissions to access this page.') );
	}


   
 // See if the user has posted us some information
    // If they did, this hidden field will be set to 'Y'

if( isset($_POST[  $this->hidden_field_name ]) && $_POST[  $this->hidden_field_name ] == 'Y' ) {

if (($_POST[ $this->page_id_field ] != "") and ($page = get_page(sanitize_text_field($_POST[ $this->page_id_field ])))){

if ( has_shortcode( $page->post_content, 'lh_recover_password_form' ) ) {

$options[ $this->page_id_field ] = sanitize_text_field($_POST[ $this->page_id_field ]);

} else {

echo "shortcode not found";


}

}

if ($_POST[$this->email_title_field] != ""){

$options[ $this->email_title_field ] = sanitize_text_field($_POST[ $this->email_title_field ]);


}

if ($_POST[$this->email_message_field] != ""){

$options[ $this->email_message_field ] = wp_kses_post($_POST[ $this->email_message_field ]);


}


if (update_option( $this->opt_name, $options )){

$this->options = get_option($this->opt_name);


?>
<div class="updated"><p><strong><?php _e('Recover Password settings saved', $this->namespace ); ?></strong></p></div>
<?php

} 


}

  // Now display the settings editing screen

    echo '<div class="wrap">';

    // header

    echo "<h1>" . __('LH Recover Password Settings', $this->namespace ) . "</h21>";

    // settings form
    
    ?>

<form name="lh_recover_password-backend_form" method="post" action="">
<input type="hidden" name="<?php echo $this->hidden_field_name; ?>" id="<?php echo $this->hidden_field_name; ?>" value="Y" />

<p><label for="<?php echo $this->page_id_field; ?>"><?php _e("Recover Password Page ID;", $this->namespace ); ?></label>
<input type="number" name="<?php echo $this->page_id_field; ?>" id="<?php echo $this->page_id_field; ?>" value="<?php echo $this->options[ $this->page_id_field ]; ?>" size="10" /><a href="<?php echo get_permalink($this->options[ $this->page_id_field ]); ?>">Link</a>
</p>

<p><label for="<?php echo $this->email_title_field; ?>"><?php _e("Email Title;", $this->namespace ); ?></label>
<input type="text" name="<?php echo $this->email_title_field; ?>" id="<?php echo $this->email_title_field; ?>" value="<?php echo $this->options[ $this->email_title_field ]; ?>" size="50" />
</p>
<p>
<?php  wp_editor( $this->options[ $this->email_message_field ], $this->email_message_field, $settings = array() );  ?>
</p>

<p class="submit">
<input type="submit" name="lh_recover_password-backend_form-submit" class="button-primary" value="<?php esc_attr_e('Save Changes', $this->namespace) ?>" />
</p>

</form>



</div>

<?php


}

public function lostpassword_url( $lostpassword_url, $redirect ) {

$return = get_permalink($this->options[$this->page_id_field]);

return $return;

}

public function hide_title($title,$id){


if (in_the_loop() && is_singular() && $id == $this->options[$this->page_id_field]){


return '';


} else {

return $title;

}




}


// add a settings link next to deactive / edit
public function add_settings_link( $links, $file ) {

	if( $file == $this->filename ){
		$links[] = '<a href="'. admin_url( 'options-general.php?page=' ).$this->filename.'">Settings</a>';
	}
	return $links;
}




public function on_activate( $network){

global $wpdb;

  if ( is_multisite() && $network ) {
        // store the current blog id
        $current_blog = $wpdb->blogid;
        // Get all blogs in the network and activate plugin on each one
        $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
        foreach ( $blog_ids as $blog_id ) {
            switch_to_blog( $blog_id );
$this->create_page();
            restore_current_blog();
        }

    } else {

$this->create_page();

}


}


function __construct() {

$this->filename = plugin_basename( __FILE__ );
$this->options = get_option($this->opt_name);

add_action( 'init', array($this,"register_shortcodes"));
add_action( 'wp', array($this,"action_recovery"));
add_action('admin_menu', array($this,"plugin_menu"));
add_action('lh_recover_password-sendemail', array($this,"send_email"));
add_filter( 'lostpassword_url', array($this,"lostpassword_url"), 10, 2 );
add_filter('the_title', array( $this, 'hide_title' ),10,2);
add_filter('plugin_action_links', array($this,"add_settings_link"), 10, 2);

}


}


$lh_recover_password_instance = new LH_recover_password_plugin();
register_activation_hook(__FILE__, array($lh_recover_password_instance,'on_activate') , 10, 1);



function lh_recover_password_uninstall(){

delete_option('lh_recover_password-options');

}


register_uninstall_hook( __FILE__, 'lh_recover_password_uninstall' );


?>
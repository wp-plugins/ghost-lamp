<?php
/*
  Plugin Name: Ghost Lamp plugin
  Plugin URI: http://www.cifrawebtech.com
  Description: A plugin for Ghost Lamp users to activate Ghost Lamp on a WordPress website.
  Author: Vijay
  Version: 0.3beta
*/
?>
<?php
register_activation_hook(__FILE__,'ghostlamp_install');
register_deactivation_hook(__FILE__,'ghostlamp_uninstall');

// On installation create 3 option variables
function ghostlamp_install(){
	add_option('gl_domain','');
	add_option('gl_key','');
	add_option('gl_beta','');
}

//While uninstalling plugin delete value of 3 options
function ghostlamp_uninstall(){
	delete_option('gl_domain');
	delete_option('gl_key');
	add_option('gl_beta');
}

// Set up menu
add_action('admin_menu', 'ghostlamp_menu');
function ghostlamp_menu() { 			
	add_menu_page('Ghost Lamp', 'Ghost Lamp', 'manage_options', 'ghost_lamp', 'gl_form');
}

// Ghost Lamp form in admin to store key and domain
function gl_form(){
$gl_msg="";
if(isset($_POST['gl_domain'])){
	update_option('gl_domain',$_POST['gl_domain']);
	update_option('gl_key', $_POST['gl_key']);
	if(isset($_POST['gl_beta'])){
	  $gl_beta=1;
	}else{
	  $gl_beta=0;
	}
	update_option('gl_beta',$gl_beta);
	$gl_msg="Values added succcessfully!";
}
?>
<form method="POST">
<div class="wrap" id="gl_wrap">
<h3>Ghost Lamp</h3>
<lable class="gl_lbl">Your domain</lable>
<div class="gl_txt_wrap">
<input type="text" name="gl_domain" value="<?php echo get_option('gl_domain','');?>" class="gl_txt" required/>
</div>
<lable class="gl_lbl">Your access key</lable>
<div class="gl_txt_wrap">
<input type="text" name="gl_key" value="<?php echo get_option('gl_key','');?>" class="gl_txt" required/>
</div>
<div id="gl_submit">
<?php
$gl_beta=get_option('gl_beta','');
?>
Beta : <input type="checkbox" value="1" name="gl_beta" <?php if($gl_beta==1){echo "checked";}?>/>
<input type="image" src="/wp-content/plugins/ghostlamp/btn_save.png"/>
</div>
</div>
</form>
<?php if($gl_msg!=""){?>
<div id="gl_msg"><?php echo $gl_msg;?></div>
<?php }?>
<style type="text/css">
#gl_wrap{width:500px;margin-top: 60px;margin-left: 73px;} .gl_lbl{margin-top: 18px;float: left;margin-bottom: 8px;}
.gl_txt_wrap{width:300px;} .gl_txt{width:300px;border:1px solid #706A6A!important;}
#gl_submit{width:300px;margin-top:10px;} #gl_submit input[type='image']{float: right;height: 40px;padding: 0px;outline: none;}
#gl_msg{color: #008000;font-weight: bold;font-size: 15px;font-family: verdana;border: 1px solid #A29A9A;padding: 8px;
clear: both;width: 283px;margin-top: 23px;float: left;margin-left: 73px;}
</style>
<?php
}
?>

<?php
function data_attributes( $url ){
return $url ."/scripts/require.min.js' data-main='" . $url . "/scripts/main-b";
}
    // Inject script to admin footer
    add_action('in_admin_footer', 'inject_admin_footer');
    function inject_admin_footer () {
    	$currFile = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
        if($currFile == 'post.php' || $currFile == 'post-new.php') {
	    $gl_domain=get_option('gl_domain','');
	    $gl_key=get_option('gl_key','');
	    $gl_beta=get_option('gl_beta','');
	    if($gl_beta==1){$gl_uri="http://cdn-beta.ghostlamp.com";}else{$gl_uri="http://cdn.ghostlamp.com";}
	    ?>
	    <script>var glRefKey = '<?php echo $gl_key;?>', glRefDomain='<?php echo $gl_domain;?>';</script>
	    <?php
wp_register_script( 'ghostlamp-require-js', $gl_uri,array(), null, false);
wp_enqueue_script( 'ghostlamp-require-js' );
add_filter( 'clean_url', 'data_attributes', 11, 1 );
         }
     }
?>


<?php
    // Inject script to front footer
    add_action('wp_footer', 'inject_front_footer');
    function inject_front_footer () {
            if(get_post_type(get_the_ID())=='post'){
	    $gl_domain=get_option('gl_domain','');
	    $gl_key=get_option('gl_key','');
	    $gl_beta=get_option('gl_beta','');
	    if($gl_beta==1){$gl_uri="http://cdn-beta.ghostlamp.com";}else{$gl_uri="http://cdn.ghostlamp.com";}
	    ?>
	    <script>var glRefKey = '<?php echo $gl_key;?>', glRefDomain='<?php echo $gl_domain;?>';</script>
	    <script data-main="<?php echo $gl_uri;?>/scripts/main-f" src="<?php echo $gl_uri;?>/scripts/require.min.js"></script>
	    <?php
            }
     }

?>

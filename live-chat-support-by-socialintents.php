<?php
/*
Plugin Name: Social Intents
Plugin URI: https://www.socialintents.com
Description: Chat with your website visitors right from Microsoft Teams, Slack, Google Chat and ChatGPT chatbots trained on your content. To get started: 1) Click the "Activate" link to the left of this description, 2) Go to your Live Chat configuration page, and register for a new account.
Version: 1.6.14
Author: Social Intents
Author URI: https://www.socialintents.com/
*/

$silc_domain = plugins_url();
add_action('init', 'silc_init');
add_action('admin_notices', 'silc_notice');
add_filter('plugin_action_links', 'silc_plugin_actions', 10, 2);
add_action('wp_footer', 'silc_insert',4);
add_action('admin_footer', 'siRedirect');

define('SI_DASHBOARD_URL', "https://www.socialintents.com/chat.do");
define('SI_SMALL_LOGO',plugin_dir_url( __FILE__ ).'si-small.png');

function silc_init() {
    if(function_exists('current_user_can') && current_user_can('manage_options')) {
        add_action('admin_menu', 'silc_add_settings_page');
        add_action('admin_menu', 'silc_create_menu');
    }
	

}

function silc_insert() {

    global $current_user;
    if(strlen(get_option('silc_widgetID')) == 32 ) {
	echo("\n\n<!-- www.socialintents.com -->\n<script type=\"text/javascript\">\n");
        
	echo("(function() {function socialintents(){\n");
        echo("    var siJsHost = ((\"https:\" === document.location.protocol) ? \"https://\" : \"http://\");\n");
        echo("    var s = document.createElement('script');s.type = 'text/javascript';s.async = true;s.src = siJsHost+'www.socialintents.com/api/chat/socialintents.1.4.js#".get_option('silc_widgetID')."';\n");
        
        echo("    var x = document.getElementsByTagName('script')[0];x.parentNode.insertBefore(s, x);};\n");
        echo("if (window.attachEvent)window.attachEvent('onload', socialintents);else window.addEventListener('load', socialintents, false);})();\n");
        echo("</script>\n");
    }
}

function silc_notice() {
    if(!get_option('silc_widgetID')) echo('<div class="error"><p><strong>'.sprintf(__('Your Social Intents Plugin is disabled. Please go to the <a href="%s">plugin settings</a> to register.  ' ), admin_url('options-general.php?page=live-chat-support-by-socialintents')).'</strong></p></div>');
}

function silc_plugin_actions($links, $file) {
    static $this_plugin;
    $silc_domain = plugins_url();
    if(!$this_plugin) $this_plugin = plugin_basename(__FILE__);
    if($file == $this_plugin && function_exists('admin_url')) {
        $settings_link = '<a href="'.admin_url('options-general.php?page=live-chat-support-by-socialintents').'">'.__('Settings', $silc_domain).'</a>';
        array_unshift($links, $settings_link);
    }
    return($links);
}

function silc_add_settings_page() {
    function silc_settings_page() {
        global $silc_domain ?>
<div class="wrap">
        <?php screen_icon() ?>
    <h2><?php _e('Live Chat by Social Intents', $silc_domain) ?></h2>



    <div class="metabox-holder meta-box-sortables ui-sortable pointer">

<div class="postbox" style="float:left;width:35em">
            <h3 class="hndle"><span id="silc_noAccountSpan"><?php _e('Social Intents Free Registration', $silc_domain) ?></span></h3>
            <div id="silc_register" class="inside" style="padding: -30px 10px">	
<p style="text-align:center"><?php wp_nonce_field('update-options') ?>
			<a href="https://www.socialintents.com/" title="Live Chat to help grow your business">
			<?php echo '<img src="'.plugins_url( 'socialintents.png' , __FILE__ ).'" height="94" "/> ';?></a></p>		
		<p><?php printf(__('Join 55,000+ companies using our tools to improve customer service and sell more. Visit %1$sSocial Intents%2$ssocialintents.com%3$s to 
				see how we can help you.', $silc_domain), '<a href="
https://www.socialintents.com" target="_blank" title="', '">', '</a>') ?></p>
			
	<div style='text-align:center'>		
	<a href='https://www.socialintents.com/signup.do?wptype=wpchat' class="button button-primary" target="_blank">Register For Free Now!</a></div>
</div>
<div id="silc_registerComplete" class="inside" style="padding: -20px 10px;display:none;">
<p>Simply open the Live Chat console to answer chats right in your browser.</p>
		<p>Just Getting Started?  <a href='https://www.socialintents.com/assets/pdfs/LiveChatSupportGuide.pdf' target="_blank">Download Our Live Chat Help Guide</a>
		<p><a href='https://www.socialintents.com/chat.do' class="button button-primary" target="_blank">Live Chat Console</a>&nbsp;
			<a href='https://www.socialintents.com/widget.do?id=<?php echo(get_option('silc_widgetID')) ?>' class="button button-primary" target="_blank">Customize My Settings</a>&nbsp;
<a href='https://www.socialintents.com/preview.do?wid=<?php echo(get_option('silc_widgetID')) ?>' class="button button-primary" target="_blank">Preview Popup</a>&nbsp;</p><br>
<div style="text-align:center">
<a id="changeWidget" class="" target="_blank">Enter Different App Key</a>&nbsp;
		</div>
	    </div>
	</div>

	
        <div  id="addAppKey" class="postbox" style="float:left;width:35em;margin-right:10px">
            <h3 class="hndle"><span><?php _e('Registered?  Now, enter your App Key', $silc_domain) ?></span></h3> 
            <div class="inside" style="padding: 0 10px">
                <form id="saveSettings" method="post" action="options.php">
                    <?php wp_nonce_field('update-options') ?>

                    <p><label for="silc_widgetID"><?php printf(__('Enter your App Key below to activate your plugin.  <br><br> If you\'ve already signed up, <a href=\'https://www.socialintents.com/login.do\' target=\'_blank\'>login here</a> to grab your key under My Apps, select Live Chat, then Edit Settings.  Your Key is under the "API Key" tab.<br>', $silc_domain), '<strong><a href="https://www.socialintents.com/" title="', '">', '</a></strong>') ?></label><br />
			<input type="text" name="silc_widgetID" id="silc_widgetID" placeholder="Your API Key" value="<?php echo(get_option('silc_widgetID')) ?>" style="width:100%" />
                    <p class="submit" style="padding:0">

			<input type="hidden" name="action" value="update" />
                        <input type="hidden" name="page_options" value="silc_widgetID" />
                        <input type="submit" name="silc_submit" id="silc_submit" value="<?php _e('Save Settings', $silc_domain) ?>" class="button-primary" /> 
<a href='https://www.socialintents.com/apps.do' class="button button-primary" target="_blank">Find My Key</a>
			</p>
<br><?php echo '<img src="'.plugins_url( 'app-key.png' , __FILE__ ).'" width="250" "/> ';?>
                 </form>
            </div>
        </div>
	

        

	    
        
    </div>
</div>
<script>
jQuery(document).ready(function($) {

var silc_wid= $('#silc_widgetID').val();

if (silc_wid=='') 
{}
else
{
	$( "#silc_register" ).hide();
	$( "#addAppKey" ).hide();
	$( "#silc_registerComplete" ).show();
	$( "#silc_noAccountSpan" ).html("Live Chat Plugin Settings");

}
$(document).on("click", "#changeWidget", function () {
$( "#addAppKey" ).show();
});


$(document).on("click", "#silc_inputSaveSettings", function () {

var silc_wid= $('#silc_widgetID').val();
var silc_tt= encodeURIComponent($('#silc_tab_text').val());
var silc_ht= encodeURIComponent($('#silc_header_text').val());
var silc_to= encodeURIComponent($('#silc_tab_offline_text').val());
var silc_tc= encodeURIComponent($('#silc_tab_color').val());
var silc_top= $('#silc_time_on_page').val();


var url = 'https://www.socialintents.com/json/jsonSaveChatSettings.jsp?tc='+silc_tc+'&tt='+silc_tt+'&ht='+silc_ht+'&wid='+silc_wid+'&to='+silc_to+'&top='+silc_top+'&callback=?';sessionStorage.removeItem("settings");
$.ajax({
   type: 'GET',
    url: url,
    async: false,
    jsonpCallback: 'jsonCallBack',
    contentType: "application/json",
    dataType: 'jsonp',
    success: function(json) {
       $('#silc_widgetID').val(json.key);
	sessionStorage.removeItem("settings");
	sessionStorage.removeItem("socialintents_vs_chat");
	sessionStorage.setItem("hasSeenPopup","false");
	$( "#saveDetailSettings" ).submit();
	
    },
    error: function(e) {
    }
});
});

  });
</script>
<?php }
$silc_domain = plugins_url();
add_submenu_page('options-general.php', __('Live Chat', $silc_domain), __('Live Chat', $silc_domain), 'manage_options', 'live-chat-support-by-socialintents', 'silc_settings_page');
}
function addSilcLink() {
$dir = plugin_dir_path(__FILE__);
include $dir . 'options.php';
}
function silc_create_menu() {
  $optionPage = add_menu_page('Live Chat', 'Live Chat', 'administrator', 'silc_dashboard', 'addSilcLink', plugins_url('live-chat-support-by-social-intents/si-small.png'));
}
function siRedirect() {
$redirectUrl = "https://www.socialintents.com/chat.do";
echo "<script> jQuery('a[href=\"admin.php?page=silc_dashboard\"]').attr('href', '".$redirectUrl."').attr('target', '_blank') </script>";
}
?>
<?php

if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}

add_action('admin_menu', 'my_menu_pages');
function my_menu_pages(){
    add_menu_page('Signifyd', 'Signifyd', 'edit_posts', 'signifyd-menu', 'signifyd_settings');
    add_submenu_page('signifyd-menu', 'Signifyd Settings', 'Signifyd Settings', 'edit_posts', 'signifyd-menu' );
    add_submenu_page('signifyd-menu', 'Signifyd Dashboard', 'Signifyd Dashboard', 'edit_posts', 'signifyd-dashboard',  'signifyd_dashboard');
}

// Signifyd Settings Page
function signifyd_settings() {
    global $title; 
    if($_POST['oscimp_hidden'] == 'Y') {
		$slogin = $_POST['signifyd_login'];
		if (strlen($slogin) > 0 ) {
		update_option('signifyd_login', $slogin);
		} 
		$spassword = $_POST['signifyd_password'];
		if (strlen($spassword) > 0 ) {
		update_option('signifyd_password', $spassword);
		}
		$sapi = $_POST['signifyd_api'];
		if (strlen($sapi) > 0 ) {
		update_option('signifyd_api', $sapi);
		}
		
		if( !get_option( 'signifyd_registered' ) ) {
			if (strlen($sapi) > 0 ) {
				// if user NOT already registered and if API length entered greater than 0, log as new user
				?>
					<script>
					
						var d = new Date();
						minutes = d.getMinutes().toString().length == 1 ? '0'+d.getMinutes() : d.getMinutes(),
						hours = d.getHours().toString().length == 1 ? '0'+d.getHours() : d.getHours(),
						ampm = d.getHours() >= 12 ? 'pm' : 'am',
						months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
						datetime = months[d.getMonth()]+' '+d.getDate()+', '+d.getFullYear()+' '+hours+':'+minutes+' '+ampm;
						
						(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
						(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
						m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
						})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

						ga('create', 'UA-51241721-1', 'auto');
						ga('send', 'pageview');
						ga('_setDomainName', '<?php echo get_option('home');?>');
						ga('send', 'event', 'Signifyd', '<?php echo get_option('home');?>', datetime, 1);
					  
					</script>
					
				<?php
				update_option('signifyd_registered', 'true');
			}
		}
		?>
		<div class="updated"><p><strong><?php _e('Options saved.' ); ?></strong></p></div>
		<?php
	} 

	
	?>
	<div class="wrap">
		<div style="width:90%; display:block">
		<div style="width:44%; display:inline; float:left;">
		<?php    echo "<h2>" . $title . "</h2>"; ?>
		<form name="oscimp_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
			<input type="hidden" name="oscimp_hidden" value="Y">
			<p><?php _e("API Key: " ); ?>&nbsp;&nbsp;&nbsp;<input type="text" name="signifyd_api" placeholder="<?php echo get_option('signifyd_api'); ?>"  value="<?php echo $sapi; ?>" size="25"></p>
			<p>-------------------------------------------------------------------</p>
			<p><?php _e("Login: " ); ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="signifyd_login" placeholder="<?php echo get_option('signifyd_login'); ?>" value="<?php echo $slogin; ?>" size="25"></p>
			<p><?php _e("Password: " ); ?><input type="password" name="signifyd_password" placeholder="**********" value="<?php echo $spassword; ?>" size="25"></p>
			<p class="submit">
			<input type="submit" name="Submit" value="<?php _e('Update Options', 'oscimp_trdom' ) ?>" />
			</p>
		</form>
		
		</div>
		<div style="width:44%; display:inline; float:right;">
		<iframe width="560" height="315" src="//www.youtube.com/embed/gpBz4RxqCik" frameborder="0" allowfullscreen></iframe>
		<br/><br/><br/>
		<h3>Signifyd Contact:</h3>
			<p>
				<strong>Have a sales question?</strong>
				<br>Email our sales team at <br>
				<a href="mailto:sales@signifyd.com">sales@signifyd.com</a>
			</p>
			<p>
				<strong>Would like to discuss further?</strong>
				<br>Feel free to call us at <br>
				(866) 893-0777
			</p>			
		<br/>
		<h3>Plugin Contact:</h3>
			<p>
				<strong>Have a technical question?</strong>
				<br>Email us at <br>
				<a href="admin@nxgninnovations.com">admin@nxgninnovations.com</a>
			</p>				
		</div>
	</div>
	
    <?php	
}

// Integrated Signifyd Dashboard to Wordpress Menubar
function signifyd_dashboard() {

	global $mfwp_options;

	ob_start(); ?>

	<div class="wrap">

				<form id="login" target="frame" method="post" action="https://signifyd.com/login">
					  <input TYPE="hidden" NAME="email" VALUE="<?php echo get_option('signifyd_login'); ?>"> 
					  <input TYPE="hidden" NAME="password" VALUE="<?php echo get_option('signifyd_password'); ?>">
				</form>

				<iframe id="sig_dash_frame" name="frame" style="width:100%; height:800px; border=0;"></iframe>

				<script type="text/javascript">
					document.getElementById('login').submit();

					var iframeSig = document.getElementById('sig_dash_frame');
					iframeSig.onload = function() {
						if (iframeSig.src != "https://signifyd.com/cases?teamId=".get_option('signifyd_login')) {
							iframeSig.src = "https://signifyd.com/cases?teamId=".get_option('signifyd_login');
						}
					}
				</script>
	</div>
	<?php
	echo ob_get_clean();
	
}

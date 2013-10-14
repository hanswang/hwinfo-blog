<?php
/**
 * Plugin Name: QQ登录
 * Plugin URI: http://www.94qing.com/qqconnect.html
 * Description: 使用QQ号登录的一个插件
 * Author: 情留メ蚊子
 * Author URI: http://www.94qing.com/
 * Version: 1.0.1
 * License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

class QQConnect {
	var $conifg = array();
	function QQConnect() {
		if (is_file(dirname(__FILE__) . '/conifg.php')) {
			$this -> conifg = require dirname(__FILE__) . '/conifg.php';
		} else {
			$this -> conifg = array();
			$this -> conifg['appid'] = '';
			$this -> conifg['appkey'] = '';
		} 
		$this -> conifg['scope'] = 'get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo';
		$this -> conifg['callback'] = home_url( '/' );
		add_action('admin_menu', array(&$this, 'admin_menu'));
	} 

	function sava() {
		$cachefile = dirname(__FILE__) . '/conifg.php';
		$fp = fopen($cachefile, 'w');
		$s = "<?php\r\n";
		$s .= 'return ' . var_export($this -> conifg, true) . ";\r\n";
		fwrite($fp, $s);
		fclose($fp);
	} 

	function admin_menu() {
		if (current_user_can('manage_options')) {
			add_options_page('QQ登录', 'QQ登录', 'manage_options', plugin_basename(__FILE__), array(&$this, 'options_page'));
		} 
	} 

	function options_page() {
		if (isset($_POST['appid']) && isset($_POST['appkey'])) {
			$this -> conifg['appid'] = $_POST['appid'];
			$this -> conifg['appkey'] = $_POST['appkey'];
			$this -> sava();
			echo "<div id='setting-error-settings_updated' class='updated settings-error'> <p><strong>QQ登录设置已保存。</strong></p></div>";
		} 

		echo '<div class="wrap">';
		echo '<div id="icon-options-general" class="icon32"><br /></div><h2>常规选项</h2>';
		echo '<form name="b_form" method="post" action="">';

		echo '<p style="font-weight:bold;">在你使用本插件之前请到 <a href="http://connect.qq.com/" target="_blank"><u style="color:blue">http://connect.qq.com/</u></a>申请appid, appkey, 并注册callback地址</a></p>';

		echo '<table class="form-table">';
		echo '<tr valign="top">';
		echo '<th scope="row"><label for="appid">APP ID</label></th>';
		echo '<td><input name="appid" type="text" id="appid" value="' . $this -> conifg['appid'] . '" class="regular-text" /></td>';
		echo '</tr>';
		echo '<tr valign="top">';
		echo '<th scope="row"><label for="appkey">APP KEY</label></th>';
		echo '<td><input name="appkey" type="text" id="appkey" value="' . $this -> conifg['appkey'] . '" class="regular-text" /></td>';
		echo '</tr>';
		echo '<tr valign="top">';
		echo '<th scope="row"><label for="callback">回调地址</label></th>';
		echo '<td><input name="callback" type="text" id="callback" disabled="disabled" value="' . $this -> conifg['callback'] . '" class="regular-text code disabled" /></td>';
		echo '</tr>';
		echo '</table>';

		echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="保存更改"  /></p>';
		echo '</form>';

		echo '</div>';
	} 

	function qq_loginurl() {
		$callback = $this -> conifg['callback'];
		$callback .= '/?plugin=qqconnect&action=callback';
		$_SESSION['state'] = md5(uniqid(rand(), true)); //CSRF protection
		$login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id="
		 . $this -> conifg['appid'] . "&redirect_uri=" . urlencode($callback)
		 . "&state=" . $_SESSION['state']
		 . "&scope=" . $this -> conifg['scope'];
		return $login_url;
	} 

	function qq_callback($code) {
		$callback = $this -> conifg['callback'];
		$callback .= '/?plugin=qqconnect&action=callback';
		$token_url = "https://graph.qq.com/oauth2.0/token?grant_type=authorization_code&"
		 . "client_id=" . $this -> conifg['appid'] . "&redirect_uri=" . urlencode($callback)
		 . "&client_secret=" . $this -> conifg['appkey'] . "&code=" . $code;

		$response = file_get_contents($token_url);
		if (strpos($response, "callback") !== false) {
			$lpos = strpos($response, "(");
			$rpos = strrpos($response, ")");
			$response = substr($response, $lpos + 1, $rpos - $lpos -1);
			$msg = json_decode($response);
			if (isset($msg -> error)) {
				$error = "<h3>error:</h3>" . $msg -> error;
				$error .= "<h3>msg  :</h3>" . $msg -> error_description;
				wp_die($error);
			} 
		} 

		$params = array();
		parse_str($response, $params);

		$_SESSION["access_token"] = $params["access_token"];
	} 

	function get_openid() {
		$graph_url = "https://graph.qq.com/oauth2.0/me?access_token=" . $_SESSION['access_token'];

		$str = file_get_contents($graph_url);
		if (strpos($str, "callback") !== false) {
			$lpos = strpos($str, "(");
			$rpos = strrpos($str, ")");
			$str = substr($str, $lpos + 1, $rpos - $lpos -1);
		} 

		$user = json_decode($str);
		if (isset($user -> error)) {
			$error = "<h3>error:</h3>" . $user -> error;
			$error .= "<h3>msg  :</h3>" . $user -> error_description;
			wp_die($error);
		} 
		$_SESSION["openid"] = $user -> openid;
	} 
	function isbing($openid) {
		global $wpdb;
		$sql = "SELECT user_id FROM $wpdb->usermeta WHERE meta_key = '%s' AND meta_value = '%s'";
		return $wpdb -> get_var($wpdb -> prepare($sql, 'qqconnect_openid', $openid));
	} 
	function willdo() {
		if (!$_SESSION["openid"]) {
			return;
		} 
		if (is_user_logged_in()) {
			$user = wp_get_current_user();
			if ($this -> isbing($_SESSION["openid"])) {
				return;
			} 
			$openid = get_usermeta($user -> ID, 'qqconnect_openid');
			if (!$openid) {
				update_usermeta($user -> ID, 'qqconnect_openid', $_SESSION["openid"]);
				$openid = $_SESSION["openid"];
			} 
			if ($openid == $_SESSION["openid"]) {
				update_usermeta($user -> ID, 'qqconnect_access_token', $_SESSION["access_token"]);
			} 
			unset($_SESSION["openid"]);
			unset($_SESSION["access_token"]);
			unset($_SESSION['state']);
		} else {
			$user_id = $this -> isbing($_SESSION["openid"]);
			if ($user_id) {
				update_usermeta($user_id, 'qqconnect_access_token', $_SESSION["access_token"]);
				wp_set_auth_cookie($user_id, true, false);
				wp_set_current_user($user_id);
				unset($_SESSION["openid"]);
				unset($_SESSION["access_token"]);
				unset($_SESSION['state']); 
				// wp_redirect( home_url( '/' ) );
				// exit();
			} else {
				wp_redirect(wp_login_url());
				exit();
			} 
		} 
	} 

	function qq_init() {
		if ($_GET['plugin'] == 'qqconnect') {
			if ($_GET['action'] == 'login') {
				$url = $this -> qq_loginurl();
				header("Location:$url");
				exit();
			} else if ($_GET['action'] == 'callback') {
				if ($_GET['state'] == $_SESSION['state']) { // csrf
					$this -> qq_callback($_GET['code']);
					$this -> get_openid();
					$this -> willdo();
				} 
			} else if ($_GET['action'] == 'unbing') {
				if (is_user_logged_in()) {
					$user = wp_get_current_user();
					delete_user_meta($user -> ID, 'qqconnect_openid');
					delete_user_meta($user -> ID, 'qqconnect_access_token');
				} 
			} 
		} 
	} 
} 
// http://www.94qing.com/?plugin=qqconnect&action=login
if (!session_id()) session_start();
$qqconnect = new QQConnect();
add_action('init', array(&$qqconnect, 'qq_init'), 1);
add_action('wp_login', 'qqconnect_wp_login', 120);
add_action('login_form', 'qqconnect_login_form');
add_action('comment_form', 'qqconnect_login_form');
add_action('personal_options', 'qqconnect_personal_options');
add_action('register_form', 'qqconnect_show_password_field');
add_action('register_post', 'qqconnect_check_fields', 120, 3);
add_action('user_register', 'qqconnect_register_extra_fields');

if (!function_exists('wp_new_user_notification')) :
	/**
	 * Notify the blog admin of a new user, normally via email.
	 * 
	 * @since 2.0
	 * @param int $user_id User ID
	 * @param string $plaintext_pass Optional. The user's plaintext password
	 */
	function wp_new_user_notification($user_id, $plaintext_pass = '', $flag = '') {
	if (func_num_args() > 1 && $flag !== 1)
		return;

	$user = new WP_User($user_id);

	$user_login = stripslashes($user -> user_login);
	$user_email = stripslashes($user -> user_email); 
	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$message = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

	if (empty($plaintext_pass))
		return; 
	// 你可以在此修改发送给用户的注册通知Email
	$message = sprintf(__('Username: %s'), $user_login) . "\r\n";
	$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
	$message .= '登陆网址: ' . wp_login_url() . "\r\n"; 
	// sprintf(__('[%s] Your username and password'), $blogname) 为邮件标题
	wp_mail($user_email, sprintf(__('[%s] Your username and password'), $blogname), $message);
} 
endif;

function qqconnect_wp_login($login) {
	global $qqconnect;
	$openid = $_POST['openid'];
	$access_token = $_POST['access_token'];
	if (!$openid || !$access_token) {
		return;
	} 
	$user_id = $qqconnect -> isbing($openid);
	if ($user_id) {
		return;
	} 
	$user = get_userdatabylogin($login);
	$openid = get_usermeta($user -> ID, 'qqconnect_openid');
	if (!$openid) {
		update_usermeta($user -> ID, 'qqconnect_openid', $openid);
		update_usermeta($user -> ID, 'qqconnect_access_token', $access_token);
	} 
	unset($_SESSION["openid"]);
	unset($_SESSION["access_token"]);
	unset($_SESSION['state']); 
	// wp_redirect( home_url( '/' ) );
	// exit();
} 

function qqconnect_register_extra_fields($user_id, $password = "", $meta = array()) {
	$userdata = array();
	$userdata['ID'] = $user_id;
	$userdata['user_pass'] = $_POST['user_pass'];
	wp_new_user_notification($user_id, $_POST['user_pass'], 1);
	wp_update_user($userdata);

	delete_user_setting('default_password_nag', $user_id);
	delete_user_meta($user_id, 'default_password_nag');

	$openid = $_POST['openid'];
	$access_token = $_POST['access_token'];
	if ($openid && $access_token) {
		add_user_meta($user_id, 'qqconnect_openid', $openid, true);
		add_user_meta($user_id, 'qqconnect_access_token', $access_token, true);
		unset($_SESSION["openid"]);
		unset($_SESSION["access_token"]);
		unset($_SESSION['state']);
	} 
	wp_set_auth_cookie($user_id, true, false);
	wp_set_current_user($user_id);
	wp_redirect(home_url('/'));
	exit();
} 

function qqconnect_check_fields($login, $email, $errors) {
	if (strlen($_POST['user_pass']) < 6) {
		$errors -> add('password_length', "<strong>错误</strong>：密码长度至少6位");
	} elseif ($_POST['user_pass'] != $_POST['user_pass2']) {
		$errors -> add('password_error', "<strong>错误</strong>：两次输入的密码必须一致");
	} 
} 

function qqconnect_show_password_field() {

	?>
<p>
	<label for="user_pwd1">密码(至少6位)<br/>
		<input id="user_pwd1" class="input" type="password" tabindex="21" size="25" value="" name="user_pass"/>
	</label>
</p>
<p>
	<label for="user_pwd2">重复密码<br/>
		<input id="user_pwd2" class="input" type="password" tabindex="22" size="25" value="" name="user_pass2" />
	</label>
</p>
<?php
	if (isset($_SESSION["openid"]) && $_SESSION["openid"] && isset($_SESSION["access_token"]) && $_SESSION["access_token"]) {
		echo '<input type="hidden" name="openid" value="' . $_SESSION["openid"] . '" />';
		echo '<input type="hidden" name="access_token" value="' . $_SESSION["access_token"] . '" />';
		echo '<p class="message register">如果有账号则<a href="' . site_url('wp-login.php', 'login') . '">登录</a>绑定，否则请注册</p><br/>';
	} else {
		qqconnect_login_form();
	} 
} 
function qqconnect_login_form() {
	if (isset($_SESSION["openid"]) && $_SESSION["openid"] && isset($_SESSION["access_token"]) && $_SESSION["access_token"]) {
		echo '<input type="hidden" name="openid" value="' . $_SESSION["openid"] . '" />';
		echo '<input type="hidden" name="access_token" value="' . $_SESSION["access_token"] . '" />';
		echo '<p class="message register">如果有账号则登录绑定，否则请<a href="' . site_url('wp-login.php?action=register', 'login') . '">注册</a></p><br/>';
	} else {
		if (is_user_logged_in()){
			return ;
		}
		global $qqconnect;
		$sc_url = WP_PLUGIN_URL . '/' . dirname(plugin_basename (__FILE__));
		echo '<p><img onclick=\'window.open("' . home_url('/') . '?plugin=qqconnect&action=login", "TencentLogin","width=450,height=320,menubar=0,scrollbars=1, resizable=1,status=1,titlebar=0,toolbar=0,location=1");return false;\' src="' . $sc_url . '/qq_login.png" alt="使用QQ登陆" style="cursor: pointer; margin-right: 20px;" /></p>';
	} 
} 
function qqconnect_personal_options() {
	global $qqconnect;
	$sc_url = WP_PLUGIN_URL . '/' . dirname(plugin_basename (__FILE__));
	$user = wp_get_current_user();
	$openid = get_usermeta($user -> ID, 'qqconnect_openid');

	echo '<tr>';
	echo '<th scope="row"></th>';
	if ($openid) {
		echo '<td><input class="button-primary" type="button" value="解除QQ绑定" onclick="if (confirm(\'你确定要解除QQ绑定？\')){window.location.href=\'' . home_url('/') . '?plugin=qqconnect&action=unbing\';};" /></td>';
	} else {
		echo '<td><img onclick=\'window.open("' . home_url('/') . '?plugin=qqconnect&action=login", "TencentLogin","width=450,height=320,menubar=0,scrollbars=1, resizable=1,status=1,titlebar=0,toolbar=0,location=1");return false;\' src="' . $sc_url . '/qq_bind.gif" alt="使用QQ登陆" style="cursor: pointer; margin-right: 20px;" /></td>';
	} 
	echo '</tr>';
} 

?>
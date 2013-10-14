<?php
/*
 * Plugin Name: 腾讯微博插件
 * Plugin URI: http://l-da.com/94/
 * Description: 一个简单的在WordPress边栏显示本人发布的最近腾讯微博的插件, 支持Widget拖拽方式设置插件显示的位置
 * Author: shapherd
 * Version: 1.1
 * Author URI: http://www.l-da.com
 */

session_start();
$this_dir = dirname(__FILE__);
include_once($this_dir.'/config.php');
include_once($this_dir.'/oauth.php');
include_once($this_dir.'/opent.php');
include_once($this_dir.'/api_client.php');

class qqWeiboPlugin
{

    /**
     * 获取存放在数据库中的配置信息
     * */
    function get_weibo_options()
    {
        $oauth_token = get_option('weibo_oauth_token', '');
        $oauth_secret = get_option('weibo_oauth_secret', '');
        if(empty($oauth_token))
        {
            return false;
        }
        else
        {
            return array('key'=>$oauth_token, 'secret'=>$oauth_secret);
        }
    }

    function weibo_session_end($oauth_token, $oauth_secret)
    {
        $client = new MBApiClient( MB_AKEY, MB_SKEY, $oauth_token, $oauth_secret);
        $msg = $client->end_session();
        if ($msg === false || $msg === null)
        {
            echo "Error occured";
            return false;
        }

        if (isset($msg['name']))
        {
            delete_option('weibo_oauth_token');
            delete_option('weibo_oauth_secret');
        }
    }

    function get_access_token()
    {
        $oauth = new MBOpenTOAuth( MB_AKEY , MB_SKEY , $_SESSION['keys']['oauth_token'] , $_SESSION['keys']['oauth_token_secret']  );
        $token = $oauth->getAccessToken( $_REQUEST['oauth_verifier']);
        if($token)
        {
            update_option('weibo_oauth_token', $token['oauth_token']);
            update_option('weibo_oauth_secret', $token['oauth_token_secret']);
        }
        }

    /**
     * 输出微博内容
     */
    function output_tweets($oauth_token , $oauth_secret, $tweet_count=15)
    {
        $c = new MBApiClient( MB_AKEY , MB_SKEY , $oauth_token , $oauth_secret );
	echo '<div style="display:none;">';
	$me = $c->getUserInfo();
	    $p =array(
		'f' => 0,
		't' => 0,		
		'n' => $tweet_count,
		'name' => $me['data']['name']
	    );
		
        $ms = $c->getTimeline($p);
		
        
	echo '</div>';
		
        if(count($ms) == 0)
        {
            echo '<p>你还没有发过微博吧，到腾讯微博去发一个呗 :)</p>';
        }
		
        else
		
        {
			$temp = $ms['data']['info'];
			//echo $ms['ret'];
			//echo $ms['Data'];
        ?>
	    <div >
            <ol id="weibo">
                <?php foreach( $temp as $item ){ ?>
                <li style="list-style-type: none;margin-bottom: 8px;background-repeat: no-repeat;background-position: 0px 0px;padding-left: 24px;">
                    <?php
                    $text = $this->format_tweet($item['text']);
                    echo ($text);
                    //$format = human_readable_time($item['created_at']);
                    //$tweet_url = 'http://open.t.qq.com/api/t/show?format=xml&id='.$item['user']['id'];
                    //echo "&nbsp;&nbsp;<a href='$tweet_url' class='weibo_link' target='_blank'>${format}前</a>";
                    ?>
                </li>
                <?php }?>
            </ol>
	    </div>
        <?php
            echo "<p class='follow_me' style='background-repeat: no-repeat;background-position: 0px 0px;padding-left: 20px;float: right;'><a href='http://t.qq.com/" . $me['data']['name']. "' target='_blank'>关注我吧</a></p><p class='clear'></p>";
        }
    }

    private function format_tweet($tweet_msg)
    {
        $tweet_msg = add_topic_link($tweet_msg);
        //$tweet_msg = add_url_link($tweet_msg);
        $tweet_msg = add_at_link($tweet_msg);
        return $tweet_msg;
    }

    /**
     * 生成授权url
     */
    function output_authorize_url($callback_url)
    {
		$o = new MBOpenTOAuth( MB_AKEY , MB_SKEY  );
		$keys = $o->getRequestToken($callback_url);//这里填上你的回调URL
		$aurl = $o->getAuthorizeURL( $keys['oauth_token'] ,false,'');
		$_SESSION['keys'] = $keys;
		?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>腾讯微博</h2>
            <p>
                <a target="_blank" href="http://t.qq.com/agreement">腾讯微博使用条款和隐私权政策</a>
            </p>
            <p>
                <a href="<?php echo $aurl?>">授权</a>
            </p>
        </div>
        <?php
    }
}

function qq_weibo_plugin_admin()
{
    $token = qqWeiboPlugin::get_weibo_options();
    $callback_url = "http://".$_SERVER ['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    //点击了取消授权按钮
    if($_POST['action'] == 'end_session')
    {
        qqWeiboPlugin::weibo_session_end($token['key'], $token['secret']);
        unset($_SESSION['request']);
    }

    //点击了授权按钮返回wp
    if(isset($_REQUEST['oauth_verifier']))
    {
        qqWeiboPlugin::get_access_token();
    }

    //已经获取了腾讯微博的授权
    $token = qqWeiboPlugin::get_weibo_options();
?>
<?php
    if($token)
    {
	

        $c = new MBApiClient( APP_KEY , APP_SECRET , $token['key'] , $token['secret'] );
        //$ms  = $c->getTimeline();
        //$me = $c->verify_credentials();
        ?>
		授权完成
        <h2>腾讯微博</h2>
        <p>授权腾讯用户名：<?=$me['name']?></p>
        <form action="" method="POST">
            <input type="hidden" name="action" value="end_session"/>
            <!-- <input type="submit" value="修改授权帐号" /> -->
        </form>
        <?php
    }
    else
    {
        qqWeiboPlugin::output_authorize_url($callback_url);
    }
}

/**
 * 微博后台管理小工具，可通过拖拽以及简单的配置对微博列表显示的位置进行设置
 */
class qqWeibo_Widget extends WP_Widget
{
    function qqWeibo_Widget()
    {
        parent::WP_Widget(false, $name = '腾讯微博');
    }

    function widget($args, $instance)
    {
        extract( $args );
        $title = apply_filters('widget_title', $instance['title']);

        echo $before_widget;
        if ( $title )
        {
            echo $before_title . $title . $after_title;
        }

        $weibo = new qqWeiboPlugin();
        $opts = $weibo->get_weibo_options();
        if( $opts == False )
        {
            echo "<p>你还没有获取腾讯微博授权，点击  <a href='wp-admin/plugins.php?page=qq-weibo-auth' target='_blank'>这里</a>授权^^</p>";
        }
        else
        {
            $weibo->output_tweets($opts['key'], $opts['secret'], $instance['tweet_count']);
            echo $after_widget;
        }
    }

    function form($instance)
    {
        $title = esc_attr($instance['title']);
        $tweet_count = esc_attr($instance['tweet_count']);
        ?>
            <p>
                <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('tweet_count'); ?>"><?php _e('显示微博条数:'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('tweet_count'); ?>" name="<?php echo $this->get_field_name('tweet_count'); ?>" type="text" value="<?php echo $tweet_count; ?>" />
            </p>
        <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['tweet_count'] = strip_tags($new_instance['tweet_count']);
        return $instance;
    }
}
//注册腾讯微博小工具
add_action('widgets_init', create_function('', 'return register_widget("qqWeibo_Widget");'));

/**
 * 微博后台管理页面菜单
 */
function qq_weibo_admin_page()
{
    add_submenu_page('plugins.php', '腾讯微博', '腾讯微博', 'manage_options', 'qq-weibo-auth', 'qq_weibo_plugin_admin');
}
//向系统菜单注册"腾讯微博"菜单项
add_action('admin_menu', 'qq_weibo_admin_page');

/**
 * 添加微博列表的样式表
 */
function qq_weibo_include_css()
{
    $plugin_name = dirname(__FILE__);
    $segs = preg_split('|[\\/\\\\]|', $plugin_name);
    $plugin_name = $segs[count($segs)-1];
?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo WP_PLUGIN_URL.'/'.$plugin_name.'/'?>weibo.css" />
<?php
}
//注册微博列表的样式
add_action('wp_head', 'qq_weibo_include_css');
?>
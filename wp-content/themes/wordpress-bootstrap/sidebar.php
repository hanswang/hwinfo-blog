                <div id="sidebar1" class="fluid-sidebar sidebar span4" role="complementary">
                    <div id="weibo-show" class="widget widget_weibo_show">
                        <iframe width="100%" height="550" class="share_self"  frameborder="0" scrolling="no" src="http://widget.weibo.com/weiboshow/index.php?language=&width=0&height=550&fansRow=2&ptype=1&speed=0&skin=1&isTitle=1&noborder=0&isWeibo=1&isFans=1&uid=3177787670&verifier=73eeca34&dpc=1"></iframe>
                    </div>
				
					<?php if ( is_active_sidebar( 'sidebar1' ) ) : ?>

						<?php dynamic_sidebar( 'sidebar1' ); ?>

					<?php else : ?>

						<!-- This content shows up if there are no widgets defined in the backend. -->
						
						<div class="alert alert-message">
						
							<p><?php _e("Please activate some Widgets","bonestheme"); ?>.</p>
						
						</div>

					<?php endif; ?>

				</div>

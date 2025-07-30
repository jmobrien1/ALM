<?php

/* 	Design Theme's Right Sidebar Area

	Copyright: 2012, D5 Creation, www.d5creation.com

	Based on the Simplest D5 Framework for WordPress

	Since Design 1.0

*/

?>

<div id="right-sidebar">

<?php if ( ! dynamic_sidebar( 'sidebar-1' ) ) : ?>



				<aside id="archives" class="widget">

					<h3 class="widget-title"><?php _e( 'Archives', 'design' ); ?></h3>

					<ul>

						<?php wp_get_archives( array( 'type' => 'monthly' ) ); ?>

					</ul>

				</aside>



				<aside id="meta" class="widget">

					<h3 class="widget-title"><?php _e( 'Meta', 'design' ); ?></h3>

					<ul>

						<?php wp_register(); ?>

						<li><?php wp_loginout(); ?></li>

						<?php wp_meta(); ?>

					</ul>

				</aside>



<?php endif; // end sidebar widget area ?>

</div>
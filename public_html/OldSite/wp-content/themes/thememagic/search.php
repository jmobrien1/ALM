<?php get_header(); ?>



		<!--inside container-->

		<div id="content">

		

			<!-- left-col-->

			<div id="left-col">



			<?php if ( have_posts() ) : ?>

			

			<div class="post-head-search">

				

				<h1><?php printf( __( 'Search Results for: %s', 'thememagic' ), '' . get_search_query() . '' ); ?></h1>

				

			</div><!--head end-->

				

				<?php get_template_part( 'loop', 'search' ); ?>

<?php else : ?>



					<div class="post-head-notfound">

					

						<h1><?php _e( 'Nothing Found', 'thememagic' ); ?></h1>

					

					</div><!--head end-->

					

					<p><?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.', 'thememagic' ); ?></p>

					

<?php endif; ?>



</div> <!--left-col end-->



<?php get_sidebar(); ?>





</div> <!--content end-->

	

</div>

<!--wrapper end-->



<?php get_footer(); ?>


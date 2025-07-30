<?php get_header(); ?>



		<!--content-->

		<div id="content">

		

			<div id="left-col">



				<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>



				<div class="post-head">



			<h1><?php the_title(); ?></h1>				

					

				</div><!--post-heading end-->

					

					<div class="post-entry">



						<?php the_content(); ?>

						<div class="clear"></div>

						<?php wp_link_pages( array( 'before' => '' . __( 'Pages:', 'thememagic' ), 'after' => '' ) ); ?>

						

					</div><!--post-entry end-->

					

					<?php comments_template( '', true ); ?>





<?php endwhile; ?>

</div> <!--left-col end-->



<?php get_sidebar(); ?>



</div> <!--content end-->

	

</div>

<!--wrapper end-->

<?php get_footer(); ?>
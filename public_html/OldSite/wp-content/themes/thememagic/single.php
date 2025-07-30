<?php get_header(); ?>



	<!--content-->

<div id="content">

		

	<div id="left-col">



			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

			

				

					<div class="post-head">

					

						<h1><?php the_title(); ?></h1>

					

					</div><!--post-heading end-->



			<div class="meta-data">

			

			<!-- <?php the_time(__ ( 'M j', 'thememagic')); ?> <span><?php the_time( 'y' ); ?></span> under <?php the_category(', '); ?> | <?php comments_popup_link( __( 'Leave a comment', 'thememagic' ), __( '1 Comment', 'thememagic' ), __( '% Comments', 'thememagic' ) ); ?> -->

			

			</div><!--meta data end-->



			<div class="post-entry">

<?php

$thumb_small = '';



	if(has_post_thumbnail(get_the_ID(), 'large'))

	{

	    $image_id = get_post_thumbnail_id(get_the_ID());

	    $thumb_small = wp_get_attachment_image_src($image_id, 'large', true);

	}



if($thumb_small<>'') { ?>



		

<p align="center"><img src="<?php echo $thumb_small[0]; ?>" alt="<?php the_title(); ?>" /></p>



<?php } ?>



						<?php the_content( __( '', 'thememagic' ) ); ?>

<p><strong>Universes</strong></p>

<?php meta('universes'); ?>

<p><strong>Fees/Selects</strong></p>

<?php meta('fees'); ?>

						<div class="clear"></div>

			<?php wp_link_pages( array( 'before' => '' . __( 'Pages:', 'thememagic' ), 'after' => '' ) ); ?>

						

						<?php the_tags('Social tagging: ',' > '); ?>

						

					<nav id="nav-single"> <span class="nav-previous">

            <?php previous_post_link( '%link', '<span class="meta-nav">&larr;</span> Previous Data Card '); ?>

            </span> <span class="nav-next">

            <?php next_post_link( '%link', 'Next Data Card <span class="meta-nav">&rarr;</span>'); ?>

            </span> </nav>

						

					</div><!--post-entry end-->

	



				<?php comments_template( '', true ); ?>



<?php endwhile; ?>



</div> <!--left-col end-->



<?php get_sidebar(); ?>



</div> <!--content end-->

	

</div>

<!--wrapper end-->



<?php get_footer(); ?>
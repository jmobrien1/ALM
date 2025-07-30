<?php

/*

Template Name: Datacard Boxes

*/

?>



<?php get_header(); ?>



		<!--content-->



		<div id="content_container">



			<div id="content">



				<div id="left-col">



				<h1>List Catalogue</h1>

<!-- <img src="/web/wp-content/uploads/2013/02/list_catalogue2.jpg" style="margin: 0 0 0 -13px;"> -->

<p>&nbsp;</p>

<p><strong><a href="data-cards/">Click here to view entire selection in table format</a></strong></p>



<?php





global $more; $more = false; # some wordpress wtf logic



  $query_args = array(



     'post_type' => 'data-card', 



     'nopaging' => 'true'



      );



$query_args = apply_filters( 'thememagic_blog_template_query_args', $query_args ); 	  



query_posts($query_args);



?>



<!--pagination-->



<?php // if (function_exists('wp_pagenavi')) { wp_pagenavi(); } 



	// 	else { ?>



	<!-- <div class="navigation">



		<div class="alignleft"><strong><?php previous_posts_link( __( '&larr; View previous list options', 'thememagic' ) ); ?></strong></div>

		<div class="alignright"><strong><?php next_posts_link( __( 'View more available lists &rarr;', 'thememagic' ) ); ?></strong></div>



	</div>

-->

<?php // } ?>



<?php



if (have_posts()) : while (have_posts()) : the_post();





	$thumb_small = '';



	if(has_post_thumbnail(get_the_ID(), 'large'))



	{



	    $image_id = get_post_thumbnail_id(get_the_ID());



	    $thumb_small = wp_get_attachment_image_src($image_id, 'large', true);



	}



?>



<div class="databox">



			<p><strong><a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'thememagic' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark"><?php the_title(); ?></a></strong></p>



				<?php if($thumb_small<>'') { ?>



		<p><a href="<?php the_permalink() ?>"><img src="<?php echo $thumb_small[0]; ?>" width="150" alt="<?php the_title(); ?>" /></a></p>



		<?php }



		else {echo "<td>&nbsp;</td>";}?>



<?php the_excerpt(); ?>



<?php meta('universes'); ?>



<?php meta('fees'); ?>

</div>



<?php endwhile; endif; ?>

<!--pagination-->



<?php // if (function_exists('wp_pagenavi')) { wp_pagenavi(); } 



	// 	else { ?>

<!--

	<div class="navigation">



		<div class="alignleft"><strong><?php previous_posts_link( __( '&larr; View previous list options', 'thememagic' ) ); ?></strong></div>

		<div class="alignright"><strong><?php next_posts_link( __( 'View more available lists &rarr;', 'thememagic' ) ); ?></strong></div>



	</div>

--> <?php // } ?>



</div> <!--left-col end-->



<?php get_sidebar(); ?>



	</div>



</div>



<!--content end-->



</div>



<!--wrapper end-->



<?php get_footer(); ?>
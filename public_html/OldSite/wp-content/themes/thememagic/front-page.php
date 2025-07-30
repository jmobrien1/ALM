<?php get_header(); ?>





<!--slideshow-->



<div id="slide-container">



<?php wowslider(5); ?>



	<!-- <div id="coin-slider">

	

	<?php for ($i = 1; $i <= 2; $i++) { ?>

	

				 <a href="<?php echo of_get_option('slider_link'.$i); ?>"><img src="<?php if(of_get_option('slider_image'.$i) != NULL){ echo of_get_option('slider_image'.$i);} else echo get_template_directory_uri() . '/images/slide'.$i.'.png' ?>" alt="<?php echo of_get_option('slider_head'.$i); ?>" />

			<span><?php if(of_get_option('slider_head'.$i) != NULL){ echo of_get_option('slider_head'.$i);} else echo "100% Professional Business wordpress theme" ?></span></a>

			

	<?php } ?>		

	

	</div> -->

	

</div>

<!--slideshow end-->



<div class="clear"></div>



		<!--boxes-->

		

	<div id="box-container">

		

		<?php for ($i = 1; $i <= 1; $i++) { ?>

		

				<div class="boxes">

				

					<div class="box-head">

						

						<!-- <span class="box-icon"><img src="<?php if(of_get_option('box_image'.$i) != NULL){ echo of_get_option('box_image'.$i);} else echo get_template_directory_uri() . '/images/ic'.$i.'.png' ?>" alt="" /></span> -->

						

					

					</div> <!--box-head close-->

					

					<div class="title-box"><?php if(of_get_option('box_head' . $i) != NULL){ echo of_get_option('box_head' . $i);} else echo "Box heading" ?></div><!--title-box close-->

					

					<div class="box-content">



				<?php if(of_get_option('box_text' . $i) != NULL){ echo of_get_option('box_text' . $i);} else echo "Nullam posuere felis a lacus tempor eget dignissim arcu adipiscing. Donec est est, rutrum vitae bibendum vel, suscipit non metus. Nullam posuere felis a lacus tempor eget dignissim arcu adipiscing." ?>

					

					</div> <!--box-content close-->

					

				<span class="read-more"><a href="<?php echo of_get_option('box_link'.$i); ?>">Read More</a></span>	

				

				</div><!--boxes  end-->

				

		<?php } ?>

	</div> 	<!--box container end-->



	

</div>

<!--wrapper end-->



<?php get_footer(); ?>
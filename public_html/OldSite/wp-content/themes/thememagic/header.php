<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>





	<head profile="http://gmpg.org/xfn/11">

		<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

		<title><?php 	

		

		wp_title( '|' );



	// Add the blog description for the home/front page.

	$site_description = get_bloginfo( 'description', 'display' );

	if ( $site_description && ( is_home() || is_front_page() ) )

		echo " | $site_description"; ?>

		

		</title>

		

		

<link rel="stylesheet" type="text/css" media="all" href="<?php echo get_stylesheet_uri(); ?>" />



<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />



	

	<?php wp_head(); ?>

	

</head>



<body <?php body_class(); ?>>



	<!--wrapper-->

	<div id="wrapper">

	

	<!--headercontainer-->

	<div id="header_container">

	

		<!--header-->

		<div id="header">

		

			<?php if ( ( of_get_option('logo_image') ) != '' ) { ?>

		<div id="logo"><a href="<?php echo home_url(); ?>" title="<?php bloginfo('description'); ?>"><img src="<?php echo of_get_option('logo_image'); ?>" alt="<?php echo of_get_option('footer_cr'); ?>" /></a></div><!--logo end-->

	<?php } else { ?>

			<div id="logo2"><a href="<?php echo home_url(); ?>" title="<?php bloginfo('description'); ?>"><?php bloginfo( 'name' ); ?></a></div><!--logo end-->

	<?php } ?>

			

			

			<div id="search-header">

			

				<?php get_search_form(); ?>

			

			</div><!--search header end-->

			

			</div><!-- header end-->

			<div class="clear"></div>

		</div><!--header container end-->		

		

	<div class="clear"></div>

		

		<!--menu-->

	<div id="menu_container">

				

		<div id="menubar">

		

		<?php $navcheck = '' ; ?>

	

	<?php $navcheck = wp_nav_menu( array( 'container_class' => 'menu-header', 'theme_location' => 'primary' , 'menu_class' => 'nav', 'fallback_cb' => '', 'echo' => false ) ); ?>



 <?php  if ($navcheck == '') { ?>

	

	<ul class="nav">

		<li class="page_item"><a href="<?php echo home_url(); ?>" title="Home"><?php _e( 'Home', 'thememagic' ); ?></a></li>				

		<?php wp_list_pages('title_li=&sort_column=menu_order'); ?>



	</ul>

<?php } else echo($navcheck); ?>  



	</div>	

	<div class="clear"></div>

</div><!--menu container end-->	

	<!--menu end-->
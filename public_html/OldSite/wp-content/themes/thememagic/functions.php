<?php
/** Tell WordPress to run thememagic_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'thememagic_setup' );
if ( ! function_exists( 'thememagic_setup' ) ):
function thememagic_setup() {
	 global $content_width;
     if (!isset($content_width))
            $content_width = 620;
	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );
	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );	
	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'thememagic', get_template_directory() . '/languages' );
	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );
			// This theme uses wp_nav_menu() in one location.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'thememagic' ),
	) );
}
endif;
?>
<?php
function thememagic_list_pings($comment, $args, $depth) { 
	$GLOBALS['comment'] = $comment; ?>
	<li id="comment-<?php comment_ID(); ?>"><?php comment_author_link(); ?>
<?php } ?>
<?php
add_filter('get_comments_number', 'thememagic_comment_count', 0);
function thememagic_comment_count( $count ) {
	if ( ! is_admin() ) {
	global $id;
	$comments_by_type = &separate_comments(get_comments('status=approve&post_id=' . $id));
	return count($comments_by_type['comment']);
} else {
return $count;
}
}
?>
<?php

if ( ! function_exists( 'thememagic_comment' ) ) :

function thememagic_comment( $comment, $args, $depth ) {

	$GLOBALS['comment'] = $comment;

	switch ( $comment->comment_type ) :

		case '' :

	?>

	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">

		<div id="comment-<?php comment_ID(); ?>">

		<div class="comment-author vcard">

			<?php echo get_avatar( $comment, 40 ); ?>

			<?php printf( __( '%s', 'thememagic' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?>

		</div><!-- .comment-author .vcard -->

		<?php if ( $comment->comment_approved == '0' ) : ?>

			<em><?php _e( 'Your comment is awaiting moderation.', 'thememagic' ); ?></em>

			<br />

		<?php endif; ?>



		<div class="comment-meta commentmetadata"><a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">

			<?php

				/* translators: 1: date, 2: time */

				printf( __( '%1$s at %2$s', 'thememagic' ), get_comment_date(),  get_comment_time() ); ?></a><?php edit_comment_link( __( '(Edit)', 'thememagic' ), ' ' );

			?>

		</div><!-- .comment-meta .commentmetadata -->



		<div class="comment-body"><?php comment_text(); ?></div>



		<div class="reply">

			<?php comment_reply_link( array_merge( $args, array( 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>

		</div><!-- .reply -->

	</div><!-- #comment-##  -->



	<?php

			break;

		case 'pingback'  :

		case 'trackback' :

	?>

	<li class="post pingback">

		<p><?php _e( 'Pingback:', 'thememagic' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __('(Edit)', 'thememagic'), ' ' ); ?></p>

	<?php

			break;

	endswitch;

}

endif;



/**

 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.

 *

 * To override thememagic_widgets_init() in a child theme, remove the action hook and add your own

 * function tied to the init hook.

 */

function thememagic_widgets_init() {

	// Area 1, located at the top of the sidebar.

	register_sidebar( array(

		'name' => __( 'Primary Widget Area', 'thememagic' ),

		'id' => 'primary-widget-area',

		'description' => __( 'The primary widget area', 'thememagic' ),

		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',

		'after_widget' => '</li>',

		'before_title' => '<h3 class="widget-title">',

		'after_title' => '</h3>',

	) );



	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.

	register_sidebar( array(

		'name' => __( 'Secondary Widget Area', 'thememagic' ),

		'id' => 'secondary-widget-area',

		'description' => __( 'The secondary widget area', 'thememagic' ),

		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',

		'after_widget' => '</li>',

		'before_title' => '<h3 class="widget-title">',

		'after_title' => '</h3>',

	) );

}

if ( ! function_exists( 'thememagic_posted_on' ) ) :

/**

 * Prints HTML with meta information for the current post�date/time and author.

 */

function thememagic_posted_on() {

	printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'thememagic' ),

		'meta-prep meta-prep-author',

		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',

			get_permalink(),

			esc_attr( get_the_time() ),

			get_the_date()

		),

		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',

			get_author_posts_url( get_the_author_meta( 'ID' ) ),

			sprintf( esc_attr__( 'View all posts by %s', 'thememagic' ), get_the_author() ),

			get_the_author()

		)

	);

}

endif;

/** Register sidebars by running thememagic_widgets_init() on the widgets_init hook. */

add_action( 'widgets_init', 'thememagic_widgets_init' );



/** filter function for wp_title */

function thememagic_filter_wp_title( $old_title, $sep, $sep_location ){

 

// add padding to the sep

$ssep = ' ' . $sep . ' ';

 

// find the type of index page this is

if( is_category() ) $insert = $ssep . 'Category';

elseif( is_tag() ) $insert = $ssep . 'Tag';

elseif( is_author() ) $insert = $ssep . 'Author';

elseif( is_year() || is_month() || is_day() ) $insert = $ssep . 'Archives';

else $insert = NULL;

 

// get the page number we're on (index)

if( get_query_var( 'paged' ) )

$num = $ssep . 'page ' . get_query_var( 'paged' );

 

// get the page number we're on (multipage post)

elseif( get_query_var( 'page' ) )

$num = $ssep . 'page ' . get_query_var( 'page' );

 

// else

else $num = NULL;

 

// concoct and return new title

return get_bloginfo( 'name' ) . $insert . $old_title . $num;

}



// call our custom wp_title filter, with normal (10) priority, and 3 args

add_filter( 'wp_title', 'thememagic_filter_wp_title', 10, 3 );



/*-----------------------------------------------------------------------------------*/

/* Exclude categories from displaying on the "Blog" page template.

/*-----------------------------------------------------------------------------------*/



// Exclude categories on the "Blog" page template.

add_filter( 'thememagic_blog_template_query_args', 'thememagic_exclude_categories_blogtemplate' );



function thememagic_exclude_categories_blogtemplate ( $args ) {



	if ( ! function_exists( 'thememagic_prepare_category_ids_from_option' ) ) { return $args; }



	$excluded_cats = array();



	// Process the category data and convert all categories to IDs.

	$excluded_cats = thememagic_prepare_category_ids_from_option( 'exclude_cat' );





	if ( count( $excluded_cats ) > 0 ) {



		// Setup the categories as a string, because "category__not_in" doesn't seem to work

		// when using query_posts().



		foreach ( $excluded_cats as $k => $v ) { $excluded_cats[$k] = '-' . $v; }

		$cats = join( ',', $excluded_cats );



		$args['cat'] = $cats;

	}



	return $args;



}



/*-----------------------------------------------------------------------------------*/

/* thememagic_prepare_category_ids_from_option()

/*-----------------------------------------------------------------------------------*/



if ( ! function_exists( 'thememagic_prepare_category_ids_from_option' ) ) {



	function thememagic_prepare_category_ids_from_option ( $option ) {



		$cats = array();



		$stored_cats = of_get_option( $option );



		$cats_raw = explode( ',', $stored_cats );



		if ( is_array( $cats_raw ) && ( count( $cats_raw ) > 0 ) ) {

			foreach ( $cats_raw as $k => $v ) {

				$value = trim( $v );



				if ( is_numeric( $value ) ) {

					$cats_raw[$k] = $value;

				} else {

					$cat_obj = get_category_by_slug( $value );

					if ( isset( $cat_obj->term_id ) ) {

						$cats_raw[$k] = $cat_obj->term_id;

					}

				}



				$cats = $cats_raw;

			}

		}



		return $cats;



	}



}





// custom function

function thememagic_head_css() {

		$output = '';

		$custom_css = of_get_option('custom_css');

		if ($custom_css <> '') {

			$output .= $custom_css . "\n";

		}	

		// Output styles

		if ($output <> '') {

			$output = "<!-- Custom Styling -->\n<style type=\"text/css\">\n" . $output . "</style>\n";

			echo $output;

		}

	

}



add_action('wp_head', 'thememagic_head_css');



function thememagic_of_analytics(){

$googleanalytics= of_get_option('go_code');

echo stripslashes($googleanalytics);

}

add_action( 'wp_footer', 'thememagic_of_analytics' );



function thememagic_favicon() {

	if (of_get_option('favicon_image') != '') {

	echo '<link rel="shortcut icon" href="'. of_get_option('favicon_image') .'"/>'."\n";

	}

}



add_action('wp_head', 'thememagic_favicon');





function thememagic_of_register_js() {

	if (!is_admin()) {

		

		wp_register_script('superfish', get_template_directory_uri() . '/js/superfish.js', 'jquery', '1.0', TRUE);

		wp_register_script('coin', get_template_directory_uri() . '/js/coin-slider.min.js', 'jquery', '1.0', TRUE);

		wp_register_script('thememagic_custom', get_template_directory_uri() . '/js/jquery.custom.js', 'jquery', '1.0', TRUE);

		

		wp_enqueue_script('jquery');

		wp_enqueue_script('superfish');

		wp_enqueue_script('coin');

		wp_enqueue_script('thememagic_custom');

	}

}

add_action('init', 'thememagic_of_register_js');



function thememagic_of_single_scripts() {

	if(is_singular()) wp_enqueue_script( 'comment-reply' ); // loads the javascript required for threaded comments 

}

add_action('wp_print_scripts', 'thememagic_of_single_scripts');



function thememagic_of_styles() {

		wp_register_style( 'superfish', get_template_directory_uri() . '/css/superfish.css' );

		wp_register_style( 'coinslider', get_template_directory_uri() . '/css/coin-slider-styles.css' );

				

		wp_enqueue_style( 'superfish' );

		wp_enqueue_style( 'coinslider' );

}

add_action('wp_print_styles', 'thememagic_of_styles');



/** redirect */

if ( is_admin() && isset($_GET['activated'] ) && $pagenow ==	"themes.php" )

	wp_redirect( 'themes.php?page=options-framework');



// include panel file.

if ( !function_exists( 'optionsframework_init' ) ) {



	/*-----------------------------------------------------------------------------------*/

	/* Options Framework Theme

	/*-----------------------------------------------------------------------------------*/



	/* Set the file path based on whether the Options Framework Theme is a parent theme or child theme */



	if ( get_stylesheet_directory() == get_template_directory_uri() ) {

		define('OPTIONS_FRAMEWORK_URL', get_template_directory() . '/admin/');

		define('OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/admin/');

	} else {

		define('OPTIONS_FRAMEWORK_URL', get_template_directory() . '/admin/');

		define('OPTIONS_FRAMEWORK_DIRECTORY', get_template_directory_uri() . '/admin/');

	}



	require_once (OPTIONS_FRAMEWORK_URL . 'options-framework.php');



}

function register_shortcodes() {

	add_shortcode('my-login', 'wp_login_form');

}

add_action( 'init', 'register_shortcodes');
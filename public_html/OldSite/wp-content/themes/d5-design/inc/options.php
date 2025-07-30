<?php

/**

 * Design Options Page

 * @ Copyright: D5 Creation, All Rights, www.d5creation.com

 */



function optionsframework_option_name() {



	// This gets the theme name from the stylesheet

	$themename = 'design';

	$optionsframework_settings = get_option( 'optionsframework' );

	$optionsframework_settings['id'] = $themename;

	update_option( 'optionsframework', $optionsframework_settings );

}



/**

 * Defines an array of options that will be used to generate the settings page and be saved in the database.

 * When creating the 'id' fields, make sure to use all lowercase and no spaces.

 *

 * If you are making your theme translatable, you should replace 'design'

 * with the actual text domain for your theme.  Read more:

 * http://codex.wordpress.org/Function_Reference/load_theme_textdomain

 */





function optionsframework_options() {

	

	add_filter( 'wp_default_editor', create_function('', 'return "html";') );

	

	$options[] = array(

		'name' => 'Design Theme Options',

		'type' => 'heading');

		

	$options[] = array(

		'desc' => '<span class="donation">If you like this FREEE Theme You can consider for a small Donation to us. Your Donation will be spent for the Disadvantaged Children and Students. You can visit our <a href="http://d5creation.com/donate/" target="_blank"><strong>DONATION PAGE</strong></a> and Take your decision.</span><br /><br /><span class="donation">Need More Features and Options? Try <a href="http://d5creation.com/theme/design" target="_blank"><strong>Design Extend</strong></a>. Design Extend has more than 100 Theme Options and Features. You can control almost everything within a box. Please visit to see the working of Design Theme <a href="http://demo.d5creation.com/wp/themes/design/" target="_blank"><strong>HERE (Demo)</strong></a>.</span>',

		'type' => 'info');

		

	$options[] = array(

		'name' => 'Banner Image', 

		'desc' => 'Upload an image for the Front Page Banner.950px X 300px image is recommended.', 

		'id' => 'banner-image',

		'std' => get_template_directory_uri() . '/images/slide-image/slide-image1.jpg',

		'type' => 'upload');

	

	$options[] = array(

		'name' => 'Front Page Heading',

		'desc' => 'Input your heading text here. Plese limit it within 100 Letters.',

		'id' => 'heading_text',

		'std' => 'You can go with Pro Version for more Features and Functionalities. Visit www.d5creation.com for details',

		'type' => 'text');

	

// Quotation	

	$options[] = array(

		'desc' => '<span class="featured-area-title">Bottom Quotation</span>',

		'type' => 'info');

	

	$options[] = array(

		'name' => 'Quote Text',

		'desc' => 'Input your Quotation here. Plese limit it within 150 Letters.',

		'id' => 'bottom-quotation',

		'std' => 'All the developers of D5 Creation have come from the disadvantaged part or group of the society. All have established themselves after a long and hard struggle in their life ----- D5 Creation Team',

		'type' => 'textarea');

		

// Front Page Fearured Images



	$fbsin=array("1","2","3");

	foreach ($fbsin as $fbsinumber) {

	

	$options[] = array(

		'desc' => '<span class="featured-area-title">' . 'Front Page Featured Image: 0' . $fbsinumber . '</span>',

		'type' => 'info');

		

	$options[] = array(

		'name' => 'Featured Image',

		'desc' => 'Upload an image for the Featured Box. 270px X 200px image is recommended. If you do not want to show anything here leave the box blank.',

		'id' => 'featured-image' . $fbsinumber,

		'std' => get_template_directory_uri() . '/images/featured-image' . $fbsinumber . '.jpg',

		'type' => 'upload');	

	

	$options[] = array(

		'name' => 'Featured Link',

		'desc' => 'Input your Featured Image Link here.',

		'id' => 'featured-link' . $fbsinumber,

		'std' => '#',

		'type' => 'text');



	}

	

// Front Page Fearured Contents

	

	$fbsind=array("1","2");

	foreach ($fbsind as $fbsinumberd) {

	

	$options[] = array(

		'desc' => '<span class="featured-area-title">' . 'Front Page Featured Content: 0' . $fbsinumberd . '</span>',

		'type' => 'info');

	

	$options[] = array(

		'name' => 'Featured Title First Part, Black Color',

		'desc' => 'Input your Featured Title here. Plese limit it within 15 Letters. If you do not want to show anything here leave the box blank.',

		'id' => 'fcontent01-title' . $fbsinumberd,

		'std' => 'Design a Smart Theme by',

		'type' => 'text');



	$options[] = array(

		'name' => 'Featured Title Second Part, Orange Color',

		'desc' => 'Input your Featured Title here. Plese limit it within 15 Letters. If you do not want to show anything here leave the box blank.',

		'id' => 'fcontent02-title' . $fbsinumberd,

		'std' => 'D5 Creation',

		'type' => 'text');



	$options[] = array(

		'name' => 'Featured Description',

		'desc' => 'Input the description of Featured Areas. Please limit the words within 30 so that the layout should be clean and attractive. You can also input any HTML, Videos or iframe here. But Please keep in mind about the limitation of Width of the box.',

		'id' => 'fcontent-description' . $fbsinumberd,

		'std' => 'The Customizable Background and other options of Design Theme will give the WordPress Driven Site an attractive look.  Design Theme is super elegant and Professional Responsive which will create the business widely expressed.',

		'type' => 'textarea');



	$options[] = array(

		'name' => 'Featured Link',

		'desc' => 'Input your Featured Items URL here.',

		'id' => 'fcontent-link' . $fbsinumberd,

		'std' => '#',

		'type' => 'text');



	}

	return $options;

}



/*

 * This is an example of how to add custom scripts to the options panel.

 * This example shows/hides an option when a checkbox is clicked.

 */



add_action('optionsframework_custom_scripts', 'optionsframework_custom_scripts');



function optionsframework_custom_scripts() { ?>



<?php

}
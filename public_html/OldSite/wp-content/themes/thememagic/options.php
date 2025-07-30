<?php

/**

 * A unique identifier is defined to store the options in the database and reference them from the theme.

 * By default it uses the theme name, in lowercase and without spaces, but this can be changed if needed.

 * If the identifier changes, it'll appear as if the options have been reset.

 * 

 */



function optionsframework_option_name() {



	// This gets the theme name from the stylesheet (lowercase and without spaces)

	$themename = wp_get_theme(get_stylesheet_directory() . '/style.css');

	$themename = $themename['Name'];

	$themename = preg_replace("/\W/", "", strtolower($themename) );

	$themename = 'thememagic';

	

	$optionsframework_settings = get_option('optionsframework');

	$optionsframework_settings['id'] = $themename;

	update_option('optionsframework', $optionsframework_settings);

	

	// echo $themename;

}



/**

 * Defines an array of options that will be used to generate the settings page and be saved in the database.

 * When creating the "id" fields, make sure to use all lowercase and no spaces.

 *  

 */



function optionsframework_options() {

	

	// Test data

	$test_array = array("one" => "One","two" => "Two","three" => "Three","four" => "Four","five" => "Five");

	

	// Multicheck Array

	$multicheck_array = array("one" => "French Toast", "two" => "Pancake", "three" => "Omelette", "four" => "Crepe", "five" => "Waffle");

	

	// Multicheck Defaults

	$multicheck_defaults = array("one" => "1","five" => "1");

	

	

	// Pull all the categories into an array

	$options_categories = array();  

	$options_categories_obj = get_categories();

	foreach ($options_categories_obj as $category) {

    	$options_categories[$category->cat_ID] = $category->cat_name;

	}

	

	// Pull all the pages into an array

	$options_pages = array();  

	$options_pages_obj = get_pages('sort_column=post_parent,menu_order');

	$options_pages[''] = 'Select a page:';

	foreach ($options_pages_obj as $page) {

    	$options_pages[$page->ID] = $page->post_title;

	}

		

	// If using image radio buttons, define a directory path

	$imagepath =  get_stylesheet_directory_uri() . '/images/';

		

	$options = array();

		

	$options[] = array( "name" => "Homepage Settings",

						"type" => "heading");																									

								

	$options[] = array( "name" => "Slider heading",

						"desc" => "Heading for the slider.",

						"id" => "slider_head1",

						"std" => "",

						"type" => "text");

						

	$options[] = array( "name" => "Slider image",

						"desc" => "920px x 350px exact. Upload your image for homepage slider.",

						"id" => "slider_image1",

						"type" => "upload");

						

	$options[] = array( "name" => "Slider link",

						"desc" => "Paste here the link of the page or post.",

						"id" => "slider_link1",

						"std" => "",

						"type" => "text");

						

	$options[] = array( "name" => "Slider heading 2",

						"desc" => "Heading for the slider 2.",

						"id" => "slider_head2",

						"std" => "",

						"type" => "text");

						

	$options[] = array( "name" => "Slider image 2",

						"desc" => "920px x 350px exact. Upload your image for homepage slider.",

						"id" => "slider_image2",

						"type" => "upload");

						

	$options[] = array( "name" => "Slider link 2",

						"desc" => "Paste here the link of the page or post.",

						"id" => "slider_link2",

						"std" => "",

						"type" => "text");																		 



	$options[] = array( "name" => "Homepage Box 1 heading",

						"desc" => "Heading for homepage box1.",

						"id" => "box_head1",

						"std" => "",

						"type" => "text");

						

	$options[] = array( "name" => "Homepage Box 1 text",

						"desc" => "Textarea for homepage box1.",

						"id" => "box_text1",

						"std" => "",

						"type" => "textarea");

						

	$options[] = array( "name" => "Homepage Box 1 Icon image",

						"desc" => "32px x 32px exact. Upload your Icon for homepage box 1.",

						"id" => "box_image1",

						"type" => "upload");						

						

	$options[] = array( "name" => "Homepage Box 1 link",

						"desc" => "Paste here the link of the page or post.",

						"id" => "box_link1",

						"std" => "",

						"type" => "text");

						

	$options[] = array( "name" => "Homepage Box 2 heading",

						"desc" => "Heading for homepage box2.",

						"id" => "box_head2",

						"std" => "",

						"type" => "text");

						

	$options[] = array( "name" => "Homepage Box 2 text",

						"desc" => "Textarea for homepage box2.",

						"id" => "box_text2",

						"std" => "",

						"type" => "textarea");

						

	$options[] = array( "name" => "Homepage Box 2 Icon image",

						"desc" => "32px x 32px exact. Upload your Icon for homepage box 2.",

						"id" => "box_image2",

						"type" => "upload");						

						

	$options[] = array( "name" => "Homepage Box 2 link",

						"desc" => "Paste here the link of the page or post.",

						"id" => "box_link2",

						"std" => "",

						"type" => "text");

						

	$options[] = array( "name" => "Homepage Box 3 heading",

						"desc" => "Heading for homepage box3.",

						"id" => "box_head3",

						"std" => "",

						"type" => "text");

						

	$options[] = array( "name" => "Homepage Box 3 text",

						"desc" => "Textarea for homepage box3.",

						"id" => "box_text3",

						"std" => "",

						"type" => "textarea");

						

	$options[] = array( "name" => "Homepage Box 3 Icon image",

						"desc" => "32px x 32px exact. Upload your Icon for homepage box 3.",

						"id" => "box_image3",

						"type" => "upload");						

						

	$options[] = array( "name" => "Homepage Box 3 link",

						"desc" => "Paste here the link of the page or post.",

						"id" => "box_link3",

						"std" => "",

						"type" => "text");																																										

						

	$options[] = array( "name" => "Logo Settings",

						"type" => "heading");

						

	$options[] = array( "name" => "Logo image",

						"desc" => "Upload your logo image over here.",

						"id" => "logo_image",

						"type" => "upload");

						

	$options[] = array( "name" => "Favicon image",

						"desc" => "Upload your favicon image over here or enter url.",

						"id" => "favicon_image",

						"type" => "upload");	

						

	$options[] = array( "name" => "Blog Settings",

						"type" => "heading");

						

	$options[] = array( "name" => "Blog Exclude Categories",

						"desc" => "Specify a comma seperated list of category IDs (eg: 1,4,8) or slugs that you would like to exclude from your blog page (eg: uncategorized).",

						"id" => "exclude_cat",

						"std" => "",						

						"type" => "text");											

						

	$options[] = array( "name" => "Footer Settings",

						"type" => "heading");

						

	$options[] = array( "name" => "Footer copyright text",

						"desc" => "Enter your company name here.",

						"id" => "footer_cr",

						"std" => "",

						"type" => "text");	

						

	$options[] = array( "name" => "Google Analytics Code",

						"desc" => "You can paste your Google Analytics or other tracking code in this box.",

						"id" => "go_code",

						"std" => "",

						"type" => "textarea");

						

	$options[] = array( "name" => "Facebook",

						"desc" => "Insert your facebook URL here.",

						"id" => "footer_facebook",

						"std" => "",

						"type" => "text");



	$options[] = array( "name" => "Twitter",

						"desc" => "Insert your link to the twitter page here.",

						"id" => "footer_twitter",

						"std" => "",

						"type" => "text");



	$options[] = array( "name" => "Style Settings",

						"type" => "heading");

						

	$options[] = array( "name" => "Custom CSS",

						"desc" => "Add css to modify the theme here instead of adding it to style.css file.",

						"id" => "custom_css",

						"std" => "",

						"type" => "textarea");							

															

	return $options;

}
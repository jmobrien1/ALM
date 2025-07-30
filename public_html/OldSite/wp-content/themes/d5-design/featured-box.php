<?php

/* 	Design Theme's Featured Box to show the Featured Items of Front Page

	Copyright: 2012, D5 Creation, www.d5creation.com

	Based on the Simplest D5 Framework for WordPress

	Since Design 1.0

*/

?>



<div class="featured-boxs">



<span class="featured-box">

<a href="<?php echo of_get_option('featured-link1', '#'); ?>">

<img src="<?php echo of_get_option('featured-image1', get_template_directory_uri() . '/images/featured-image1.jpg') ?>"/>

<span class="read-more">Read more...</span>

</a>

</span>

<span class="featured-box">

<a href="<?php echo of_get_option('featured-link2', '#'); ?>">

<img src="<?php echo of_get_option('featured-image2', get_template_directory_uri() . '/images/featured-image2.jpg') ?>"/>

<span class="read-more">Read more...</span>

</a>

</span>



<span class="featured-box">

<a href="<?php echo of_get_option('featured-link3', '#'); ?>">

<img src="<?php echo of_get_option('featured-image3', get_template_directory_uri() . '/images/featured-image3.jpg') ?>"/>

<span class="read-more">Read more...</span>

</a>

</span>



</div> <!-- featured-boxs -->



<div class="sep3">sep</div>



<span class="featured-content1">

<h2><?php echo of_get_option('fcontent01-title1', 'Design a Smart Theme by '); ?><span> <?php echo of_get_option('fcontent02-title1', 'D5 Creation'); ?></span></h2>

<p><?php echo of_get_option('fcontent-description1', 'The Customizable Background and other options of Design Theme will give the WordPress Driven Site an attractive look.  Design Theme is super elegant and Professional Responsive which will create the business widely expressed.'); ?></p>

<a href="<?php echo of_get_option('fcontent-link1', '#'); ?>" class="read-more">Read more...</a>

</span>



<span class="featured-content2">

<h2><?php echo of_get_option('fcontent01-title2', 'Design a Smart Theme by '); ?><span> <?php echo of_get_option('fcontent02-title2', 'D5 Creation'); ?></span></h2>

<p><?php echo of_get_option('fcontent-description2', 'The Customizable Background and other options of Design Theme will give the WordPress Driven Site an attractive look.  Design Theme is super elegant and Professional Responsive which will create the business widely expressed.'); ?></p>

<a href="<?php echo of_get_option('fcontent-link2', '#'); ?>" class="read-more">Read more...</a>

</span>



<div class="sep2">sep</div>
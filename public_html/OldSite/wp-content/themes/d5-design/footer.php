<?php

/* Design Theme's Footer

	Copyright: 2012, D5 Creation, www.d5creation.com

	Based on the Simplest D5 Framework for WordPress

	Since Design 1.0

*/

?>









</div> <!-- conttainer -->

<div id="footer">



<?php

   	get_sidebar( 'footer' );

?>

</div> <!-- footer -->



<div id="creditline">&copy;&nbsp;<?php echo date("Y") ?>&nbsp;<?php bloginfo( 'name' ); design_credit(); ?></div>



<?php wp_footer(); ?>

</body>

</html>
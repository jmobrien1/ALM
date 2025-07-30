/*-----------------------------------------------------------------------------------



 	Custom JS - All front-end jQuery

 

-----------------------------------------------------------------------------------*/

 





jQuery(document).ready(function() {

								

								

 if (jQuery().superfish) {	

 

   jQuery('ul.nav').superfish({ 

            delay:       200,                            // one second delay on mouseout 

            animation:   {opacity:'show',height:'show'},  // fade-in and slide-down animation 

            speed:       'fast',                          // faster animation speed 

            autoArrows:  false,                           // disable generation of arrow mark-up 

            dropShadows: false                            // disable drop shadows 

        }); 

} 



 if (jQuery().coinslider) {



jQuery('#coin-slider').coinslider({ width: 920, height: 350,navigation: true, delay: 3000})



}



});
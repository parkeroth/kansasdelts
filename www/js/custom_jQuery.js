/************************
 *      
 *      GENERIC DOCUMENT READY FUNCTION
 *              sets things in motion by auto-firing once the
 *              current page is fully loaded
 *
 *************************/
$(document).ready(function() {
    //setup fancybox handler
    $('.popOut').deltFancyBox();
    //this section handles the login slider
    $('#login form').hide();
    $('#login a').toggle(function() {
        $(this).addClass('active').next('form').slideDown();
    }, function() {
        $(this).removeClass('active').next('form').slideUp();
    });
    $('#login form :submit').click(function() {
        $(this).parent().prev('a').click();
    });
});

/************************
 *
 *      CUSTOM PLUGINS
 *              developed custom for the delt site
 *
 *************************/
(function($) { /* Fancybox Handler Function */
    $.fn.deltFancyBox = function() {
        //this section handles all fancyboxes
        //to use: give element a class of "popOut"
        return $(this).fancybox({
            'hideOnContentClick': false,
            'zoomSpeedIn': 300,
            'zoomSpeedOut': 300,
            'overlayShow': true
        });
    };
})(jQuery);
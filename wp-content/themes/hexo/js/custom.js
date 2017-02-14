/**
 * File custom.js.
 *
 * Handles custom scripts required by the theme
 */
( function( $ ) {
 var $hamburger = $(".hamburger");
  $hamburger.on("click", function(e) {
    $hamburger.toggleClass("is-active");
  });

	// $( ".menu-item-has-children" ).click(function() {
  // $( ".dropdown-menu" ).slideDown( "slow", function() {
  //   // Animation complete.
  // });
	// });

  //smootscroll
  $('.to-top').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 1000);
        return false;
      }
    }
  });

} )( jQuery );

/* Page Loader
    -----------------------------------------------*/

if ((".loader").length) {
      // show Preloader until the website ist loaded
      $(window).on('load', function () {
        $(".loader").fadeOut("slow");
      });
    }


/* Nivo lightbox
    -----------------------------------------------*/
  $('#hotels .col-md-4 a').nivoLightbox({
        effect: 'fadeScale',
    });


/* Istope Portfolio
-----------------------------------------------*/
jQuery(document).ready(function($){

  if ( $('.iso-box-wrapper').length > 0 ) { 

      var $container  = $('.iso-box-wrapper'), 
        $imgs     = $('.iso-box img');

      $container.imagesLoaded(function () {

        $container.isotope({
        layoutMode: 'fitRows',
        itemSelector: '.iso-box'
        });

        $imgs.load(function(){
          $container.isotope('reLayout');
        })

      });

      //filter items on button click

      $('.filter-wrapper li a').click(function(){

          var $this = $(this), filterValue = $this.attr('data-filter');

      $container.isotope({ 
        filter: filterValue,
        animationOptions: { 
            duration: 750, 
            easing: 'linear', 
            queue: false, 
        }                
      });             

      // don't proceed if already selected 

      if ( $this.hasClass('selected') ) { 
        return false; 
      }

      var filter_wrapper = $this.closest('.filter-wrapper');
      filter_wrapper.find('.selected').removeClass('selected');
      $this.addClass('selected');

        return false;
      }); 

  }

});


$(document).ready(function() {

  /* Hide mobile menu after clicking on a link
    -----------------------------------------------*/
    $('.navbar-collapse a').click(function(){
        $(".navbar-collapse").collapse('hide');
    });


  /*  smoothscroll
  ----------------------------------------------*/
   $(function() {
        $('#banner a, .navbar-default a').bind('click', function(event) {
            var $anchor = $(this);
            $('html, body').stop().animate({
                scrollTop: $($anchor.attr('href')).offset().top - 49
            }, 1000);
            event.preventDefault();
        });
    });
  

    /* Magnific Popup
-----------------------------------------------*/
$(document).ready(function() {
    $('.popup-youtube').magnificPopup({
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,
         fixedContentPos: false,
    });
});
    

  /* home slideshow section
  -----------------------------------------------*/
  $(function(){
    jQuery(document).ready(function() {
    $('#banner').backstretch([
       "images/banner-bg-01.jpg", 
       "images/banner-bg-02.jpg",
       "images/banner-bg-03.jpg",
        ],  {duration: 3000, fade: 900});
    });
  })


   

  /* Parallax section
    -----------------------------------------------*/
  function initParallax() {
    $('#about').parallax("100%", 0.1);
    $('#todo').parallax("100%", 0.3);
    $('#about').parallax("100%", 0.1);
    $('#video-sec').parallax("100%", 0.2);
    $('#location').parallax("100%", 0.3);
    $('#team').parallax("100%", 0.3);
    $('#hotels').parallax("100%", 0.1);
    $('#contact').parallax("100%", 0.2);
  }
  initParallax();


  


  /* wow
  -------------------------------*/
  new WOW({ mobile: false }).init();

  });


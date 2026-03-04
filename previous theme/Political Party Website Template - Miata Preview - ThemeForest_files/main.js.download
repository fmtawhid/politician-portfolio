(function ($) {
 "use strict";
 
    /*----------------------------
    jQuery MeanMenu
    ------------------------------ */
    $('nav#dropdown').meanmenu({
        meanScreenWidth: "991",
        meanMenuContainer: ".mobile-menu",
    });
    
    /*----------------------------
     counter js active
    ------------------------------ */
    $('.counter').counterUp({
        delay: 10,
        time: 2000
    });
    
    
    /* Slider active */
    $('.slider-active').owlCarousel({
        loop: true,
        nav: true,
        autoplay: false,
        autoplayTimeout: 5000,
        navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        item: 4,
        margin:30,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 2
            },
            768: {
                items: 2
            },
            992: {
                items: 3
            },
            1200: {
                items: 4
            }
        }
    });
    
    
    
    
    /*----------------------------
     owl active
    ------------------------------ */  
    $('.slider-active2').owlCarousel({
        loop: true,
        nav: true,
        autoplay: false,
        autoplayTimeout: 5000,
        navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
        animateOut: 'fadeOut',
        animateIn: 'fadeIn',
        item: 3,
        margin:30,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 2
            },
            768: {
                items: 2
            },
            992: {
                items: 3
            },
            1200: {
                items: 3
            }
        }
    });
    
    
    
    
    /*----------------------------
     owl active
    ------------------------------ */  
     $('.slider-active3').owlCarousel({
        loop: true,
        nav: true,
        autoplay: false,
        autoplayTimeout: 5000,
        navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
        item: 1,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 1
            },
            768: {
                items: 1
            },
            992: {
                items: 1
            },
            1200: {
                items: 1
            }
        }
    });
    
    
    
    
    /*----------------------------
     owl active
    ------------------------------ */  
     $('.slider-active4').owlCarousel({
        loop: true,
        nav: true,
        autoplay: false,
        autoplayTimeout: 5000,
        navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
        item: 3,
        margin: 30,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 2
            },
            768: {
                items: 2
            },
            992: {
                items: 3
            },
            1200: {
                items: 3
            }
        }
    });
    
    
    /*----------------------------
     owl active
    ------------------------------ */  
     $('.slider-active5').owlCarousel({
        loop: true,
        nav: true,
        autoplay: false,
        autoplayTimeout: 5000,
        navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
        item: 4,
        margin: 30,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 2
            },
            768: {
                items: 2
            },
            992: {
                items: 3
            },
            1200: {
                items: 4
            }
        }
    });
    
    
    
    /*----------------------------
     owl active
    ------------------------------ */  
     $('.slider-active6').owlCarousel({
        loop: true,
        nav: false,
        autoplay: false,
        autoplayTimeout: 5000,
        item: 1,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 1
            },
            768: {
                items: 1
            },
            992: {
                items: 1
            },
            1200: {
                items: 1
            }
        }
    });
    
    /*----------------------------
     owl active
    ------------------------------ */  
     $('.three-tab2').owlCarousel({
        loop: true,
        nav: true,
        autoplay: false,
        autoplayTimeout: 5000,
        navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
        item: 4,
        margin: 30,
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 2
            },
            768: {
                items: 2
            },
            992: {
                items: 3
            },
            1200: {
                items: 4
            }
        }
    });
    
    /*--------------------------
    tab active
    ---------------------------- */
	var ProductDetailsSmall = $('.three-tab2 a');
	
    ProductDetailsSmall.on('click', function(e) {
        e.preventDefault();
        
        var $href = $(this).attr('href');
        
        ProductDetailsSmall.removeClass('active');
        $(this).addClass('active');
        
        $('.tab-content2 .tab-pane').removeClass('active');
        $('.tab-content2 ' + $href).addClass('active');
    })
    
    
    
    
    
    /*--------------------------
    scrollUp
    ---------------------------- */	
    $(window).on('scroll',function () {
        if($(window).scrollTop()>200) {
            $("#toTop").fadeIn();
        } else {
            $("#toTop").fadeOut();
        }
    });
    $('#toTop').on('click', function() {
        $("html,body").animate({
            scrollTop:0
        }, 500)
    });   
    
    /*--
    Magnific Popup
    ------------------------*/
    $('.video-popup').magnificPopup({
        type: 'iframe',
        mainClass: 'mfp-fade',
        removalDelay: 160,
        preloader: false,
        zoom: {
            enabled: true,
        }
    });
    
    /*---------------------
    countdown
    --------------------- */
    $('[data-countdown]').each(function() {
		var $this = $(this), finalDate = $(this).data('countdown');
		$this.countdown(finalDate, function(event) {
            $this.html(event.strftime('<span class="cdown day">%-D <p>Days</p></span> <span class="cdown hour">%-H <p>Hour</p></span> <span class="cdown minutes">%M <p>Min</p></span class="cdown second"> <span>%S <p>Sec</p></span>'));
		});
    });
    
    //venbox start
    $('.venobox').venobox({
        numeratio: true,
        infinigall: true,
        titleattr: 'data-title'
    })
    //venbox end
    
    /*---------------------
    chosen
    --------------------- */
    jQuery('.orderby').chosen({disable_search: true, width: "auto"});
    
    /*----------------------------
     sticky active
    ------------------------------ */  
    $(window).on('scroll',function() {    
        var scroll = $(window).scrollTop();
        if (scroll < 245) {
            $('.stick-h2').removeClass('stick');
        }else{
            $('.stick-h2').addClass('stick');
        }
    }); 
    
    
    /*----------------------------
     wow js active
    ------------------------------ */
	new WOW().init();
    
    
    
    
 
 
})(jQuery); 
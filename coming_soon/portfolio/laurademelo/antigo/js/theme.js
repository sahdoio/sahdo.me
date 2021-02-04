//	
//	Arquivo javascript mestre 
//


/////////////////////////////////////////////////////////////////////
// jQuery para o efeito scrolling feature - requires jQuery Easing plugin
/////////////////////////////////////////////////////////////////////
$('.page-scroll').bind('click', function(event) {
    var $anchor = $(this);
		$('html, body').stop().animate({
			scrollTop: $($anchor.attr('href')).offset().top -5
		}, 
		1500 //deplay scroll
	);
	
    event.preventDefault();
});

////////////////////////////////////////////////////////////////////////
// Animação do "sublinhado" que fica em cima dos links da navbar https://github.com/codrops/AnimatedHeader
////////////////////////////////////////////////////////////////////////
var cbpAnimatedHeader = (function() {

    var docElem = document.documentElement,
        header = document.querySelector( '.navbar-fixed-top' ),
        didScroll = false,
        changeHeaderOn = 10;

    function init() {
        window.addEventListener( 'scroll', function( event ) {
            if( !didScroll ) {
                didScroll = true;
                setTimeout( scrollPage, 100 );
            }
        }, false );
    }

    function scrollPage() {
        var sy = scrollY();
        if ( sy >= changeHeaderOn ) {
            classie.add( header, 'navbar-shrink' );
        }
        else {
            classie.remove( header, 'navbar-shrink' );
        }
        didScroll = false;
    }

    function scrollY() {
        return window.pageYOffset || docElem.scrollTop;
    }

    init();
})();


//////////////////////////////////////////////
// Sublinha acima dos links da nav bar apos o scrolling ocorrer
//////////////////////////////////////////////
$('body').scrollspy({
    target: '.navbar',
    offset: 200
}) 



///////////////////////////////////////////
// Display loading image while page loads
///////////////////////////////////////////
// Wait for window load
$(window).load(function() {
    // Animate loader off screen
    $(".page-loader").fadeOut("slow");
});


////////////////////////////////////////////////////
// OWL Carousel: http://owlgraphic.com/owlcarousel
////////////////////////////////////////////////////
// Intro text carousel
$("#owl-intro-text").owlCarousel({
    singleItem : true,
    autoPlay : 4000,
    stopOnHover : false,
    navigation : false,
    navigationText : false,
    pagination : false
})

// Partner carousel
$("#owl-partners").owlCarousel({
    items : 4,
    itemsDesktop : [1199,3],
    itemsDesktopSmall : [980,2],
    itemsTablet: [768,2],
    autoPlay : 5000,
    stopOnHover : true,
    pagination : false
})

// Testimonials carousel
$("#owl-family").owlCarousel({
    singleItem : true,
    pagination : true,
    autoHeight : false,
	autoPlay : 9000
})


////////////////////////////////////////////////////////////////////
// Stellar (parallax): https://github.com/markdalgleish/stellar.js
////////////////////////////////////////////////////////////////////
$.stellar({
    // Set scrolling to be in either one or both directions
    horizontalScrolling: false,
    verticalScrolling: true,
});


///////////////////////////////////////////////////////////
// WOW animation scroll: https://github.com/matthieua/WOW
///////////////////////////////////////////////////////////
new WOW().init();


////////////////////////////////////////////////////////////////////////////////////////////
// spacer-family-Up (requires jQuery waypoints.js plugin): https://github.com/bfintal/Counter-Up
////////////////////////////////////////////////////////////////////////////////////////////
$('.spacer-family').counterUp({
    delay: 10,
    time: 2000
});



////////////////////////////////////////////////////////////////////////////////////////////
// Isotop Package
////////////////////////////////////////////////////////////////////////////////////////////
$(window).load(function() {
	$('.family_menu ul li').click(function(){
		$('.family_menu ul li').removeClass('active_prot_menu');
		$(this).addClass('active_prot_menu');
	});
	
	var $container = $('#family');
	$container.isotope({
	  itemSelector: '.col-sm-4',
	  layoutMode: 'fitRows'
	});
	
	$('#filters').on( 'click', 'a', function() {
	  var filterValue = $(this).attr('data-filter');
	  $container.isotope({ filter: filterValue });
	  return false;
	});
});



/////////////////////////
// Scroll to top button
/////////////////////////
// Check to see if the window is top if not then display button
$(window).scroll(function(){
    if ($(this).scrollTop() > 100) {
        $('.scrolltotop').fadeIn();
    } else {
        $('.scrolltotop').fadeOut();
    }
});

// Click event to scroll to top
$('.scrolltotop').click(function(){
    $('html, body').animate({scrollTop : 0}, 1500, 'easeInOutExpo');
    return false;
});


////////////////////////////////////////////////////////////////////
// Close mobile menu when click menu link (Bootstrap default menu)
////////////////////////////////////////////////////////////////////
$(document).on('click','.navbar-collapse.in',function(e) {
    if( $(e.target).is('a') && $(e.target).attr('class') != 'dropdown-toggle' ) {
        $(this).collapse('hide');
    }
});

////////////////////////////////////////////////////////////////////
// Google Map Customization
////////////////////////////////////////////////////////////////////
(function(){

	var map;

	map = new GMaps({
		el: '#gmap',
		lat: -3.036018,
		lng: -59.999091,
		scrollwheel:true,
		zoom: 14,
		zoomControl : false,
		panControl : false,
		streetViewControl : false,
		mapTypeControl: false,
		overviewMapControl: false,
		clickable: false
	});

	var image = 'img/map-icon.png';
	map.addMarker({
		lat: -3.036018,
		lng: -59.999091,
		icon: image,
		animation: google.maps.Animation.DROP,
		verticalAlign: 'bottom',
		horizontalAlign: 'center',
		backgroundColor: '#3e8bff',
	});

	var styles = [ 

	{
		"featureType": "road",
		"stylers": [
		{ "color": "#deb8e1" }
		]
	},{
		"featureType": "water",
		"stylers": [
		{ "color": "#d8d8d8" }
		]
	},{
		"featureType": "landscape",
		"stylers": [
		{ "color": "#f1f1f1" }
		]
	},{
		"elementType": "labels.text.fill",
		"stylers": [
		{ "color": "#000000" }
		]
	},{
		"featureType": "poi",
		"stylers": [
		{ "color": "#d9d9d9" }
		]
	},{
		"elementType": "labels.text",
		"stylers": [
		{ "saturation": 1 },
		{ "weight": 0.1 },
		{ "color": "#000000" }
		]
	}

	];

	map.addStyle({
		styledMapName:"Styled Map",
		styles: styles,
		mapTypeId: "map_style"  
	});

	map.setStyle("map_style");
}());

// Portfolio Items
$(document).ready(function() {
		// jQuery Lightbox
		$(".litebox").liteBox();	
});


$(window).load(function(){
		var $container = $('#gallery_list');
		
		$container.isotope({
			filter: '*',
			animationOptions: {
				duration: 750,
				easing: 'linear',
				queue: false
			}
		});
	 
		$('.gallery-filters li').click(function(){
			$('.gallery-filters .active').removeClass('active');
			$(this).addClass('active');
	 
			var selector = $(this).attr('data-filter');
			$container.isotope({
				filter: selector,
				animationOptions: {
					duration: 750,
					easing: 'linear',
					queue: false
				}
			 });
			 return false;
		});
});



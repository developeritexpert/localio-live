window.addEventListener('load', () => {
  aos.init();
});

//product- comparison page slider

$(document).ready(function () {
  $('.compari-pack').slick({
     infinite: false,
     slidesToShow: 1,
     slidesToScroll: 1,
     dots: false,
     arrows: false,
     infinite: true,


  });
});


// header dropdown at mobile js

//  $('.mobile-drop').on('click', function (e) {
//    e.preventDefault();
//    $('.mobile-drop').not(this).removeClass('menu-active'); 
//       $(this).toggleClass('menu-active');
//    $('.mobile-drop').not(this).find('.mob-drp-contnt').removeClass('show');
//     $(this).find('.mob-drp-contnt').toggleClass('show');
   
//  });



// // // Scroll blocker functions
// function preventScroll(e) {
//   e.preventDefault();
// }
// function disableScrollByMouse() {
//   window.addEventListener('wheel', preventScroll, { passive: false });
//   window.addEventListener('mousewheel', preventScroll, { passive: false });
// }
// function enableScrollByMouse() {
//   window.removeEventListener('wheel', preventScroll);
//   window.removeEventListener('mousewheel', preventScroll);
// }

// // Your click handler with scroll toggle
// $('.mobile-drop').on('click', function (e) {
//   e.preventDefault();

//   // Remove class from other elements
//   $('.mobile-drop').not(this).removeClass('menu-active');
//   $('.mobile-drop').not(this).find('.mob-drp-contnt').removeClass('show');

//   // Toggle current menu
//   $(this).toggleClass('menu-active');
//   $(this).find('.mob-drp-contnt').toggleClass('show');

//   // Scroll toggle based on class presence
//   if ($(this).hasClass('menu-active')) {
//     disableScrollByMouse();
//   } else {
//     enableScrollByMouse();
//   }



  
// });



function preventScroll(e) {
  e.preventDefault();
}

function disableScrollByMouse() {
  window.addEventListener('wheel', preventScroll, { passive: false });
  window.addEventListener('mousewheel', preventScroll, { passive: false });
}

function enableScrollByMouse() {
  window.removeEventListener('wheel', preventScroll);
  window.removeEventListener('mousewheel', preventScroll);
}

// Your click handler with scroll toggle
$('.mobile-drop').on('click', function (e) {
  e.preventDefault();
  e.stopPropagation(); // Prevent this click from reaching document click handler

  // Remove class from other elements
  $('.mobile-drop').not(this).removeClass('menu-active');
  $('.mobile-drop').not(this).find('.mob-drp-contnt').removeClass('show');

  // Toggle current menu
  $(this).toggleClass('menu-active');
  const isActive = $(this).hasClass('menu-active');
  $(this).find('.mob-drp-contnt').toggleClass('show', isActive);

  // Scroll toggle based on class presence
  if (isActive) {
    disableScrollByMouse();
  } else {
    enableScrollByMouse();
  }
});

// Click handler for document to close dropdown when clicking outside
$(document).on('click', function(e) {
  if (!$(e.target).closest('.mobile-drop').length) {
    // Click occurred outside of any dropdown
   //  alert('hii');
    $('.mobile-drop').removeClass('menu-active');
    $('.mob-drp-contnt').removeClass('show');
    enableScrollByMouse();
  }
});

// Optional: Prevent dropdown content clicks from closing the dropdown
$('.mob-drp-contnt').on('click', function(e) {
  e.stopPropagation();
});







$('.close_btn_mobile svg').on('click', function () {
  $('#navbarSupportedContent').removeClass('show');
  $('.navbar-toggler').addClass('collapsed');
});










// $(document).ready(function () {
//     // Handle click on .mobile-drop <li>
//     $('.mobile-drop').on('click', function (e) {
//         e.stopPropagation(); // Stop bubbling to document click

//         // Close other dropdowns if open
//         $('.mobile-drop').not(this).removeClass('menu-active')
//             .find('.mob-drp-contnt').removeClass('show');

//         // Toggle current dropdown
//         $(this).toggleClass('menu-active');
//         $(this).find('.mob-drp-contnt').toggleClass('show');
//     });

//     // Close on clicking outside
//     $(document).on('click', function (e) {
//         if (
//             $(e.target).closest('.mobile-drop').length === 0 &&
//             $(e.target).closest('.mob-drp-contnt').length === 0
//         ) {
//             $('.mobile-drop').removeClass('menu-active')
//                 .find('.mob-drp-contnt').removeClass('show');
//         }
//     });
// });






$(document).ready(function () {
  var $counter = $('.slide-count');
  var $slider = $('.adp_slider');

  $slider.on('init reInit afterChange', function (event, slick, currentSlide, nextSlide) {
     var i = (currentSlide ? currentSlide : 0) + 1;
     $counter.text(i + '/' + slick.slideCount);
  });

  $slider.slick({
     infinite: false,
     dots: false,
     arrows: true,
     autoplay: true,

  });
});


const optionMenu = document.querySelector(".select-menu"),
  selectBtn = optionMenu.querySelector(".select-btn"),
  options = optionMenu.querySelectorAll(".option"),
  sBtn_text = optionMenu.querySelector(".sBtn-text");

selectBtn.addEventListener("click", () =>
  optionMenu.classList.toggle("active")
);

options.forEach((option) => {
  option.addEventListener("click", () => {
     let selectedOption = option.querySelector(".option-text").innerText;
     sBtn_text.innerText = selectedOption;

     optionMenu.classList.remove("active");
  });
});
// Close dropdown if clicked outside
document.addEventListener("click", (e) => {
    if (!optionMenu.contains(e.target)) {
      optionMenu.classList.remove("active");
    }
  });

$(document).ready(function () {
  $('.reviews_slider').slick({
     infinite: false,
     slidesToShow: 4,
     slidesToScroll: 4,
     dots: false,
     arrows: true,
     prevArrow: '.reviews-prev',
     nextArrow: '.reviews-next',
     autoplay: false,
     swipeToSlide: true,
     responsive: [{
            breakpoint: 1200,
            settings: {
               slidesToShow: 3.2,
               slidesToScroll: 3
            }
         },
         {
            breakpoint: 991,
            settings: {
               slidesToShow: 2.2,
               slidesToScroll: 2
            }
         },
         {
            breakpoint: 575,
            settings: {
               slidesToShow: 1.2,
               slidesToScroll: 1
            }
         }
      ]
   });
});

$(document).ready(function () {
  $('.xclusve-slider').slick({
     slidesToShow: 3,
     slidesToScroll: 1,
     dots: false,
     arrows: true,
     infinite: false,
     responsive: [{
           breakpoint: 992,
           settings: {
              slidesToShow: 2,
              slidesToScroll: 1,
              infinite: false,
              dots: false,
           }
        },
        {
           breakpoint: 767,
           settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
              infinite: false,
              dots: false,
           }
        },
        {
           breakpoint: 575,
           settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
              infinite: true,
              dots: false,
              arrows: true,
           }
        },
     ]

  });
});

$(document).ready(function () {
  $('.top-rated-slider').slick({
     slidesToShow: 4,
     slidesToScroll: 1,
     dots: false,
     arrows: true,
     infinite: false,
     responsive: [{
           breakpoint: 1599,
           settings: {
              slidesToShow: 3,
              slidesToScroll: 1,
              infinite: false,
              dots: false,
           }
        },
        {
           breakpoint: 991,
           settings: {
              slidesToShow: 2,
              slidesToScroll: 1,
              infinite: false,
              dots: false,
           }
        },

        {
           breakpoint: 767,
           settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
              infinite: false,
              dots: false,
           }
        },
     ]
  });
});

$(document).ready(function () {
  $('.trust-brnd-marque').slick({
     slidesToShow: 6,
     slidesToScroll: 1,
     autoplay: true,
     autoplaySpeed: 1,
     speed: 5000,
     dots: false,
     arrows: false,
     cssEase: 'linear',
     waitForAnimate: false,
     pauseOnFocus: false,
     pauseOnHover: false,
     infinite: true,
     responsive: [{
           breakpoint: 991,
           settings: {
              slidesToShow: 4,
              slidesToScroll: 1,
              infinite: true,
              dots: false,
           }
        },
        {
           breakpoint: 575,
           settings: {
              slidesToShow: 2,
              slidesToScroll: 1,
              infinite: true,
              dots: false,
           }
        },

     ]
  });

  // product detail page js


  $('.slider-for').slick({
     slidesToShow: 1,
     slidesToScroll: 1,
     arrows: false,
     fade: true,
     asNavFor: '.slider-nav'
  });
  $('.slider-nav').slick({
     slidesToShow: 5,
     slidesToScroll: 1,
     asNavFor: '.slider-for',
     dots: false,
     focusOnSelect: true
  });

  $('#togglePassword').on('click', function () {
     let passwordField = $('#password');
     let icon = $(this).find('i');

     // Toggle the password field type
     if (passwordField.attr('type') === 'password') {
        passwordField.attr('type', 'text');
        icon.removeClass('fa-eye-slash').addClass('fa-eye');
     } else {
        passwordField.attr('type', 'password');
        icon.removeClass('fa-eye').addClass('fa-eye-slash');
     }
  });

});


// range js
window.onload = function () {
  slideOne();
  slideTwo();
};

let sliderOne = document.getElementById("slider-1");
let sliderTwo = document.getElementById("slider-2");
let displayValOne = document.getElementById("range1");
let displayValTwo = document.getElementById("range2");
let minGap = 0;
let sliderTrack = document.querySelector(".range-slider-track");
let sliderMaxValue = document.getElementById("slider-1").max;

function slideOne() {
  if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap) {
     sliderOne.value = parseInt(sliderTwo.value) - minGap;
  }
  displayValOne.textContent = sliderOne.value;
  fillColor();
}

function slideTwo() {
  if (parseInt(sliderTwo.value) - parseInt(sliderOne.value) <= minGap) {
     sliderTwo.value = parseInt(sliderOne.value) + minGap;
  }
  displayValTwo.textContent = sliderTwo.value;
  fillColor();
}

function fillColor() {
  percent1 = (sliderOne.value / sliderMaxValue) * 100;
  percent2 = (sliderTwo.value / sliderMaxValue) * 100;
  sliderTrack.style.background = `linear-gradient(to right, #dadae5 ${percent1}% , #06498b ${percent1}% , #06498b ${percent2}%, #dadae5 ${percent2}%)`;
}

// header search js /////////////////////////////////////////////////////////
// myID = document.getElementById("myID");

// var myScrollFunc = function () {
//     var y = window.scrollY;
//     if (y >= 200) {
//         myID.className = "bottomMenu show"
//     } else {
//         myID.className = "bottomMenu hide"
//     }
// };

window.addEventListener("scroll", myScrollFunc);


// search bar

function checkScroll() {
   var myElement = document.getElementById("myID");

   // Check if the page has been scrolled 200px or more
   if (window.scrollY > 460) {
     myElement.style.visibility = "visible";  // Show the element
   } else {
     myElement.style.visibility = "hidden";   // Hide the element
   }
 }

 // Add the scroll event listener
 window.addEventListener("scroll", checkScroll);

 // Run the checkScroll function on initial page load to account for already scrolled pages
 window.onload = checkScroll;



//  14-april-2025

// jQuery(window).scroll(function () {
//     var scroll = jQuery(window).scrollTop();
//     if (scroll >= 200) {
//         jQuery(".asn_main_sec > .asn_dv").addClass("fixed-div");
//     } else {
//         jQuery(".asn_main_sec > .asn_dv").removeClass("fixed-div");
//     }
//   });

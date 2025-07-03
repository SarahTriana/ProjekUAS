$(document).ready(() => {
    $(document).scroll(()=> {
        $('.navbar').toggleClass('scrolled', $(this).scrollTop() > $('.navbar').height())
    })
})


      var swiper = new Swiper(".mySwiperTesti", {
        spaceBetween: 20,
        loop: true,
        pagination: {
          el: ".swiper-pagination",
          dynamicBullets: true,
        },
        autoplay: {
            delay : 1200
        },
        breakpoints: {
            768: {
                slidesPerView: 2,
            },
            1200:{
                slidesPerView: 3,
            }
        }
      });
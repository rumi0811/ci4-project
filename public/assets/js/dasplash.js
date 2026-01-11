var is_chrome = navigator.userAgent.indexOf('Chrome') > -1;
var is_explorer = navigator.userAgent.indexOf('MSIE') > -1;
var is_firefox = navigator.userAgent.indexOf('Firefox') > -1;
var is_safari = navigator.userAgent.indexOf("Safari") > -1;
var is_opera = navigator.userAgent.toLowerCase().indexOf("op") > -1;
if ((is_chrome) && (is_safari)) {
  is_safari = false;
}
if ((is_chrome) && (is_opera)) {
  is_chrome = false;
}

$(document).ready(function() {

  if ((is_chrome) || (is_firefox)) {

    // $('<div id="dasplash2"></div><div id="dasplash"></div>').prependTo("body");
    $('<div id="dasplash"></div>').prependTo("body");

    var currentMousePos = {
      x: -1,
      y: -1
    };
    $(document).mousemove(function(event) {
      currentMousePos.x = event.pageX;
      currentMousePos.y = event.pageY;
    });

    function dasplashBig() {
      $("#dasplash").css({
        'top': (currentMousePos.y - 10),
        'left': (currentMousePos.x - 10),
        'opacity': '.2',
        'transform': 'scale(10)'
      });

      setTimeout(function() {
        $("#dasplash").css({
          'transition': 'all 0.45s ease-out',
          'transform': 'scale(200)',
          'background-color': '#fff',
          'opacity': '1'
        });
      }, 150);

      setTimeout(function() {
        $("#dasplash").addClass("dasplash-white");
      }, 150);
    }

    function dasplashSmall() {
      $("#dasplash").css({
        'top': (currentMousePos.y - 10),
        'left': (currentMousePos.x - 10),
        'transition': 'all .5s ease-in',
        'opacity': '.2',
        'transform': 'scale(10)'
      });

      setTimeout(function() {
        $("#dasplash").css({
          'opacity': '0',
        });
      }, 250);

      setTimeout(function() {
        $("#dasplash").removeAttr("style");
      }, 500);
    }

    // function dasplash2Small() {
    //   $("#dasplash2").css({
    //     'top': (currentMousePos.y - 10),
    //     'left': (currentMousePos.x - 10),
    //     'transition': 'all .5s ease-in',
    //     'opacity': '.5',
    //     'transform': 'scale(5)'
    //   });

    //   setTimeout(function() {
    //     $("#dasplash2").css({
    //       'opacity': '0',
    //     });
    //   }, 250);

    //   setTimeout(function() {
    //     $("#dasplash2").removeAttr("style");
    //   }, 500);
    // }

    // $("body").mousedown(function() {
      // dasplash2Small();
    // });

    $("a").click(function(e) {
      e.preventDefault();
      var href = $(this).attr('href');
      var data_action = $(this).attr('data-action');

      if (typeof data_action !== typeof undefined && data_action !== false) {
        return;
      }

      if ((href == "#") || (href == null) || (href == "javascript:void(0);")) {
        return;
      }

      if (href != null) {
        dasplashBig();

        setTimeout(function() {
          $("#header").css("margin-top","");
          $("#left-panel").css("left","-220px");
          $("#main").css({"opacity":"0","margin-left":"100%","transform":"scale(0,1)"});
        }, 30);

        setTimeout(function() {
          window.location.href = href;
        }, 50);
      }

    });

  }

});

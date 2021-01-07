var callback_url = updater.callback;
var scopes = 'user-top-read';
var client_id = updater.client_id;
var ajax_url = updater.ajaxurl;
var response_type = "token";
var avl_tracks = null;
var tracks_rand = [];
var iterator = 0;
var updated = false;
var loader_url = updater.loader_url;

jQuery(document).ready(function () {
  jQuery('#img-wrapper').fadeIn(1500);
});

function update() {
  jQuery("#spot-content-wrapper").fadeIn();
  jQuery("#img-wrapper").hide();
  jQuery("#spot-menu").fadeIn();
  jQuery("#tops_anzeige").fadeIn();
  jQuery("#spotify_login").fadeIn();
};

jQuery("#spotify_login").on("click", function () {
  jQuery("#form-wrapper").fadeIn();
  jQuery("#spot-log-wrapper").fadeIn();
  jQuery("#tops_anzeige").hide();
  jQuery("#spotify_login").hide();
  jQuery("#spot-menu").hide();
});

jQuery("#tops_anzeige").on("click", function () {
  jQuery("#spot-tops").fadeIn();
  jQuery("#tops_anzeige").hide();
  jQuery("#spotify_login").hide();
  jQuery("#spot-menu").hide();
  ukv_tops();
});

jQuery("#close").on("click", function () {
  jQuery("#spot-content-wrapper").hide();
  jQuery("#img-wrapper").fadeIn();
  jQuery("#spot-tops").hide();
  jQuery("#spot-log-wrapper").hide();
  jQuery("#spot-menu").hide();
});

function login() {
      var popup = window.open(
        "https://accounts.spotify.com/authorize?client_id=" +
          client_id +
          "&response_type=" +
          response_type +
          "&redirect_uri=" +
          callback_url +
          "&scope=" +
          scopes +
          "&show_dialog=true",
        "Spotify Login",
        "width = 800, height = 600"
      );
      updated = true;
        jQuery("#spot-content-wrapper").hide();
        jQuery("#img-wrapper").fadeIn();
        jQuery("#spot-tops").hide();
        jQuery("#spot-log-wrapper").hide();
        jQuery("#spot-menu").hide();
    
}

function ukv_tops() {
  jQuery("#spotify_loader").fadeIn();
  if(avl_tracks == null || avl_tracks == 0 || avl_tracks == 1 || updated == true){
    jQuery("#spot-tops")
      .html(
        '<img id="spotify_loader" style="height:30px; width: 30px;" src="'+ loader_url + '" ></img>'
      )
      .fadeIn();
    jQuery("#carouselItem_" + tracks_rand[iterator] + " > .trackTitle").hide();
    jQuery.ajax({
      type: "post",
      url: ajax_url,
      dataType: "text",
      data: {
        action: "show_ukv_tops",
      },
      error: function () {
        console.log("ERROR - TIMEOUT");
      },

      success: function (response) {
        
        jQuery('#spot-tops').html(response).fadeIn();
        avl_tracks = jQuery('td').length - 2;
        i = 0;
        if(avl_tracks == 1){
          updated = false;
          tracks_rand = [];
          tracks_rand.push(0);
          jQuery("#carouselItem_" + tracks_rand[i]).fadeIn();
        }else{
          updated = false;
          while(tracks_rand.length != 0){
            tracks_rand.pop();
          }
          while (tracks_rand.length < avl_tracks && 2 <= avl_tracks){

            var r = getRandomIntInclusive(0, avl_tracks-1);
            if(!tracks_rand.includes(r)){
              tracks_rand.push(r);
            } 
          }
          while (jQuery("#carouselItem_" + tracks_rand[i] + " > .trackTitle").text() == "" && 2 < avl_tracks){
            i++;
          };
          jQuery("#carouselItem_" + tracks_rand[i]).fadeIn();
        }
      },timeout : 30000
    });
  }else{
    jQuery("#spot-tops").fadeIn();
  }
}

function getNext(n){
  if(tracks_rand.length == 1){
    window.alert("Es sind noch keine Datenbankeinträge vorhanden.");
  }else{
    jQuery("#carouselItem_" + tracks_rand[iterator]).hide();
    iterator = iterator + n;
    if(iterator > tracks_rand.length - 1){
      iterator = 0;
    }else if (iterator < 0){
      iterator = tracks_rand.length - 1;
    }
    while(jQuery(
        "#carouselItem_" + tracks_rand[iterator] + " > .trackTitle").text() == "" 
      || jQuery(
        "#carouselItem_" + tracks_rand[iterator] + " > .artistName").text() == "" 
      || jQuery(
        "#carouselItem_" + tracks_rand[iterator] + " > .albumTitle").text() == "" 
      || jQuery(
        "#carouselItem_" + tracks_rand[iterator] + " > .songUrl").text() == "" 
      || jQuery(
        "#carouselItem_" + tracks_rand[iterator] + " > .albumImg").attr("src") == ""
    ) {
      iterator = iterator + n;
    }
    jQuery("#carouselItem_" + tracks_rand[iterator]).fadeIn(250);
    if (jQuery("#carouselItem_" + tracks_rand[iterator] + " > .trackTitle").prop("scrollWidth") > jQuery("#carouselItem_" + tracks_rand[iterator] + " > .trackTitle").width()){
      var scroll = function (width) {
        var i = null;
        if (width!=0){
          i = (width - jQuery("#carouselItem_" + tracks_rand[iterator] + " > .trackTitle").width()) * 40;
          if (i < 5000) {
            i = 5000;
          }
        }
        jQuery("#carouselItem_" + tracks_rand[iterator] + " > .trackTitle").animate(
          {
            scrollLeft: width,
          },
          i
        );
      };

      var loopForever = function (delay, callback) {
        var loop = function () {
          callback();
          setTimeout(loop, delay);
        };
        loop();
      };

      loopForever(5000, function () {
        scroll(jQuery("#carouselItem_" + tracks_rand[iterator] + " > .trackTitle").prop("scrollWidth"));
        setTimeout(3000);
        scroll(0);
        setTimeout(3000);
      });

    }
  }
}

function getRandomIntInclusive(min, max) {
  min = Math.ceil(min);
  max = Math.floor(max);
  return Math.floor(Math.random() * (max - min + 1)) + min;
}
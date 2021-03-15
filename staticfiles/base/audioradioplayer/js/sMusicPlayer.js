/** Copyright (C) 2016 Danial Sabagh | DanialSabagh.com - All Rights Reserved
 *  '<div><input class="share" type="radio"><div class="icon-facebook"></div><div class="icon-twitter"></div><div class="icon-email"></div></div>' + **/
(function ($) {

$(".sMusicPlayer").append('<div class="upperSec"><div class="leftSide"><div class="title"></div><div class="artist"></div></div>' +
    '<div class="rightSide">' +
    '<div><input class="settings" type="radio" checked><div class="icon-repeat"></div><div class="icon-shuffle"></div><div class="icon-volume"></div></div>' +
   
    '<div><input class="resource" type="radio"><div class="icon-apple"></div><div class="icon-amazon"></div><div class="icon-download"></div><div class="icon-cart"></div></div>' +
    '</div></div>' +
    '<div class="lowerSec">' +
    '<div class="playButton"></div><div class="seekbar"><div class="curT">00:00</div><div class="dueT">00:00</div><div class="bar"><div class="buffer"></div><div class="played"><div class="handle"></div></div></div> </div>' +
    '<div class="panel"><label class="btn2 share"></label><label class="btn3 resource"></label><label class="btn1 settings"></label></div></div>');

$.fn.MusicPlayer = function(options) {
    var settings = $.extend({
        // These are the defaults
        type: "",
        soundcloud_id: "",
        soundcloud_url: "",
        itunes_id:"",
        playlist: "false",
        playlist_path:"",
        soundcloud_playlist : "",
        title: "",
        artist: "",
        track_URL: "",
        downloadable: "false",
        apple_music: "",
        amazon_music: "",
        extra_purchase: "",
        ID3:"",
        autoplay: "false",
        theme: "",
        font_color:"",
        background: "",
        background_size: "",
        background_position: ""
    }, options);
    var audio, thisObj, playPauseBTN;
    audio = new Audio();
    audio.preload = "auto";
    thisObj = this;
    playPauseBTN = $(".playButton", thisObj); // button in the current instance
    var currentRow = 0;

    //Statuses Evnts
    $(audio).on("loadstart play waiting canplay seeking ratechange canplaythrough loadeddata progress", function (){
        //check the buffer works in all states in different browsers
        updateLoadProgress();

    });
    $(audio).on("loadedmetadata", function () {
        if (getReadableTime(this.duration) == "live"){
            $(".dueT", thisObj).addClass("live").text("Live");
        }else{
            $(".dueT", thisObj).text(getReadableTime(this.duration));
        }
        updateLoadProgress();
    });
    $(audio).on("playing", function() {
        updateLoadProgress();
        togglePlying(playPauseBTN, true);
        $(playPauseBTN, thisObj).addClass("pause");
        if(settings.playlist == "true"){
            if(currentRow == 0){currentRow++}
            $(".playlist > .row").removeClass("whiteBG");
            $(".playlist > div#" + currentRow, thisObj).addClass("whiteBG");
        }
    });
    $(audio).on("timeupdate", function() {
        $(".curT", thisObj).text(getReadableTime(this.currentTime));
        if (getReadableTime(this.duration) == "live") {
            $(".played", thisObj).css("width", "100%").addClass("meter");
            $(".handle", thisObj).css("opacity", "0");
        }
        else{
            $(".played", thisObj).css("width", audio.currentTime * (100 / audio.duration) + "%"); // UPDATEs SEEKBAR
        }
        updateLoadProgress();
    });
    $(audio).on("pause", function() {
        togglePlying(playPauseBTN, false);
        $(playPauseBTN, thisObj).removeClass("pause");
        updateLoadProgress();
    });
    $(audio).on("ended", function() {
        updateLoadProgress();
        var list_lendth = 0;
        if(settings.playlist == "true"){
            list_lendth = $(".playlist > div.row", thisObj).length;
            if($('.dueT', thisObj).hasClass("live")){
                removeLive();
            }
        }
        if($('.icon-shuffle', thisObj).hasClass("active")){
            var max = list_lendth;
            var randomNumber = Math.floor(Math.random() * max) + 1;
            currentRow = randomNumber;
            var next_track = $(".playlist > div#" + currentRow, thisObj);
            updateAudioSrc(next_track.attr("data-track_url"));
            updateTitle(next_track.attr("data-title"));
            updateArtist(next_track.attr("data-artist"));
            playManagement();
        }
        else{
            if(settings.playlist == "true" && currentRow < list_lendth){
                currentRow++;
                var next_track = $(".playlist > div#" + currentRow, thisObj);
                updateAudioSrc(next_track.attr("data-track_url"));
                updateTitle(next_track.attr("data-title"));
                updateArtist(next_track.attr("data-artist"));
                playManagement();
            }
            else if(settings.playlist == "true" && currentRow >= list_lendth || settings.playlist != "true"){
                audio.currentTime = 0;
                $(".playlist > .row").removeClass("whiteBG");
            }
        }


    });

    playPauseBTN.on("click tap", function() {
        playManagement();
    });

    $(".bar", thisObj).on('click tap',function (e) {
        x = e.pageX - $(this).offset().left,
            width = $(this).width(),
            duration = audio.duration;
        audio.currentTime = (x / width) * duration;

    });


    thisObj.each(function() {
        // sTART OF SETTINGS
        if (settings.autoplay == "true"){
            audio.autoplay = true;
        }
        thisObj.css({
            background: settings.background,
            "background-size": settings.background_size,
            "background-position": settings.background_position,
            color: settings.font_color,
        });
        if (settings.theme == "black"){
            thisObj.addClass("black");
        }
        // END OF SETTINGS

        var type = settings.type;
        if (type == "audio") {
            updateAudioSrc(settings.track_URL);
            if(settings.ID3 == "true"){
                // ID3 Check
                ID3.loadTags(audio.src, function() {
                    var tags = ID3.getAllTags(audio.src);
                    if(settings.title == ""){
                        updateTitle(tags.title);
                        settings.title = tags.title;
                    }
                    if(settings.artist == "") {
                        updateArtist(tags.artist);
                        settings.artist = tags.artist;
                    }
                });
            }


            updateTitle(settings.title);
            updateArtist(settings.artist);

        }
        else if(type == "soundcloud") {
            $(".dueT", thisObj).before("<a class='icon-soundcloud branding' href='" + settings.soundcloud_url + "' target='_blank' title='Listen to it on SoundCloud'></a>");
            settings.downloadable = "false";
            var scID = settings.soundcloud_id;
            var scURL = settings.soundcloud_url;
            SC.initialize({
                client_id: scID
            });

            //substation
            SC.resolve(scURL).then(function (sound) {
                item1 = sound.uri + "/stream?client_id=" + scID;
                updateAudioSrc(sound.uri + "/stream?client_id=" + scID);
                updateTitle(sound.title);
                updateArtist(sound.user.username);
            });
        }
        else if(type == "itunes"){

            settings.downloadable = "false";
            var id = settings.itunes_id;
            var iTuensLink = "https://itunes.apple.com/lookup?id=" + id + "&callback=?";
            $.getJSON(iTuensLink, function(data) {
                if (data.results[0].previewUrl.length > 0){
                    $(".dueT", thisObj).before("<a class='icon-apple branding' href='" + data.results[0].trackViewUrl + "' target='_blank' title='Listen to it on iTunes'></a>");
                    updateAudioSrc(data.results[0].previewUrl);
                    updateTitle(data.results[0].trackName);
                    updateArtist(data.results[0].artistName);
                }else{
                    console.log("No valid iTunes track ID is provided! " + id)
                }
            }).fail(function() {
                console.log("Data could not be loaded: A valid iTunes track ID is not provided! " + id);
            });
        };

        if(settings.playlist == "true"){
            currentRow = 0;

            $(thisObj).css("height", "250px");
            $(".lowerSec", thisObj).addClass("borderStyle");
            $(".lowerSec", thisObj).after("<div class='playlist'></div>");


            if(settings.playlist_path.length > 0){

                var x= [];
                var total = 0;
                var count = 0;
                function check(){
                    if(count==total){
                        $.each(x, function(){
                            this.resolve();
                        });
                    }
                }

                $.ajax({
                    url: settings.playlist_path,
                    dataType: "xml",
                    success: parse,
                    error: function () {
                        alert("Error: Something went wrong with loading the xml playlist! Make sure the provided xml URL is correct*");
                    }
                });
                function parse(document) {
                    var inc = 1;

                    $(document).find("track").each(function() {
                        total++;
                    });

                    $(document).find("track").each(function () {

                        var rowNum = inc;
                        var trackType = $(this).find('type').text();
                        var trackTitle = $(this).find('title').text();
                        var trackArtist = $(this).find('artist').text();
                        var track_Url = $(this).find('track_url').text();
                        var downloadable = $(this).find('downloadable').text();
                        var apple_music = $(this).find('apple_music').text();
                        var extra_purchase = $(this).find('extra_purchase').text();
                        var amazon = $(this).find('amazon').text();
                        var soundcloud_id = $(this).find('soundcloud_id').text();
                        var soundcloud_url = $(this).find('soundcloud_url').text();
                        var itunes_id = $(this).find('itunes_id').text();
                        var directLink = "";
                        var ID3_tag = $(this).find('ID3').text();


                        if (trackType == "audio") {
                            inc = inc + 1;
                            settings.apple_music = apple_music;
                            settings.extra_purchase = extra_purchase;
                            settings.downloadable = downloadable;
                            settings.amazon_music = amazon;

                            if(ID3_tag == "true"){
                                // ID3 Check
                                ID3.loadTags(track_Url, function() {
                                        var tags = ID3.getAllTags(track_Url);
                                        if(trackTitle.length <= 0 || trackArtist.length <= 0){
                                            if(trackTitle.length <= 0){
                                                trackTitle = tags.title;
                                            }
                                            if (trackArtist.length <= 0){
                                                trackArtist = tags.artist;
                                            }
                                        }
                                    },
                                    {
                                        onError: function(reason) {
                                            if (reason.error === "xhr") {
                                                console.log("There was a network error: ", reason.xhr);
                                            }
                                        }
                                    });
                            }

                            // ********************* //
                            var dfd = jQuery.Deferred();
                            dfd.then(function() {
                                $(".playlist", thisObj).append("<div id='" + rowNum +"' class='" + rowNum +" row borderStyle' data-title='"+ trackTitle +"' data-artist='"+ trackArtist +"' data-track_url='" + track_Url + "'><div class='left'>" + checkTextLength(trackTitle, 35) + "<span> by " + checkTextLength(trackArtist, 20) +"</span></div><div class='right'><span class='icon-audio'></span></div></div>");
                                if(rowNum == 1){
                                    updateAudioSrc(track_Url);
                                    updateTitle(trackTitle);
                                    updateArtist(trackArtist);
                                }
                            });
                            x.push(dfd);
                            count++;check();


                        }
                        else if(trackType == "soundcloud") {
                            inc = inc + 1;
                            var scID = soundcloud_id;
                            var scURL = soundcloud_url;
                            SC.initialize({
                                client_id: scID
                            });
                            //substation
                            SC.resolve(scURL).then(function (sound) {
                                track_Url = sound.uri + "/stream?client_id=" + scID;
                                trackTitle = sound.title;
                                trackArtist = sound.user.username;
                                directLink = sound.permalink_url;
                                count++;check();
                            });

                            // ********************* //
                            var dfd = jQuery.Deferred();
                            dfd.then(function() {
                                $(".playlist", thisObj).append("<div id='" + rowNum +"' class='" + rowNum +" row borderStyle' data-title='"+ trackTitle +"' data-artist='"+ trackArtist +"' data-track_url='" + track_Url + "'><div class='left'>" + checkTextLength(trackTitle, 35) + "<span> by " + checkTextLength(trackArtist, 20) +"</span></div><div class='right'><span class='icon-soundcloud'><a class='overlapper' href='"+ directLink +"' target='_blank' title='Listen to " + trackTitle + " on SoundCloud'></a></span></div></div>");
                                if(rowNum == 1){
                                    updateAudioSrc(track_Url);
                                    updateTitle(trackTitle);
                                    updateArtist(trackArtist);
                                }
                            });
                            x.push(dfd)

                        }
                        else if(trackType == "itunes"){
                            inc = inc + 1;
                            var id = itunes_id;
                            var iTuensLink = "https://itunes.apple.com/lookup?id=" + id + "&callback=?";
                            $.getJSON(iTuensLink, function(data) {
                                if (data.results[0].previewUrl.length > 0){
                                    track_Url = data.results[0].previewUrl;
                                    trackTitle = data.results[0].trackName;
                                    trackArtist = data.results[0].artistName;
                                    directLink = data.results[0].trackViewUrl;
                                    count++;check();
                                }else{
                                    console.log("No valid iTunes track ID is provided! " + id)
                                }
                            }).fail(function() {
                                console.log("Data could not be loaded: A valid iTunes track ID is not provided! " + id);
                            });

                            // ********************* //
                            var dfd = jQuery.Deferred();
                            dfd.then(function() {
                                $(".playlist", thisObj).append("<div id='" + rowNum +"' class='" + rowNum +" row borderStyle' data-title='"+ trackTitle +"' data-artist='"+ trackArtist +"' data-track_url='" + track_Url + "'><div class='left'>" + checkTextLength(trackTitle, 35) + "<span> by " + checkTextLength(trackArtist, 20) +"</span></div><div class='right'><span class='icon-apple'><a class='overlapper' href='"+ directLink +"' target='_blank' title='Listen to " + trackTitle + " on iTunes'></a></span></div></div>");
                                if(rowNum == 1){
                                    updateAudioSrc(track_Url);
                                    updateTitle(trackTitle);
                                    updateArtist(trackArtist);
                                }
                            });
                            x.push(dfd)
                        }

                    });
                    fixPlaylist(total);
                }
            }
            else if(settings.soundcloud_playlist.length > 0){
                var inc = 1;
                var rowNum = inc;
                var scID = settings.soundcloud_id;
                var playlistURL = settings.soundcloud_playlist;
                SC.initialize({
                    client_id: scID
                });
                SC.resolve(playlistURL).then(function(sound) {
                    var inc = 0;
                    $(sound.tracks).each(function(index) {
                        var track_Url = sound.tracks[inc].uri + "/stream?client_id=" + scID;
                        var trackTitle = sound.tracks[inc].title;
                        var trackArtist = sound.tracks[inc].user.username;
                        var track_url= sound.tracks[inc].permalink_url;
                        $(".playlist", thisObj).append("<div id='" + rowNum +"' class='row borderStyle'  data-title='"+ trackTitle +"' data-artist='"+ trackArtist +"' data-track_url='" + track_Url + "'><div class='left'>" + checkTextLength(trackTitle, 35) + "<span> by " + checkTextLength(trackArtist, 20) +"</span></div><div class='right'><span class='icon-soundcloud'><a class='overlapper' href='"+ track_url +"' target='_blank' title='Listen to " + trackTitle + " on SoundCloud'></a></span></div></div>");

                        if(rowNum == 1){
                            updateAudioSrc(track_Url);
                            updateTitle(trackTitle);
                            updateArtist(trackArtist);
                        }

                        rowNum++;
                        inc++;
                    });
                    fixPlaylist(inc);
                });

            }

            $.each(x, function(){
                this.resolve();
            });
        }
        if(settings.playlist != "true"){
            $('.icon-shuffle', thisObj).remove();
        }
        checkResources();
    });

    checkURL();


    thisObj.on("click tap", "input", function() {
        $(this).siblings("div").addClass("showing");
        $(this).parent().siblings().find("input").prop("checked", false);
    });
    thisObj.on("click tap", ".playlist > .row > .left", function(){
        updateAudioSrc($(this).parent().attr("data-track_url"));
        updateTitle($(this).parent().attr("data-title"));
        updateArtist($(this).parent().attr("data-artist"));
        currentRow = $(this).parent().attr("id");
        if ($("input.share:checked", thisObj).length == 1){
            $("input.settings", thisObj).click();
        };
        if($('.dueT', thisObj).hasClass("live")){
            removeLive();
        }
        playManagement();
    });



    // Buttons
    $(".icon-volume", thisObj).on("click tap", function() {
        if($(this).hasClass("icon-mute")){
            $(audio).animate({volume: 1}, 500);
            $(this, thisObj).removeClass("icon-mute", 1000, "linear" );
        }
        else{
            $(audio).animate({volume: 0}, 500);
            $(this, thisObj).addClass("icon-mute", 1000, "linear" );
        }
    });
    $(".icon-repeat", thisObj).on("click tap", function() {
        $(this, thisObj).toggleClass("active");
        if ($(this, thisObj).hasClass("active")) {
            audio.loop = true;
            $(this).addClass("icon-repeat2");
        } else {
            audio.loop = false;
            $(this).removeClass("icon-repeat2");
        }

    });
    $('.share', thisObj).on("click tap", function() {
        if($('.share', thisObj).hasClass("pressed") == false){
            var trackURL = getTrackURL();
            setFBShareAttr(trackURL);
            setTWShareAttr(trackURL);
            setEmailAttr(trackURL);
            $('.icon-share', thisObj).addClass("pressed");
        }
    });
    $('.icon-shuffle', thisObj).on("click tap", function() {
        $(this, thisObj).toggleClass("active");
        if ($(this, thisObj).hasClass("active")) {
            $(this).addClass("icon-shuffle2");
        } else {
            $(this).removeClass("icon-shuffle2");
        }

    });





    // Utility functions
    function togglePlying(aClassName, bool) {
        // makes sure one player is playing at the time.
        $(aClassName).toggleClass("playing", bool);
    }
    function getReadableTime(value) {
        //Convert milisec to "readable" time
        if (value == "Infinity") {
            return "live";
        } else {
            var durmins = Math.floor(value / 60);
            var dursecs = Math.floor(value - durmins * 60);
            if (dursecs < 10) {
                dursecs = "0" + dursecs;
            }
            if (durmins < 10) {
                durmins = "0" + durmins;
            }
            return durmins + ":" + dursecs;
        }

    }
    function updateLoadProgress(){
        if(audio.buffered.length > 0){
            var percent = (audio.buffered.end(0) / audio.duration) * 100;
            $(".buffer", thisObj).css("width", percent + "%");
        }
    }
    function cleanURL() {
        var hash = window.location.hash;
        var dan = hash;
        var player, track;
        if(hash.length > 0){
            player = hash.match(/#player\d+/);
            player = player ? player[0] : "";
            track = hash.match(/#track\d+/);
            track = track ? track[0] : "";

            if(player.length > 0){
                dan = dan.replace(player, '')
            }
            if(track.length > 0){
                dan = dan.replace(track, '');
            }
            if (typeof dan == 'undefined'){
                dan = '';
            }
        }
        return dan;
    }
    function checkResources() {
        if (settings.downloadable == "true") {
            $(".icon-download", thisObj).append(
                "<a class='overlapper' href='" + settings.track_URL + "' class='download' target='_blank' title='Downlod it' download></a>"
            );
        } else {
            $(".icon-download", thisObj).css("display", "none");
        }

        if (settings.amazon_music.length > 0) {
            $(".icon-amazon", thisObj).append(
                "<a class='overlapper' href='" + settings.amazon_music + "' class='amazon' target='_blank' title='Listen it on Amazon'></a>"
            );
        } else {
            $(".icon-amazon", thisObj).css("display", "none");
        }

        if (settings.apple_music.length > 0) {
            $(".icon-apple", thisObj).append(
                "<a class='overlapper' href='" + settings.apple_music + "' class='apple' target='_blank' title='Listen it on Apple Music'></a>"
            );
        } else {
            $(".icon-apple", thisObj).css("display", "none");
        }
        if (settings.extra_purchase.length > 0) {
            $(".icon-cart", thisObj).append(
                "<a class='overlapper' href='" + settings.extra_purchase + "' class='purchase' target='_blank' title='Purchase it'></a>"
            );
        } else {
            $(".icon-cart", thisObj).css("display", "none");
        }

        if (settings.downloadable != "true" &&
            !(settings.amazon_music.length > 0) &&
            !(settings.apple_music.length > 0) &&
            !(settings.extra_purchase.length > 0) ||
            settings.playlist == "true") {
            $(".resource", thisObj).remove();
        }
    }
    function getTrackURL() {
        var trackid = '';
        if(settings.playlist == "true"){
            trackid = '';
            if($(".playlist", thisObj).find(".row").hasClass("whiteBG") ){
                trackid = "#track" + $(".playlist", thisObj).find(".whiteBG").attr("id");
            }
        }
        var hash4URL = "#" + $(thisObj).attr("id") + trackid + cleanURL();
        return window.location.origin + window.location.pathname + hash4URL;
    }
    function setFBShareAttr(trackURL) {
        var url = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(trackURL);
        $(".icon-facebook", thisObj).append(
            "<a class='overlapper' href='" + url + "' target='_blank' title='Share on Facebook'></a>"
        );
    }
    function setTWShareAttr(trackURL) {
        var url = "https://twitter.com/home?status=" + encodeURIComponent(trackURL);
        $(".icon-twitter", thisObj).append(
            "<a class='overlapper' href='" + url + "' target='_blank' title='Share on Twitter'></a>"
        );
    }
    function setEmailAttr(trackURL){
        var trackArtist = settings.artist;
        var trackTitle = settings.title;
        if (settings.type == "soundcloud") {
            trackTitle = $(".title", thisObj).attr("data-title");
            trackArtist = $(".artist", thisObj).attr("data-artist")
        }
        var subjectText = encodeURIComponent(trackTitle + " by " + trackArtist);
        var bodyText = encodeURIComponent("Check out the track " + trackTitle+ " by " + trackArtist + " on ") + encodeURI(trackURL);

        var url = 'mailto:' + '' + '?subject=' + subjectText + '&body=' + bodyText;
        $(".icon-email", thisObj).on("click tap", function(event) {
            event.preventDefault();
            window.location = url;
        });
    }
    function updateTitle(data) {
        var carLendth = 25;
        if(checkWidth() < 400){
            carLendth = 19;
        }

        if(data.length > carLendth){
            $(".title", thisObj).attr("data-text", data);
        }
        data = checkTextLength(data, carLendth);
        $(".title", thisObj).text(data);
    }
    function updateArtist(data) {
        var carLendth = 36;
        if(checkWidth() < 400){
            carLendth = 28;
        }

        if(data.length > carLendth){
            $(".artist", thisObj).attr("data-text", data)
        }
        data = checkTextLength(data, carLendth);
        $(".artist", thisObj).text(data);
    }
    function updateAudioSrc(data) {
        audio.src = data;
    }
    function playManagement(){
        if (audio.paused) {
            //play after 150ms
            setTimeout(function () {
                audio.play();
            }, 150);

            var $playing = $('.playButton.playing');
            if ($(thisObj).find($playing).length === 0) {
                $playing.click();
            }

            $(thisObj).addClass("bekhon");
            $(".sMusicPlayer").removeClass("nakhon ");
        } else {
            audio.pause();

            $(thisObj).addClass("nakhon");
            $(".sMusicPlayer").removeClass("bekhon");
        }

    }
    function checkTextLength(text, lendth){
        if(checkWidth() < 400){
            lendth = 21;
        }

        if(text.length > lendth){
            return text.substring(0, lendth - 1) + "...";
        }else{
            return text;
        }
    }
    function fixPlaylist(rows){
        if(rows > 6){
            $(".playlist", thisObj).addClass("scroll");
        }
        else{
            var playlist_height = rows * 25;
            var totalHeight = playlist_height + 100;
            $(".playlist", thisObj).css("height", "125px");
            $(thisObj).css("height", totalHeight);
        }
    }
    function checkURL(){
        var hash = window.location.hash;
        var player, track;

        if(hash.length > 0 && hash.indexOf("player") >= 0){

            player = hash.match(/#player\d+/);
            player = player ? player[0] : "";

            if(hash.indexOf("track") >= 0){
                track = hash.match(/#track\d+/);
                track = track ? track[0] : "";
            }

            $(".sMusicPlayer").each(function(){
                if($(this).attr("id") == player.substr(1)){

                    if(typeof track != 'undefined'){
                        setTimeout(function() {
                            $('div' + player +'.sMusicPlayer> .playlist > div#' + track.substr(6)+'.row > .left').click();
                        },1000);
                    }
                    else{
                        $("div"+ player +".sMusicPlayer").find("div.lowerSec > div.playButton").click();
                    }
                }
            });
            var title = $("div#player"+player).find("div.upperSec > div.leftSide > div.title").text();
        };


    }
    function checkWidth() {
        // To make text does now overflow in smartphone [.upperSec > .leftSide only]
        var val = thisObj.css("width").slice(0, thisObj.css("width").indexOf("px"));
        val = parseInt(val, 10);
        return val;
    }
    function removeLive(){
        $('.dueT', thisObj).removeClass("live")
        $(".played", thisObj).css("width", "0").removeClass("meter");
        $(".handle", thisObj).css("opacity", "1");
    }


    // dynamically-generate id/for values
    $('div.sMusicPlayer').each(function(index, el) {
        var $panel = $(el); // sMusicPlayer
        var panelId = $panel.attr('id'); //PLAYER1
        var $panelBottom = $('div.panel', $panel); // panel

        $('div.upperSec > .rightSide > div > input', $panel).each(function(indexInput, elInput) {
            var $input = $(elInput);
            var inputId = panelId + '-box-' + indexInput;


            var $inputClass = $input.attr("class");
            if( $inputClass == "settings"){
                inputId = panelId + '-box-settings';
                $input.attr('id', inputId);
                $(".settings", $panelBottom).attr('for', inputId);
            }
            else if($inputClass == "share"){
                inputId = panelId + '-box-share';
                $input.attr('id', inputId);
                $(".share", $panelBottom).attr('for', inputId);
            }
            else if($inputClass == "resource"){
                inputId = panelId + '-box-resource';
                $input.attr('id', inputId);
                $(".resource", $panelBottom).attr('for', inputId);
            }

        });
    });


    // Keyboard
    $(window).keypress(function(e) {
        if (e.keyCode === 0 || e.keyCode === 32) {
            e.preventDefault();
            if ($(thisObj).hasClass("bekhon")) {
                audio.pause();
                $(thisObj).removeClass("bekhon");
                $(thisObj).addClass("nakhon");
            } else if ($(thisObj).hasClass("nakhon")) {
                audio.play();
                $(thisObj).removeClass("nakhon");
                $(thisObj).addClass("bekhon");
            }
        }
    })


}

}( jQuery ));
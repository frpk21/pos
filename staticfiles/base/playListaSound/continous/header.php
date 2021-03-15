<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>HTML5 Audio Player with Playlist</title>
       
        <link rel="stylesheet" type="text/css" href="css/hap-fontawesome.css"/>
        <link rel="stylesheet" type="text/css" href="css/jquery.mCustomScrollbar.css" media="all" /><!-- playlist scroll -->
        <link rel="stylesheet" type="text/css" href="css/fixed_bottom6_continous.css" />
        
        <script src="js/jquery-3.2.1.min.js"></script>
        <script src="js/new.js"></script>   
        <script>


            /* START PLAYER TOGGLE CODE */
            
            var playerOpened = true, 
            playlistOpened,
            wrapper,
            playerOuter,
            playlistHolder,
            playbackBtn;

            function setData(playing){
                if(playing) playbackBtn.find('i').removeClass('fa-play').addClass('fa-pause');
                else playbackBtn.find('i').removeClass('fa-pause').addClass('fa-play');
            }

            /* END PLAYER TOGGLE CODE */


            var hap_player; 
            jQuery(document).ready(function($){
                
                var settings = {
                    instanceName:"fixed_bottom6",
                    sourcePath:"",
                    activePlaylist:"#playlist-audio2",
                    activeItem:0,
                    volume:0.5,
                    autoPlay:true,
                    preload:"auto",
                    randomPlay:false,
                    loopingOn:true,
                    soundCloudAppId:"",
                    usePlaylistScroll:true,
                    playlistScrollOrientation:"vertical",
                    playlistScrollTheme:"minimal",
                    facebookAppId:"",
                    useNumbersInPlaylist: true,
                    numberTitleSeparator: ".  ",
                    artistTitleSeparator: " - ",
                    playlistItemContent:"title",
                    continousPlayback:true,
                    continousKey:'hap_continous_key123'
                };

                /* START PLAYER TOGGLE CODE */

                wrapper = $('#hap-wrapper'),
                playerOuter = $('.hap-player-outer'),
                playlistHolder = $('.hap-playlist-holder'),
                playerToggle = $('.hap-player-toggle'),
                playbackBtn = $('.hap-playback-toggle-ex').on('click',function(e){
                    hap_player.togglePlayback();
                });  

                playerToggle.on('click', function(){
                    if(playerOpened){
                        wrapper.stop().animate({'bottom': -playerOuter.height()-playlistHolder.height()+'px'}, {duration: 300, complete: function(){
                        }});
                        playerToggle.find('i').removeClass('fa-chevron-down').addClass('fa-chevron-up'); 
                        playlistOpened=false;
                    }else{
                        wrapper.stop().animate({'bottom': -playlistHolder.height()+'px'}, {duration: 300});  
                        playerToggle.find('i').removeClass('fa-chevron-up').addClass('fa-chevron-down'); 
                    }   
                    playerOpened = !playerOpened;
                });

                /* END PLAYER TOGGLE CODE */



                hap_player = $("#hap-wrapper").on('playlistEndLoad', function(e, data){
                    //called on playlist end load, returns (instance, instanceName)
                    wrapper.css({'bottom':-(playlistHolder.height())+'px', opacity:1});
                }).on('soundPlay', function(e, data){
                    //called on song play, returns (instance, instanceName, counter)
                    setData(true);
                }).on('soundPause', function(e, data){
                    //called on song pause, returns (instance, instanceName, counter)
                    setData();
                }).hap(settings);  

                hap_player.togglePlaylist = function(){
                    if(playlistOpened){
                        wrapper.stop().animate({'bottom': -playlistHolder.height()+'px'}, {duration: 400});
                    }else{
                        wrapper.stop().animate({'bottom': 0+'px'}, {duration: 400});
                    }   
                    playlistOpened = !playlistOpened;
                }

            });

        </script>
        
    </head>
    <body>
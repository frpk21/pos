<?php
  error_reporting(E_ALL);
  ini_set('display_errors', 1);
  ini_set('display_startup_errors',1);
  include_once "publisher.php";
?><!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>WordPress ZoomSounds DZS Preview Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=2">
    <link href="./bootstrap/bootstrap.min.css" rel="stylesheet">
    <link rel='stylesheet' type="text/css" href="style/style.css"/>
    <script src="js/jquery.js" type="text/javascript"></script>

    <link rel='stylesheet' type="text/css" href="dzstooltip/dzstooltip.css"/>
    <link rel='stylesheet' type="text/css" href="audioplayer/audioplayer.css"/>
    <script src="audioplayer/audioplayer.js" type="text/javascript"></script>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
</head>
<body>
<div class="mwrap-wrapper">





    <!--

    ===end mainmenu


    -->


    <section class="mcon-otherdemos shadow-top">
        <div class="container" style="padding-top: 100px;">
            <div class="row">
                <div class="col-md-12 border-box" style="padding:0 30px;">
                    <h2 class="hero-heading">The audio player you have been waiting for.</h2>
                </div>
            </div>
        </div>
    </section>



    <section class="mcon-otherdemos">
        <!--
        <div class="pat-bg">
            <div class="pat-bg-inner">

            </div>
        </div>
        -->
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2>Choose a demo.</h2>
                    <div class="separator-short m20"><figure class="the-graphic"></figure></div>
                </div>
            </div>


            <script>

                function test(){
                    console.log('player ended event');
                }
                </script>
            <div class="row">




                <div data-thumb="img/adg3.jpg"  data-type="audio" class="aptest-with-play skin-wave-mode-small audioplayer-tobe skin-wave button-aspect-noir     " data-source="http://devsite/wpfactory/dzsap/wp-content/uploads/sites/4/2017/10/Jama.m4a"   data-playfrom="last" data-fakeplayer=".dzsap_footer" data-pcm="<?php echo get_pcm('','http://devsite/wpfactory/dzsap/wp-content/uploads/sites/4/2017/10/Jama.m4a'); ?>">
                    <div class="meta-artist">
                        <span class="the-artist">Mick Jagger - Revenge</span><span class="the-name"><a href="#">Buy now!</a></span>
                    </div>
                </div>



                <script>
                    jQuery(document).ready(function ($) {



                        dzsap_init(".aptest-with-play", {
                            autoplay: "off"
                            ,cue: "on"
                            ,init_each: "on"
                            ,disable_volume: "on"
                            ,skinwave_mode: 'normal'
//            ,sample_time_start: '30' // --- if this is a sample to a complete song, you can write here start times, if not, leave to 0.
//            ,sample_time_end: '87'
//            ,sample_time_total: '120'
                            ,settings_backup_type: 'light' // == light or full
                            ,skinwave_: 'light' // == light or full
                            ,skinwave_enableSpectrum: "off"
                            ,embed_code: 'light' // == light or full
                            ,skinwave_wave_mode_canvas_waves_number: "3"
                            ,skinwave_wave_mode_canvas_waves_padding: "1"
                            ,skinwave_wave_mode_canvas_reflection_size: '0' // == light or full
                            ,design_color_bg: '999999,ffffff' // --  light or full
                            ,skinwave_wave_mode_canvas_mode: 'reflecto' // --  light or full
                            ,preview_on_hover: 'off' // --  light or full
                            ,preload_method: 'metadata' // --  light or full
                            ,design_wave_color_progress: 'ff657a,ffffff' // -- light or full
                            ,settings_php_handler:'publisher.php'
                            ,pcm_data_try_to_generate:'on'
                            ,pcm_data_try_to_generate_wait_for_real_pcm:'on'
//            ,controls_external_scrubbar:'.zoomsounds-external-scrubbar-for-with-play'
                            ,skinwave_comments_enable: 'on' // -- enable the comments, publisher.php must be in the same folder as this html, also if you want the comments to automatically be taken from the database remember to set skinwave_comments_retrievefromajax to ON
                            ,skinwave_comments_retrievefromajax: 'on'// --- retrieve the comment form ajax
                            ,failsafe_repair_media_element: 500 // == light or full

                            ,settings_extrahtml_in_float_right: '<div class="player-but dzstooltip-con" style=";"><div class="the-icon-bg"></div> <span class="dzstooltip arrow-from-start transition-slidein arrow-bottom skin-black align-right" style="width: auto; white-space: nowrap;">Add to Cart</span><i class=" svg-icon fa fa-shopping-cart"></i></div>  <div class="player-but dzstooltip-con" style=";"><div class="the-icon-bg"></div>  <span class="dzstooltip arrow-from-start transition-slidein arrow-bottom skin-black align-right" style="width: auto; white-space: nowrap;">Download</span>  <i class="svg-icon fa fa-download"></i>  </div>'
                        });

                        setTimeout(function(){
//            $('.the-media').html('alceva');
                        },100);
                    });
                </script>



            </div>
            <div class="row">
            </div>
        </div>
    </section>


<section class="mcon-otherdemos">
    <div class="container">
        <div class="row">
            <div class="col-md-6 border-box" style="padding: 0px 60px 20px; "></div>
            <div class="col-md-6">
                <h2>Multiple Skins</h2>
                <div class="separator-short m20"><figure class="the-graphic"></figure></div>
                <p>It takes just 3 minutes to install, activate and setup your first pricing table like the one above. It's all possible because of a smart WYSIWYG shortcode generator and premade examples so you can customize from there to your needs.</p>
                <p>



                </p>
                <br>
                <p><a href="skins.html" class="dzs-button skin-emerald">Check out skins!</a></p>
                <br/>
            </div>
        </div>


    </div>
</section>


    <section class="mcon-otherdemos-alt" style="padding-bottom: 0;">
        <div class="container">
            <div class="row" style="margin-bottom: 0;">
                <div class="col-md-6">
                    <h2>Responsive</h2>
                    <div class="separator-short m20"><figure class="the-graphic"></figure></div>
                    <p>Mobile ready! Retina ready! Tested on iOS and Android 4.0+. Responsive from mobile to HD due to CSS 3 Media Queries. Retina ready due to use of font icons and no images. Scales to any dimension!</p>
                </div>
                <div class="col-md-6 border-box" style="padding: 0px 60px 0px; "></div>
            </div>


        </div>
    </section>


    <section class="mcon-features">

        <div class="pat-bg">
            <div class="pat-bg-inner">

            </div>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="bigfeature"><i class="icon-thumbs-up"></i></div>
                    <h4>Easy Install</h4>
                    <p style="text-align: center">Install WordPress Pricing Tables in just a couple of minutes. <a href="http://digitalzoomstudio.net/docs/wppricingtables/" target="_blank">Docs</a> are also there.</p>
                </div>
                <div class="col-md-3">
                    <div class="bigfeature"><i class="icon-desktop"></i></div>
                    <h4>Responsive</h4>
                    <p style="text-align: center">From mobile to HD, pricing tables are ultra responsive. Also has retina graphics.</p>
                </div>
                <div class="col-md-3">
                    <div class="bigfeature"><i class="icon-pencil"></i></div>
                    <h4>Customizable</h4>
                    <p style="text-align: center">Customize Pricing Tables with the awesome Builder included.</p>
                </div>
                <div class="col-md-3">
                    <div class="bigfeature"><i class="icon-briefcase"></i></div>
                    <h4>SEO Friendly</h4>
                    <p style="text-align: center">Built with SEO in mind, Pricing Tables parses html content into working magic.</p>
                </div>
            </div>
        </div>
    </section>


    <section class="mcon-footer">
        <div class="container">
            <div class="row">

            </div>
            <div class="row">
                <div class="col-md-6">
                    &copy; copyright <a href="http://bit.ly/nM4R6u">ZoomIt</a> 2013

                </div>
                <div class="col-md-6" style="text-align: right">
                    <iframe src="//www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2Fdigitalzoomstudio&amp;width=150&amp;height=21&amp;colorscheme=light&amp;layout=button_count&amp;action=like&amp;show_faces=true&amp;send=false&amp;appId=569360426428348" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:150px; height:21px;" allowTransparency="true"></iframe>
                </div>
            </div>
        </div>
</section>
<div class="dzsap-sticktobottom-placeholder"></div>
    <section class="dzsap-sticktobottom  dzsap-sticktobottom-for-skin-wave-small">

        <style>.audioplayer.skin-wave#dzsap_footer .ap-controls .con-playpause .playbtn, .audioplayer.skin-wave#dzsap_footer .btn-embed-code, .audioplayer.skin-wave#dzsap_footer .ap-controls .con-playpause .pausebtn { background-color: #111111;}  </style>


        <div class="audioplayer-tobe dzsap_footer theme-dark button-aspect-noir button-aspect-noir--stroked skinvariation-wave-righter" style="" id="ap_footer"   data-type="fake" data-source="fake" data-thumb="img/adg3.jpg"  >
            <span class="meta-artist"><span class="the-artist">Adrien Gardiner</span><span class="the-name"><a href="http://codecanyon.net/item/zoomsounds-wordpress-audio-player/6181433?ref=ZoomIt" target="_blank">Eco2 the Boost</a></span>
                </span>
        </div>

        <script>(function(){
            var auxap = jQuery("#ap_footer");
            jQuery(document).ready(function ($){
                var settings_ap = {
                    design_skin: "skin-wave"
                    ,autoplay: "on"
                    ,disable_volume:"off"
                    ,cue: "on"
                    ,embedded: "off"
                    ,skinwave_dynamicwaves:"off"
                    ,skinwave_enableSpectrum:"off"
                    ,settings_backup_type:"full"
                    ,skinwave_enableReflect:"on",playfrom:"off",soundcloud_apikey:""
                    ,skinwave_comments_enable:"off"

                    ,skinwave_mode:"small"
                    ,skinwave_comments_playerid:""
                    ,php_retriever:"./soundcloudretriever.php"
                    ,swf_location:"./ap.swf"
                    ,settings_php_handler: './publisher.php' // -- the path of the publisher.php file, this is used to handle comments, likes etc.

                    ,swffull_location:"./apfull.swf"
                    ,design_wave_color_bg:'ffffff'
                    ,design_wave_color_prog:'52cd92'


                    ,player_navigation : "on"
                    ,action_audio_end: test
                    ,pcm_data_try_to_generate: 'on' // --- try to find out the pcm data and sent it via ajax ( maybe send it via php_handler
                    ,pcm_data_try_to_generate_wait_for_real_pcm: 'on' // --- if set to on, the fake pcm data will not be generated

                };

//                console.info(settings_ap);
                dzsap_init(auxap,settings_ap);
            });
        })();</script></section>

</div>



<script>

    jQuery(document).ready(function ($) {

        var settings_ap = {
            disable_volume: 'off'
            ,autoplay: 'off'
            ,cue: 'on'
            ,disable_scrub: 'default'
            ,design_skin: 'skin-silver'
            ,skinwave_dynamicwaves:'on'
            ,skinwave_enableSpectrum: "off"
            ,settings_backup_type:'full'
            ,settings_useflashplayer:'auto'
            ,skinwave_spectrummultiplier: '4'
            ,skinwave_comments_enable:'off'
            ,skinwave_mode: 'small'
        };
//        dzsap_init('#ap_footer',settings_ap);
    });
</script>
<link rel="stylesheet" href="fontawesome/font-awesome.min.css">
<script type="text/javascript" src="js/main.js"></script>
<link rel="stylesheet" href="zoombox/zoombox.css">
<script type="text/javascript" src="zoombox/zoombox.js"></script>
</body>
</html>

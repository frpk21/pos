<!DOCTYPE HTML>
<html><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.js" integrity="sha256-16cdPddA6VdVInumRGo6IbivbERE8p7CQR3HzTBuELA=" crossorigin="anonymous"></script>
    <title>Video / Youtube Player / AS3</title><link rel="stylesheet" type="text/css" href="style/style.css">

<style>
    .mwrap{
        padding: 10px;
    }

    .setting{
        margin-bottom: 21px;;
    }
    .setting .setting-label{
        margin-bottom: 7px;;
        font-weight: bold;
    }
    .setting .sidenote{
        margin-top: 7px;
        font-style: italic;;

    }
    .output:empty{
        display: none;
    }
    .output{
        white-space: normal;

        font-size: 11px;
        background-color: #dadada;
        color: #444444;

        padding: 15px;
        word-break: break-all;
    }
    </style>
<body>
<div class="mwrap">

<?php
    if(isset($_POST['media'])) {

        ?>

    <form method="post">
        <button class="button-secondary">Clear Data</button>
        </form>
        <br>
        <div id="flashcon1" style="position: absolute; width:1px; height:1px; top:0; left:0;">
            <object type="application/x-shockwave-flash" data="wavegenerator.swf" width="1" height="1"
                    id="flashcontent" style="visibility: visible; ">
                <param name="movie" value="wavegenerator.swf">
                <param name="menu" value="false">
                <param name="allowScriptAccess" value="always">
                <param name="scale" value="noscale">
                <param name="allowFullScreen" value="true">
                <param name="wmode" value="opaque">
                <param name="flashvars"
                       value="media=<?php echo $_POST['media']; ?>&wave_generation=wavearr">
            </object>


        </div>
        <div class="output"></div>
        <script>
            window.api_wavesentfromflash = function (arg) {
                if (window.console) {
                    console.info(arg);
                }
                ;
            }
            window.api_wave_data = function (arg) {
                if (window.console) {
                    console.info(arg);
                }

                var str = '['+arg+']';


                ;

                jQuery('.output').text(str);
            }
        </script>

    <?php
    }else{

?>

        <form method="post">
            <div class="setting">
                <div class="setting-label">
                    Media
                </div>
                <input type="text" name="media" value="song.mp3">
            </div>
            <div class="">
                <button class="button-primary">Submit</button>
            </div>
        </form>

<?php

    }

?>
</div>
</body></html>
<?php
//sleep(5);
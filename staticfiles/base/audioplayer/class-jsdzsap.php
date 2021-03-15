<?php
//error_log("ceva");

class DZSAudioPlayer {

  public $thepath;
  public $base_url;
  public $base_path;
  public $admin_capability = 'manage_options';
  public $dbname_mainitems = 'dzsap_items';
  public $dbname_mainitems_configs = 'dzsap_vpconfigs';
  public $dbname_options = 'dzsap_options';
  public $dbname_dbs = 'dzsap_dbs';
  public $adminpagename = 'dzsap_menu';
  public $adminpagename_configs = 'dzsap_configs';
  public $adminpagename_mo = 'dzsap-mo';
  public $page_mainoptions_link = 'dzsap-mo';
  public $the_shortcode = 'zoomsounds';
  public $mainitems;
  public $mainitems_configs;
  public $mainoptions;
  public $sliders_index = 0;
  public $sliders__player_index = 0;
  public $cats_index = 0;
  public $dbs = array();
  public $currDb = '';
  public $currSlider = '';
  public $pluginmode = "plugin";
  public $alwaysembed = "on";
  public $httpprotocol = 'https';
  public $sample_data = array();
  private $dbname_sample_data = 'dzsap_sample_data';

  public $options_item_meta = array();
  public $og_data = array();


  public $has_generated_product_player = false;


  public $options_array_player = array();

  private $usecaching = true;
  private $sw_enable_multisharer = false;
  private $debug = false;

  function __construct() {
    if ($this->pluginmode == 'theme') {
      $this->thepath = THEME_URL . 'plugins/dzs-zoomsounds/';
    } else {
      $this->thepath = plugins_url('', __FILE__) . '/';
    }


    $this->base_path = dirname(__FILE__) . '/';

    $this->base_url = $this->thepath;

    //clear database
    //update_option($this->dbname_dbs, '');


    $currDb = '';
    if (isset($_GET['dbname'])) {
      $this->currDb = $_GET['dbname'];
      $currDb = $_GET['dbname'];
    }


    if (isset($_GET['currslider'])) {
      $this->currSlider = $_GET['currslider'];
    } else {
      $this->currSlider = 0;
    }

    $this->dbs = get_option($this->dbname_dbs);
    //$this->dbs = '';
    if ($this->dbs == '') {
      $this->dbs = array('main');
      update_option($this->dbname_dbs, $this->dbs);
    }
    if (is_array($this->dbs) && !in_array($currDb, $this->dbs) && $currDb != 'main' && $currDb != '') {
      array_push($this->dbs, $currDb);
      update_option($this->dbname_dbs, $this->dbs);
    }
    //echo 'ceva'; print_r($this->dbs);
    if ($currDb != 'main' && $currDb != '') {
      $this->dbname_mainitems.='-' . $currDb;
    }

    include("class_parts/options-item-meta.php");


    if(isset($_GET) && isset($_GET['dzsap_debug']) && $_GET['dzsap_debug']=='on'){
      $this->debug = true;
    }

    $this->mainitems = get_option($this->dbname_mainitems);
    if (is_array($this->mainitems)==false) {
      $aux = 'a:2:{i:0;a:3:{s:8:"settings";a:17:{s:2:"id";s:20:"playlist_wave_simple";s:5:"width";s:0:"";s:6:"height";s:0:"";s:11:"galleryskin";s:9:"skin-wave";s:12:"menuposition";s:6:"bottom";s:17:"design_menu_state";s:4:"open";s:36:"design_menu_show_player_state_button";s:3:"off";s:18:"design_menu_height";s:7:"default";s:13:"cuefirstmedia";s:2:"on";s:8:"autoplay";s:2:"on";s:12:"autoplaynext";s:2:"on";s:25:"disable_player_navigation";s:3:"off";s:7:"bgcolor";s:11:"transparent";s:8:"vpconfig";s:20:"skinwavewithcomments";s:12:"enable_views";s:3:"off";s:12:"enable_likes";s:3:"off";s:12:"enable_rates";s:3:"off";}i:0;a:17:{s:4:"type";s:5:"audio";s:6:"source";s:78:"http://www.stephaniequinn.com/Music/Allegro%20from%20Duet%20in%20C%20Major.mp3";s:19:"soundcloud_track_id";s:0:"";s:23:"soundcloud_secret_token";s:0:"";s:9:"sourceogg";s:0:"";s:15:"linktomediafile";s:0:"";s:5:"thumb";s:101:"https://lh5.googleusercontent.com/-RhXJ4O5JiEQ/UoKDBeGx5-I/AAAAAAAAAEU/Dkace1QwAKU/s80/smalllogo2.jpg";s:8:"playfrom";s:0:"";s:7:"bgimage";s:0:"";s:21:"play_in_footer_player";s:3:"off";s:10:"extra_html";s:0:"";s:15:"extra_html_left";s:0:"";s:27:"extra_html_in_controls_left";s:0:"";s:28:"extra_html_in_controls_right";s:0:"";s:15:"menu_artistname";s:4:"Tony";s:13:"menu_songname";s:4:"Tail";s:14:"menu_extrahtml";s:0:"";}i:1;a:17:{s:4:"type";s:5:"audio";s:6:"source";s:45:"http://www.stephaniequinn.com/Music/Canon.mp3";s:19:"soundcloud_track_id";s:0:"";s:23:"soundcloud_secret_token";s:0:"";s:9:"sourceogg";s:0:"";s:15:"linktomediafile";s:0:"";s:5:"thumb";s:101:"https://lh5.googleusercontent.com/-RhXJ4O5JiEQ/UoKDBeGx5-I/AAAAAAAAAEU/Dkace1QwAKU/s80/smalllogo2.jpg";s:8:"playfrom";s:0:"";s:7:"bgimage";s:0:"";s:21:"play_in_footer_player";s:3:"off";s:10:"extra_html";s:0:"";s:15:"extra_html_left";s:0:"";s:27:"extra_html_in_controls_left";s:0:"";s:28:"extra_html_in_controls_right";s:0:"";s:15:"menu_artistname";s:4:"Tony";s:13:"menu_songname";s:5:"Cairn";s:14:"menu_extrahtml";s:0:"";}}i:1;a:4:{s:8:"settings";a:17:{s:2:"id";s:21:"gallery_with_comments";s:5:"width";s:0:"";s:6:"height";s:0:"";s:11:"galleryskin";s:9:"skin-aura";s:12:"menuposition";s:6:"bottom";s:17:"design_menu_state";s:4:"open";s:36:"design_menu_show_player_state_button";s:3:"off";s:18:"design_menu_height";s:7:"default";s:13:"cuefirstmedia";s:2:"on";s:8:"autoplay";s:2:"on";s:12:"autoplaynext";s:2:"on";s:25:"disable_player_navigation";s:3:"off";s:7:"bgcolor";s:11:"transparent";s:8:"vpconfig";s:20:"skinwavewithcomments";s:12:"enable_views";s:2:"on";s:12:"enable_likes";s:2:"on";s:12:"enable_rates";s:3:"off";}i:0;a:17:{s:4:"type";s:5:"audio";s:6:"source";s:78:"http://www.stephaniequinn.com/Music/Allegro%20from%20Duet%20in%20C%20Major.mp3";s:19:"soundcloud_track_id";s:0:"";s:23:"soundcloud_secret_token";s:0:"";s:9:"sourceogg";s:0:"";s:15:"linktomediafile";s:1:"1";s:5:"thumb";s:74:"https://placeholdit.imgix.net/~text?txtsize=22&txt=placeholder&w=300&h=300";s:8:"playfrom";s:0:"";s:7:"bgimage";s:0:"";s:21:"play_in_footer_player";s:3:"off";s:10:"extra_html";s:0:"";s:15:"extra_html_left";s:0:"";s:27:"extra_html_in_controls_left";s:0:"";s:28:"extra_html_in_controls_right";s:0:"";s:15:"menu_artistname";s:8:"Artist 1";s:13:"menu_songname";s:7:"Track 1";s:14:"menu_extrahtml";s:0:"";}i:1;a:17:{s:4:"type";s:5:"audio";s:6:"source";s:45:"http://www.stephaniequinn.com/Music/Canon.mp3";s:19:"soundcloud_track_id";s:0:"";s:23:"soundcloud_secret_token";s:0:"";s:9:"sourceogg";s:0:"";s:15:"linktomediafile";s:1:"2";s:5:"thumb";s:74:"https://placeholdit.imgix.net/~text?txtsize=33&txt=placeholder&w=300&h=300";s:8:"playfrom";s:0:"";s:7:"bgimage";s:0:"";s:21:"play_in_footer_player";s:3:"off";s:10:"extra_html";s:0:"";s:15:"extra_html_left";s:0:"";s:27:"extra_html_in_controls_left";s:0:"";s:28:"extra_html_in_controls_right";s:0:"";s:15:"menu_artistname";s:8:"Artist 1";s:13:"menu_songname";s:7:"Track 1";s:14:"menu_extrahtml";s:0:"";}i:2;a:17:{s:4:"type";s:5:"audio";s:6:"source";s:93:"http://www.stephaniequinn.com/Music/Handel%20-%20Entrance%20of%20the%20Queen%20of%20Sheba.mp3";s:19:"soundcloud_track_id";s:0:"";s:23:"soundcloud_secret_token";s:0:"";s:9:"sourceogg";s:0:"";s:15:"linktomediafile";s:4:"1000";s:5:"thumb";s:0:"";s:8:"playfrom";s:0:"";s:7:"bgimage";s:0:"";s:21:"play_in_footer_player";s:3:"off";s:10:"extra_html";s:0:"";s:15:"extra_html_left";s:0:"";s:27:"extra_html_in_controls_left";s:0:"";s:28:"extra_html_in_controls_right";s:0:"";s:15:"menu_artistname";s:8:"Artist 3";s:13:"menu_songname";s:7:"Track 3";s:14:"menu_extrahtml";s:0:"";}}}';
      $this->mainitems = unserialize($aux);
      //$this->mainitems = array();
      update_option($this->dbname_mainitems, $this->mainitems);
    }

    $this->mainitems_configs = get_option($this->dbname_mainitems_configs);
    //cho 'ceva'.is_array($this->mainitems_configs);
    if ($this->mainitems_configs == '' || (is_array($this->mainitems_configs) && count($this->mainitems_configs) == 0)) {
      //echo 'ceva';
      $this->mainitems_configs = array();
      $aux = 'a:3:{i:0;a:1:{s:8:"settings";a:7:{s:2:"id";s:20:"skinwavewithcomments";s:7:"skin_ap";s:9:"skin-wave";s:20:"settings_backup_type";s:4:"full";s:21:"skinwave_dynamicwaves";s:3:"off";s:23:"skinwave_enablespectrum";s:3:"off";s:22:"skinwave_enablereflect";s:2:"on";s:24:"skinwave_comments_enable";s:2:"on";}}i:1;a:1:{s:8:"settings";a:13:{s:2:"id";s:11:"footer-wave";s:7:"skin_ap";s:9:"skin-wave";s:20:"settings_backup_type";s:4:"full";s:14:"disable_volume";s:7:"default";s:19:"enable_embed_button";s:3:"off";s:8:"playfrom";s:3:"off";s:14:"colorhighlight";s:6:"111111";s:21:"skinwave_dynamicwaves";s:3:"off";s:23:"skinwave_enablespectrum";s:3:"off";s:22:"skinwave_enablereflect";s:2:"on";s:24:"skinwave_comments_enable";s:3:"off";s:13:"skinwave_mode";s:5:"small";s:23:"enable_alternate_layout";s:3:"off";}}i:2;a:1:{s:8:"settings";a:13:{s:2:"id";s:17:"example-skin-aria";s:7:"skin_ap";s:9:"skin-aria";s:20:"settings_backup_type";s:6:"simple";s:14:"disable_volume";s:7:"default";s:19:"enable_embed_button";s:3:"off";s:8:"playfrom";s:3:"off";s:14:"colorhighlight";s:6:"111111";s:21:"skinwave_dynamicwaves";s:3:"off";s:23:"skinwave_enablespectrum";s:3:"off";s:22:"skinwave_enablereflect";s:2:"on";s:24:"skinwave_comments_enable";s:3:"off";s:13:"skinwave_mode";s:6:"normal";s:23:"enable_alternate_layout";s:3:"off";}}}';
      $this->mainitems_configs = unserialize($aux);
      //print_r($this->mainitems_configs);
      //$this->mainitems = array();
      update_option($this->dbname_mainitems_configs, $this->mainitems_configs);
    }
    $vpconfigsstr = '';
    //print_r($this->mainitems_configs);
    $i23 = 0;
    foreach ($this->mainitems_configs as $vpconfig) {
      //print_r($vpconfig);
      $vpconfigsstr .='<option data-sliderlink="'.$i23.'" value="' . $vpconfig['settings']['id'] . '">' . $vpconfig['settings']['id'] . '</option>';

      $i23++;
    }

    $this->sample_data = get_option($this->dbname_sample_data);

    $defaultOpts = array(
      'usewordpressuploader' => 'on',
      'embed_prettyphoto' => 'on',
      'embed_masonry' => 'on',
      'is_safebinding' => 'on',
      'tinymce_disable_preview_shortcodes' => 'off',
      'use_api_caching' => 'on',
      'debug_mode' => 'off',
      'extra_css' => '',
      'extra_js' => '',
      'js_init_timeout' => '',
      'download_link_links_directly_to_file' => 'off',
      'force_autoplay_when_coming_from_share_link' => 'off',
      'replace_playlist_shortcode' => 'off',
      'replace_audio_shortcode' => 'off',
      'replace_audio_shortcode_play_in_footer' => 'off',
      'replace_powerpress_plugin' => 'off',
      'pcm_data_try_to_generate' => 'on',
      'enable_global_footer_player' => 'off',
      'skinwave_wave_mode' => 'canvas',
      'skinwave_wave_mode_canvas_reflection_size' => '0.25',
      'skinwave_wave_mode_canvas_waves_number' => '3',
      'skinwave_wave_mode_canvas_waves_padding' => '1',
      'admin_close_otheritems' => 'on',
      'force_file_get_contents' => 'off',
      'pcm_notice' => 'off',
      'color_waveformbg' => '111111', //==no hash
      'color_waveformprog' => 'ef6b13',
      'settings_wavestyle' => 'reflect',
      'soundcloud_api_key' => '',
      'wc_single_product_player' => 'off',
      'wc_loop_product_player' => 'off',
      'wc_product_play_in_footer' => 'off',
      'try_to_hide_url' => 'off',
      'wc_loop_player_position' => 'top',
      'play_remember_time' => '120',
      'activate_comments_widget' => 'off',
      'settings_trigger_resize' => 'off',
      'mobile_disable_footer_player' => 'off',
      'enable_raw_shortcode' => 'off',
      'enable_auto_backup' => 'on',
      'dzsap_meta_post_types' => array('dzsap_items'),
      'www_handle' => 'default',
      'dzsap_sliders_rewrite' => 'audio-sliders',
      'str_likes_part1' => '<span class="btn-zoomsounds btn-like"><span class="the-icon">{{heart_svg}}</span><span class="the-label hide-on-active">Like</span><span class="the-label show-on-active">Liked</span></span>',
      'str_views' => '<div class="counter-hits"><i class="fa fa-play"></i><span class="the-number">{{get_plays}}</span></div>',
      'str_downloads_counter' => '<div class="counter-hits"><i class="fa fa-cloud-download"></i><span class="the-number">{{get_downloads}}</span></div>',
      'str_likes_part2' => '<div class="counter-likes"><i class="fa fa-heart"></i><span class="the-number">{{get_likes}}</span></div>',
      'str_rates' => '<div class="counter-rates"><span class="the-number">{{get_rates}}</span> rates</div>',
      'waveformgenerator_multiplier' => '1',
      'use_external_uploaddir' => 'on',
      'always_embed' => 'off',
      'failsafe_repair_media_element' => 'off',
      'construct_player_list_for_sync' => 'off',
      'i18n_buy' => '',
      'i18n_play' => '',
      'i18n_free_download' => __("Free Download"),
      'dzsap_categories_rewrite' => __("Audio Category"),
    );
    $this->mainoptions = get_option($this->dbname_options);

    //==== default opts / inject into db
    if ($this->mainoptions == '') {
      $this->mainoptions = $defaultOpts;
      update_option($this->dbname_options, $this->mainoptions);
    }

    $this->mainoptions = array_merge($defaultOpts, $this->mainoptions);
    //print_r($this->mainoptions);
    //===translation stuff
    load_plugin_textdomain('dzsap', false, basename(dirname(__FILE__)) . '/languages');


    if($this->mainoptions['i18n_buy']==''){
      $this->mainoptions['i18n_buy']= __("Buy");
    }
    if($this->mainoptions['i18n_play']==''){
      $this->mainoptions['i18n_play']= __("Play");
    }
    if($this->mainoptions['i18n_title']==''){
      $this->mainoptions['i18n_title']= __("Title");
    }

    $this->post_options();






    require_once("class_parts/options_array_player.php");


    if (isset($_POST['deleteslider'])) {
      //print_r($this->mainitems);
      if (isset($_GET['page']) && $_GET['page'] == $this->adminpagename) {
        unset($this->mainitems[$_POST['deleteslider']]);
        $this->mainitems = array_values($this->mainitems);
        $this->currSlider = 0;
        //print_r($this->mainitems);
        update_option($this->dbname_mainitems, $this->mainitems);
      }


      if (isset($_GET['page']) && $_GET['page'] == $this->adminpagename_configs) {
        unset($this->mainitems_configs[$_POST['deleteslider']]);
        $this->mainitems_configs = array_values($this->mainitems_configs);
        $this->currSlider = 0;
        //print_r($this->mainitems);
        update_option($this->dbname_mainitems_configs, $this->mainitems_configs);
      }
    }

    //echo get_admin_url('', 'options-general.php?page=' . $this->adminpagename) . dzs_curr_url();
    //echo $newurl;

    $uploadbtnstring = '<button class="button-secondary action upload_file ">'.__("Upload").'</button>';



    if ($this->mainoptions['usewordpressuploader'] != 'on') {
      $uploadbtnstring = '<div class="dzs-upload">
<form name="upload" action="#" method="POST" enctype="multipart/form-data">
    	<input type="button" value="Upload" class="btn_upl"/>
        <input type="file" name="file_field" class="file_field"/>
        <input type="submit" class="btn_submit"/>
</form>
</div>
<div class="feedback"></div>';
    }

    ///==== important: settings must have the class mainsetting
    $this->sliderstructure = '<div class="slider-con" style="display:none;">

        <div class="settings-con">
        <h4>' . __('General Options', 'dzsap') . '</h4>
        <div class="setting type_all">
            <div class="setting-label">' . __('ID', 'dzsap') . '</div>
            <input type="text" class="textinput mainsetting main-id" name="0-settings-id" value="default"/>
            <div class="sidenote">' . __('Choose an unique id.', 'dzsap') . '</div>
        </div>
        
        
        <div class="setting type_all">
            <div class="setting-label">' . __('Gallery Skin', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-galleryskin">
                <option>skin-wave</option>
                <option>skin-default</option>
                <option>skin-aura</option>
            </select>
        </div>
        <div class="setting type_all vpconfig-wrapper">
            <div class="setting-label">' . __('ZoomSounds Player Configuration', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme vpconfig-select" name="0-settings-vpconfig">
                <option value="default">' . __('default', 'dzsap') . '</option>
                ' . $vpconfigsstr . '
            </select>
            <div class="sidenote" style="">' . __('setup these inside the <strong>ZoomSounds Player Configs</strong> admin', 'dzsap') . ' <a id="quick-edit" class="quick-edit-vp" href="'.admin_url('admin.php?page=' . $this->adminpagename_configs.'&currslider=0&from=shortcodegenerator').'" class="sidenote" style="cursor:pointer;">'.__("Quick Edit ").'</a></div>
            <div class="edit-link-con"></div>
        </div>';


    $lab = 'mode';
    $this->sliderstructure.='<div class="setting type_all">
            <div class="setting-label">' . __('Mode', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme dzs-dependency-field" name="0-settings-'.$lab.'">
                <option value="mode-normal">'.__("Default").'</option>
                <option value="mode-showall">'.__("Show All").'</option>
            </select>
            <div class="sidenote">' . sprintf(__('%sshow all%s lists the players one below the other ', 'dzsap'),'<strong>','</strong>') . '</div>
        </div>';



    $dependency = array(

      array(
        'element'=>'0-settings-mode',
        'value'=>array('mode-normal'),
      ),
    )
    ;

    $aux = json_encode($dependency);
    $aux_dependency_for_mode_normal = str_replace('"','{quotquot}',$aux);




    $dependency = array(

      array(
        'element'=>'0-settings-mode',
        'value'=>array('mode-showall'),
      ),
    )
    ;

    $aux = json_encode($dependency);
    $aux_dependency_for_mode_show_all = str_replace('"','{quotquot}',$aux);



    /*
         *
         *
         *
         *
         */


    /*
         *
        <div class="setting type_all">
            <div class="setting-label">' . __('Player Navigation', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-player_navigation">
                <option value="default">' . __('Default', 'dzsap') . '</option>
                <option value="off">' . __('Force Disable', 'dzsap') . '</option>
                <option value="on">' . __('Force Enable', 'dzsap') . '</option>
            </select>
            <div class="sidenote">' . __('Default will decide automatically if the player needs navigation or no', 'dzsap') . '</div>
        </div>
         *
         *
         */

    $this->sliderstructure.='
        
        
        <div class="setting type_all" data-dependency="'.$aux_dependency_for_mode_show_all.'">
            <div class="setting-label">' . __('Enable number indicator', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-settings_mode_showall_show_number">
                <option value="on">' . __('on', 'dzsap') . '</option>
                <option value="off">' . __('off', 'dzsap') . '</option>
            </select>
            <div class="sidenote">' . __('Disable arrows for gallery navigation on the player ', 'dzsap') . '</div>
        </div>
        
        
        
        <div class="setting type_all">
            <div class="setting-label">' . __('Disable Player Navigation', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-disable_player_navigation">
                <option value="off">' . __('off', 'dzsap') . '</option>
                <option value="on">' . __('on', 'dzsap') . '</option>
            </select>
            <div class="sidenote">' . __('Disable arrows for gallery navigation on the player ', 'dzsap') . '</div>
        </div>
        
        <div class="setting">
            <div class="setting-label">' . __('Background', 'dzsap') . '</div>
            <input type="text" class="textinput mainsetting with-colorpicker" name="0-settings-bgcolor" value="transparent"/><div class="picker-con"><div class="the-icon"></div><div class="picker"></div></div>
        </div>
        
        
        
        <div class="setting type_all">
            <div class="setting-label">' . __('Linking', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-settings_enable_linking">
                <option value="off">' . __('off', 'dzsap') . '</option>
                <option value="on">' . __('on', 'dzsap') . '</option>
            </select>
            <div class="sidenote">' . __('when selecting a track in the menu the link will update to reflect the new track selected', 'dzsap') . '</div>
        </div>
        
        
        
        
        
        <br>
        <div class="dzstoggle toggle1" rel=""  data-dependency="'.$aux.'">
<div class="toggle-title" style="">' . __('Menu Options', 'dzsap') . '</div>
<div class="toggle-content">


<div class="setting type_all" data-dependency="'.$aux.'">
            <div class="setting-label">' . __('Menu Position', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-menuposition">
                <option>bottom</option>
                <option>none</option>
                <option>top</option>
            </select>
        </div>
        <div class="setting type_all" data-dependency="'.$aux.'" >
            <div class="setting-label">' . __('Menu State', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-design_menu_state">
                <option value="open">'.__("Open").'</option>
                <option value="closed">'.__("Closed").'</option>
            </select>
            <div class="sidenote">' . __('If you set this to closed, you should enable the <strong>Menu State Button</strong> below. ', 'dzsap') . '</div>
        </div>
        <div class="setting type_all" data-dependency="'.$aux.'">
            <div class="setting-label">' . __('Menu State Button', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-design_menu_show_player_state_button">
                <option>off</option>
                <option>on</option>
            </select>
        </div>
        <div class="setting type_all" >
            <div class="setting-label">' . __('Facebook Share', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-menu_facebook_share">
                <option>auto</option>
                <option>off</option>
                <option>on</option>
            </select>
            <div class="sidenote">' . __('enable a facebook share button in the menu ', 'dzsap') . '</div>
        </div>
        <div class="setting type_all" >
            <div class="setting-label">' . __('Like Button', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-menu_like_button">
                <option>auto</option>
                <option>off</option>
                <option>on</option>
            </select>
            <div class="sidenote">' . __('enable a like button in the menu ', 'dzsap') . '</div>
        </div>


</div>
</div>


        <div class="dzstoggle toggle1" rel="">
<div class="toggle-title" style="">' . __('Autoplay Options', 'dzsap') . '</div>
<div class="toggle-content">


        <div class="setting type_all">
            <div class="setting-label">' . __('Cue First Media', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-cuefirstmedia">
                <option value="on">' . __('on', 'dzsap') . '</option>
                <option value="off">' . __('off', 'dzsap') . '</option>
            </select>
        </div>

        <div class="setting type_all">
            <div class="setting-label">' . __('Autoplay', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-autoplay">
                <option value="on">' . __('on', 'dzsap') . '</option>
                <option value="off">' . __('off', 'dzsap') . '</option>
            </select>
        </div>
        
        
        
        <div class="setting type_all">
            <div class="setting-label">' . __('Autoplay Next', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-autoplaynext">
                <option value="on">' . __('on', 'dzsap') . '</option>
                <option value="off">' . __('off', 'dzsap') . '</option>
            </select>
        </div>
</div>
</div>
        
        
        <div class="dzstoggle toggle1" rel="">
<div class="toggle-title" style="">' . __('Play / Like Settings', 'dzsap') . '</div>
<div class="toggle-content">


<div class="setting type_all">
            <div class="setting-label">' . __('Enable Play Count', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-enable_views">
                <option value="off">' . __('off', 'dzsap') . '</option>
                <option value="on">' . __('on', 'dzsap') . '</option>
            </select>
            <div class="sidenote">' . __('enable play count - warning: the media file has to be attached to a library item ( the Link To Media field .. ) ', 'dzsap') . '</div>
        </div>


<div class="setting type_all">
            <div class="setting-label">' . __('Enable Downloads Counter', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-enable_downloads_counter">
                <option value="off">' . __('off', 'dzsap') . '</option>
                <option value="on">' . __('on', 'dzsap') . '</option>
            </select>
            <div class="sidenote">' . __('enable download count - warning: the media file has to be attached to a library item ( the Link To Media field .. ) ', 'dzsap') . '</div>
        </div>

        <div class="setting type_all">
            <div class="setting-label">' . __('Enable Like Count', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-enable_likes">
                <option value="off">' . __('off', 'dzsap') . '</option>
                <option value="on">' . __('on', 'dzsap') . '</option>
            </select>
            <div class="sidenote">' . __('enable like count - warning: the media file has to be attached to a library item ( the Link To Media field .. ) ', 'dzsap') . '</div>
        </div>


        <div class="setting type_all">
            <div class="setting-label">' . __('Enable Rating', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-enable_rates">
                <option value="off">' . __('off', 'dzsap') . '</option>
                <option value="on">' . __('on', 'dzsap') . '</option>
            </select>
            <div class="sidenote">' . __('enable rating - warning: the media file has to be attached to a library item ( the Link To Media field .. ) ', 'dzsap') . '</div>
        </div>



</div>
</div>





        
        
        <div class="dzstoggle toggle1" rel="">
<div class="toggle-title" style="">' . __('Force Dimensions', 'dzsap') . '</div>
<div class="toggle-content">


        <div class="setting type_all">
            <div class="setting-label">' . __('Force Width', 'dzsap') . '</div>
            <input type="text" class="textinput mainsetting" name="0-settings-width" value=""/>
            <div class="sidenote">' . __('Force a fix width, leave blank for responsive mode ', 'dzsap') . '</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">' . __('Force Height', 'dzsap') . '</div>
            <input type="text" class="textinput mainsetting" name="0-settings-height" value=""/>
            <div class="sidenote">' . __('Force a fix height, leave blank for default mode ', 'dzsap') . '</div>
        </div>
        
        
        
        <div class="setting type_all" data-dependency="'.$aux.'">
            <div class="setting-label">' . __('Menu Maximum Height', 'dzsap') . '</div>
            <input type="text" class="textinput mainsetting" name="0-settings-design_menu_height" value="default"/>
        </div>
        
</div>
</div>

        

        </div><!--end settings con-->

        <div class="master-items-con mode_all">
        <div class="items-con "></div>
        <a href="#" class="add-item"></a>
        </div><!--end master-items-con-->
        <div class="clear"></div>
        </div>';
    $this->itemstructure = $this->generate_item_structure();




    /*
         *
         *
         *
        <div class="setting">
            <div class="setting-label">' . __('Background', 'dzsap') . '</div>
            <input type="text" class="textinput mainsetting with-colorpicker" name="0-settings-bgcolor" value="transparent"/><div class="picker-con"><div class="the-icon"></div><div class="picker"></div></div>
        </div>
         *
         *
         */




    $this->videoplayerconfig = '<div class="slider-con" style="display:none;">
        <div class="settings-con">';





    $this->videoplayerconfig .= '
        <div class="dzs-tabs auto-init-from-nice">

                <div class="dzs-tab-tobe">
                    <div class="tab-menu with-tooltip">
                        <i class="fa fa-tachometer"></i>'.__("General").'
                    </div>
                    <div class="tab-content">
                        <br>




        
        <div class="setting type_all">
            <div class="setting-label">' . __('Config ID', 'dzsap') . '</div>
            <input type="text" class="textinput mainsetting main-id" name="0-settings-id" value="default"/>
            <div class="sidenote">' . __('Choose an unique id.', 'dzsap') . '</div>
        </div>
        <div class="setting styleme">
            <div class="setting-label">' . __('Audio Player Skin', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-skin_ap">
                <option>skin-wave</option>
                <option>skin-default</option>
                <option>skin-minimal</option>
                <option>skin-minion</option>
                <option>skin-justthumbandbutton</option>
                <option>skin-pro</option>
                <option>skin-aria</option>
                <option>skin-silver</option>
                <option>skin-steel</option>
                <option>skin-customcontrols</option>
            </select>
            <div class="sidenote">' . __('choose a skin.', 'dzsap') . '</div>
        </div>
        
        
        
        
        
        <div class="setting styleme">
            <div class="setting-label">' . __('Enable Embed Button', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-enable_embed_button">
                <option value="off">'.__("Disable").'</option>
                <option value="in_player_controls">'.__("In player controls").'</option>
                <option value="in_extra_html">'.__("Below player").'</option>
            </select>
            <div class="sidenote">' . __('enable a embed button for visitors to be able the embed the player on their sites.', 'dzsap') . '</div>
        </div>
        
        
        
                    
        <div class="setting styleme">
            <div class="setting-label">' . __('Hover to Play', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-preview_on_hover">
                <option>off</option>
                <option>on</option>
            </select>
            <div class="sidenote">' . __('zoomsounds offers the possibility to play tracks on hover', 'dzsap') . '</div>
        </div>
        
        
        
        
        
        
        <div class="setting styleme">
            <div class="setting-label">' . __('Loop', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-loop">
                <option>off</option>
                <option>on</option>
            </select>
            <div class="sidenote">' . __('Loop the track on song end', 'dzsap') . '</div>
        </div>
        <div class="setting styleme">
            <div class="setting-label">' . __('Preload Method', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-preload_method">
                <option>metadata</option>
                <option>auto</option>
                <option>none</option>
            </select>
            <div class="sidenote">' . __('none - preload no info / metadata - preload only metadata ( total time and thumbnail ) / auto - preload the whole track ', 'dzsap') . '</div>
        </div>
        <div class="setting type_all">
            <div class="setting-label">' . __('Play From', 'dzsap') . '</div>
            <input type="text" class="textinput mainsetting" name="0-settings-playfrom" value="off"/>
            <div class="sidenote">' . __('This is a default setting, it can be changed individually per item ( it will be overwritten if set ) . - choose a number of seconds from which the track to play from ( for example if set "70" then the track will start to play from 1 minute and 10 seconds ) or input "last" for the track to play at the last position where it was.', 'dzsap') . '</div>
        </div>
        
        
        <div class="setting type_all">
            <div class="setting-label">' . __('Default Volume', 'dzsap') . '</div>
            <input type="text" class="textinput mainsetting" name="0-settings-default_volume" value="default"/>
            <div class="sidenote">' . __('number / set the default volume 0-1 or "last" for the last known volume', 'dzsap') . '</div>
        </div>
        
        
        
        
        
        ';



    $lab = 'menu_right_enable_facebook_share';
    $this->videoplayerconfig.='
        <div class="setting styleme">
            <div class="setting-label">' . __('Enable Facebook Share in Player', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-'.$lab.'">
                <option>off</option>
                <option>on</option>
            </select>
            <div class="sidenote">' . __('deprecated - use universal share instead ', 'dzsap') . '</div>
        </div>';



    $lab = 'menu_right_enable_multishare';
    $this->videoplayerconfig.='
        <div class="setting styleme">
            <div class="setting-label">' . __('Enable Universal Share in Player', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-'.$lab.'">
                <option>off</option>
                <option>on</option>
            </select>
            <div class="sidenote">' . __('enable a button that brings up a lightbox with all share options', 'dzsap') . '</div>
        </div>';

    $lab = 'player_navigation';
    $this->videoplayerconfig.='
        <div class="setting styleme">
            <div class="setting-label">' . __('Player Navigation', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-'.$lab.'">
                <option value="default">'.__("Detect").'</option>
                <option value="off">'.__("Disable").'</option>
                <option value="on">'.__("Force On").'</option>
            </select>
            <div class="sidenote">' . __('show or not the left and right arrows alongside the play button - leave default for the player to auto detect if it needs them', 'dzsap') . '</div>
        </div>';


    $this->videoplayerconfig.='



</div>
</div>







                <div class="dzs-tab-tobe tab-disabled">
                    <div class="tab-menu ">
                        &nbsp;&nbsp;
                    </div>
                    <div class="tab-content">

                    </div>
                </div>



                <div class="dzs-tab-tobe">
                    <div class="tab-menu with-tooltip">
                        <i class="fa fa-paint-brush"></i>'.__("Styling").'
                    </div>
                    <div class="tab-content">
                    
                    
                    
        <div class="setting styleme">
            <div class="setting-label">' . __('Animate Play Pause', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-design_animateplaypause">
                <option>default</option>
                <option>off</option>
                <option>on</option>
            </select>
            <div class="sidenote">' . __('fade animation on play / pause', 'dzsap') . '</div>
        </div>
        
        
        
        
        
        ';






    $lab = 'enable_footer_close_button';
    $this->videoplayerconfig.='
        <div class="setting styleme">
            <div class="setting-label">' . __('Enable Footer Close Button', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-'.$lab.'">
                <option>off</option>
                <option>on</option>
            </select>
            <div class="sidenote">' . __('only for footer players', 'dzsap') . '</div>
        </div>';

    $lab = 'disable_scrubbar';
    $this->videoplayerconfig.='
        <div class="setting styleme">
            <div class="setting-label">' . __('Disable Scrubbar', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-'.$lab.'">
                <option>off</option>
                <option>on</option>
            </select>
            <div class="sidenote">' . __('disable the scrubbar / wave', 'dzsap') . '</div>
        </div>';


    $this->videoplayerconfig.='
        <div class="setting styleme">
            <div class="setting-label">' . __('Disable Volume', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-disable_volume">
                <option value="default">'.__("Detect").'</option>
                <option value="off">'.__("Disable").'</option>
                <option value="on">'.__("Force On").'</option>
            </select>
            <div class="sidenote">' . __('disable the volume bar if set to "on". set to skin default when "default" is set.', 'dzsap') . '</div>
        </div>';




    // 111111

    $this->videoplayerconfig.='<div class="setting type_all"><div class="label">'.__("Highlight Color").'</div><input type="text" name="0-settings-colorhighlight" class="textinput mainsetting colorpicker-nohash with_colorpicker" value=""/><div class="picker-con"><div class="the-icon"></div><div class="picker"></div></div><div class="sidenote">' . __('Only for <strong>skin-wave</strong>', 'dzsap') . '</div></div>';



    $this->videoplayerconfig.='
                    
                    
                    
                    </div>
                    
                    </div>









                <div class="dzs-tab-tobe tab-disabled">
                    <div class="tab-menu ">
                        &nbsp;&nbsp;
                    </div>
                    <div class="tab-content">

                    </div>
                </div>



                <div class="dzs-tab-tobe">
                    <div class="tab-menu with-tooltip">
                        <i class="fa fa-bar-chart"></i>Skin-Wave
                    </div>
                    <div class="tab-content">
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    <div class="setting styleme">
            <div class="setting-label">' . __('Dynamic Waves', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-skinwave_dynamicwaves">
                <option>off</option>
                <option>on</option>
            </select>
            <div class="sidenote">' . __('*only on skin-wave - dynamic waves that act on volume change', 'dzsap') . '</div>
        </div>
        <div class="setting styleme">
            <div class="setting-label">' . __('Enable Spectrum', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-skinwave_enablespectrum">
                <option>off</option>
                <option>on</option>
            </select>
            <div class="sidenote">' . __('*only on skin-wave - enable a realtime spectrum analyzer instead of the static generated waveform / the file must be on the same server for security issues', 'dzsap') . '</div>
        </div>
        <div class="setting styleme">
            <div class="setting-label">' . __('Enable Reflect', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-skinwave_enablereflect">
                <option>on</option>
                <option>off</option>
            </select>
            <div class="sidenote">' . __('*only on skin-wave - enable a small reflection of the waves / spectrum', 'dzsap') . '</div>
        </div>
        <div class="setting styleme">
            <div class="setting-label">' . __('Enable Commenting', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-skinwave_comments_enable">
                <option>off</option>
                <option>on</option>
            </select>
            <div class="sidenote">' . __('*only on skin-wave - enable time-based commenting', 'dzsap') . '</div>
        </div>
        <div class="setting styleme">
            <div class="setting-label">' . __('Mode', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-skinwave_mode">
                <option value="normal">' . __('Normal', 'dzsap') . '</option>
                <option value="small">' . __('Slick', 'dzsap') . '</option>
                <option value="alternate">' . __('Alternate', 'dzsap') . '</option>
                <option value="nocontrols">' . __('Just Wave', 'dzsap') . '</option>
            </select>
            <div class="sidenote">' . __('choose the normal or slick theming', 'dzsap') . '</div>
        </div>
        <div class="setting styleme">
            <div class="setting-label">' . __('Wave Mode', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-skinwave_wave_mode_canvas_mode">
                <option value="normal">' . __('Bar', 'dzsap') . '</option>
                <option value="reflecto">' . __('Wave', 'dzsap') . '</option>
            </select>
            <div class="sidenote">' . __('choose a bar type format or a wave for the waveform style', 'dzsap') . '</div>
        </div>
        <div class="setting styleme">
            <div class="setting-label">' . __('Button Style', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-button_aspect">
                <option value="default">' . __('Default', 'dzsap') . '</option>
                <option value="button-aspect-noir">' . __('Aspect Noir', 'dzsap') . '</option>
                <option value="button-aspect-noir button-aspect-noir--filled">' . __('Aspect Noir Filled', 'dzsap') . '</option>
            </select>
            <div class="sidenote">' . __('Button Style', 'dzsap') . '</div>
            <p><img src="http://i.imgur.com/aVIk654.png"/> <img src="http://i.imgur.com/oVUgjff.png"/> </p>
        </div>
        <div class="setting styleme">
            <div class="setting-label">' . __('Tweak the Bar Aligment ', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-scrubbar_tweak_overflow_hidden">
                <option value="off">' . __('Off', 'dzsap') . '</option>
                <option value="on">' . __('On', 'dzsap') . '</option>
            </select>
            <div class="sidenote">' . __('set this to <strong>on</strong> to get better animation on changing songs ( recommended only if you are changing songs with a footer player ) ', 'dzsap') . '</div>
        </div>
                    
                    
                    
                    
                    
                    
                    
                    
                    </div>
                    
                    </div>







                <div class="dzs-tab-tobe tab-disabled">
                    <div class="tab-menu ">
                        &nbsp;&nbsp;
                    </div>
                    <div class="tab-content">

                    </div>
                </div>



                <div class="dzs-tab-tobe">
                    <div class="tab-menu with-tooltip">
                        <i class="fa fa-puzzle-piece"></i>'.__("Misc").'
                    </div>
                    <div class="tab-content">
                    
                    
                    
        
        <div class="setting styleme">
            <div class="setting-label">' . __('Flash Backup', 'dzsap') . '</div>
            <select class="textinput mainsetting styleme" name="0-settings-settings_backup_type">
                <option>full</option>
                <option>simple</option>
            </select>
            <div class="sidenote">' . __('the flash backup type that will appear for browsers that do not have mp3 support and no ogg file has been '
        . 'specified. simple is seamless but unstable, full shows the full flash player.', 'dzsap') . '</div>
        </div>
                    
                    
                    
                    
                    
                    
        <div class="setting type_all">
            <div class="setting-label">' . __('Extra Classes for Player', 'dzsap') . '</div>
            <input type="text" class="textinput mainsetting" name="0-settings-extra_classes_player" />
            <div class="sidenote">' . __('extra classes ', 'dzsap') . '</div>
        </div>
                    
                    
                    
                    
                    
        <div class="setting type_all">
            <div class="setting-label">' . __('Classes for window width under 400 px ', 'dzsap') . '</div>
            <input type="text" class="textinput mainsetting" name="0-settings-restyle_player_under_400" />
            <div class="sidenote">' . __('developers only - ', 'dzsap') . '* - ' . __('apply some special classes for when the viewport is under 400px (mobiles view)', 'dzsap') . '</div>
        </div>
                    
                    
                    
                    
        <div class="setting type_all">
            <div class="setting-label">' . __('Classes for window width over 400 px ', 'dzsap') . '</div>
            <input type="text" class="textinput mainsetting" name="0-settings-restyle_player_over_400" />
            <div class="sidenote">' . __('developers only - ', 'dzsap') . '* - ' . __('apply some special classes for when the viewport is over 400px (desktop / tablet view)', 'dzsap') . ' / ' . __('remember that in order for the mobile setting to work, this must have input also', 'dzsap') . '</div>
        </div>
                    
                    
                    
                    
        <div class="setting type_all">
            <div class="setting-label">' . __('Extra HTML After Artist', 'dzsap') . '</div>
            <textarea rows="5" type="text" style="width: 100%;" class="textinput mainsetting" name="0-settings-settings_extrahtml_after_artist" ></textarea>
            <div class="sidenote">' . __('extra html on the rift of the artist field ( first line ) ', 'dzsap') . '</div>
        </div>
                    
                    
                    
                    
                    
        <div class="setting type_all">
            <div class="setting-label">' . __('Extra HTML in Right Controls', 'dzsap') . '</div>
            <textarea rows="5" type="text" style="width: 100%;" class="textinput mainsetting" name="0-settings-js_settings_extrahtml_in_float_right_from_config" ></textarea>
            <div class="sidenote">' . __('extra html on the in the right controls ', 'dzsap') . '</div>
        </div>
                    
                    
                    </div>
                    
                    </div>
                    
                    
                    
                    
                    </div><!-- end .tabs-->
        
        
        ';

    /*
        */

    $this->videoplayerconfig.='

        
        ';



    $val = 'ea8c52';

//        <h3>Wave Form Options</h3>
    //</div>
    //
//        <div class="setting">
//<div class="label">' . __('Highlight Color', 'dzsap') . '</div>





    $this->videoplayerconfig.='


        </div><!--end settings con-->
        <div class="clearboth"></div>
        </div>';




    // --- check posts
    if(isset($_GET['dzsap_shortcode_builder']) && $_GET['dzsap_shortcode_builder']=='on'){
//            dzsprx_shortcode_builder();

      include_once(dirname(__FILE__).'/tinymce/popupiframe.php');
      define('DONOTCACHEPAGE', true);
      define('DONOTMINIFY', true);

    }
    if(isset($_GET['dzsap_shortcode_player_builder']) && $_GET['dzsap_shortcode_player_builder']=='on'){
//            dzsprx_shortcode_builder();

      include_once(dirname(__FILE__).'/shortcodegenerator/generator_player.php');
      define('DONOTCACHEPAGE', true);
      define('DONOTMINIFY', true);

    }



    if($this->mainoptions['replace_powerpress_plugin']=='on'){

      add_filter('the_content', array($this,'filter_the_content'));
    }


    add_shortcode('zoomsounds_player', array($this, 'shortcode_player'));
    add_shortcode('zoomsounds_player_comment_field', array($this, 'shortcode_player_comment_field'));

    add_shortcode('dzsap_woo_grid', array($this, 'shortcode_woo_grid'));
    add_shortcode($this->the_shortcode, array($this, 'show_shortcode'));
    add_shortcode($this->the_shortcode.'_in_lightbox', array($this, 'show_shortcode_lightbox'));
    add_shortcode('dzs_' . $this->the_shortcode, array($this, 'show_shortcode'));
    add_shortcode('' . $this->the_shortcode.'_showcase', array($this, 'shortcode_showcase'));

    if($this->mainoptions['replace_playlist_shortcode'] == 'on'){

      add_shortcode('playlist', array($this, 'shortcode_playlist'));

//            add_editor_style( $this->base_url . 'audioplayer/audioplayer.css' );
    }
    if($this->mainoptions['replace_audio_shortcode'] && $this->mainoptions['replace_audio_shortcode']!=='off'){

      add_shortcode('audio', array($this, 'shortcode_audio'));
    }



    add_filter('attachment_fields_to_edit', array($this, 'filter_attachment_fields_to_edit'), 10, 2);
    add_filter('attachment_fields_to_save', array($this, "filter_attachment_fields_to_save"), null, 2);

    add_action('init', array($this, 'handle_init'));
    add_action('admin_init', array($this, 'handle_admin_init'));

    add_action('wp_ajax_dzs_get_attachment_src', array($this, 'ajax_get_attachment_src'));
    add_action('wp_ajax_dzsap_ajax', array($this, 'post_save'));
    add_action('wp_ajax_dzsap_save_configs', array($this, 'post_save_configs'));
    add_action('wp_ajax_dzsap_ajax_mo', array($this, 'post_save_mo'));
    add_action('wp_ajax_dzsap_delete_pcm', array($this, 'ajax_delete_pcm'));
    add_action('wp_ajax_dzsap_send_queue_from_sliders_admin', array($this, 'ajax_send_queue_from_sliders_admin'));


    add_action('wp_ajax_dzsap_front_submitcomment', array($this, 'ajax_front_submitcomment'));
    add_action('wp_ajax_dzsap_get_thumb_from_meta', array($this, 'ajax_get_thumb_from_meta'));
    add_action('wp_ajax_dzsap_submit_download', array($this, 'ajax_submit_download'));
    add_action('wp_ajax_dzsap_submit_views', array($this, 'ajax_submit_views'));
    add_action('wp_ajax_dzsap_submit_like', array($this, 'ajax_submit_like'));
    add_action('wp_ajax_dzsap_retract_like', array($this, 'ajax_retract_like'));
    add_action('wp_ajax_dzsap_submit_rate', array($this, 'ajax_submit_rate'));
    add_action('wp_ajax_dzsap_get_pcm', array($this, 'ajax_get_pcm'));
    add_action('wp_ajax_nopriv_dzsap_get_pcm', array($this, 'ajax_get_pcm'));

    add_action('wp_ajax_nopriv_dzsap_front_submitcomment', array($this, 'ajax_front_submitcomment'));
    add_action('wp_ajax_nopriv_dzsap_submit_download', array($this, 'ajax_submit_download'));
    add_action('wp_ajax_nopriv_dzsap_submit_views', array($this, 'ajax_submit_views'));
    add_action('wp_ajax_nopriv_dzsap_submit_like', array($this, 'ajax_submit_like'));
    add_action('wp_ajax_nopriv_dzsap_retract_like', array($this, 'ajax_retract_like'));
    add_action('wp_ajax_nopriv_dzsap_submit_rate', array($this, 'ajax_submit_rate'));
    add_action('wp_ajax_dzsap_submit_pcm', array($this, 'ajax_submit_pcm'));
    add_action('wp_ajax_nopriv_dzsap_submit_pcm', array($this, 'ajax_submit_pcm'));

    add_action('wp_ajax_ajax_dzsap_insert_sample_tracks', array($this, 'ajax_submit_sample_tracks'));
    add_action('wp_ajax_nopriv_ajax_dzsap_insert_sample_tracks', array($this, 'ajax_submit_sample_tracks'));

    add_action('wp_ajax_ajax_dzsap_remove_sample_tracks', array($this, 'ajax_remove_sample_tracks'));
    add_action('wp_ajax_nopriv_ajax_dzsap_remove_sample_tracks', array($this, 'ajax_remove_sample_tracks'));


    if ($this->mainoptions['activate_comments_widget']=='on') {
      add_action('wp_dashboard_setup', array($this, 'wp_dashboard_setup'));
    }


    if ($this->mainoptions['enable_raw_shortcode']=='on') {
      remove_filter('the_content', 'wpautop');
      remove_filter('the_content', 'wptexturize');
      add_filter('the_content', array($this, 'my_formatter'), 99);
    }



    if ($this->mainoptions['tinymce_disable_preview_shortcodes'] != 'on') {
//            add_filter('mce_external_plugins', array( &$this, 'tinymce_external_plugins' ));
//            add_filter('tiny_mce_before_init', array( $this, 'myformatTinyMCE' ) );
    }


    add_action('admin_menu', array($this, 'handle_admin_menu'));
    add_action('admin_head', array($this, 'handle_admin_head'));
    add_action('admin_footer', array($this, 'handle_admin_footer'));


    add_action('wp_footer', array($this, 'handle_wp_footer'));
    add_action('wp_head', array($this, 'handle_wp_head'));


    add_action('add_meta_boxes',array($this,'handle_add_meta_boxes'));

    add_action('save_post',array($this,'admin_meta_save'));



//        add_action('woocommerce_after_main_content',array($this,'handle_woocommerce_after_main_content'));
//        add_action('woocommerce_after_shop_loop_item',array($this,'handle_woocommerce_after_shop_loop_item'));
//        add_action('woocommerce_shop_loop_item_title',array($this,'handle_woocommerce_shop_loop_item_title'));




    if($this->mainoptions['wc_single_product_player'] && $this->mainoptions['wc_single_product_player']!='off'){


//            echo ' $this->mainoptions[\'wc_loop_player_position\'] -  '.$this->mainoptions['wc_loop_player_position'];
      if($this->mainoptions['wc_loop_player_position']=='top'){


        add_action('woocommerce_single_product_summary',array($this,'handle_woocommerce_single_product_summary'));
      }
      if($this->mainoptions['wc_loop_player_position']=='overlay'){
        add_action('woocommerce_single_product_summary',array($this,'handle_woocommerce_single_product_summary'));
      }
      if($this->mainoptions['wc_loop_player_position']=='bellow'){

//                echo 'hmm';
        add_action('woocommerce_single_product_summary',array($this,'handle_woocommerce_single_product_summary'));
      }


    }
    if($this->mainoptions['wc_loop_product_player'] && $this->mainoptions['wc_loop_product_player']!='off'){


//            echo ' $this->mainoptions[\'wc_loop_player_position\'] -  '.$this->mainoptions['wc_loop_player_position'];
      if($this->mainoptions['wc_loop_player_position']=='top'){
        add_action('woocommerce_before_shop_loop_item',array($this,'handle_woocommerce_before_shop_loop_item'));
      }


      if($this->mainoptions['wc_loop_player_position']=='overlay'){
        add_action('woocommerce_before_shop_loop_item_title',array($this,'handle_woocommerce_before_shop_loop_item'));
      }

      if($this->mainoptions['wc_loop_player_position']=='bellow'){
        add_action('woocommerce_after_shop_loop_item',array($this,'handle_woocommerce_before_shop_loop_item'));
      }


    }


//        add_action('woocommerce_product_thumbnails', array($this, 'test'));
//        add_action('woocommerce_add_to_cart',array($this,'handle_woocommerce_add_to_cart'));
//        add_action('woocommerce_before_shop_loop',array($this,'handle_woocommerce_before_shop_loop'));
//        add_action('woocommerce_after_cart',array($this,'handle_woocommerce_after_cart'));
//        add_filter('wc_add_to_cart_message',array($this,'filter_wc_add_to_cart_message'));



    if(isset($_GET['taxonomy']) && $_GET['taxonomy']=='dzsap_sliders'){
      include_once('admin/sliders_admin.php');
      add_action('in_admin_footer','dzsap_sliders_admin');


    }

    include( dirname(__FILE__).'/woo/woo-plugin.php' );





    if(isset($_GET) && isset($_GET['load-lightbox-css']) && $_GET['load-lightbox-css']=='on'){

      header("Content-type: text/css");
      ?>
      .dzsap-main-con.loaded-item {
      opacity: 1;
      visibility: visible; }

      .dzsap-main-con.loading-item {
      opacity: 1;
      visibility: visible; }

      .dzsap-main-con {
      z-index: 5555;
      position: fixed;
      width: 100%;
      height: 100%;
      opacity: 0;
      visibility: hidden;
      top: 0;
      left: 0;
      transition-property: opacity, visibility;
      transition-duration: 0.3s;
      transition-timing-function: ease-out; }

      .dzsap-main-con .overlay-background {
      background-color: rgba(50, 50, 50, 0.5);
      position: absolute;
      width: 100%;
      height: 100%; }

      .dzsap-main-con .box-mains-con {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      pointer-events: none; }

      .dzsap-main-con .box-main {
      pointer-events: auto;
      max-width: 100%;
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate3d(-50%, -50%, 0);
      transition-property: left, opacity;
      transition-duration: 0.3s;
      transition-timing-function: ease-out; }

      .dzsap-main-con.transition-slideup.loaded-item .transition-target {
      opacity: 1;
      visibility: visible;
      transform: translate3d(0, 0, 0); }

      .dzsap-main-con.transition-slideup .transition-target {
      opacity: 0;
      visibility: hidden;
      transform: translate3d(0, 50px, 0);
      transition-property: all;
      transition-duration: 0.3s;
      transition-timing-function: ease-out; }

      .dzsap-main-con .box-main-media-con {
      max-width: 100%; }

      .dzsap-main-con .box-main .close-btn-con {
      position: absolute;
      right: -15px;
      top: -15px;
      z-index: 5;
      cursor: pointer;
      width: 30px;
      height: 30px;
      background-color: #dadada;
      border-radius: 50%; }

      .dzsap-main-con.gallery-skin-default .box-main-media {
      box-shadow: 0 0 10px 0 rgba(0, 0, 0, 0.3); }

      .dzsap-main-con .box-main-media-con .box-main-media {
      transition-property: width, height;
      transition-duration: 0.3s;
      transition-timing-function: ease-out; }

      .box-main-media.type-inlinecontent {
      background-color: #ffffff;
      padding: 15px; }

      .dzsap-main-con.skin-default .box-main:not(.with-description) .real-media {
      border-radius: 5px; }

      .dzsap-main-con .box-main-media-con .box-main-media > .real-media {
      width: 100%;
      height: 100%; }





      .real-media .hidden-content-for-zoombox, .real-media > .hidden-content {
      display: block !important; }

      .hidden-content {
      display: none !important; }

      .social-icon {
      margin-right: 3px;
      position: relative; }

      .social-icon > .fa {
      font-size: 30px;
      color: #999;
      transition-property: color;
      transition-duration: 0.3s;
      transition-timing-function: cubic-bezier(0.165, 0.84, 0.44, 1); }

      .social-icon > .the-tooltip {
      line-height: 1;
      padding: 6px 5px;
      background: rgba(0, 0, 0, 0.7);
      color: #FFFFFF;
      font-family: "Lato", "Open Sans", arial;
      font-size: 11px;
      font-weight: bold;
      position: absolute;
      left: 8px;
      white-space: nowrap;
      pointer-events: none;
      bottom: 100%;
      margin-bottom: 7px;
      opacity: 0;
      visibility: hidden;
      transition-property: opacity,visibility;
      transition-duration: 0.3s;
      transition-timing-function: cubic-bezier(0.165, 0.84, 0.44, 1); }


      .social-icon:hover > .the-tooltip{
      opacity:1;
      visibility: visible;
      }

      .social-icon > .the-tooltip:before {
      content: "";
      width: 0;
      height: 0;
      border-style: solid;
      border-width: 6px 6px 0 0;
      border-color: rgba(0, 0, 0, 0.7) transparent transparent transparent;
      position: absolute;
      left: 0;
      top: 100%; }

      h6.social-heading {
      display: block;
      text-transform: uppercase;
      font-family: "Lato",sans-sarif;
      font-size: 11px;
      font-weight: bold;
      margin-top: 20px;
      margin-bottom: 10px;
      color: #222222; }

      .field-for-view {
      background-color: #f0f0f0;
      line-height: 1;
      color: #555;
      padding: 8px;
      white-space: nowrap;
      font-size: 13px;
      overflow: hidden;
      text-overflow: ellipsis;
      font-family: 'Monospaced', Arial; }

      textarea.field-for-view {
      width: 100%;
      white-space: pre-line;
      line-height: 1.75; }
      <?php
      die();
    }


//        error_log(print_rrr($_GET));

//        echo 'ceva';
  }


  function shortcode_player_comment_field(){

    $fout = '';

    global $current_user;


    if($current_user->ID){
      $fout.= '<div class="zoomsounds-comment-wrapper">
                <div class="zoomsounds-comment-wrapper--avatar divimage" style="background-image: url(http://www.gravatar.com/avatar/?d=identicon);"></div>
                <div class="zoomsounds-comment-wrapper--input-wrap">
                    <input type="text" class="comment_text" placeholder="'.__("Write a comment").'"/>
                    <input type="text" class="comment_email" placeholder="'.__("Your email").'"/>
                    <!--<input type="text" class="comment_user" placeholder="'.__("Your display name").'"/>-->
                </div>

                <div class="zoomsounds-comment-wrapper--buttons">
                    <span class="dzs-button comments-btn-cancel">'.__("Cancel").'</span>
                    <span class="dzs-button comments-btn-submit">'.__("Submit").'</span>
                </div>
            </div>';
    }else{
      $fout.=__("You need to be logged in to comment");
    }


    return $fout;


  }

  function test(){
//        echo 'ceva2';
  }

  function handle_woocommerce_after_main_content(){
    echo 'woocommerce_after_main_content';
  }
  function handle_woocommerce_after_shop_loop_item(){
    echo 'woocommerce_after_shop_loop_item';
  }

  function handle_woocommerce_shop_loop_item_title(){
    echo 'woocommerce_shop_loop_item_title';
  }
  function handle_woocommerce_single_product_summary(){
//        echo 'woocommerce_single_product_summary';



    global $post;

//        echo 'whaaa';


    if($this->has_generated_product_player){
      return false;
    }


    $id = 0;

    if($post && $post->ID){
      $id = $post->ID;
    }

    $product = wc_get_product($id);


//        print_rr($product);
    if($product->is_type('grouped')){
      $children = $product->get_children();

//            print_rr($children);


      $ids = '';



      foreach ($children as $poid){
        if(get_post_meta($poid,'dzsap_woo_product_track',true)){

//                    echo 'whaaa';
          if($ids){
            $ids.=',';
          }
          $ids.=$poid;
        }
      }



//            echo 'ids - '.$ids;



      $fout = '';
      $iout = ''; //items parse





      echo '<div class="wc-dzsap-wrapper for-dzsag ';

      if($this->mainoptions['wc_loop_player_position']=='overlay') {
        echo 'go-after-thumboverlay ';
      }

      echo '">';

      if($ids){

        echo $this->shortcode_showcase(array(

          'feed_from'=>'audio_items',
          'ids'=>$ids,
        ));



        $this->has_generated_product_player = true;
      }

      echo '</div>';

    }else{
      $this->wc_generate_player($id);
    }





  }


  function wc_generate_player($id){


    if($this->has_generated_product_player){
      return false;
    }

    $this->has_generated_product_player= false;


    $post = get_post($id);
    if($id && get_post_meta($post->ID,'dzsap_woo_product_track',true)) {

//            echo 'whaaa';
      $this->sliders__player_index++;

      $fout = '';


      $src = get_post_meta($post->ID, 'dzsap_woo_product_track', true);



      $this->front_scripts();

      $margs = array('mp3' => $src, 'config' => $this->mainoptions['wc_single_product_player'],);

//        $margs = array_merge($margs, $atts);

//        print_r($margs);
      $margs['source'] = $margs['mp3'];
      $margs['called_from'] = 'single_product_summary';
      $margs['config'] = $this->mainoptions['wc_single_product_player'];

      $playerid = '';
      if($this->mainoptions['wc_product_play_in_footer']=='on'){
        $margs['faketarget']='#dzsap_footer';
      }



      if($this->mainoptions['wc_loop_player_position']=='overlay'){

        $margs['extra_classes']=' prevent-bubble';
      }



      echo '<div class="wc-dzsap-wrapper  ';

      if($this->mainoptions['wc_loop_player_position']=='overlay') {
        echo 'go-to-thumboverlay center-ap-inside';
      }

      echo '">';
//            echo 'whaa';
      echo $this->shortcode_player($margs, '');
      echo '</div>';

//        print_r($its); print_r($margs); echo 'alceva'.$fout;
    }

  }




  function handle_woocommerce_before_shop_loop_item(){
//        echo 'woocommerce_single_product_summary';


//        echo 'whaa';

    global $post;


    if($post && $post->ID && get_post_meta($post->ID,'dzsap_woo_product_track',true)){

      $this->wc_generate_player($post->ID);

    }
//        print_r($its); print_r($margs); echo 'alceva'.$fout;



  }
  function handle_woocommerce_before_shop_loop(){
    echo 'woocommerce_before_shop_loop';
  }


  function handle_woocommerce_add_to_cart(){
    echo 'woocommerce_add_to_cart';
  }
  function handle_woocommerce_after_cart(){
    echo 'woocommerce_after_cart';
  }

  function filter_wc_add_to_cart_message($fout){
    return  'wc_add_to_cart_message'.$fout.'wc_add_to_cart_message_end';
  }



  function ajax_get_pcm(){
    echo '';



    $id = '';


    if(isset($_POST['playerid']) && $_POST['playerid']){
      $id = $_POST['playerid'];

    }else{

      if(isset($_POST['source']) && $_POST['source']){
        $id = $_POST['source'];
      }
    }

    $id = $this->clean($id);


    $fout = '';
    $lab  = 'dzsap_pcm_data_'.$this->clean($id);




    $pcm = '';
    $pcm = get_option($lab);

//                echo 'pcm - '.$pcm. ' - source ( dzsap_pcm_data_'.$this->clean($che['source']).' ) |||'."\n\n";
//                echo ' source ( dzsap_pcm_data_'.$this->clean($che['source']).' )';

    if($pcm=='' || $pcm=='[]'){

//            if(isset($che['linktomediafile'])){
//                if($che['linktomediafile']){
//                    $lab  = 'dzsap_pcm_data_'.$che['linktomediafile'];
//                }
//            }
      $pcm = get_option($lab);

      if( ( $pcm == '' || $pcm== '[]') && isset($che['playerid']) && $che['playerid']){
        $lab  = 'dzsap_pcm_data_'.$che['playerid'];
        $pcm = get_option($lab);
      }

//                    echo 'lab - '.$lab;
//                    $lab = 'dzsap_pcm_data_735';

//                    echo 'lab - '.$lab;

//                    echo 'pcm - '.$pcm;

    }


    echo $pcm;




    die();
  }

  function filter_the_content($fout){

//        echo 'what what';

//        $fout='ceva'.$fout;





    if($this->mainoptions['replace_powerpress_plugin']=='on'){

      global $post;

      global $powerpress_feed;
//            print_rr($powerpress_feed);

// PowerPress settings:
      $GeneralSettings = get_option('powerpress_general');
//                print_rr($GeneralSettings);


      $feed_slug = 'podcast';

      $EpisodeData = powerpress_get_enclosure_data($post->ID, $feed_slug);

//            print_rr($EpisodeData);


      if($EpisodeData && isset($EpisodeData['url'])){








//            echo 'whaaa';
        $this->sliders__player_index++;

//                $fout = '';


        $src = get_post_meta($post->ID, 'dzsap_woo_product_track', true);



        $this->front_scripts();

        $margs = $this->powerpress_generate_margs();


        $args = array(

        );

        $aux = $this->shortcode_player($margs);



        return $aux.$fout;


      }

    }


    return $fout;
  }

  function powerpress_generate_margs(){

    global $post;

    global $powerpress_feed;
//            print_rr($powerpress_feed);

// PowerPress settings:
    $GeneralSettings = get_option('powerpress_general');
//                print_rr($GeneralSettings);


    $feed_slug = 'podcast';

    $margs = array();

    $EpisodeData = powerpress_get_enclosure_data($post->ID, $feed_slug);

//            print_rr($EpisodeData);


    if($EpisodeData && isset($EpisodeData['url'])) {


//            echo 'whaaa';
      $this->sliders__player_index++;

//                $fout = '';


      $src = get_post_meta($post->ID, 'dzsap_woo_product_track', true);


      $this->front_scripts();

      $margs = array('config' => 'powerpress_player',);

//        $margs = array_merge($margs, $atts);

//        print_r($margs);
      $margs['source'] = $EpisodeData['url'];
      $margs['called_from'] = 'powerpress';
      $margs['config'] = 'powerpress_player';
      $margs['artistname'] = $post->post_title;
//            $margs['js_settings_extrahtml_in_float_right'] = '<div><span class="display-inline-block">Share:</span>&nbsp;&nbsp;&nbsp;<div class="display-inline-block dzstooltip-con" style=";"><div class="the-icon-bg"></div> <span class="dzstooltip arrow-from-start transition-slidein arrow-bottom skin-black align-right" style="width: auto; white-space: nowrap;">Share on Twitter</span><i class=" svg-icon fa fa-twitter" style="color: #5aacff;"></i></div>   <div class="display-inline-block dzstooltip-con" style=";"><div class="the-icon-bg"></div> <span class="dzstooltip arrow-from-start transition-slidein arrow-bottom skin-black align-right" style="width: auto; white-space: nowrap;">Share on Facebook</span><i class=" svg-icon fa fa-facebook-square" style="color: #2288ff;"></i></div> </div><br><a class="button-grad " href="{{meta2val}}">                                        <i class="fa fa-apple"></i>                                        <span class="i-label">iTunes</span>                                    </a>  <a class="button-grad " href="{{meta1val}}">                                        <i class="fa fa-rss"></i>                                        <span class="i-label">RSS</span>                                    </a>  <a class="button-grad dzsap-multisharer-but " href="#">                                        <i class="fa fa-share "></i>                                        <span class="i-label">Embed</span>                                    </a>  ';

      if (get_the_post_thumbnail_url($post)) {

        $margs['thumb'] = get_the_post_thumbnail_url($post);
      }



      $categories = get_the_terms( $post->ID, 'category' );

//            print_rr($post);
//            print_rr($categories);
      if ( ! $categories || is_wp_error( $categories ) )
        $categories = array();

      $categories = array_values( $categories );


      if(count($categories)){



      }
      foreach ( $categories as $key => $val ) {
//                print_rr($val);


        // Get the URL of this category
        $category_link = get_category_link( $val->term_id );

//                print_rr($category_link);
//                libxml_use_internal_errors(false);
//                $myXMLData = DZSHelpers::get_contents($category_link.'feed');




        global $dzsap_got_category_feed;



        $lasttime = get_option('dzsap_last_read_category');

//                echo 'lasttime - '.$lasttime.'<br>';
//                echo 'lasttime ... time()-8 -> '.($lasttime-8).'<br>';
//                echo 'time() - '.time().'<br>';
//                echo 'lasttime==false - '.$lasttime==false.'<br>';
//                echo 'lasttime ... time()-8 - '.(($lasttime<time())-8).'<br>';






        $myXMLData = '';

        if(get_option('taxonomy_'.$val->term_id)){
          $aux = get_option('taxonomy_'.$val->term_id);

//                    print_rr($aux);

          if(isset($aux['feed_xml'])){
            $myXMLData = $aux['feed_xml'];
          }


          $myXMLData = stripslashes($myXMLData);
        }



        if($myXMLData=='' && ($lasttime==false || $lasttime<time()-7)){




          if($this->debug){

            print_rr($category_link.'feed') ;
          }
          update_option('dzsap_last_read_category',time());
          $myXMLData = @file_get_contents($category_link.'feed');
//                    $myXMLData = @file_get_contents('https://www.almightyballer.com/category/a-team/buzz-beat/feed/');
//                    $myXMLData = DZSHelpers::get_contents('https://www.almightyballer.com/category/a-team/buzz-beat/feed/');




//                    echo file_get_contents('https://www.almightyballer.com/category/a-team/buzz-beat/feed/');

          if($this->debug) {
            echo '<pre class="hmm">';   print_r($myXMLData);  echo '</pre>';
          }

//                    echo 'yes';

          $dzsap_got_category_feed = true;

        }

        if($myXMLData){

//                    print_rr($myXMLData);

          if(strpos($myXMLData,'<?xml')!==false && strpos($myXMLData,'<?xml')<30){

            $xml = simplexml_load_string($myXMLData);


//                        echo '<pre class="the-xml">';print_r($xml);echo '</pre>';





            if($xml){


              if($xml->channel->image[1]->url->__toString()){
                $margs['thumb']=$xml->channel->image[1]->url->__toString();
              }

              if($xml->channel->title->__toString()){
                $margs['songname']=$xml->channel->title->__toString();
              }
            }
          }

        }

      }

    }


    return $margs;


  }



  function handle_add_meta_boxes() {



    add_meta_box('dzsap_footer_player_options',__('Footer Player Settings'),array($this,'admin_meta_options'),'page','normal','high');
    add_meta_box('dzsap_footer_player_options',__('Footer Player Settings'),array($this,'admin_meta_options'),'post','normal','high');



    add_meta_box('dzsap_waveform_generation',__('ZoomSounds Waveforms'),array($this,'admin_meta_download_waveforms'),'download','normal','high');






    add_meta_box('dzsap_meta_options', __('Audio Item Settings'), array($this, 'dzsap_admin_meta_options'), 'dzsap_items', 'normal');




    $meta_post_array = $this->mainoptions['dzsap_meta_post_types'];


    if($meta_post_array && is_array($meta_post_array) && count($meta_post_array)){


      foreach ($meta_post_array as $post_type){
        if($post_type=='dzsap_items'){
          continue;
        }


        add_meta_box('dzsap_meta_options', __('Audio Item Settings'), array($this, 'dzsap_admin_meta_options'), $post_type, 'normal');

      }
    }

    //add_meta_box( 'attachment_video_thumb', __( 'Thumbnail', 'dzsap' ), array($this,'admin_meta_attachment_video_thumb'), 'attachment', 'normal' );

//        if ($this->db_mainoptions['enable_meta_for_pages_too'] == 'on') {
//            add_meta_box('dzsap_meta_options',__('DZS ZoomFolio Settings'),array($this,'admin_meta_options'),'page','normal','high');
//            add_meta_box('dzsap_meta_gallery',__('Item Gallery','dzsap'),array($this,'admin_meta_gallery'),'page','side');
//        }
  }

  function admin_meta_download_waveforms(){

    global $post;

    $po_id = $post->ID;

    $aux = '';
    $uploadbtnstring = '<button class="button-secondary action upload_file ">Upload</button>';



    if($this->mainoptions['skinwave_wave_mode']!='canvas') {

      $lab = 'dzsap_meta_waveformbg';
      $val = get_post_meta($po_id, $lab, true);

      $aux .= '<div class="setting type_all type_mediafile_hide">
            <h4 class="setting-label">' . __('WaveForm Background Image', 'dzsap') . '</h4>
' . DZSHelpers::generate_input_text($lab, array('class' => 'textinput upload-prev', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '"')) . $uploadbtnstring . ' <span class="aux-wave-generator"><button class="btn-autogenerate-waveform-bg button-secondary">' . __("Auto Generate") . '</button></span>
            <div class="sidenote">' . __('Optional waveform image / ', 'dzsap') . ' / ' . __('Only for skin-wave', 'dzsap') . '</div>
        </div>';


      //simple with upload and wave generator
      $lab = 'dzsap_meta_waveformprog';
      $val = get_post_meta($po_id, $lab, true);

      $aux .= '<div class="setting type_all type_mediafile_hide">
            <h4 class="setting-label">' . __('WaveForm Progress Image', 'dzsap') . '</h4>
' . DZSHelpers::generate_input_text($lab, array('class' => 'textinput upload-prev', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '"')) . $uploadbtnstring . ' <span class="aux-wave-generator"><button class="btn-autogenerate-waveform-prog button-secondary">Auto Generate</button></span>
            <div class="sidenote">' . __('Optional waveform image / ', 'dzsap') . ' / ' . __('Only for skin-wave', 'dzsap') . '</div>
        </div>';
    }

    echo $aux;
  }



  function dzsap_admin_meta_options() {
    global $post, $wp_version;
    $struct_uploader = '<div class="dzsvg-wordpress-uploader">
<a href="#" class="button-secondary">' . __('Upload', 'dzsap') . '</a>
</div>';
    //$wp_version = '3.4.1';
    if ($wp_version < 3.5) {
      $struct_uploader = '<div class="dzs-single-upload">
<input id="files-upload" class="" name="file_field" type="file">
</div>';
    }
    ?>
    <div class="select-hidden-con">
      <input type="hidden" name="dzs_nonce" value="<?php echo wp_create_nonce('dzs_nonce'); ?>"/>




      <div class="dzs-setting">
        <h4><?php echo __('Thumbnail', 'dzsap'); ?></h4>
        <?php echo DZSHelpers::generate_input_text('dzsap_thumb', array('class' => 'input-big-image main-thumb', 'def_value' => '', 'seekval' => get_post_meta($post->ID, 'dzsap_thumb', true))); ?>
        <?php echo $struct_uploader; ?>
        <button style="display: inline-block; vertical-align: top;" class="refresh-main-thumb button-secondary">
          Auto Generate
        </button>
        <div
          class='sidenote'><?php echo __('select a thumbnail for the video ( can auto generate if it is an Vimeo or YouTube track )', 'dzsap'); ?></div>
      </div>

      <div class="dzs-setting">
        <h4><?php echo __('Extra Classes', 'dzsap'); ?></h4>
        <?php echo DZSHelpers::generate_input_text('dzsap_extra_classes', array('class' => '', 'def_value' => '', 'seekval' => get_post_meta($post->ID, 'dzsap_extra_classes', true))); ?>
        <div
          class='sidenote'><?php echo __('[advanced] some extra classes that you want added to the portfolio item', 'dzsap'); ?></div>
      </div>

    </div>

    <?php
    include_once('class_parts/item-meta.php');

    wp_enqueue_style('dzssel', $this->base_url.'libs/dzsselector/dzsselector.css');
    wp_enqueue_script('dzssel', $this->base_url.'libs/dzsselector/dzsselector.js');
  }


  function ajax_get_attachment_src(){

    $fout = wp_get_attachment_image_src($_POST['id'], 'full');

    echo $fout[0];
    die();
  }


  function admin_meta_options() {
    global $post,$wp_version;
    $struct_uploader = '
<a href="#" class="button-secondary upload-for-target">'.__('Upload','dzsap').'</a>
';
    //$wp_version = '3.4.1';
    if ($wp_version < 3.5) {
      $struct_uploader = '<div class="dzs-single-upload">
<input id="files-upload" class="" name="file_field" type="file">
</div>';
    }


    $vpconfigs_arr = array(
      array('lab'=>'default', 'val'=>'default')
    );

    $i23=0;
    foreach ($this->mainitems_configs as $vpconfig) {
      //print_r($vpconfig);


      $auxa = array(
        'lab'=>$vpconfig['settings']['id'],
        'val'=>$vpconfig['settings']['id'],
        'extraattr'=>'data-sliderlink="'.$i23.'"',
      );

      array_push($vpconfigs_arr, $auxa);

      $i23++;
    }



    ?>
    <div class="dzsap-meta-bigcon">
      <input type="hidden" name="dzs_nonce" value="<?php echo wp_create_nonce('dzs_nonce'); ?>" />


      <?php
      ?>





      <div class="dzs-setting">
        <?php
        $lab = 'dzsap_footer_enable';

        echo DZSHelpers::generate_input_text($lab,array(
          'class' => 'fake-input',
          'def_value' => '',
          'seekval' => 'off',
          'input_type' => 'hidden',
        ));
        ?>
        <h4><?php echo __('Enable Sticky Player','dzsap'); ?></h4>
        <?php

        echo '<div class="dzscheckbox skin-nova">
                                        '.DZSHelpers::generate_input_checkbox($lab,array('id' => $lab,'class' => 'mainsetting dzs-dependency-field', 'val' => 'on','seekval' => get_post_meta($post->ID,$lab,true))).'
                                        <label for="'.$lab.'"></label>
                                    </div>';



        // -- for future we can do a logical set like "(" .. ")" .. "AND" .. "OR"
        $dependency = array(

          array(
            'lab'=>'dzsap_footer_enable',
            'val'=>array('on'),
          ),
//                    'relation'=>'AND',
        );


        ?>

      </div>



      <div data-dependency='<?php echo json_encode($dependency); ?>'>

        <?php


        $feed_type = array(
          array(
            'lab'=>'parent',
            'val'=>'parent',
          ),
          array(
            'lab'=>'custom',
            'val'=>'custom',
          ),
        );
        ?>




        <div class="dzs-setting "  >
          <h4><?php echo __('Feed Type','dzsap'); ?></h4>
          <?php
          $lab = 'dzsap_footer_feed_type';
          echo DZSHelpers::generate_select($lab,array('class' => 'dzs-style-me  dzs-dependency-field opener-listbuttons','options' => $feed_type,'seekval' => get_post_meta($post->ID,$lab,true)));


          ?>

          <ul class="dzs-style-me-feeder">

            <div class="bigoption">
              <span class="option-con"><img src="<?php echo $this->base_url; ?>tinymce/img/footer_type_parent.png"><span class="option-label"><?php echo __("Parent Player"); ?></span></span>
            </div>

            <div class="bigoption">
              <span class="option-con"><img src="<?php echo $this->base_url; ?>tinymce/img/footer_type_media.png"><span class="option-label"><?php echo __("Custom Media"); ?></span></span>
            </div>

          </ul>


          <div class="sidenote">
            <?php echo __("Select parent player for the sticky player to await being played from the outside ( a track on the page or select custom media to set a custom mp3 to play directly in the sticky player."); ?>
          </div>

        </div>



        <div class="dzs-setting vpconfig-wrapper"  >
          <h4><?php echo __('Player configuration','dzsap'); ?></h4>
          <?php
          $lab = 'dzsap_footer_vpconfig';
          echo DZSHelpers::generate_select($lab,array('class' => 'vpconfig-select styleme','options' => $vpconfigs_arr,'seekval' => get_post_meta($post->ID,$lab,true))); ?>

          <div class="edit-link-con" style="margin-top: 10px;"></div>

        </div>


        <?php



        // -- for future we can do a logical set like "(" .. ")" .. "AND" .. "OR"
        $dependency = array(

          array(
            'lab'=>'dzsap_footer_feed_type',
            'val'=>array('custom'),
          ),
//                    'relation'=>'AND',
        );



        ?>

        <div class="dzs-setting" data-dependency='<?php echo json_encode($dependency); ?>'>
          <h4><?php echo __('Featured Media','dzsap'); ?></h4>
          <?php
          $lab = 'dzsap_footer_featured_media';
          echo DZSHelpers::generate_input_text($lab,array('class' => 'input-big-image upload-target-prev','def_value' => '','seekval' => get_post_meta($post->ID,$lab,true))); ?>
          <?php echo $struct_uploader; ?>

        </div>

        <?php



        ?>



        <div class="dzs-setting "  data-dependency='<?php echo json_encode($dependency); ?>'>
          <h4><?php echo __('Media Type','dzsap'); ?></h4>
          <?php
          $types_arr = array(
            array('lab'=>'audio','val'=>'audio'),
            array('lab'=>'shoutcast','val'=>'shoutcast'),
            array('lab'=>'soundcloud','val'=>'soundcloud'),
            array('lab'=>'youtube','val'=>'youtube'),
            array('lab'=>'fake','val'=>'fake'),
          );
          $lab = 'dzsap_footer_type';
          echo DZSHelpers::generate_select($lab,array('class' => ' styleme','options' => $types_arr,'seekval' => get_post_meta($post->ID,$lab,true))); ?>

          <div class="edit-link-con"></div>

        </div>


      </div>



    </div>



    <?php
  }



  function admin_meta_save($post_id) {
    global $post;
    if (!$post) {
      return;
    }
    /* Check autosave */
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
      return $post_id;
    }
    if (isset($_REQUEST['dzs_nonce'])) {
      $nonce = $_REQUEST['dzs_nonce'];
      if (!wp_verify_nonce($nonce,'dzs_nonce'))
        wp_die('Security check');
    }
    if (is_array($_POST)) {
      $auxa = $_POST;
      foreach ($auxa as $label => $value) {

        //print_r($label); print_r($value);
        if (strpos($label,'dzsap_') !== false) {
          dzs_savemeta($post->ID,$label,$value);
        }
      }
    }
  }


  function filter_woocommerce_get_settings_pages( $settings ) {
//        echo 'hmmdada';
//        $settings[] =
//        return $settings;
  }


  function handle_wp_footer(){
    global $post, $wp_query;





    $footer_player_enabled = false;
    $footer_player_source = 'fake';
    $footer_player_config = 'fake';
    $footer_player_type = 'fake';



    if($this->mainoptions['enable_global_footer_player']!='off'){

      $footer_player_enabled = true;
      $footer_player_source = 'fake';
      $footer_player_type = 'fake';
      $footer_player_config = $this->mainoptions['enable_global_footer_player'];
    }

    if($wp_query && $wp_query->post) {
      if ( (get_post_meta($wp_query->post->ID, 'dzsap_footer_featured_media', true) || get_post_meta($wp_query->post->ID, 'dzsap_footer_enable', true)=='on') && get_post_meta($wp_query->post->ID, 'dzsap_footer_enable', true)!='off')  {

        $footer_player_enabled = true;




//               echo 'get_post_meta($wp_query->post->ID, \'dzsap_footer_type\', true) - '.get_post_meta($wp_query->post->ID, 'dzsap_footer_type', true);
        $footer_player_config = get_post_meta($wp_query->post->ID,'dzsap_footer_vpconfig',true);
        if (get_post_meta($wp_query->post->ID, 'dzsap_footer_feed_type', true)=='custom') {

          $footer_player_source = get_post_meta($wp_query->post->ID,'dzsap_footer_featured_media',true);
          $footer_player_type = get_post_meta($wp_query->post->ID,'dzsap_footer_type',true);

        }
      }
    }




    if($footer_player_enabled){
      if($footer_player_source){

        $this->front_scripts();



        $vpsettingsdefault = array(
          'id' => 'default',
          'skin_ap' => 'skin-default',
          'settings_backup_type' => 'full',
          'skinwave_dynamicwaves' => 'off',
          'skinwave_enablespectrum' => 'off',
          'skinwave_enablereflect' => 'on',
          'skinwave_comments_enable' => 'off',
          'skinwave_mode' => 'normal',
        );



        $cue = 'on';
        if($footer_player_type==='fake'){

          $cue = 'off';


        }

        $args = array(
          'player_id'=>'dzsap_footer',

          'source' => $footer_player_source,
          'cue' => $cue,
          'config' => $footer_player_config,
          'autoplay' => 'off',
          'type' => $footer_player_type,
        );


        $vpconfig_k = -1;
        $vpconfig_id = $footer_player_config;
        for ($i = 0; $i < count($this->mainitems_configs); $i++) {
          if ((isset($vpconfig_id)) && ($vpconfig_id == $this->mainitems_configs[$i]['settings']['id'])) {
            $vpconfig_k = $i;
          }
        }



        if ($vpconfig_k > -1) {
          $vpsettings = $this->mainitems_configs[$vpconfig_k];
        } else {
          $vpsettings['settings'] = $vpsettingsdefault;
        }





//                print_r($vpsettings);


//                echo 'hmm';


        echo '<div class="dzsap-sticktobottom-placeholder dzsap-sticktobottom-placeholder-for-'.$vpsettings['settings']['skin_ap'].'"></div>
<section class="dzsap-sticktobottom ';


        if(isset($vpsettings['settings']['skin_ap'])==false || ($vpsettings['settings']['skin_ap']=='skin-wave'&&$vpsettings['settings']['skinwave_mode']=='small')){
          echo ' dzsap-sticktobottom-for-skin-wave';
        }

//                print_r($vpsettings); echo 'ceva';

        if(isset($vpsettings['settings']['skin_ap'])==false || ($vpsettings['settings']['skin_ap']=='skin-silver')){
          echo ' dzsap-sticktobottom-for-skin-silver';
        }




        echo '">';

        echo '<div class="dzs-container">';


        if(isset($vpsettings['settings']['enable_footer_close_button'])==false || ($vpsettings['settings']['enable_footer_close_button']=='on')){
          echo '<div class="sticktobottom-close-con"><svg version="1.1" class="svg-icon icon-hide" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="144.883px" height="145.055px" viewBox="0 0 144.883 145.055" enable-background="new 0 0 144.883 145.055" xml:space="preserve"> <g> <g> <g> <g> <g> <path fill="#5A5B5D" d="M72.527,145.055C32.535,145.055,0,112.52,0,72.527S32.535,0,72.527,0c37.921,0,69.7,29.6,72.35,67.387 c0.097,1.377-0.942,2.572-2.319,2.669c-1.384,0.087-2.571-0.941-2.669-2.319C137.423,32.557,107.834,5,72.527,5 C35.293,5,5,35.293,5,72.527s30.293,67.527,67.527,67.527c35.271,0,64.858-27.525,67.355-62.665 c0.098-1.377,1.302-2.396,2.672-2.316c1.377,0.099,2.414,1.294,2.316,2.672C142.188,115.488,110.41,145.055,72.527,145.055z"/> </g> </g> <g> <g> <g> <path fill="#5A5B5D" d="M45.658,101.897c-0.64,0-1.279-0.244-1.768-0.732c-0.977-0.976-0.977-2.559,0-3.535l25.102-25.103 L43.891,47.425c-0.977-0.977-0.977-2.56,0-3.535c0.977-0.977,2.559-0.977,3.535,0l26.869,26.87 c0.977,0.977,0.977,2.559,0,3.535l-26.869,26.87C46.938,101.653,46.298,101.897,45.658,101.897z"/> </g> </g> <g> <g> <path fill="#5A5B5D" d="M99.396,101.896c-0.64,0-1.279-0.244-1.768-0.732L70.76,74.295c-0.977-0.977-0.977-2.559,0-3.535 l26.869-26.87c0.977-0.977,2.559-0.977,3.535,0c0.977,0.976,0.977,2.559,0,3.535L76.062,72.527l25.102,25.102 c0.977,0.977,0.977,2.559,0,3.535C100.676,101.652,100.036,101.896,99.396,101.896z"/> </g> </g> </g> </g> </g> </g> </svg><svg version="1.1" class="svg-icon icon-show" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="148.025px" height="148.042px" viewBox="0 0 148.025 148.042" enable-background="new 0 0 148.025 148.042" xml:space="preserve"> <g> <g> <g> <g> <g> <g> <path fill="#5A5B5D" d="M74.038,148.042c-8.882,0-17.778-1.621-26.329-4.873C14.546,130.561-5.043,96.09,1.132,61.206 c0.241-1.359,1.537-2.268,2.897-2.026c1.359,0.241,2.267,1.538,2.026,2.897c-5.757,32.523,12.508,64.662,43.431,76.418 c17.222,6.551,35.964,6.003,52.771-1.544c16.809-7.547,29.672-21.188,36.221-38.411c6.552-17.222,6.004-35.963-1.543-52.771 c-7.546-16.809-21.188-29.672-38.411-36.222C68.706-1.792,35.266,8.613,17.206,34.85c-0.783,1.138-2.338,1.424-3.478,0.642 c-1.137-0.783-1.424-2.34-0.642-3.478C32.458,3.874,68.324-7.283,100.301,4.873c18.472,7.024,33.103,20.821,41.195,38.848 c8.094,18.027,8.682,38.127,1.655,56.597c-7.023,18.472-20.819,33.102-38.846,41.195 C94.624,145.859,84.342,148.041,74.038,148.042z"/> </g> </g> </g> <g> <g> <g> <g> <g> <path fill="#5A5B5D" d="M53.523,111.167c-0.432,0-0.863-0.111-1.25-0.335c-0.773-0.446-1.25-1.271-1.25-2.165V39.376 c0-0.894,0.477-1.719,1.25-2.165c0.773-0.447,1.727-0.447,2.5,0l60.014,34.646c0.773,0.446,1.25,1.271,1.25,2.165 s-0.477,1.719-1.25,2.165l-60.014,34.645C54.387,111.056,53.955,111.167,53.523,111.167z M56.023,43.706v60.631 l52.514-30.314L56.023,43.706z"/> </g> </g> </g> </g> </g> </g> </g> </g> </svg> </div>';
        }



        $aux = array('called_from'=> 'footer_player');

        $args = array_merge($args, $aux);



        echo $this->shortcode_player($args);



        echo '</div>';
        echo '</section>';


      }
    }


    if($this->mainoptions['extra_js']){
      echo '<script>';
      echo stripslashes($this->mainoptions['extra_js']);
      echo '</script>';
    }



    if($this->og_data && count($this->og_data)){

      $image = '';
//            if (get_post_meta($post->ID, 'dzsvp_thumb', true)) {
//                $image = get_post_meta($post->ID, 'dzsvp_thumb', true);
//            }else{
//
//                $image = $this->sanitize_id_to_src( get_post_thumbnail_id($post->ID) );
//            }


      $image=$this->og_data['image'];



      echo '<meta property="og:title" content="' . $this->og_data['title'] . '" />';

      echo '<meta property="og:description" content="' . strip_tags($this->og_data['description']) . '" />';

      if($image){

        echo '<meta property="og:image" content="' . $image . '" />';
      }


    }

    /*
         *
         * <h6 class="social-heading">Social Networks</h6> <a class="social-icon" href="#" onclick="window.dzs_open_social_link(&quot;https://www.facebook.com/sharer.php?u={{replacewithcurrurl}}&amp;title=test&quot;); return false;"><i class="fa fa-facebook-square"></i><span class="the-tooltip">SHARE ON FACEBOOK</span></a> <a class="social-icon" href="#" onclick="window.dzs_open_social_link(&quot;https://twitter.com/share?url={{replacewithcurrurl}}&amp;text=Check this out!&amp;via=ZoomPortal&amp;related=yarrcat&quot;); return false;"><i class="fa fa-twitter"></i><span class="the-tooltip">SHARE ON TWITTER</span></a> <a class="social-icon" href="#" onclick="window.dzs_open_social_link(&quot;https://plus.google.com/share?url={{replacewithcurrurl}}&quot;); return false; "><i class="fa fa-google-plus-square"></i><span class="the-tooltip">SHARE ON GOOGLE PLUS</span></a> <a class="social-icon" href="#" onclick="window.dzs_open_social_link(&quot;https://www.linkedin.com/shareArticle?mini=true&amp;url={{replacewithcurrurl}}&amp;title=Check%20this%20out%20&amp;summary=&amp;source=http://localhost:8888/soundportal/source/index.php?page=page&amp;page_id=20&quot;); return false; "><i class="fa fa-linkedin"></i><span class="the-tooltip">SHARE ON LINKEDIN</span></a> <a class="social-icon" href="#" onclick="window.dzs_open_social_link(&quot;https://pinterest.com/pin/create/button/?url={{replacewithcurrurl}}&amp;text=Check this out!&amp;via=ZoomPortal&amp;related=yarrcat&quot;); return false;"><i class="fa fa-pinterest"></i><span class="the-tooltip">SHARE ON PINTEREST</span></a>
         *
         *
         *
         */


    if($this->sw_enable_multisharer){
      ?><script>
        window.dzsap_social_feed_for_social_networks = '<h6 class="social-heading">Social Networks</h6> <a class="social-icon" href="#" onclick="window.dzs_open_social_link(&quot;https://www.facebook.com/sharer.php?u={{shareurl}}&amp;title=test&quot;); return false;"><i class="fa fa-facebook-square"></i><span class="the-tooltip">SHARE ON FACEBOOK</span></a> <a class="social-icon" href="#" onclick="window.dzs_open_social_link(&quot;https://twitter.com/share?url={{shareurl}}&amp;text=Check this out!&amp;via=ZoomPortal&amp;related=yarrcat&quot;); return false;"><i class="fa fa-twitter"></i><span class="the-tooltip">SHARE ON TWITTER</span></a> <a class="social-icon" href="#" onclick="window.dzs_open_social_link(&quot;https://plus.google.com/share?url={{shareurl}}&quot;); return false; "><i class="fa fa-google-plus-square"></i><span class="the-tooltip">SHARE ON GOOGLE PLUS</span></a> <a class="social-icon" href="#" onclick="window.dzs_open_social_link(&quot;https://www.linkedin.com/shareArticle?mini=true&amp;url={{shareurl}}&amp;title=Check%20this%20out%20&amp;summary=&amp;source=http://localhost:8888/soundportal/source/index.php?page=page&amp;page_id=20&quot;); return false; "><i class="fa fa-linkedin"></i><span class="the-tooltip">SHARE ON LINKEDIN</span></a> <a class="social-icon" href="#" onclick="window.dzs_open_social_link(&quot;https://pinterest.com/pin/create/button/?url={{shareurl}}&amp;text=Check this out!&amp;via=ZoomPortal&amp;related=yarrcat&quot;); return false;"><i class="fa fa-pinterest"></i><span class="the-tooltip">SHARE ON PINTEREST</span></a>';


        window.dzsap_social_feed_for_share_link = '<h6 class="social-heading">Share Link</h6> <div class="field-for-view field-for-view-link-code">{{replacewithcurrurl}}</div>';


        window.dzsap_social_feed_for_embed_link = ' <h6 class="social-heading">Embed Code</h6> <div class="field-for-view field-for-view-embed-code">{{replacewithembedcode}}</div>';
      </script>
      <?php
    }



    if( ( $this->mainoptions['wc_loop_product_player'] && $this->mainoptions['wc_loop_product_player']!='off' )  || ($this->mainoptions['wc_single_product_player'] && $this->mainoptions['wc_single_product_player']!='off')) {


//            echo ' $this->mainoptions[\'wc_loop_player_position\'] -  '.$this->mainoptions['wc_loop_player_position'];
      if ($this->mainoptions['wc_loop_player_position'] == 'overlay') {


        ?><script>
          jQuery(document).ready(function($){

            var _body = $('body').eq(0);

            if(_body.hasClass('single-product')){
              var _c = $('.woocommerce-product-gallery__wrapper').eq(0);
              _c.append($('.go-to-thumboverlay').eq(0));
              var _c2 = $('.go-to-thumboverlay').eq(0);
              _c.css({

                'position': 'relative'
                ,'display': 'block'
              })
              _c2.css({
                'position': 'absolute'
                ,'width':'100%'
                ,'height':'100%'
                ,'top':'0'
                ,'left':'0'
              })
              _c.append($('.go-after-thumboverlay').eq(0));
              var _c2 = $('.go-after-thumboverlay').eq(0);
              _c2.css({
//            'position': 'absolute'
//            ,'width':'100%'
//            ,'height':'100%'
//            ,'top':'0'
//            ,'left':'0'
              })
            }else{


              $('.go-to-thumboverlay').each(function(){
                var _t = $(this);


                console.log('_t - ',_t, _t.siblings('.wp-post-image'));

                if(_t.siblings('.wp-post-image').length){
                  _t.parent().css({

                    'position': 'relative'
                    ,'display': 'block'
                  })
                  _t.css({
                    'position': 'absolute'
                    ,'width':'100%'
                    ,'height':_t.siblings('.wp-post-image').eq(0).height()
                    ,'top':'0'
                    ,'left':'0'
                  })
                }
              })
            }

          })

        </script><?php
      }
    }





    if(isset($this->mainoptions['replace_powerpress_plugin']) && $this->mainoptions['replace_powerpress_plugin']=='on') {


      ?>
      <style>
        .powerpress_player {
          display: none;
        }
      </style><?php


      global $post;

      global $powerpress_feed;
      //            print_rr($powerpress_feed);

      // PowerPress settings:
      $GeneralSettings = get_option('powerpress_general');
      //                print_rr($GeneralSettings);


      $feed_slug = 'podcast';


      if ($powerpress_feed) {


        $EpisodeData = powerpress_get_enclosure_data($post->ID, $feed_slug);

        //            print_rr($EpisodeData);


        if ($EpisodeData && isset($EpisodeData['url'])) {


          //            echo 'whaaa';
          $this->sliders__player_index++;

          //                $fout = '';


          $src = get_post_meta($post->ID, 'dzsap_woo_product_track', true);


          $this->front_scripts();

          $margs = $this->powerpress_generate_margs();


          //        $enc_margs = simple_encrypt(json_encode($margs),'1111222233334444');
          //        $enc_margs = gzcompress(json_encode($embed_margs),9);
          $enc_margs = json_encode($margs);
          $enc_margs = base64_encode(json_encode($margs));
          //        $enc_margs = base64_decode(base64_encode(json_encode($embed_margs)));

          //        $embed_code = '<iframe src=\'' . $this->base_url . 'bridge.php?type=player&margs='.urlencode($enc_margs).'\' style="overflow:hidden; transition: height 0.3s ease-out;" width="100%" height="152" scrolling="no" frameborder="0"></iframe>';


          $embed_url = site_url() . '?action=embed_zoomsounds&type=player&margs=' . urlencode($enc_margs);
          $embed_code = '<iframe src=\'' . $embed_url . '\' style="overflow:hidden; transition: height 0.3s ease-out;" width="100%" height="152" scrolling="no" frameborder="0"></iframe>';


          ?>
          <meta name="twitter:card" content="player">
          <meta name="twitter:site" content="@youtube">
          <meta name="twitter:url" content="<?php echo get_permalink($post->ID); ?>">
          <meta name="twitter:title" content="<?php echo get_permalink($post->post_title); ?>">
          <meta name="twitter:description" content="<?php echo get_permalink($post->post_content); ?>">
          <meta name="twitter:image" content="">
          <meta name="twitter:app:name:iphone" content="<?php echo get_permalink($post->ID); ?>">
          <meta name="twitter:app:name:googleplay" content="<?php echo get_permalink($post->post_title); ?>">
          <meta name="twitter:player" content="<?php echo $embed_url; ?>">
          <meta name="twitter:player:width" content="1280">
          <meta name="twitter:player:height" content="300"><?php


        }


      }

    }






    if(isset($_GET['action'])){
      if($_GET['action']=='embed_zoomsounds'){


        echo '<div class="zoomsounds-embed-con">';

        $args = array();
        if(isset($_GET['type']) && $_GET['type']=='gallery'){

          $args = array(
            'id' => $_GET['id'],
            'embedded' => 'on',
          );


          if(isset($_GET['db'])){
            $args['db'] = $_GET['db'];
          };
          echo $this->show_shortcode($args);

        }
        if(isset($_GET['type']) && $_GET['type']=='playlist'){

          $args = array(
            'ids' => $_GET['ids'],
            'embedded' => 'on',
          );


          if(isset($_GET['db'])){
            $args['db'] = $_GET['db'];
          };
          echo $this->shortcode_playlist($args);

        }




        if(isset($_GET['type']) && $_GET['type']=='player'){


//    echo $_GET['margs'];
          $args = array();
          try{
//        echo '.'.stripslashes($_GET['margs']).'.';
            $args = @unserialize((stripslashes($_GET['margs'])));
          }catch(Exception $e){

//        $args = array();
          }




//    print_r($args);

          if(is_array($args)){

          }else{
            $args = array();



//        echo 'try json decode -> ';
//        echo stripslashes(stripslashes($_GET['margs']));
//        echo ' <- ';
//
//        echo '
//        try json decode -> ';
//        echo (stripslashes($_GET['margs']));
//        echo ' <- ';


            $args = json_decode((stripslashes(base64_decode($_GET['margs']))),true);

//        print_rr($args);

            if(is_object($args) || is_array($args)){

            }else{
              $args = array();



            }

          }
//    print_r($args);
          $args['embedded']='on';
          $args['extra_classes']=' test';


          echo $this->shortcode_player($args);

        }
        echo '</div>';
      }
    }
  }

  function my_formatter($content) {
    $new_content = '';
    $pattern_full = '{(\[raw\].*?\[/raw\])}is';
    $pattern_contents = '{\[raw\](.*?)\[/raw\]}is';
    $pieces = preg_split($pattern_full, $content, -1, PREG_SPLIT_DELIM_CAPTURE);

    foreach ($pieces as $piece) {
      if (preg_match($pattern_contents, $piece, $matches)) {
        $new_content .= $matches[1];
      } else {
        $new_content .= wptexturize(wpautop($piece));
      }
    }
    return $new_content;
  }


  //include the tinymce javascript plugin
  function tinymce_external_plugins($plugin_array) {
    $plugin_array['ve_zoomsounds_player'] = $this->base_url.'/tinymce/visualeditor/editor_plugin.js';
    $plugin_array['noneditable'] = $this->base_url.'/tinymce/noneditable/plugin.min.js';
    return $plugin_array;
  }

  //include the css file to style the graphic that replaces the shortcode
  function myformatTinyMCE($options){

    $ext = 'iframe[align|longdesc|name|width|height|frameborder|scrolling|marginheight|marginwidth|src|id|class|title|style],video[source],source[*]';

//    if ( isset( $options['extended_valid_elements'] ) )
//        $options['extended_valid_elements'] .= ',' . $ext;
//    else
//        $options['extended_valid_elements'] = $ext;
//
//
//        $options['media_strict'] = 'false';
//        $options['noneditable_leave_contenteditable'] = 'true';
//


//        $options['content_css'] .= ",".$this->base_url.'/tinymce/visualeditor/editor-style.css';


    if($this->mainoptions['replace_playlist_shortcode'] == 'on'){


      $options['content_css'] .= ",".$this->base_url.'audioplayer/audioplayer.css';
    }
    if($this->mainoptions['replace_audio_shortcode'] && $this->mainoptions['replace_audio_shortcode']!=='off'){


      $options['content_css'] .= ",".$this->base_url.'audioplayer/audioplayer.css';
    }

//    print_r($options);
    return $options;
  }

  public function generate_item_structure($pargs = null) {
    $margs = array(
      'generator_type' => 'normal',
      'type' => '',
      'source' => '',
      'sourceogg' => '',
      'waveformbg' => '',
      'waveformprog' => '',
      'thumb' => '',
      'linktomediafile' => '',
      'playfrom' => '',
      'bgimage' => '',
      'extra_html' => '',
      'extra_html_left' => '',
      'extra_html_in_controls_left' => '',
      'extra_html_in_controls_right' => '',
      'menu_artistname' => '',
      'menu_songname' => '',
      'menu_extrahtml' => '',
    );

    if (is_array($pargs) == false) {
      $pargs = array();
    }

    $margs = array_merge($margs, $pargs);


    $lab = 'type';
    $val = $margs[$lab];




    $uploadbtnstring = '<button class="button-secondary action upload_file ">Upload</button>';



    if ($this->mainoptions['usewordpressuploader'] != 'on') {
      $uploadbtnstring = '<div class="dzs-upload">
<form name="upload" action="#" method="POST" enctype="multipart/form-data">
    	<input type="button" value="Upload" class="btn_upl"/>
        <input type="file" name="file_field" class="file_field"/>
        <input type="submit" class="btn_submit"/>
</form>
</div>
<div class="feedback"></div>';
    }



    $aux = '';
    if ($margs['generator_type'] != 'onlyitems') {
      $aux = '<div class="item-con">
            <div class="item-delete">x</div>
            <div class="item-duplicate"></div>
        <div class="item-preview" style="">
        </div>
        <div class="item-settings-con">';
    }

    $aux.='<div class="setting type_all">
            <h4 class="non-underline"><span class="underline">' . __('Type', 'dzsap') . '*</span>&nbsp;&nbsp;&nbsp;<span class="sidenote">select one from below</span></h4>

            <div class="main-feed-chooser select-hidden-metastyle select-hidden-foritemtype">
' . DZSHelpers::generate_select('0-0-' . $lab, array('options' => array('mediafile', 'soundcloud', 'shoutcast', 'youtube', 'audio', 'inline'), 'seekval' => $val, 'class' => 'textinput item-type', 'extraattr' => ' data-label="' . $lab . '"')) . '
                <div class="option-con clearfix">

                    <div class="an-option">
                    <div class="an-title">
                    ' . __('Media File', 'dzsap') . '
                    </div>
                    <div class="an-desc">
                    ' . __('Link to a media file from your WordPress Media Library.', 'dzsap') . '
                    </div>
                    </div>

                    <div class="an-option">
                    <div class="an-title">
                    ' . __('SoundCloud Sound', 'dzsap') . '
                    </div>
                    <div class="an-desc">
                    ' . __('Stream SoundCloud sounds. Input the full link to the sound in the Source field. '
        . 'You will have to input your SoundCloud API Key into ZoomSounds > Settings.', 'dzsap') . ' <a href="' . $this->base_url . 'readme/index.html#handbrake" target="_blank" class="">Documentation here</a>.
                    </div>
                    </div>

                    <div class="an-option">
                    <div class="an-title">
                    ' . __('ShoutCast Radio', 'dzsap') . '
                    </div>
                    <div class="an-desc">
                    ' . __('Insert a shoutcast radio address. It will have to stream in mpeg format. Input the address, example:  ', 'dzsap') . ' - http://vimeo.com/<strong>55698309</strong>
                    </div>
                    </div>

                    <div class="an-option">
                    <div class="an-title">
                    ' . __('YouTube', 'dzsap') . '
                    </div>
                    <div class="an-desc">
                    ' . __('Input the YouTube video id. Warning - will not work on iOS.', 'dzsap') . '
                    </div>
                    </div>
                    
                    
                    
                    <div class="an-option">
                    <div class="an-title">
                    
                    ' . __('Self-Hosted Audio', 'dzsap') . '
                    </div>
                    <div class="an-desc">
                    ' . __('Only mp3 is mandatory. Browsers that cannot decode mp3 will use the included Flash Player backup '
        . '. If you want full html5 player, you must set a ogg sound too.', 'dzsap') . '
                    </div>
                    </div>
                    
                    

                    <div class="an-option">
                    <div class="an-title">
                    ' . __('Inline Content', 'dzsap') . '
                    </div>
                    <div class="an-desc">
                    ' . __('Insert in the <strong>Source</strong> field custom content ( ie. embed from a custom site ).', 'dzsap') . '
                    </div>
                    </div>
                </div>
            </div>
        </div>';




    $lab = 'source';
    $val = $margs[$lab];


    $aux.='<div class="setting type_all type_mediafile_hide">
            <div class="setting-label">' . __('Source', 'dzsap') . '*
                <div class="info-con">
                <div class="info-icon"></div>
                <div class="sidenote">' . __('Below you will enter your audio file address. If it is a video from YouTube or Vimeo you just need to enter
                the id of the video in the . The ID is the bolded part http://www.youtube.com/watch?v=<strong>j_w4Bi0sq_w</strong>.
                If it is a local video you just need to write its location there or upload it through the Upload button ( .mp3 format ).', 'dzsap') . '
                    </div>
                </div>
            </div>
' . DZSHelpers::generate_input_textarea('0-0-' . $lab, array('class' => 'textinput main-source type_all upload-type-audio', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '" style="width:160px; height:23px;"')) . $uploadbtnstring . '
        </div>';



    $lab = 'soundcloud_track_id';
    $val = '';

    if(isset($margs[$lab])){
      $val = $margs[$lab];
    }


    $aux.='<div class="setting type_soundcloud">
            <div class="setting-label">' . __('Track ID', 'dzsap') . '
            </div>
' . DZSHelpers::generate_input_text('0-0-' . $lab, array('class' => 'textinput ', 'seekval' => $val, 'extraattr' => '')).'
                <div class="sidenote">' . __('Only for Private Soundcloud files. Guide on how to get the track_id - ', 'dzsap') .'<a href="http://digitalzoomstudio.net/docs/wpzoomsounds/#faq_secret_token">'.__("here").'</a>' . '
        </div>
        </div>';



    $lab = 'soundcloud_secret_token';
    $val = '';

    if(isset($margs[$lab])){
      $val = $margs[$lab];
    }


    $aux.='<div class="setting type_soundcloud">
            <div class="setting-label">' . __('Secret Token', 'dzsap') . '
            </div>
' . DZSHelpers::generate_input_text('0-0-' . $lab, array('class' => 'textinput ', 'seekval' => $val, 'extraattr' => '')).'
                <div class="sidenote">' . __('Only for Private Soundcloud files. Guide on how to get the track_id - ', 'dzsap') .'<a href="http://digitalzoomstudio.net/docs/wpzoomsounds/#faq_secret_token">'.__("here").'</a>' . '
                    </div>
        </div>';


    $lab = 'sourceogg';
    $val = $margs[$lab];

    $aux.='<div class="setting type_all type_mediafile_hide">
            <div class="setting-label">HTML5 OGG ' . __('Format', 'dzsap') . '</div>
            <div class="sidenote">' . __('Optional ogg / ogv file', 'dzsap') . ' / ' . __('Only for the Video or Audio type', 'dzsap') . '</div>
' . DZSHelpers::generate_input_text('0-0-' . $lab, array('class' => 'textinput upload-prev', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '"')) . $uploadbtnstring . '
        </div>';



    if($this->mainoptions['skinwave_wave_mode']!='canvas') {
      $lab = 'waveformbg';
      $val = $margs[$lab];

      $aux .= '<div class="setting type_all type_mediafile_hide">
            <div class="setting-label">' . __('WaveForm Background Image', 'dzsap') . '</div>
            <div class="sidenote">' . __('Optional waveform image / ', 'dzsap') . ' / ' . __('Only for skin-wave', 'dzsap') . '</div>
' . DZSHelpers::generate_input_text('0-0-' . $lab, array('class' => 'textinput upload-prev', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '"')) . $uploadbtnstring . ' <span class="aux-wave-generator"><button class="btn-autogenerate-waveform-bg button-secondary">' . __("Auto Generate") . '</button></span>
        </div>';


      //simple with upload and wave generator
      $lab = 'waveformprog';
      $val = $margs[$lab];

      $aux .= '<div class="setting type_all type_mediafile_hide">
            <div class="setting-label">' . __('WaveForm Progress Image', 'dzsap') . '</div>
            <div class="sidenote">' . __('Optional waveform image / ', 'dzsap') . ' / ' . __('Only for skin-wave', 'dzsap') . '</div>
' . DZSHelpers::generate_input_text('0-0-' . $lab, array('class' => 'textinput upload-prev', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '"')) . $uploadbtnstring . ' <span class="aux-wave-generator"><button class="btn-autogenerate-waveform-prog button-secondary">Auto Generate</button></span>
        </div>';
    }



    $lab = 'linktomediafile';
    $val = $margs[$lab];

    $aux.='<div class="setting type_all">
            <div class="setting-label">' . __('Link To Media File', 'dzsap') . '</div>
            <div class="sidenote">' . __('you can link to a media file in order to have comment / rates - just input the id of the media here or ', 'dzsap') . '</div>
' . DZSHelpers::generate_input_text('0-0-' . $lab, array('class' => 'textinput type_all upload-type-audio upload-prop-id main-media-file', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '"')) . $this->misc_generate_upload_btn(array('label' => 'Link')) . '
</div>';


    //textarea special thumb
    $lab = 'thumb';
    $val = $margs[$lab];


    $aux.='
        <div class="setting type_all ">
            <div class="setting-label">' . __('Thumbnail', 'dzsap') . '</div>
            <div class="sidenote">' . __('a thumbnail ', 'dzsap') . '</div>
' . DZSHelpers::generate_input_textarea('0-0-' . $lab, array('class' => 'textinput main-thumb type_all upload-type-image', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '" style="width:160px; height:23px;"')) . $uploadbtnstring . '
</div>';





    //simple with upload and wave generator
    $lab = 'playfrom';
    $val = $margs[$lab];

    $aux.='<div class="setting type_all">
            <div class="setting-label">' . __('Play From', 'dzsap') . '</div>
            <div class="sidenote">' . __('choose a number of seconds from which the track to play from ( for example if set "70" then the track will start to play from 1 minute and 10 seconds ) or input "last" for the track to play at the last position where it was.', 'dzsap') . '</div>
' . DZSHelpers::generate_input_text('0-0-' . $lab, array('class' => 'textinput upload-prev', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '"')) . '
        </div>';



    //simple with upload and wave generator
    $lab = 'bgimage';
    $val = $margs[$lab];

    $aux.='<div class="setting type_all">
            <div class="setting-label">' . __('Background Image', 'dzsap') . '</div>
            <div class="sidenote">' . __('optional - choose a background image to appear ( needs a wrapper / read docs )', 'dzsap') . '</div>
' . DZSHelpers::generate_input_text('0-0-' . $lab, array('class' => 'textinput upload-prev', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '"'))  . $this->misc_generate_upload_btn(array('label' => __('Upload', 'dzsap'))) .'
        </div>';


    $lab = 'play_in_footer_player';
    $val = '';

    $aux.='<div class="setting type_all">
            <div class="setting-label">' . __('Play in footer player', 'dzsap') . '</div>
            <div class="sidenote">' . __('optional - play this track in the footer player ( footer player must be setuped on the page ) ', 'dzsap') . '</div>
' . DZSHelpers::generate_select('0-0-' . $lab, array('class' => 'textinput  styleme', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '"', 'options' => array('off','on') )) .'
        </div>';


    $lab = 'enable_download_button';
    $val = '';

    $aux.='<div class="setting type_all">
            <div class="setting-label">' . __('Enable Download Button', 'dzsap') . '</div>
            <div class="sidenote">' . __('optional - Enable Download Button for this track', 'dzsap') . '</div>
' . DZSHelpers::generate_select('0-0-' . $lab, array('class' => 'textinput  styleme', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '"', 'options' => array('off','on') )) .'
        </div>';


    $lab = 'download_custom_link';
    $val = '';

    if(isset($margs[$lab])){

      $val = $margs[$lab];
    }

    $aux.='<div class="setting type_all">
            <div class="setting-label">' . __('Custom Link Download', 'dzsap') . '</div>
            <div class="sidenote">' . __('a custom link for the download button - clicknig it will go to this link if set, if it is not set then it will just download the track', 'dzsap') . '</div>
' . DZSHelpers::generate_input_text('0-0-' . $lab, array('class' => 'textinput upload-prev', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '"')) . '
        </div>';




    $aux.='<br>';
    $aux.='<div class="dzstoggle toggle1" rel="">
        <div class="toggle-title" style="">' . __('Extra HTML Options', 'dzsap') . '</div>
        <div class="toggle-content" style="z-index:5;">';

    $aux.='<img src="https://lh3.googleusercontent.com/JY9Q72y_Wkx4Au0Ijxjf2GCZUblfYbpyjooMaSt90XG9zOjd7vlddxLJTTX7C2UEV5TqBKBsSaFw3Pr8Psafl8XvzWMOzFaxJfndci9idgqFHSnEw9rd5K92tQyAiVqxPO30qznMwqIjIHQTm2hijSLM2S9OqVinEP_TGoKhtmgrCro7NmsNn0-T4N_Mmn3htOFy4o4mMZciif-zVcQ6T0HTB4n2xzI49Sn_s08ekF8DFwcE58n8Dp5LGfQpUeI8nfK8LSv4mKC1TKiewKkOm-YwGy3bhC8BFRsUXBDHd-YtX0y7HV7SfIg9hvA4QRJHBUQPod5YrDIODH7YLQi7HVIceBwyaYPvTAZEZh5oifrCCj61sSZztfjra-WbcxoRoUVrZSssvxLR1lJgH8WpnxdV-1qmDAr-0p7LKhdJM2_4P79SIOIKuYOWaDyx7GQ8CAjco--fhiwbYCxqgCXyGtRjpGYJV6IEKh7UhwEsNnkUAxWB-YoQrtFgoB3Rw4uFRdQCs--YHTeydLCEaAEL5CNwd6j0hh1UDunj1Xj7bmc=w736-h291-no"/>';

    //textarea simple
    $lab = 'extra_html';
    $val = $margs[$lab];


    $aux.='
       <div class="setting type_all">
                <div class="setting-label">' . __('Extra HTML', 'dzsap') . '</div>
' . DZSHelpers::generate_input_textarea('0-0-' . $lab, array('class' => 'textinput', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '" style="width:160px; height:23px;"')) . '
                <div class="sidenote">' . __('(1) extra html you may want underneath item', 'dzsap') . '</div>
</div>';




    $lab = 'extra_html_left';
    $val = $margs[$lab];


    $aux.='
       <div class="setting type_all">
                <div class="setting-label">' . __('Extra HTML to the Left', 'dzsap') . '</div>
' . DZSHelpers::generate_input_textarea('0-0-' . $lab, array('class' => 'textinput', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '" style="width:160px; height:23px;"')) . '
                <div class="sidenote">' . __('(2) extra html placed in the left of Like button', 'dzsap') . '</div>
</div>';




    $lab = 'extra_html_in_controls_left';
    $val = $margs[$lab];


    $aux.='
       <div class="setting type_all">
                <div class="setting-label">' . __('Extra HTML in Left Controls', 'dzsap') . '</div>
' . DZSHelpers::generate_input_textarea('0-0-' . $lab, array('class' => 'textinput', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '" style="width:160px; height:23px;"')) . '
                <div class="sidenote">' . __('(3) extra html placed in the player&quot;s ', 'dzsap') . '</div>
</div>';


    $lab = 'extra_html_in_controls_right';
    $val = $margs[$lab];


    $aux.='
       <div class="setting type_all">
                <div class="setting-label">' . __('Extra HTML in Right Controls', 'dzsap') . '</div>
' . DZSHelpers::generate_input_textarea('0-0-' . $lab, array('class' => 'textinput', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '" style="width:160px; height:23px;"')) . '
                <div class="sidenote">' . __('(3) extra html placed in the player&quot;s ', 'dzsap') . '</div>
</div>';


    $aux.='</div>
        </div>';



    $aux.='<div class="dzstoggle toggle1" rel="">
        <div class="toggle-title" style="">' . __('Menu Options', 'dzsap') . '</div>
        <div class="toggle-content">';


    //textarea simple
    $lab = 'menu_artistname';
    $val = $margs[$lab];


    $aux.='
       <div class="setting type_all">
                <div class="setting-label">' . __('Artist Name', 'dzsap') . '</div>
                <div class="sidenote">' . __('an artist name if you include this item in a playlist', 'dzsap') . '</div>
' . DZSHelpers::generate_input_textarea('0-0-' . $lab, array('class' => 'textinput', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '" style="width:160px; height:23px;"')) . '
</div>';


    //textarea simple
    $lab = 'menu_songname';
    $val = $margs[$lab];


    $aux.='
       <div class="setting type_all">
                <div class="setting-label">' . __('Song Name', 'dzsap') . '</div>
                <div class="sidenote">' . __('a song name', 'dzsap') . '</div>
' . DZSHelpers::generate_input_textarea('0-0-' . $lab, array('class' => 'textinput', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '" style="width:160px; height:23px;"')) . '
</div>';
    //textarea simple
    $lab = 'menu_extrahtml';
    $val = $margs[$lab];


    $aux.='
       <div class="setting type_all">
                <div class="setting-label">' . __('Extra HTML', 'dzsap') . '</div>
                <div class="sidenote">' . __('extra html you may want in the menu item', 'dzsap') . '</div>
' . DZSHelpers::generate_input_textarea('0-0-' . $lab, array('class' => 'textinput', 'seekval' => $val, 'extraattr' => ' data-label="' . $lab . '" style="width:160px; height:23px;"')) . '
</div>';







    $aux.='</div>
        </div>';




    if ($margs['generator_type'] != 'onlyitems') {
      $aux.='</div><!--end item-settings-con-->
</div>';
    }





    return $aux;
  }

  function handle_admin_footer() {

  }

  function wp_dashboard_setup() {

    wp_add_dashboard_widget(
      'dzsap_dashboard_widget_comments', // Widget slug.
      'ZoomSounds Comments Statistic', // Title.
      array($this, 'dashboard_comments_display') // Display function.
    );
  }

  public static function sort_commnr($a, $b) {
    $key = 'commnr';
    return $b[$key] - $a[$key];
  }

  function dashboard_comments_display() {

//	echo "Hello World, I'm a great Dashboard Widget";

    $type = 'attachement';
    $args = array(
      'post_type' => 'attachment',
      'numberposts' => null,
      'posts_per_page' => '-1',
      'post_mime_type' => 'audio',
      'post_status' => null
    );
    $attachments = get_posts($args);

    $arr_attcomms = array();
    foreach ($attachments as $att) {
      $comments_count = wp_count_comments($att->ID);
      $aux = array('id' => $att->ID, 'commnr' => ($comments_count->approved));
      array_push($arr_attcomms, $aux);
    }
    //print_r($arr_attcomms);



    usort($arr_attcomms, array('DZSAudioPlayer', 'sort_commnr'));

//        print_r($arr_attcomms);


    echo '<div id="chart_div"></div>';
    //print_r($arr_attcomms);



    echo '<script type="text/javascript">
      google.load("visualization", "1.0", {"packages":["corechart"]});

      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
        console.info("drawChart");
      function drawChart() {
        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn("string", "Topping");
        data.addColumn("number", "Slices");
        data.addRows([';
    $i = 0;
    foreach ($arr_attcomms as $att) {
      echo '';
//            ['Mushrooms', 3],
      $auxpo = get_post($att['id']);
//            print_r($aux);

      if ($i > 0) {
        echo ',';
      }
      echo '["' . $auxpo->post_title . '", ' . $att['commnr'] . ']';
      $i++;
      //echo 'Track <strong>'.$att['id'].'</strong>, '.$auxpo->post_title.' - '.$att['commnr'].' comments<br/>';
    };

    echo ']);

// Set chart options
var options = {"title":"' . __('Number of Comments', 'dzsap') . '",
               "width":"100%",
               "height":300};

// Instantiate and draw our chart, passing in some options.
var chart = new google.visualization.PieChart(document.getElementById("chart_div"));
chart.draw(data, options);
}
</script>';
  }

  function handle_wp_head() {
    echo '<script>';
    echo 'window.dzsap_swfpath="' . $this->base_url . 'apfull.swf";';
    echo 'window.ajaxurl="' . admin_url('admin-ajax.php') . '";';
    echo '</script>';

    if ($this->mainoptions['extra_css']) {
      echo '<style class="dzsap-extrastyling">';
      echo $this->mainoptions['extra_css'];
      echo '</style>';
    }





    if(isset($_GET['action'])) {
      if ($_GET['action'] == 'embed_zoomsounds') {

        ?>
        <style>
          body > * {
            display: none !important;
          }

          body .zoomsounds-embed-con {
            display: block !important;
            position: fixed;
            top:0;
            left:0;
            width: 100%;
          }
        </style><?php

      }
    }


    if (isset($_GET['dzsap_generate_pcm']) && $_GET['dzsap_generate_pcm']) {
      ?>
      <style>
        html{
          margin-top:0!important;
        }
        body > *{
          opacity: 0;
          display: none;
        }
        body > #ap_regenerate{
          opacity: 1;
          display: block;
        }
      </style>
      <script>
        jQuery(document).ready(function($){
          var aux = '';

          $('body').addClass('dzsap-ready');

          $('body').prepend('<div id="ap_regenerate" data-type="audio" class="audioplayer-tobe skin-wave " data-source="<?php echo $_GET['dzsap_source']; ?>" data-playerid="<?php echo $_GET['dzsap_generate_pcm']; ?>" data-playfrom="0"> </div>');

          // -- waveform regeneration

          setTimeout(function(){
            dzsap_init(".audioplayer-tobe", {
              autoplay: "off"
              ,skinwave_mode: 'normal'
              ,settings_php_handler: window.ajaxurl // -- the path of the publisher.php file, this is used to handle comments, likes etc.
              ,skinwave_wave_mode: 'canvas' // --- "normal" or "canvas"
              ,skinwave_wave_mode_canvas_waves_number: '3' // --- the number of waves in the canvas
              ,skinwave_wave_mode_canvas_waves_padding: '1' // --- padding between waves
              ,skinwave_wave_mode_canvas_reflection_size: '0.25' // --- the reflection size
              ,pcm_data_try_to_generate: 'on' // --- try to find out the pcm data and sent it via ajax ( maybe send it via php_handler
              ,skinwave_comments_enable: 'off' // -- enable the comments, publisher.php must be in the same folder as this html, also if you want the comments to automatically be taken from the database remember to set skinwave_comments_retrievefromajax to ON
              ,failsafe_repair_media_element: 500 // == light or full
              ,settings_extrahtml_in_float_right: '<div class="orange-button dzstooltip-con" style="top:10px;"><span class="dzstooltip arrow-from-start transition-slidein arrow-bottom skin-black align-right" style="width: auto; white-space: nowrap;">Add to Cart</span><i class="fa fa-shopping-cart"></i></div><div class="orange-button dzstooltip-con" style="top:10px;"><span class="dzstooltip arrow-from-start transition-slidein arrow-bottom skin-black align-right" style="width: auto; white-space: nowrap;">Download</span><i class="fa fa-download"></i></div>'
            })
          })

//        $('body').children().css('display','none');
        });
        //    jQuery('body').children().css('display','none');
        console.info('hmmdada');
      </script>


      <?php
      wp_enqueue_script('dzsap', $this->base_url . "audioplayer/audioplayer.js");
      wp_enqueue_style('dzsap', $this->base_url . 'audioplayer/audioplayer.css');
    }
  }

  function ajax_get_thumb_from_meta() {

    //print_r($_POST);


//        echo 'hmm';

    $pid = $_POST['postdata'];



//        print_r($file);

//        print_r($metadata);


    if(get_post_meta($pid, '_dzsap-thumb',true)){

      echo get_post_meta($pid, '_dzsap-thumb',true);
    }else{





      $upload_dir = wp_upload_dir();
      $upload_dir_url = $upload_dir['url'].'/';
      $upload_dir_path = $upload_dir['path'].'/';


//            print_r($upload_dir);





      $file = get_attached_file($pid);
      $metadata = wp_read_audio_metadata( $file );
//            echo 'image data - ';
      if(isset($metadata['image']) && $metadata['image']['data']){
//                echo base64_encode($metadata['image']['data']);


        file_put_contents($upload_dir_path.'audio_image_'.$pid.'.jpg', $metadata['image']['data']);


        echo $upload_dir_url.'audio_image_'.$pid.'.jpg';

      }
    }



//        $meta = wp_get_attachment_metadata($_POST['postdata']);

//        print_r($meta);


    die();
  }

  function ajax_front_submitcomment() {

    //print_r($_POST);

    $time = current_time('mysql');

    $playerid = $_POST['playerid'];
    $playerid = str_replace('ap', '', $playerid);

    $email = '';
    $comm_author = $_POST['skinwave_comments_account'];


    $user_id = get_current_user_id();
    $user_data = get_userdata($user_id);

//        print_r($user_data);

    if(isset($user_data->data)){

      if(isset($user_data->data->ID)){
        $email = $user_data->data->user_email;
        $comm_author = $user_data->data->user_login;
      }
    }


    $data = array(
      'comment_post_ID' => $playerid,
      'comment_author' => $comm_author,
      'comment_author_email' => $email,
      'comment_author_url' => $_POST['comm_position'],
      'comment_content' => $_POST['postdata'],
      'comment_type' => '',
      'comment_parent' => 0,
      'user_id' => 1,
      'comment_author_IP' => '127.0.0.1',
      'comment_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
      'comment_date' => $time,
      'comment_approved' => 1,
    );

    wp_insert_comment($data);


    setcookie("commentsubmitted-" . $playerid, '1', time() + 36000, COOKIEPATH);

    print_r($data);

    echo 'success';
    die();
  }
  function ajax_delete_pcm() {

    //print_r($_POST);


    $playerid = $_POST['playerid'];



    $lab = 'dzsap_pcm_data_'.($this->clean($_POST['playerid']));
    delete_option($lab);
    echo 'success - '.$lab;
    die();
  }
  function ajax_send_queue_from_sliders_admin() {

//        print_r($_POST);

    $response = array(
      'report'=>'success',
      'items'=>array(),
    );

    $queue_calls = json_decode(stripslashes($_POST['postdata']), true);

    print_r($queue_calls);

    foreach ($queue_calls as $qc){

      if($qc['type']=='set_meta_order'){
        foreach($qc['items'] as $it){

          update_post_meta($it['id'], 'dzsap_meta_order_'.$qc['term_id'],$it['order']);
        }
      }
      if($qc['type']=='set_meta'){

        update_post_meta($qc['item_id'], $qc['lab'], $qc['val']);
      }
      if($qc['type']=='create_item'){

//                print_r($qc);








        $args = array(
          'post_title' => __("Insert Name"),
          'post_content' => 'test',
          'post_status' => 'publish',
          'post_author' => 1,
          'post_type' => 'dzsap_items',
        );

        $sample_post_2_id = wp_insert_post($args);
//        wp_set_post_terms($sample_post_2_id,$arr_cats[0],$taxonomy);

        array_push($response['items'],array(
          'type'=>'create_item',
          'str'=>$this->sliders_admin_generate_item(get_post($sample_post_2_id)),
        ));
      }
    }

    echo json_encode($response);
    die();
  }


  function sliders_admin_generate_item($po){


    $fout = '';
    $thumb = '';
    $thumb_from_meta = '';

    if($po && is_int($po->ID)){

      $thumb = $this->get_post_thumb_src($po->ID);

//            echo ' thumb - ';
//            print_r($thumb);


      $thumb_from_meta = get_post_meta($po->ID, 'dzsap_meta_item_thumb',true);
    }

    if($thumb_from_meta){

      $thumb = $thumb_from_meta;
    }

    $thumb_url = '';
    if($thumb){
      $thumb_url = $this->sanitize_id_to_src($thumb);

//                    echo ' thumb - '.$this->sanitize_id_to_src($thumb);
    }



    $fout.= '<div class="slider-item dzstooltip-con for-click';

    if($po->ID=='placeholder'){
      $fout.= ' slider-item--placeholder';
    }

    $fout.= '" data-id="'.$po->ID.'">';

    $fout.= '<div class="divimage" style="background-image:url('.$thumb_url.');"></div>';
    $fout.= '<div class="slider-item--title" >'.$po->post_title.'</div>';

    $struct_uploader = '<div class="dzs-wordpress-uploader insert-id">
    <a href="#" class="button-secondary">' . __('Upload', 'dzsvp') . '</a>
</div>';
    $fout.='
        <div class="delete-btn"><i class="fa fa-times-circle-o"></i></div>
        <div class="dzstooltip skin-black transition-fade arrow-top align-center">
            <div class="dzstooltip--selector-top"></div>

            <div class="dzstooltip--content">';

    foreach ($this->options_item_meta as $lab => $oim){





      $fout.='
                    <div class="setting ';
      $option_name = $oim['name'];

      if($oim['type']=='attach'){
        $fout.=' setting-upload';
      }

      $fout.='">';
      $fout.='<h5 class="setting-label">'.$oim['title'].'</h5>';


      if($oim['type']=='attach'){
        $fout.='<span class="uploader-preview"></span>';
      }


      $val = '';

      if(is_int($po->ID)){

        $val = get_post_meta($po->ID, $option_name, true);
      }

      $class = 'setting-field medium';

      if($oim['type']=='attach'){
        $class.=' uploader-target';
      }


      if($oim['type']=='attach') {
        $fout.= DZSHelpers::generate_input_text($option_name, array(
          'class' => $class,
          'seekval' => $val,
        ));
      }
      if($oim['type']=='text') {
        $fout.= DZSHelpers::generate_input_text($option_name, array(
          'class' => $class,
          'seekval' => $val,
        ));
      }
      if($oim['type']=='select') {


        $class = 'dzs-style-me skin-beige setting-field';

        if(isset($oim['select_type']) && $oim['select_type']){
          $class.=' '.$oim['select_type'];
        }

        $fout.= DZSHelpers::generate_select($option_name, array(
          'class' => $class,
          'seekval' => $val,
          'options' => $oim['choices'],
        ));

        if(isset($oim['select_type']) && $oim['select_type']=='opener-listbuttons'){

          $fout.= '<ul class="dzs-style-me-feeder">';

          foreach ($oim['choices_html'] as $oim_html){

            $fout.= '<li>';
            $fout.= $oim_html;
            $fout.= '</li>';
          }

          $fout.= '</ul>';
        }


      }

      if($oim['type']=='attach') {
        $fout.= $struct_uploader;
      }

      if(isset($oim['sidenote']) && $oim['sidenote']){
        $fout.= '<div class="sidenote">'.$oim['sidenote'].'</div>';
      }

      $fout.='
                    </div>';



    }
    $fout.='
                    </div>';
    $fout.='
                    </div>';
    $fout.='
                    </div>';

    return $fout;
  }

  function ajax_submit_download() {

    $aux_likes = 0;
    $playerid = '';

    if (isset($_POST['playerid'])) {
      $playerid = $_POST['playerid'];
      $playerid = str_replace('ap', '', $playerid);
    }

    if (is_numeric($playerid) && get_post_meta($playerid, '_dzsap_downloads', true) != '') {
      $aux_likes = intval(get_post_meta($playerid, '_dzsap_downloads', true));
    }

    if (isset($_COOKIE['downloadsubmitted-' . $playerid])) {

    } else {

    }

    $aux_likes = $aux_likes + 1;



    $this->insert_activity(array(
      'id_video'=>$playerid,
      'type'=>'download',
    ));


    if (is_numeric($playerid)){

      update_post_meta($playerid, '_dzsap_downloads', $aux_likes);
    }



    setcookie("downloadsubmitted-" . $playerid, '1', time() + (intval($this->mainoptions['play_remember_time']) * 60), COOKIEPATH);

    echo 'success';
    die();
  }

  function ajax_submit_views() {

    $aux_likes = 0;
    $playerid = '';

    if (isset($_POST['playerid'])) {
      $playerid = $_POST['playerid'];
      $playerid = str_replace('ap', '', $playerid);
    }

    if (get_post_meta($playerid, '_dzsap_views', true) != '') {
      $aux_likes = intval(get_post_meta($playerid, '_dzsap_views', true));
    }

    if (isset($_COOKIE['viewsubmitted-' . $playerid])) {

    } else {
      $aux_likes = $aux_likes + 1;



      $this->insert_activity(array(
        'id_video'=>$playerid,
        'type'=>'view',
      ));

    }





    update_post_meta($playerid, '_dzsap_views', $aux_likes);

    setcookie("viewsubmitted-" . $playerid, '1', time() + (intval($this->mainoptions['play_remember_time']) * 60), COOKIEPATH);

    echo 'success';
    die();
  }

  function ajax_submit_rate() {

    //print_r($_COOKIE);


    $rate_index = 0;
    $rate_nr = 0;
    $playerid = '';

    if (isset($_POST['playerid'])) {
      $playerid = $_POST['playerid'];
      $playerid = str_replace('ap', '', $playerid);
    }

    if (get_post_meta($playerid, '_dzsap_rate_nr', true) != '') {
      $rate_nr = intval(get_post_meta($playerid, '_dzsap_rate_nr', true));
    }
    if (get_post_meta($playerid, '_dzsap_rate_index', true) != '') {
      $rate_index = intval(get_post_meta($playerid, '_dzsap_rate_index', true));
    }



    if (!isset($_COOKIE['dzsap_ratesubmitted-' . $playerid])) {
      $rate_nr++;
    }

    if ($rate_nr <= 0) {
      $rate_nr = 1;
    }



    $rate_index = ($rate_index * ($rate_nr - 1) + intval($_POST['postdata'])) / ($rate_nr);


    setcookie("dzsap_ratesubmitted-" . $playerid, $_POST['postdata'], time() + 36000, COOKIEPATH);



    update_post_meta($playerid, '_dzsap_rate_index', $rate_index);
    update_post_meta($playerid, '_dzsap_rate_nr', $rate_nr);

    echo 'success';
    die();
  }
  function clean($string) {
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
  }
  function ajax_submit_pcm() {

    //print_r($_COOKIE);


    $lab = 'dzsap_pcm_data_'.($this->clean($_POST['playerid']));


//        update_option("dzsap_ceva", "ceva");
//        update_option($lab, "ceva");
    update_option($lab, $_POST['postdata']);
//        echo $lab. ' ';


    echo 'success';

    die();
  }

  function ajax_submit_sample_tracks() {

    //print_r($_COOKIE);

    $this->sample_data = array(
      'media'=>array(),
    );






    $args = array(
      'post_title' => 'Track 1 from stephaniequinn.com',
      'post_content' => 'stephaniequinn.com',
      'post_status' => 'inherit',
      'post_author' => 1,
      'post_type' => 'attachment',
      'post_mime_type' => 'audio/mpeg',
      'post_name' => 'steph1',
      'guid' => 'http://www.stephaniequinn.com/Music/Commercial%20DEMO%20-%2011.mp3',
    );

    $sample_post_2_id = wp_insert_post($args);
//        wp_set_post_terms($sample_post_2_id,$arr_cats[0],$taxonomy);
    update_post_meta($sample_post_2_id,'_waveformbg','https://lh3.googleusercontent.com/OCkCqtmYpqevOPlhNY4R8oy37CmypYXtsM6CdwstJp-2X8y4O_MdmnOOyTZ2dODVq7sfxLqoRG2H-fGJ8GAwYDp7jtiyyesUiMjIZA4czV7dDqnaw0qhpkRBpfSmqW_uOkQtGvhJUn9nYAK2MQwQ_PtCfl4uHgb1cae5n7qNC8DjRgVorBBr_gZVLg0IZFXbLW0UTp-8KsqrZSyGHAgxbh7Q40-CKFvBKxZ7KblCTfwsEun4LElkYFe5ZPZOsn1EBrxsbXrSyAZVmm0VX7UXRnEQR-5YTIzZ6ttugwYonTFNwmiGxOCsg5RyYpwTNWMLE1v2fBUsBgSStiLrnwQqrK4VAfV-irLXdfXsy6ZG174u0uPdjGJq3qw3PcJUHatmxZDC5PbSrxTHR-K6OqTOV7bM641t40ZVNZfZmjOTzzL-eDWkKCUu5q5VBm254sJ4FK63bP5QbxOQem6nPadxEayRSKfyF4z4HUnoqsR1giPk8eWI63LcgGOZeSWGVw0T27N_Ugwz37Twr5Ilyk7q66elCiyOxK7IUuiur6-QYi0=w1170-h140-no');
    update_post_meta($sample_post_2_id,'_waveformprog','https://lh3.googleusercontent.com/3ZCeepH9HAhs1ojwrMVKRW4poGaqPSbeczAAs8XjBl8E4zh0vSzXY4ou7KtRXUoMDff70qz8vEa5YLwq_4kp4ufRHcTK8_7lbs5Ux4jTETAkhluI75nUweiBYztNkwtxRggzTLnu2kdyVn3lubZGDbe4-pxyvBtz2tWauKs9fw7wiMCcrkFz5BFi_X1q7ViGA205qTfuTLjltWzom09Xm8vgt5EsTHyInFoMAeSobImMrG5j67VTgrX_9vYDNu3RE_TbISRY9c7wdEXOplQZXJDHH3c86rdVaoclhGAbli3mHJ92iZmGrZM1JH0glyj-ymSSq8RU1Tw2Slb1QFYEwzJpr_wOR9BqqccLAf-yLawNG5TqTQLhrYekNfPaWEtUrcYvHMDeg2R_x7zZg0Q_FI4qvUjBrTu8ClZIf_fml4mer7KEl3uhNEDNr7pe9suucRGO_f_whT8bqjFsRCvh9obFhvj0Suvc-SNFTeLavV6EwIqFVYdHCwyedHxdmOGTsruvXw3CRqon0UFb2jqR2GO6ZUSQ9k9emXdGCZAVzqY=w1170-h140-no');
    update_post_meta($sample_post_2_id,'_dzsap-thumb','https://lh3.googleusercontent.com/dF5JBlMfXMsYxXl3pvzmAtkWOhC-aP1rPOpoDHlOSXU1s0tG9XcgfXonQ6Z27jqId77KI3yv9nkbDWVKD3DsHTjoeHfw2PgpH9aoiykmbPXmQ64OKEVn1uJ5gGeiKD1zyPRlHd-yg7wy59wLoUxYpbbJpdf4uiB8Bf7NNo_1VXpyaMGjHRI7BMl5jFyXkJA7H2J5xT3kemlEo7HMUAg7vRDhCBLdvGoyNzZCuzFJ8meA3TLxi8SoQdCn371iv7joSWSfdQH6MCbE9VmCvLnYJIpkPs1PEtYOlbPUnb2UdFEA6kNiJmWnNqjOYxdb2v9mfsggNv8rk5IEazadXCwBqhREiCYvFd0fB3zsx9-zUHASEjWCF-LNFAYHvv8N4ZM7wzeWbRSsSKbxqk2ma7aym_QVc5GqDMQkp1LlEQxMI2zCIACiukehV6DvVOvw5Z1JLLPKL6Gq4kN8oNuS8glcgHzhwIlPBXy1wQ3hz_PU_H2Iu_wZt0eag77YArwha1Av5sINngPyJHu0UI2OrqgQd-7HqiGGuzWUkumAR8UAYQ=s80-no');





    array_push($this->sample_data['media'], $sample_post_2_id);
    $this->sample_data['first_sg_id'] = $sample_post_2_id;






    $args = array(
      'post_title' => 'Track 2 from stephaniequinn.com',
      'post_content' => 'stephaniequinn.com',
      'post_status' => 'inherit',
      'post_author' => 1,
      'post_type' => 'attachment',
      'post_mime_type' => 'audio/mpeg',
      'post_name' => 'steph1',
      'guid' => 'http://www.stephaniequinn.com/Music/Commercial%20DEMO%20-%2001.mp3',
    );

    $sample_post_2_id = wp_insert_post($args);
//        wp_set_post_terms($sample_post_2_id,$arr_cats[0],$taxonomy);
    update_post_meta($sample_post_2_id,'_waveformbg','https://lh3.googleusercontent.com/s_WsedJQkZIRGfooorFv1oZRApVy4FIpYvjP76Kpbo-5leiu1avPr65ElLuMb0bzRQuLeuk8OQnU4pywclzzjIDlZbQaWnCjnOIaQzkk37zyPKSJb-nnY2aov-SavJgFmAN2P6CeBdHI74tJaAOYycRxP7KrCdMdx0vwAixVcYkeJ7zR7Iad5ifaJ-jlBh_7mf97Xro6aVawW9BdxCs006vxrIY0l4QuNvOmBJ3jFcv38qkEeemaMDKxeaYYVPCzr5_ZnfumgK6WFvIrAEjiexlcFK2m5sFXz1c1b0IWyYYAITtYcasVqgAGuCsWTM9ujqR_T0dzWeg_uWOpZNJp2Y04LIsxmqMyCo6bL9mkWly0wLGkwVSpZFSZUKGJ5Vmti94Z6NXeVC4wpb-GOaYk5U3CDbxFDTBqXA3Gi5RT7mocTG3N4ZOR2gaIb530e0to6K2rMUixSqSvfOvfqV-vfsU4AZGs_NGF5-z5bFHioCTSXtmcNfl1CQn7HZnUqbdjE90R-vvvcI0SlYp6x9VCOhWof958SJzAGQSXmubbA-Q=w1170-h140-no');
    update_post_meta($sample_post_2_id,'_waveformprog','https://lh3.googleusercontent.com/Xl5bEyPhd4Rin99rRZg8vwj7XRuee4ED9d_FGas4ayh8G_VlZFtRUlfPYozrHduEKdhiW2AgEELjpbCubLhZbUZaFUaBNgwVbkVYtlDBvs1EI78hnDsgUozzltwIAypfe6OlgZn7nyUiYtDTG4iMBgBLLFX1CeN9LDmmB3EQO4d820eyIn0xz9ba9UEERq9ILzC2QkkWeCZQXS5zElaTXOLAVlZh2qgRbNkFNMjiQfCXuLbPizNKagbixAMXqiqOD-Z_vS7JklaeW2LuYHyrtp5MVW92NgHERk_P01N04CS2-dxc0ufYpo-vAenz6s2EVxHi292aRvC95alzGIT0_B30p5Cs_9yw_06fsypf3XTPd6ZqVgW2pdGxYOMk8Kwg_2IMEjULUkf9WSoVBarxAetG0hsfIVT9KVwsZBuER9dcXmLZpndLyH6wHejzIXb6FueuTZdWpw5_opTqqxQpLEM27V9J1hLJFyCcAcysVEVZkB-m5viDePPL1WqwFebBoOETjc4OIhh8Zs-dVeZNQSMI8nzH2d9kP3w6ocm-8HQ=w1170-h140-no');
    update_post_meta($sample_post_2_id,'_dzsap-thumb','https://lh3.googleusercontent.com/dF5JBlMfXMsYxXl3pvzmAtkWOhC-aP1rPOpoDHlOSXU1s0tG9XcgfXonQ6Z27jqId77KI3yv9nkbDWVKD3DsHTjoeHfw2PgpH9aoiykmbPXmQ64OKEVn1uJ5gGeiKD1zyPRlHd-yg7wy59wLoUxYpbbJpdf4uiB8Bf7NNo_1VXpyaMGjHRI7BMl5jFyXkJA7H2J5xT3kemlEo7HMUAg7vRDhCBLdvGoyNzZCuzFJ8meA3TLxi8SoQdCn371iv7joSWSfdQH6MCbE9VmCvLnYJIpkPs1PEtYOlbPUnb2UdFEA6kNiJmWnNqjOYxdb2v9mfsggNv8rk5IEazadXCwBqhREiCYvFd0fB3zsx9-zUHASEjWCF-LNFAYHvv8N4ZM7wzeWbRSsSKbxqk2ma7aym_QVc5GqDMQkp1LlEQxMI2zCIACiukehV6DvVOvw5Z1JLLPKL6Gq4kN8oNuS8glcgHzhwIlPBXy1wQ3hz_PU_H2Iu_wZt0eag77YArwha1Av5sINngPyJHu0UI2OrqgQd-7HqiGGuzWUkumAR8UAYQ=s80-no');


    array_push($this->sample_data['media'], $sample_post_2_id);



    $time = current_time('mysql');

    $playerid = $sample_post_2_id;
//        $playerid = str_replace('ap', '', $playerid);


    $data = array(
      'comment_post_ID' => $playerid,
      'comment_author' => 'admin',
      'comment_author_email' => 'admin@admin.com',
      'comment_author_url' => 'http://',
      'comment_content' => '<span class="dzstooltip-con" style="left:37.66387884267631%"><span class="dzstooltip arrow-from-start transition-slidein arrow-bottom skin-black" style="width: 250px;"><span class="the-comment-author">@admin</span> says:<br>test</span><div class="the-avatar" style="background-image: url(http://1.gravatar.com/avatar/12d1738b0f28c211e5fd5ae066e631a1?s=20&#038;d=mm&#038;r=g)"></div></span>',
      'comment_type' => '',
      'comment_parent' => 0,
      'user_id' => 1,
      'comment_author_IP' => '127.0.0.1',
      'comment_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
      'comment_date' => $time,
      'comment_approved' => 1,
    );

    wp_insert_comment($data);


    $data = array(
      'comment_post_ID' => $playerid,
      'comment_author' => 'admin',
      'comment_author_email' => 'admin@admin.com',
      'comment_author_url' => 'http://',
      'comment_content' => '<span class="dzstooltip-con" style="left:70.9369349005425%"><span class="dzstooltip arrow-from-start transition-slidein arrow-bottom skin-black" style="width: 250px;"><span class="the-comment-author">@admin</span> says:<br>comment 2</span><div class="the-avatar" style="background-image: url(http://1.gravatar.com/avatar/12d1738b0f28c211e5fd5ae066e631a1?s=20&#038;d=mm&#038;r=g)"></div></span>',
      'comment_type' => '',
      'comment_parent' => 0,
      'user_id' => 1,
      'comment_author_IP' => '127.0.0.1',
      'comment_agent' => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.10) Gecko/2009042316 Firefox/3.0.10 (.NET CLR 3.5.30729)',
      'comment_date' => $time,
      'comment_approved' => 1,
    );

    wp_insert_comment($data);


    update_option($this->dbname_sample_data, $this->sample_data);




    echo 'success';

    die();
  }

  function ajax_remove_sample_tracks() {

    //print_r($_COOKIE);



//        print_r($this->sample_data);




    foreach ($this->sample_data['media'] as $pid) {
      wp_delete_post($pid);
    };

    $this->sample_data = false;
    update_option($this->dbname_sample_data, $this->sample_data);




    echo 'success';

    die();
  }

  function ajax_submit_like() {

    //print_r($_COOKIE);


    $aux_likes = 0;
    $playerid = '';

    if (isset($_POST['playerid'])) {
      $playerid = $_POST['playerid'];
      $playerid = str_replace('ap', '', $playerid);
    }

    if (get_post_meta($playerid, '_dzsap_likes', true) != '') {
      $aux_likes = intval(get_post_meta($playerid, '_dzsap_likes', true));
    }

    $aux_likes = $aux_likes + 1;

    update_post_meta($playerid, '_dzsap_likes', $aux_likes);

    setcookie("dzsap_likesubmitted-" . $playerid, '1', time() + 36000, COOKIEPATH);

    echo 'success';
    die();
  }

  function ajax_retract_like() {

    //print_r($_COOKIE);


    $aux_likes = 1;
    $playerid = '';

    if (isset($_POST['playerid'])) {
      $playerid = $_POST['playerid'];
      $playerid = str_replace('ap', '', $playerid);
    }


    if (get_post_meta($playerid, '_dzsap_likes', true) != '') {
      $aux_likes = intval(get_post_meta($_POST['playerid'], '_dzsap_likes', true));
    }

    $aux_likes = $aux_likes - 1;

    update_post_meta($playerid, '_dzsap_likes', $aux_likes);

    setcookie("dzsap_likesubmitted-" . $playerid, '', time() - 36000, COOKIEPATH);

    echo 'success';
    die();
  }

  function handle_admin_head(){
    // on every admin page <head>
    //echo 'ceva23';
    ///siteurl : "'.site_url().'",
    $aux = admin_url( 'admin.php?page='.$this->adminpagename);

    if (isset($_GET['page']) && $_GET['page'] == $this->adminpagename_configs) {
      $aux = admin_url( 'admin.php?page='.$this->adminpagename_configs);
    }


    $params = array('currslider' => '_currslider_');
    $newurl = add_query_arg($params, $aux);

    $params = array('deleteslider' => '_currslider_');
    $delurl = add_query_arg($params, $aux);

    $theurl_forwaveforms = $this->base_url;
    $thepath_forwaveforms = $this->base_url;

    if (isset($this->mainoptions['use_external_uploaddir']) && $this->mainoptions['use_external_uploaddir'] == 'on') {
//            $theurl_forwaveforms = site_url('wp-content') . '/upload/';

      $upload_dir = wp_upload_dir();
      $theurl_forwaveforms = $upload_dir['url'].'/';

      $aux = $upload_dir['path'].'/';
      $thepath_forwaveforms = str_replace('\\', '/', $aux);
    }

    ?><script><?php
      echo 'window.ultibox_options_init = {
\'settings_deeplinking\' : \'off\'
,\'extra_classes\' : \'close-btn-inset\'
}; window.init_zoombox_settings = { settings_disableSocial : "on" ,settings_deeplinking : "off" }; var dzsap_settings = { thepath: "' . $this->base_url . '",the_url: "' . $this->base_url . '",theurl_forwaveforms: "' . $theurl_forwaveforms . '"
,thepath_forwaveforms: "' . $thepath_forwaveforms . '"
, is_safebinding: "' . $this->mainoptions['is_safebinding'] . '", admin_close_otheritems:"' . $this->mainoptions['admin_close_otheritems'] . '",settings_wavestyle:"' . $this->mainoptions['settings_wavestyle'] . '"
,url_vpconfig:"' . admin_url( 'admin.php?page='.$this->adminpagename_configs.'&currslider={{currslider}}').'"
,shortcode_generator_url: "'.admin_url('admin.php?page='.$this->page_mainoptions_link).'&dzsap_shortcode_builder=on"
,shortcode_generator_player_url: "'.admin_url('admin.php?page='.$this->page_mainoptions_link).'&dzsap_shortcode_player_builder=on"
,translate_add_gallery : "'.__('Add Playlist').'"
,translate_add_player : "'.__('Add Player').'"
,soundcloud_apikey : "' . $this->mainoptions['soundcloud_api_key'] . '"
';

      //echo 'hmm';
      if (isset($_GET['page']) && $_GET['page'] == $this->adminpagename && (isset($this->mainitems[$this->currSlider])==false || $this->mainitems[$this->currSlider] == '')) {
        echo ', addslider:"on"';
      }
      if (isset($_GET['page']) && $_GET['page'] == $this->adminpagename_configs && (isset($this->mainitems_configs[$this->currSlider])==false || $this->mainitems_configs[$this->currSlider] == '')) {
        echo ', addslider:"on"';
      }
      echo ',urldelslider:"' . $delurl . '", urlcurrslider:"' . $newurl . '", currSlider:"' . $this->currSlider . '", currdb:"' . $this->currDb . '", color_waveformbg:"' . $this->mainoptions['color_waveformbg'] . '", color_waveformprog:"' . $this->mainoptions['color_waveformprog'] . '", waveformgenerator_multiplier:"' . $this->mainoptions['waveformgenerator_multiplier'] . '"};';



      ?></script><?php



    if ($this->mainoptions['enable_auto_backup'] == 'on') {
//            $this->do_backup();
      $last_backup = get_option('dzsap_last_backup');

      if ($last_backup) {

        $timestamp = time();
        if (abs($timestamp - $last_backup) > (3600 * 24)) {

          $this->do_backup();
        }

      } else {
        $this->do_backup();
      }
    }
    if (isset($_GET['page']) && $_GET['page'] == $this->adminpagename){
    }
  }

  function do_backup() {

    $timestamp = time();

//        echo 'time - '.$timestamp;

    $data = get_option($this->dbname_mainitems);

    if (is_array($data)) {
      $data = serialize($data);
    }

//        echo ' data - '.$data;
//        file_put_contents('backups/backup_'.$timestamp,$data);
    $upload_dir = wp_upload_dir();
//        file_put_contents($this->base_path . 'backups/backup_' . $timestamp . '.txt', $data);



    if(file_exists($upload_dir['basedir'] . '/dzsap_backups')){

//            echo 'dada';
    }else{

//            echo 'nunu';
      mkdir($upload_dir['basedir'] . '/dzsap_backups', 0755);
    }

    file_put_contents($upload_dir['basedir'] . '/dzsap_backups/backup_' . $timestamp . '.txt', $data);


//        $theurl_forwaveforms = $upload_dir['url'].'/';

//        echo $upload_dir['basedir'] . '/dzsap_backups/backup_' . $timestamp . '.txt';

//        print_r($upload_dir);

    update_option('dzsap_last_backup', $timestamp);


    if (is_array($this->dbs)) {
      foreach ($this->dbs as $adb) {
        $data = get_option($this->dbname_mainitems . '-' . $adb);

        if (is_array($data)) {
          $data = serialize($data);
        }
//                file_put_contents($this->base_path . 'backups/backup_' . $adb . '_' . $timestamp . '.txt', $data);
        file_put_contents($upload_dir['basedir'] . '/dzsap_backups/backup_' . $adb . '_' . $timestamp . '.txt', $data);


      }
    }
  }


  function shortcode_woo_grid($atts, $content = null) {
    //[dzsap_woo_grid --]
    global $current_user;

    //print_r($current_user->data);
    //echo 'ceva'.isset($current_user->data->user_nicename);
    $this->sliders__player_index++;

    $fout = '';





    $this->front_scripts();
    wp_enqueue_style('dzs.zoomsounds-grid', $this->base_url . 'audioplayer/audioportal-grid.css');

    $margs = array(
      'style' => 'under',
      'vpconfig' => '',
      'settings_wpqargs' => '',
      'faketarget' => '',
      'type' => 'product',
      'cats' => '',
      'ids' => '',
      'layout' => '4-cols',
    );

    if($atts){

      $margs = array_merge($margs, $atts);
    }




    $args_wpqargs = array();
    $margs['settings_wpqargs'] = html_entity_decode($margs['settings_wpqargs']);
    parse_str($margs['settings_wpqargs'],$args_wpqargs);


    $wpqargs = array(
      'post_type' => $margs['type'],
      'posts_per_page' => '-1',
    );

    if (!isset($args_wpqargs) || $args_wpqargs == false || is_array($args_wpqargs) == false) {
      $args_wpqargs = array();
    }

    $taxonomy = 'product_cat';

    if($margs['type']=='attachment'){
      $wpqargs['post_mime_type']='audio/mpeg';
//            $wpqargs['post_mime_type'] = 'image';

      $wpqargs['post_parent']=null;
      $wpqargs['post_status']='inherit';
    }



    if($margs['cats']){


      $thecustomcats = array();
      $thecustomcats = explode(',',$margs['cats']);
      $thecustomcats = array_values($thecustomcats);

      if ($wpqargs['post_type'] == 'product') {


        $wpqargs['tax_query'] = array(
          array(
            'taxonomy' => $taxonomy,
            'field' => 'id',
            'terms' => $thecustomcats,
          )
        );
      }
      if ($wpqargs['post_type'] == 'attachment') {
      }




    }

    if($margs['ids']){

      $aux_arr = explode(',',$margs['ids']);

      $wpqargs['post__in'] = $aux_arr;



    }

    $str_layout = '';

    $str_layout.='dzs-layout--'.$margs['layout'];

    $wpqargs=array_merge($wpqargs,$args_wpqargs);


    $query = new WP_Query($wpqargs);

    $its = $query->posts;

//        print_r($query);;

    if($margs['style']=='noir' || $margs['style']=='style1' || $margs['style']=='style2'){

      $fout.='<div class="dzsap-grid '.$str_layout.' style-'.$margs['style'].'">';
    }else{
      $fout.='<div class="dzsap-woo-grid style-'.$margs['style'].'">';

    }


    if($margs['style']=='style4'){
      $fout.='<ul class="style-nova">';
    }
    if($margs['style']=='style3'){
      $fout.='<div class="dzsap-header-tr">
                            <div class="column-for-player">'.$this->mainoptions['i18n_play'].'</div>
                            <div class="column-for-title">'.$this->mainoptions['i18n_title'].'</div>
                            <div class="column-for-buy">'.$this->mainoptions['i18n_buy'].'</div>
                        </div>';

//            print_r($its);
    }

    foreach($its as $it){


      $src = get_post_meta($it->ID,'dzsap_woo_product_track',true);

      if($margs['type']=='product'){
        if($src==''){
          $aux = get_post_meta($it->ID,'_downloadable_files',true);
          if($aux && is_array($aux)){

            $aux = array_values($aux);


            if(isset($aux[0]) && isset($aux[0]['file']) && strpos($aux[0]['file'], '.mp3')!==false){

              $src = $aux[0]['file'];
            }
          }

//                    echo '$aux - ';print_r($aux);
        }
      }

      $type = 'audio';

//            print_r($margs);
      if($margs['type']=='dzsap_items'){
        $src = get_post_meta($it->ID,'dzsap_meta_item_source',true);
        $type = get_post_meta($it->ID,'dzsap_meta_type',true);
      }

      if($margs['type']=='attachment'){
        $src = $it->guid;
      }

      $buy_link =site_url().'/cart/?add-to-cart='.$it->ID;


//            print_r($it);
//            echo 'hmm';
//            echo dzs_curr_url();
      $buy_link = DZSHelpers::add_query_arg(dzs_curr_url(), 'add-to-cart',$it->ID);

      if(get_post_meta($it->ID,'dzsap_woo_custom_link',true)){

        $buy_link = get_post_meta($it->ID,'dzsap_woo_custom_link',true);
      }



      if($margs['style']=='noir' || $margs['style']=='style1' || $margs['style']=='style2'){
        $fout.='<div class="dzs-layout-item "';
        $fout.='>';
        $fout.='<div class="grid-object ';


        $fout.='"';
        $fout.='>';
      }else{

        $fout.='<div class="grid-object ';

        if($src){
          $fout.=' zoomsounds-woo-grid-item';
        }
        $fout.='">';
      }



      $cue = 'on';
      $thumb_url = '';
      $title = '';

      $shortdesc = '';
      $longdesc = '';







      $price = get_post_meta($it->ID, 'dzsap_meta_item_price',true);

      if($margs['type']=='product'){
        if(get_post_meta($it->ID, '_regular_price',true)){
          $price = '';
          if(function_exists('get_woocommerce_currency_symbol')){
            $price.=get_woocommerce_currency_symbol();
          }
          $price .= get_post_meta($it->ID, '_regular_price',true);
        }
      }





      if($margs['faketarget']){

//                    $type='fake';

      }

      $thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id( $it->ID ), 'large' );
      if(is_array($thumb_url) && isset($thumb_url[0])){
        $thumb_url = $thumb_url[0];
      }
      if($margs['type']=='attachment' && get_post_meta($it->ID,'_dzsap-thumb',true)){
        $thumb_url=get_post_meta($it->ID,'_dzsap-thumb',true);
      }


      $html_meta_artist = '';

      $title = $it->post_title;
      $shortdesc = get_post_meta($it->ID,'dzsap_woo_subtitle',true);
      $longdesc = $it->post_excerpt;

      $user_info = get_userdata($it->post_author);
//            print_r($it);
//            print_r($user_info);
      $author_name =  $user_info->data->display_name;

      if($title){
        $html_meta_artist = '<div class="meta-artist"><span class="the-artist">'.$author_name.'</span><span class="the-name">'.$title.'</span></div>';
      }




      $str_pcm = '';

      if ($this->mainoptions['skinwave_wave_mode'] == 'canvas') {
//                print_r($che);



        $args = array(
          'source'=>$src,
          'linktomediafile'=>$it->ID,
          'playerid'=>$it->ID,
        );

        $str_pcm.=$this->generate_pcm($args);
      } else {
        if (isset($che['waveformbg']) && $che['waveformbg'] != '') {
          $str_pcm .= ' data-scrubbg="' . $che['waveformbg'] . '"';
        };
        if (isset($che['waveformprog']) && $che['waveformprog'] != '') {
          $str_pcm .= ' data-scrubprog="' . $che['waveformprog'] . '"';
        };
      }


      $wavebg = get_post_meta($it->ID,'dzsap_woo_product_track_waveformbg',true);
      $waveprog = get_post_meta($it->ID,'dzsap_woo_product_track_waveformprog',true);

      if($margs['style']=='under'){


//                print_r($it);



        if($margs['faketarget']){

//                    $type='fake';

        }

        $args = array(

          'source' => $src,
          'cue' => $cue,
          'config' => $margs['vpconfig'],
          'autoplay' => 'off',
          'type' => $type,
          'faketarget' => $margs['faketarget'],
          'sample_time_start' => get_post_meta($it->ID,'dzsap_woo_sample_time_start',true),
          'sample_time_end' => get_post_meta($it->ID,'dzsap_woo_sample_time_end',true),
          'sample_time_total' => get_post_meta($it->ID,'dzsap_woo_sample_time_total',true),
          'playerid' => $it->ID,
        );


//                print_r($args);

        $fout.=$this->shortcode_player($args);

      }




      if($margs['style']=='style4'){


//                print_r($it);


        $fout.='<li>

                            <div class="li-thumb" style="background-image: url('.$thumb_url.')">
                                ';



        $args = array(

          'source' => $src,
          'cue' => $cue,
          'config' => $margs['vpconfig'],
          'autoplay' => 'off',
          'type' => $type,
          'faketarget' => $margs['faketarget'],
          'sample_time_start' => get_post_meta($it->ID,'dzsap_woo_sample_time_start',true),
          'sample_time_end' => get_post_meta($it->ID,'dzsap_woo_sample_time_end',true),
          'sample_time_total' => get_post_meta($it->ID,'dzsap_woo_sample_time_total',true),
          'playerid' => $it->ID,
        );


//                print_r($args);

        $fout.=$this->shortcode_player($args);

        $fout.='
                            </div>

                            <div class="li-meta"><a class="ajax-link track-title" href="index.php?page=track&track_id=2">'.$title.'</a><div class=" track-by">by '.$author_name.'</div><div class="the-price">'.__("Free").'</div></div>

                        </li>';




      }



      if($margs['style']=='noir'){


//                print_r($it);

//                echo 'test'.$wavebg;


//                print_r($it);



        if($margs['faketarget']){

//                    $type='fake';

        }

        $args = array(

          'source' => $src,
          'cue' => $cue,
          'config' => $margs['vpconfig'],
          'autoplay' => 'off',
          'type' => $type,
          'faketarget' => $margs['faketarget'],
          'sample_time_start' => get_post_meta($it->ID,'dzsap_woo_sample_time_start',true),
          'sample_time_end' => get_post_meta($it->ID,'dzsap_woo_sample_time_end',true),
          'sample_time_total' => get_post_meta($it->ID,'dzsap_woo_sample_time_total',true),
          'playerid' => $it->ID,
        );


//                print_r($args);

        $fout.=$this->shortcode_player($args);

        $fout.='

                        <h4 class="the-title">'.$title.'</h4>
                        <div class="the-price">'.$price.'</div>

                        <a  href="'.$buy_link.'" class="dzs-button padding-small"><span class="the-bg"></span><span class="the-text">'.$this->mainoptions['i18n_buy'].'</span></a>';
      }




      if($margs['style']=='style1'){


//                print_r($it);

//                echo 'test'.$wavebg;


        if($margs['type']=='attachment'){
          $shortdesc = $it->post_content;
        }

//                print_r($thumb_url);

        $buystring = '<a href="'.$buy_link.'" class="button-buy" style="font-size: 16px;">'.$this->mainoptions['i18n_buy'].'</a>&nbsp;';


        $waveformbg_str = '';
        $waveformprog_str = '';

        if($margs['type']=='attachment'){
          $buystring = '';

//                    echo 'ceva'.get_post_meta($it->ID,'_waveformbg',true);

          if(get_post_meta($it->ID,'_waveformbg',true)){
            $wavebg.=get_post_meta($it->ID,'_waveformbg',true);
          }

          if(get_post_meta($it->ID,'_waveformprog',true)){
            $waveprog=get_post_meta($it->ID,'_waveformprog',true);
          }

        }

        if($thumb_url){

          $fout.='<img src="'.$thumb_url.'" class="fullwidth"/>';
        }


        // daon pizda masi
        // test test test test test2134541234567890 123456678o90

//                print_r($margs);


//                echo 'ceva'.$waveformbg_str;

        $fout.='<div class="label-artist"><a href="'.get_permalink($it->ID).'">'.$title.'</a></div>
<div class="label-song">'.$shortdesc.'</div>
<div class="dzsap-grid-meta-buy" style="margin-top: 15px;">
'.$buystring;
        if($src) {
          $fout .= '
<span href="#" class="button-buy audioplayer-song-changer from-style-1" style="font-size: 16px; background-color: #a861c6" data-target="' . $margs['faketarget'] . '"  style="" data-thumb="' . $thumb_url . '"  data-bgimage="img/bg.jpg"';


          $fout.=$str_pcm;


          $fout .= ' data-type="' . $type . '" data-playerid="' . $it->ID . '" data-source="' . $src . '" >' . $this->mainoptions['i18n_play'] . '
' . $html_meta_artist . '
</span>';

        }
        $fout.='
</div>';
//$longdesc


      }


      if($margs['style']=='style2'){

//                echo 'ceva';

//                print_r($it);


        if($margs['faketarget']){

//                    $type='fake';

        }

        $thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id( $it->ID ), 'large' );
        if(is_array($thumb_url) && isset($thumb_url[0])){
          $thumb_url = $thumb_url[0];
        }
        if($margs['type']=='attachment'){
          $thumb_url=get_post_meta($it->ID,'_dzsap-thumb',true);
        }



        $title = $it->post_title;
        $shortdesc = get_post_meta($it->ID,'dzsap_woo_subtitle',true);
        $longdesc = $it->post_excerpt;


        if($margs['type']=='attachment'){
          $shortdesc = $it->post_content;
        }

//                print_r($thumb_url);



        $buystring = '<a href="'.$buy_link.'" class="button-buy" style="font-size: 16px;">'.$this->mainoptions['i18n_buy'].'</a>&nbsp;';

        if($margs['type']=='attachment'){
          $buystring = '';
        }


        $fout.='<div class="dzsap-grid-style2-item">';

        if($thumb_url){

          $fout.='<img src="'.$thumb_url.'" class="fullwidth"/>';
        }

        $fout.='<div class="centered-content-con"><div class="centered-content"><div class="label-artist">'.$title.'</div>
<div class="label-song">'.$shortdesc.'</div>
<div class="dzsap-grid-meta-buy" style="margin-top: 15px;">
'.$buystring;


        if($src){

          $fout.='
<span href="#" class="button-buy audioplayer-song-changer" style="font-size: 16px; background-color: #a861c6" data-target="'.$margs['faketarget'].'"  style="" data-thumb="'.$thumb_url.'"  data-bgimage="img/bg.jpg" data-scrubbg="'.$wavebg.'" data-scrubprog="'.$waveprog.'"  data-playerid="' . $it->ID . '" data-type="'.$type.'" data-source="'.$src.'" >'.$this->mainoptions['i18n_play'].'
'.$longdesc.'
</span>';
        }

        $fout.='
</div>
</div>
</div>';

        $fout.='</div>';


      }


      if($margs['style']=='style3'){

//                echo 'ceva';

//                print_r($it);


        if($margs['faketarget']){

//                    $type='fake';

        }

        $thumb_url = wp_get_attachment_image_src( get_post_thumbnail_id( $it->ID ), 'large' );
        if(is_array($thumb_url) && isset($thumb_url[0])){
          $thumb_url = $thumb_url[0];
        }


        if($margs['type']=='attachment'){
          $thumb_url=get_post_meta($it->ID,'_dzsap-thumb',true);
        }



        $title = $it->post_title;
        $shortdesc = get_post_meta($it->ID,'dzsap_woo_subtitle',true);
        $longdesc = $it->post_excerpt;



        if(get_permalink($it->ID)){

          $title = '<a href="'.get_permalink($it->ID).'">'.$title.'</a>';
        }



        if($margs['type']=='attachment'){
          $shortdesc = $it->post_content;
        }

//                print_r($thumb_url);

//                echo '$buy_link - '.$buy_link;

        $buystring = '<a href="'.$buy_link.'" class="button-buy grid-buy-btn" style="font-size: 16px;">'.$this->mainoptions['i18n_buy'].'</a>';

        if($margs['type']=='attachment'){
          $buystring = '';
        }

//                print_r($margs);
//                print_r($it);
        $args = array(

          'source' => $src,
          'cue' => $cue,
          'height' => '',
          'extra_classes_player' => 'position-relative',
          'config' => array(

            'skin_ap' => 'skin-customcontrols'
          ),
          'autoplay' => 'off',
          'type' => $type,
          'faketarget' => $margs['faketarget'],
          'inner_html' => ' <div class="custom-play-btn position-relative playbtn-darkround" data-border-radius="5px" data-size="30px"></div>
        <div class="custom-pause-btn position-relative pausebtn-darkround" data-border-radius="5px" data-size="30px"></div>

        <div class="meta-artist-con">

            <span class="the-artist">'.$title.'</span>
            <span class="the-name">'.$shortdesc.'</span>
        </div>',
          'sample_time_start' => get_post_meta($it->ID,'dzsap_woo_sample_time_start',true),
          'sample_time_end' => get_post_meta($it->ID,'dzsap_woo_sample_time_end',true),
          'sample_time_total' => get_post_meta($it->ID,'dzsap_woo_sample_time_total',true),
          'playerid' => $it->ID,
        );


//                print_r($args);



        /*
                 *
                 * <div  data-type="audio" class="audioplayer-tobe skin-customcontrols auto-init position-relative "   data-fakeplayer="'.$margs['faketarget'].'"  data-source="'.$src.'"  data-scrubbg="'.$wavebg.'" data-scrubprog="'.$waveprog.'" data-playfrom="last" data-type="'.$type.'" ';


                $fout.=$str_pcm;

                if($thumb_url){

                    $fout.=' data-thumb="'.$thumb_url.'"';
                }

                $fout.='>



    </div>
                 */



        $fout.='<div class="dzsap-product-tr">
<div class="column-for-player">
';
        $fout.=$this->shortcode_player($args);

        $fout.='

</div>';

        $fout.='
<div class="column-for-title">';



        /*
                $fout.=$title;

                if($shortdesc){
                    $fout.='- '.$shortdesc;
                }
                */



        $fout.='';


//                $author_user = get_user_by('id',$it->post_author);

//                print_r($author_user);

        $fout.=$title;

        $fout.=' - '.$author_name;


        $fout.='</div>';


//                print_r($it);




        $fout.='<div class="column-for-buy">'.$buystring.'</div>
</div>';



      }


      if($margs['style']=='noir' || $margs['style']=='style1' || $margs['style']=='style2') {
        $fout .= '</div>';
        $fout .= '</div>';
      }else{

        $fout .= '</div>';
      }
    }


    if($margs['style']=='style4'){
      $fout.='</ul>';
    }
    $fout.='</div>';

//        print_r($its); print_r($margs); echo 'alceva'.$fout;

    return $fout;
  }


  function shortcode_audio($atts, $content = null) {
    global $current_user;

    //print_r($current_user->data);
    //echo 'ceva'.isset($current_user->data->user_nicename);
    //[zoomsounds_player source="pathto.mp3"]
    $this->sliders__player_index++;

    $fout = '';





    $this->front_scripts();

    $margs = array(
      'mp3' => '',
      'config' => 'default',
    );

    $margs = array_merge($margs, $atts);

//        print_r($margs);
    $margs['source'] = $margs['mp3'];
    $margs['config'] = $this->mainoptions['replace_audio_shortcode'];
    $margs['called_from'] = 'audio_shortcode';

    if($this->mainoptions['replace_audio_shortcode_play_in_footer']=='on'){
      $margs['play_target']='footer';
    }

    $playerid = '';

    $fout.=$this->shortcode_player($margs, $content);

//        print_r($its); print_r($margs); echo 'alceva'.$fout;

    return $fout;
  }


  function get_track_source($source, &$playerid, &$margs){

//        echo 'ceva = alceva';
    if((intval($source))){
      $player_post_id = intval($source);
      $player_post = get_post(intval($source));


//                echo ' is_int(intval($margs[\'source\']))  - '.is_int(intval($source));
//                echo ' (intval($margs[\'source\']))  - '.(intval($source));

//                print_rr($player_post);


//            print_r($player_post);

      if($player_post && $player_post->post_type=='attachment'){
        $media = wp_get_attachment_url($player_post_id);

//                echo 'media - '.$media;
        $source = $media;
        if($playerid){

        }else{
          $playerid = $player_post_id;
          $margs['playerid'] = $player_post_id;
        }

//                    print_r($media);
      }
      if($player_post && $player_post->post_type=='product'){




        $source = get_post_meta($player_post->ID,'dzsap_woo_product_track',true);


        if($source==''){
          $aux = get_post_meta($player_post->ID,'_downloadable_files',true);
          if($aux && is_array($aux)){

            $aux = array_values($aux);


            if(isset($aux[0]) && isset($aux[0]['file']) && strpos($aux[0]['file'], '.mp3')!==false){

              $src = $aux[0]['file'];
            }
          }

//                    echo '$aux - ';print_r($aux);
        }

        if($playerid){

        }else{
          $playerid = $player_post_id;
          $margs['playerid'] = $player_post_id;
        }

//                    print_r($media);
      }
      if($player_post && $player_post->post_type=='dzsap_items'){




        $source = get_post_meta($player_post->ID,'dzsap_meta_item_source',true);





//                    print_r($media);
      }


      if($source==''){
        if(function_exists('get_field')){
          $arr = get_field('long_preview',$player_post_id);


          if($arr){

            $media = wp_get_attachment_url($arr);

//                echo 'media - '.$media;
            $source = $media;
          }

          if($source==''){
            if(function_exists('get_field')){
              $arr = get_field('short_preview',$player_post_id);


              if($arr){

                $media = wp_get_attachment_url($arr);

//                echo 'media - '.$media;
                $source = $media;
              }
            }
          }
        }
      }
    }else{


//                echo "WHAA";
      if($source=='{{postid}}'){

        global $post;


        if($post){
          $player_post = $post;
        }




        $source = get_post_meta($player_post->ID,'dzsap_woo_product_track',true);


        if($source==''){
          $aux = get_post_meta($player_post->ID,'_downloadable_files',true);
          if($aux && is_array($aux)){

            $aux = array_values($aux);


            if(isset($aux[0]) && isset($aux[0]['file']) && strpos($aux[0]['file'], '.mp3')!==false){

              $source = $aux[0]['file'];
            }
          }

//                    echo '$aux - ';print_r($aux);
        }



        if($margs['playerid']==''){
          $margs['playerid'] = $player_post->ID;
        }



      }




//                echo 'whaaaa';
    }

    return $source;
  }


  function shortcode_player($atts = array(), $content = '') {
    //[zoomsounds_player source="pathto.mp3" artistname="" songname=""]
    global $current_user,$post;

    //print_r($current_user->data);
    //echo 'ceva'.isset($current_user->data->user_nicename);
    $this->sliders__player_index++;

    $fout = '';


    $player_idx = $this->sliders__player_index;



    $this->front_scripts();

    $margs = array(
      'width' => '100%',
      'config' => 'default',
      'height' => '300',
      'source' => '',
      'sourceogg' => '',
      'coverimage' => '',
      'waveformbg' => '',
      'waveformprog' => '',
      'cue' => 'on',
      'loop' => 'off',
      'autoplay' => 'off',
      'type' => 'audio',
      'player' => '',
      'itunes_link' => '',
      'playerid' => '',
      'thumb' => '',
      'mp4' => '',
      'openinzoombox' => 'off',
      'enable_likes' => 'off',
      'enable_downloads_counter' => 'off',
      'enable_views' => 'off',
      'enable_rates' => 'off',
      'playfrom' => 'off',
      'artistname' => 'default',
      'songname' => 'default',
      'single' => 'on',
      'embedded' => 'off',
      'divinsteadofscript' => 'off',
      'init_player' => 'on',
      'faketarget' => '',
      'sample_time_start' => '',
      'sample_time_end' => '',
      'sample_time_total' => '',
      'extra_init_settings' => '',
      'called_from' => 'player',
      'player_index' => $player_idx,
      'inner_html' => '',
      'extra_classes' => '',
      'js_settings_extrahtml_in_float_right' => '',
      'play_target' => 'default',
      'outer_comments_field' => '',
    );





    $default_margs = array_merge(array(), $margs);

    $margs = array_merge($margs, $atts);




    $embed_margs = array();

    foreach ($margs as $lab=>$arg){

      if(isset($margs[$lab])){

        if(isset($default_margs[$lab])==false || $margs[$lab]!==$default_margs[$lab]){
          $embed_margs[$lab] = $margs[$lab];
        }
      }
    }

//        print_r($embed_margs);






    $playerid = '';


    $player_post = null;
    $player_post_id = 0;


//        echo 'ceva';
//        print_rr($margs);


    if($margs['play_target']=='footer'){
      if(isset($margs['faketarget']) && $margs['faketarget']){

      }else{
        $margs['faketarget'] = '#dzsap_footer';
      }
    }


    if($margs['source']){
//            echo $margs['source'];
//            echo is_int(intval($margs['source']));
      $margs['source'] = $this->get_track_source($margs['source'],$playerid, $margs);
    }

//        print_rr($margs);


//        print_rr($margs);

    // --  here we will detect video player configs and call parse_items To Be Continued...
    // --  audio player configuration setup
    $vpsettingsdefault = array(
      'id' => 'default',
      'skin_ap' => 'skin-default',
      'settings_backup_type' => 'full',
      'skinwave_dynamicwaves' => 'off',
      'skinwave_enablespectrum' => 'off',
      'skinwave_enablereflect' => 'on',
      'skinwave_comments_enable' => 'off',
      'disable_volume' => 'default',
      'playfrom' => 'default',
      'enable_embed_button' => 'off',
      'loop' => 'off',
      'soundcloud_track_id' => '',
      'soundcloud_secret_token' => '',
    );

    $vpsettings = array();

    $i = 0;
    $vpconfig_k = -1;
    $vpconfig_id = $margs['config'];


    if(is_array($margs['config'])){


      $vpsettings['settings'] = $margs['config'];


    }else{

      for ($i = 0; $i < count($this->mainitems_configs); $i++) {
        if ((isset($vpconfig_id)) && ($vpconfig_id == $this->mainitems_configs[$i]['settings']['id'])) {
          $vpconfig_k = $i;
        }
      }



      if ($vpconfig_k > -1) {
        $vpsettings = $this->mainitems_configs[$vpconfig_k];
      } else {
        $vpsettings['settings'] = $vpsettingsdefault;
      }

      if (is_array($vpsettings) == false || is_array($vpsettings['settings']) == false) {
        $vpsettings = array('settings' => $vpsettingsdefault);
      }
    }

    //print_r($vpsettings);




    if($vpsettings['settings']['skin_ap']=='skin-wave'){
      if($margs['waveformbg']==''){
        $margs['waveformbg']=$this->base_url.'waves/scrubbg_default.png';
      }
      if($margs['waveformprog']==''){
        $margs['waveformprog']=$this->base_url.'waves/scrubprog_default.png';
      }
//            print_r($margs);
    }


    if(is_array($margs['config'])){
      $vpsettings['settings'] = array_merge($vpsettingsdefault, $margs['config']);
    }


    $its = array(0 => $margs, 'settings' => array());

    if(isset($vpsettings['settings']) == false || is_array($vpsettings['settings'])==false){

      $vpsettings['settings'] = array_merge($vpsettingsdefault, array());
    }

    $its['settings'] = array_merge($its['settings'], $vpsettings['settings']);




    if($margs['enable_views']=='on'){
      $its['settings']['enable_views'] = 'on';
    }


    $margs = array_merge($margs, $vpsettings['settings']);






    if(isset($margs['js_settings_extrahtml_in_float_right_from_config']) && $margs['js_settings_extrahtml_in_float_right_from_config']){
      $margs['js_settings_extrahtml_in_float_right'].=$margs['js_settings_extrahtml_in_float_right_from_config'];
    }



    $margs['js_settings_extrahtml_in_float_right'] = str_replace('{{meta1val}}',get_post_meta($post->ID, 'dzsap_meta_extra_meta_label_1',true),$margs['js_settings_extrahtml_in_float_right']);
    $margs['js_settings_extrahtml_in_float_right'] = str_replace('{{meta2val}}',get_post_meta($post->ID, 'dzsap_meta_extra_meta_label_2',true),$margs['js_settings_extrahtml_in_float_right']);









//        print_r($margs); print_r($its); print_r($vpsettings);

//        print_r($margs);


    $has_extra_html = false;

    if (isset($margs) && ($margs['enable_views'] == 'on' || $margs['enable_downloads_counter'] == 'on' || $margs['enable_likes'] == 'on' || $margs['enable_rates'] == 'on' || (isset($margs['extra_html']) && $margs['extra_html'] ) )) {
      $has_extra_html = true;
    }



//        print_r($margs);








//        $enc_margs = simple_encrypt(json_encode($margs),'1111222233334444');
//        $enc_margs = gzcompress(json_encode($embed_margs),9);
    $enc_margs = json_encode($embed_margs);
    $enc_margs = base64_encode(json_encode($embed_margs));
//        $enc_margs = base64_decode(base64_encode(json_encode($embed_margs)));

//        $embed_code = '<iframe src=\'' . $this->base_url . 'bridge.php?type=player&margs='.urlencode($enc_margs).'\' style="overflow:hidden; transition: height 0.3s ease-out;" width="100%" height="152" scrolling="no" frameborder="0"></iframe>';
    $embed_code = '<iframe src=\'' . site_url() . '?action=embed_zoomsounds&type=player&margs='.urlencode($enc_margs).'\' style="overflow:hidden; transition: height 0.3s ease-out;" width="100%" height="152" scrolling="no" frameborder="0"></iframe>';
    $embed_code = str_replace('"',"'", $embed_code);
    $embed_code = htmlentities($embed_code, ENT_QUOTES);

    $margs['has_extra_html']=$has_extra_html;
    $margs['embed_code']=$embed_code;
//        echo ' has extra html - '.$has_extra_html;


    if ($margs['openinzoombox'] != 'on') {

//            if(isset($margs['called_from'])==false || $margs['called_from']==''){
//            }
//            $args = array('called_from'=> 'player');
//            $args = array_merge($margs, $args);
//            $fout.='make playir ->';

      if($margs['itunes_link']){

        if(isset($its[0]['extra_html'])==false){
          $its[0]['extra_html'] = '';
        }

        $its[0]['extra_html'].=' <span class=" btn-zoomsounds btn-itunes "><span class="the-icon"><i class="fa fa-apple"></i></span><span class="the-label ">iTunes</span></span>';
      }

//            print_r($margs);
      $margs['the_content']=$content;

      if($margs['songname'] && $margs['songname']!='default'){

        if(isset($its[0]['menu_songname'])==false || !($its[0]['menu_songname'] && $its[0]['menu_songname']!='default')){

          $its[0]['menu_songname'] = $margs['songname'];
        }
      }
      if($margs['artistname'] && $margs['artistname']!='default'){

        if(isset($its[0]['menu_artistname'])==false || !($its[0]['menu_artistname'] && $its[0]['menu_artistname']!='default')){

          $its[0]['menu_artistname'] = $margs['artistname'];
        }
      }




//            print_rr($its);
//            print_rr($margs);
      $fout .= $this->parse_items($its, $margs);
    }



    // -- normal mode
    if($margs['init_player']=='on'){


      wp_enqueue_style( 'dzsap', $this->base_url . 'audioplayer/audioplayer.css');
      wp_enqueue_script( 'dzsap', $this->base_url . 'audioplayer/audioplayer.js', array('jquery'));

      if ($margs['openinzoombox'] != 'on') {
        if($margs['divinsteadofscript']!='on'){
          $fout.='<script>';
        }else{
          $fout.='<div class="toexecute">';
        }




//                print_r($its);

//                echo 'what what in the butt: '.$playerid;

        $str_id = '';
        if($margs['playerid']){

          $str_id.='.ap'.$margs['playerid'];
        }


        $loop = 'off';
        if(isset($vpsettings['settings']['loop']) && $vpsettings['settings']['loop']=='on') {
          $loop = $vpsettings['settings']['loop'];
        }


//                print_r($margs);
        if(isset($margs['loop']) && $margs['loop']=='on'){
          $loop = 'on';
        }

        $preload_method = 'metadata';
        $design_animateplaypause = 'off';

        if(isset($vpsettings['settings']['preload_method'])){
          $preload_method = $vpsettings['settings']['preload_method'];
        }
        if(isset($vpsettings['settings']['design_animateplaypause'])){
          $design_animateplaypause = $vpsettings['settings']['design_animateplaypause'];
        }

        $fout.='/*console.warn("WILL INIT"); */ jQuery(document).ready(function ($){';



        if($this->mainoptions['js_init_timeout']){
          $fout.=' setTimeout(function(){';
        }


        $fout.='var settings_ap = {  design_skin: "' . $vpsettings['settings']['skin_ap'] . '"  ,autoplay: "' . $margs['autoplay'] . '"  ,disable_volume:"' . $vpsettings['settings']['disable_volume'] . '"  ,loop:"' . $loop . '"  ,cue: "' . $margs['cue'] . '"  ,embedded: "' . $margs['embedded'] . '"  ,preload_method:"' . $preload_method . '" ,design_animateplaypause:"' . $design_animateplaypause . '" ,skinwave_dynamicwaves:"' . $vpsettings['settings']['skinwave_dynamicwaves'] . '"  ,skinwave_enableSpectrum:"' . $vpsettings['settings']['skinwave_enablespectrum'] . '"  ,settings_backup_type:"' . $vpsettings['settings']['settings_backup_type'] . '"  ,skinwave_enableReflect:"' . $vpsettings['settings']['skinwave_enablereflect'] . '"';
        if(isset($vpsettings['settings']['playfrom'])){
          $fout.=',playfrom:"' . $vpsettings['settings']['playfrom'] . '"';
        }
        if(isset($vpsettings['settings']['default_volume'])){
          $fout.=',default_volume:"' . $vpsettings['settings']['default_volume'] . '"';
        }
        if(isset($vpsettings['settings']['disable_scrubbar'])){
          $fout.=',disable_scrub:"' . $vpsettings['settings']['disable_scrubbar'] . '"';
        }




        if(isset($margs['outer_comments_field']) && $margs['outer_comments_field']=='on'){



          $fout.=',skinwave_comments_mode_outer_selector: ".zoomsounds-comment-wrapper"';
        }
        if($this->mainoptions['mobile_disable_footer_player']=='on'){
          if(isset($margs['called_from']) && $margs['called_from']=='footer_player'){

            $fout.=',mobile_delete: "on"';

          }else{
            $fout.=',mobile_disable_fakeplayer: "on"';
          }
        }


        $fout.=',soundcloud_apikey:"' . $this->mainoptions['soundcloud_api_key'] . '"  ,skinwave_comments_enable:"' . $vpsettings['settings']['skinwave_comments_enable'] . '"';

        $fout.=',settings_php_handler:window.ajaxurl';
        if ($vpsettings['settings']['skinwave_comments_enable'] == 'on') {
          if (isset($current_user->data->user_nicename)) {
            $fout.=',skinwave_comments_account:"' . $current_user->data->user_nicename . '"';
            $fout.=',skinwave_comments_avatar:"' . $this->get_avatar_url(get_avatar($current_user->data->ID, 20)) . '"';
          }
        }




        if (isset($its['settings']['skinwave_mode']) && $its['settings']['skinwave_mode']) {
          $fout.=',skinwave_mode:"' . $its['settings']['skinwave_mode'] . '"';
        }


        $fout.=',skinwave_wave_mode:"' . $this->mainoptions['skinwave_wave_mode'] . '"';

        $this->mainoptions['color_waveformbg'] = str_replace('#','',$this->mainoptions['color_waveformbg']);

        if($this->mainoptions['skinwave_wave_mode']=='canvas'){
          $fout.=',pcm_data_try_to_generate: "'.$this->mainoptions['pcm_data_try_to_generate'].'"';
          $fout.=',pcm_notice: "'.$this->mainoptions['pcm_notice'].'"';
          $fout.=',design_color_bg: "'.$this->mainoptions['color_waveformbg'].'"';
          $fout.=',design_color_highlight: "'.$this->mainoptions['color_waveformprog'].'"';
          $fout.=',skinwave_wave_mode_canvas_waves_number: "'.$this->mainoptions['skinwave_wave_mode_canvas_waves_number'].'"';

          $fout.=',skinwave_wave_mode_canvas_waves_padding: "'.$this->mainoptions['skinwave_wave_mode_canvas_waves_padding'].'"';

          $fout.=',skinwave_wave_mode_canvas_reflection_size: "'.$this->mainoptions['skinwave_wave_mode_canvas_reflection_size'].'"';
        }


        if (isset($its['settings']['skinwave_wave_mode_canvas_mode']) && $its['settings']['skinwave_wave_mode_canvas_mode'] ) {
          $fout.=',skinwave_wave_mode_canvas_mode:"' . $its['settings']['skinwave_wave_mode_canvas_mode'] . '"';
        }


        if (isset($its['settings']['scrubbar_tweak_overflow_hidden']) && $its['settings']['scrubbar_tweak_overflow_hidden'] == 'on') {
          $fout.=',scrubbar_tweak_overflow_hidden:"' . $its['settings']['scrubbar_tweak_overflow_hidden'] . '"';
        }


        if (isset($its['settings']['preview_on_hover']) && $its['settings']['preview_on_hover'] ) {
          $fout.=',preview_on_hover:"' . $its['settings']['preview_on_hover'] . '"';
        }
        if (isset($its['settings']['player_navigation']) && $its['settings']['player_navigation']  && $its['settings']['player_navigation']!='default' ) {
          $fout.=',player_navigation:"' . $its['settings']['player_navigation'] . '"';
        }



        $fout.=',skinwave_comments_playerid:"' . $margs['playerid'] . '"';


        if($margs['js_settings_extrahtml_in_float_right']){
          $fout.=',settings_extrahtml_in_float_right: \''.$margs['js_settings_extrahtml_in_float_right'].'\'';


          if(strpos($margs['js_settings_extrahtml_in_float_right'], 'dzsap-multisharer-but')!==false){

            $this->sw_enable_multisharer = true;

          }

        }




        if(isset($vpsettings['settings']['restyle_player_over_400']) && $vpsettings['settings']['restyle_player_over_400']){
          $fout.=',restyle_player_over_400: "'.$vpsettings['settings']['restyle_player_over_400'].'"';
        }
        if(isset($vpsettings['settings']['restyle_player_under_400']) && $vpsettings['settings']['restyle_player_under_400']){
          $fout.=',restyle_player_under_400: "'.$vpsettings['settings']['restyle_player_under_400'].'"';
        }


        if(isset($vpsettings['settings']['enable_embed_button']) && ( $vpsettings['settings']['enable_embed_button']=='on' || $vpsettings['settings']['enable_embed_button']=='in_player_controls'  || $vpsettings['settings']['enable_embed_button']=='in_extra_html' ) && $margs['embedded']!='on'){
          $str_db = '';
//                echo 'ceva22'.$str;


          //<span class=\"dzstooltip transition-slidein arrow-bottom align-left skin-black \" style=\"width: 350px; \"><span style=\"max-height: 150px; overflow:hidden; display: block; white-space: normal; font-weight: normal\">{{embed_code}}</span> <span class=\"copy-embed-code-btn\"><i class=\"fa fa-clipboard\"></i> '.__('Copy Embed').'</span> </span>

          $fout.=',embed_code:"'.$embed_code.'"
';

          if($has_extra_html){

          }else{

            if( $vpsettings['settings']['enable_embed_button']=='on' || $vpsettings['settings']['enable_embed_button']=='in_player_controls' ){

              $fout.=',enable_embed_button:"'.'on'.'"';
            }

          }
        }



//                print_r($this->mainoptions);

        if($this->mainoptions['failsafe_repair_media_element']=='on'){
          $fout.=',failsafe_repair_media_element:1000';
        }

        if($this->mainoptions['construct_player_list_for_sync']=='on'){
          $fout.=',construct_player_list_for_sync:"'.$this->mainoptions['construct_player_list_for_sync'].'"';
        }

        $str_post_id = '';

        if($post){
          $str_post_id = '_'.$post->ID;
        }



        $fout.=',php_retriever:"' . $this->base_url . 'soundcloudretriever.php" ,swf_location:"' . $this->base_url . 'ap.swf"
,swffull_location:"' . $this->base_url . 'apfull.swf"';

        $fout.=$margs['extra_init_settings'];

        $fout.='}; ';


        $fout.=' dzsap_init(".ap_idx'.$str_post_id.'_'.$player_idx.'",settings_ap);';



        if($this->mainoptions['js_init_timeout']){
          $fout.='}, '.$this->mainoptions['js_init_timeout'].');';
        }

        $fout.=' }); ';


        //console.info("inited", $(".ap_idx'.$str_post_id.'_'.$player_idx.'"));

        if($margs['divinsteadofscript']!='on'){
          $fout.='</script>';
        }else{
          $fout.='</div>';
        }
      } else {
        // ------ zoombox open

        wp_enqueue_style('dzsulb', $this->base_url . 'libs/ultibox/ultibox.css');
        wp_enqueue_script('dzsulb', $this->base_url . 'libs/ultibox/ultibox.js');

        $fout.='<a href="' . $margs['source'] . '" data-sourceogg="' . $margs['sourceogg'] . '" data-waveformbg="' . $margs['waveformbg'] . '" data-waveformprog="' . $margs['waveformprog'] . '" data-type="' . $margs['type'] . '" data-coverimage="' . $margs['coverimage'] . '" class="zoombox effect-justopacity">' . $content . '</a>';



        if($margs['divinsteadofscript']!='on'){
          $fout.='<script>';
        }else{
          $fout.='<div class="toexecute">';
        }
        $fout.='(function(){
var auxap = jQuery(".audioplayer-tobe").last();
jQuery(document).ready(function ($){
var settings_ap = {
    design_skin: "' . $vpsettings['settings']['skin_ap'] . '"
    ,skinwave_dynamicwaves:"' . $vpsettings['settings']['skinwave_dynamicwaves'] . '"
    ,disable_volume:"' . $vpsettings['settings']['disable_volume'] . '"
    ,disable_volume:"' . $vpsettings['settings']['loop'] . '"
    ,skinwave_enableSpectrum:"' . $vpsettings['settings']['skinwave_enablespectrum'] . '"
    ,settings_backup_type:"' . $vpsettings['settings']['settings_backup_type'] . '"
    ,skinwave_enableReflect:"' . $vpsettings['settings']['skinwave_enablereflect'] . '"
    ,skinwave_comments_enable:"' . $vpsettings['settings']['skinwave_comments_enable'] . '"';

        $fout.=',settings_php_handler:window.ajaxurl';
        if ($vpsettings['settings']['skinwave_comments_enable'] == 'on') {
          if (isset($current_user->data->user_nicename)) {
            $fout.=',skinwave_comments_account:"' . $current_user->data->user_nicename . '"';
            $fout.=',skinwave_comments_avatar:"' . $this->get_avatar_url(get_avatar($current_user->data->ID, 20)) . '"';
            $fout.=',skinwave_comments_playerid:"' . $margs['playerid'] . '"';
          }
        }


        if(isset($vpsettings['settings']['disable_scrubbar'])){
          $fout.=',disable_scrub:"' . $vpsettings['settings']['disable_scrubbar'] . '"';
        }

        $fout.=',swf_location:"' . $this->base_url . 'ap.swf"
    ,swffull_location:"' . $this->base_url . 'apfull.swf"
};
$(".zoombox").zoomBox({audioplayer_settings: settings_ap});
});
})();';

        if($margs['divinsteadofscript']!='on'){
          $fout.='</script>';
        }else{
          $fout.='</div>';
        }


      }
    }



//        print_r($its); print_r($margs); echo 'alceva'.$fout;

    return $fout;
  }

  function get_avatar_url($arg) {
    preg_match("/src='(.*?)'/i", $arg, $matches);
    if (isset($matches[1])) {
      return $matches[1];
    }
    return '';
  }

  function log_event($arg) {
    $fil = dirname(__FILE__) . "/log.txt";
    $fh = @fopen($fil, 'a');
    @fwrite($fh, ($arg . "\n"));
    @fclose($fh);
  }


  function shortcode_showcase($pargs = array()) {


    //[zoomsounds_showcase feed_from="audio_items" ids="1,2,3"]
    $fout = '';

    $margs = array(
      'count' => '5',
      'feed_from' => 'audio_items',
      'mode' => 'scrollmenu',
      'style' => 'list',
      'desc_count' => 'default',
      'desc_readmore_markup' => 'default',
      'max_videos' => '',
      'cat' => '',
      'ids' => '',
    );



    if (!is_array($pargs)) {
      $pargs = array();
    }
    $margs = array_merge($margs, $pargs);





    $its = array();
    if($margs['feed_from']){

      if($margs['ids']){

      }
      $args['posts_per_page']= '-1';
      $args['post_type']= 'any';




      if($margs['ids']){
        $args['post__in']= explode(',',$margs['ids']);
      }

//            print_rr($margs['ids']);

      $query = new WP_Query($args);


//            print_rr($query);
//            print_rr($query->posts);
      $its = $this->transform_to_array_for_parse($query->posts, $margs);


//            print_rr($its);



      $margs = array(
        'ids' => '1'
      , 'embedded_in_zoombox' => 'off'
      , 'embedded' => 'off'
      , 'db' => 'main'
      );

//            if ($pargs == '') {
//                $atts = array();
//            }

//            $margs = array_merge($margs, $atts);

//            $po_array = explode(",", $margs['ids']);

      $fout.='[zoomsounds id="playlist_gallery" embedded="'.$margs['embedded'].'" extra_classes="from-wc-album" for_embed_ids="'.$margs['ids'].'"]';







      $this->front_scripts();



      $this->sliders_index++;


      $i = 0;
      $k = 0;
      $id = 'playlist_gallery';
      if (isset($margs['id'])) {
        $id = $margs['id'];
      }

      //echo 'ceva' . $id;
      for ($i = 0; $i < count($this->mainitems); $i++) {
        if ((isset($id)) && ($id == $this->mainitems[$i]['settings']['id']))
          $k = $i;
      }


      for ($i = 0; $i < count($this->mainitems); $i++) {
        if ((isset($id)) && ($id == $this->mainitems[$i]['settings']['id']))
          $k = $i;
      }


//        print_r($its);

      $enable_likes = 'off';

      $enable_views = 'off';
      $enable_downloads_counter = 'off';



      $its = array_reverse($its);

      foreach($its as $it){

//                $po = get_post($po_id);

//            print_r($po);

//                print_rr($it);



//            echo 'ceva2'.(get_post_meta($po_id,'_waveformprog',true));

//            print_r(wp_get_attachment_metadata($po_id));


        $po = get_post($it['id']);


        $title = $po->post_title;
        $desc = ' ';
        $title = ' ';
        $desc = $po->post_title;
//                $title = str_replace(array('"', '[',']'),'&quot;',$title);
//                $desc = $po->post_content;
//                $desc = str_replace(array('"', '[',']'),'&quot;',$desc);



        $src = $it['source'];





        if($this->mainoptions['try_to_hide_url']=='on'){



//                    print_r($_SESSION);

          $nonce = '{{generatenonce}}';


          $nonce = rand(0,10000);

          $id = $it['id'];








          $lab = 'dzsap_nonce_for_'.$id.'_ip_'.$_SERVER['REMOTE_ADDR'];

          $lab = $this->clean($lab);
          $_SESSION[$lab] = $nonce;

          $src = site_url().'/index.php?dzsap_action=get_track_source&id='.$id.'&'.$lab.'='.$nonce;
        }





        $sample_time_start=get_post_meta($it['id'],'dzsap_woo_sample_time_start',true);
        $sample_time_end=get_post_meta($it['id'],'dzsap_woo_sample_time_end',true);
        $sample_time_total=get_post_meta($it['id'],'dzsap_woo_sample_time_total',true);





        $fout.='[zoomsounds_player source="'.$src.'" config="playlist_player" playerid="'.$it['id'].'"  thumb="" autoplay="on" cue="on" enable_likes="'.$enable_likes.'" enable_views="'.$enable_views.'"  enable_downloads_counter="'.$enable_downloads_counter.'" songname="'.$title.'" artistname="'.$desc.'" init_player="off" called_from="just_for_vc_grouped"';


        if($sample_time_start){
          $fout.=' sample_time_start="'.$sample_time_start.'"';
        }
        if($sample_time_end){
          $fout.=' sample_time_end="'.$sample_time_end.'"';
        }
        if($sample_time_total){
          $fout.=' sample_time_total="'.$sample_time_total.'"';
        }

        $fout.=']';
      }
      $fout.='[/zoomsounds]';


//            echo 'shortcode - '.$fout;
//            echo 'do_shortcode - '.do_shortcode($fout);

      $fout=do_shortcode($fout);


      return $fout;

    }


  }
  function transform_to_array_for_parse($argits, $pargs = array()) {

    global $post;
    $margs = array(
      'type' => 'video_items',
      'mode' => 'posts',
    );

    if (!is_array($pargs)) {
      $pargs = array();
    }
    $margs = array_merge($margs, $pargs);


    $its = array();


//        print_r($argits);

    foreach ($argits as $it) {


//            print_r($it);


      $aux25 = array();

      $aux25['extra_classes'] = '';


      if ($margs['feed_from'] == 'audio_items') {
        $it_id = $it->ID;
        $aux25['id'] = $it->ID;
        $imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($it_id), "full");
//                echo 'ceva'; print_r($imgsrc);


//            print_r($author_data);


        if ($imgsrc) {

          if (is_array($imgsrc)) {
            $aux25['thumbnail'] = $imgsrc[0];
          } else {
            $aux25['thumbnail'] = $imgsrc;
          }

        } else {
          if (get_post_meta($it_id, 'dzsvp_thumb', true)) {
            $aux25['thumbnail'] = get_post_meta($it_id, 'dzsvp_thumb', true);
          }
        }


        $aux25['type'] = get_post_meta($it_id, 'dzsvp_item_type', true);
        $aux25['date'] = $it->post_date;


//                print_r($margs);

        if(isset($margs['orderby'])){

          if($margs['orderby']=='views'){

            $aux25['views'] = $this->get_views($it_id);
          }
        }


//                $aux = get_post_meta($it_id, 'dzsap_woo_product_track', true);


        $args = array();
        $aux = $this->get_track_source($it_id, $it_id, $args);





        $aux25['source'] = $aux;

//                echo 'aux - '.$aux;




        $aux25['title'] = $it->post_title;
        $aux25['id'] = $it_id;


        $aux25['permalink'] = get_permalink($it_id);
        $aux25['permalink_to_post'] = get_permalink($it_id);

//                if ($margs['linking_type'] == 'zoombox') {
//                    $aux25['permalink'] = $aux25['source'];
//                }

//                print_r($margs);


//                print_r($it);


        $maxlen = $margs['desc_count'];

//            print_r($margs);

        if ($maxlen == 'default') {

          if ($margs['mode'] == 'scrollmenu') {
            $maxlen = 50;
          }
        }
        if ($maxlen == 'default') {
          $maxlen = 100;
        }


        if ($margs['desc_readmore_markup'] == 'default') {
          if ($margs['mode'] == 'scrollmenu') {
            $margs['desc_readmore_markup'] = ' <span style="opacity:0.75;">[...]</span>';
          }
        }
        if ($margs['desc_readmore_markup'] == 'default') {
          $margs['desc_readmore_markup'] = '';
        }





//                $aux25['description'] = $this->sanitize_description($it->post_content, array('desc_count' => intval($maxlen), 'striptags' => 'on', 'try_to_close_unclosed_tags' => 'on', 'desc_readmore_markup' => $margs['desc_readmore_markup'],));


        if ($post && $post->ID === $it_id) {
          $aux25['extra_classes'] .= ' active';
        }

//                echo 'aux25';
//                print_rr($aux25);
        array_push($its, $aux25);
      }


    }


    return $its;

  }



  function shortcode_playlist($atts){

    //[playlist ids="2,3,4"]

    global $current_user;
    $fout = '';
    $iout = ''; //items parse

    $margs = array(
      'ids' => '1'
    , 'embedded_in_zoombox' => 'off'
    , 'embedded' => 'off'
    , 'db' => 'main'
    );

    if ($atts == '') {
      $atts = array();
    }

    $margs = array_merge($margs, $atts);

    $po_array = explode(",", $margs['ids']);

    $fout.='[zoomsounds id="playlist_gallery" embedded="'.$margs['embedded'].'" for_embed_ids="'.$margs['ids'].'"]';






    //===setting up the db
    $currDb = '';
    if (isset($margs['db']) && $margs['db'] != '') {
      $this->currDb = $margs['db'];
      $currDb = $this->currDb;
    }
    $this->dbs = get_option($this->dbname_dbs);

    //echo 'ceva'; print_r($this->dbs);
    if ($currDb != 'main' && $currDb != '') {
      $this->dbname_mainitems.='-' . $currDb;
      $this->mainitems = get_option($this->dbname_mainitems);
    }
    //===setting up the db END




    if ($this->mainitems == '') {
      return;
    }

    $this->front_scripts();



    $this->sliders_index++;


    $i = 0;
    $k = 0;
    $id = 'playlist_gallery';
    if (isset($margs['id'])) {
      $id = $margs['id'];
    }

    //echo 'ceva' . $id;
    for ($i = 0; $i < count($this->mainitems); $i++) {
      if ((isset($id)) && ($id == $this->mainitems[$i]['settings']['id']))
        $k = $i;
    }


    for ($i = 0; $i < count($this->mainitems); $i++) {
      if ((isset($id)) && ($id == $this->mainitems[$i]['settings']['id']))
        $k = $i;
    }

    $its = $this->mainitems[$k];


//        print_r($its);

    $enable_likes = 'off';

    $enable_views = 'off';
    $enable_downloads_counter = 'off';

    if($its){
      if($its['settings']['enable_views']){
        $enable_views = $its['settings']['enable_views'];
      }
      if($its['settings']['enable_likes']){
        $enable_likes = $its['settings']['enable_likes'];
      }
      if($its['settings']['enable_downloads_counter']){
        $enable_downloads_counter = $its['settings']['enable_downloads_counter'];
      }
    }



    foreach($po_array as $po_id){

      $po = get_post($po_id);

//            print_r($po);


      $waveformbg=$this->base_url.'waves/scrubbg_default.png';
      $waveformprog=$this->base_url.'waves/scrubprog_default.png';

      if(get_post_meta($po_id,'_waveformbg',true)){
        $waveformbg = get_post_meta($po_id,'_waveformbg',true);
      }
      if(get_post_meta($po_id,'_waveformprog',true)){
        $waveformprog = get_post_meta($po_id,'_waveformprog',true);
      }

//            echo 'ceva2'.(get_post_meta($po_id,'_waveformprog',true));

//            print_r(wp_get_attachment_metadata($po_id));


      $title = $po->post_title;
      $title = str_replace(array('"', '[',']'),'&quot;',$title);
      $desc = $po->post_content;
      $desc = str_replace(array('"', '[',']'),'&quot;',$desc);
      $fout.='[zoomsounds_player source="'.$po->guid.'" config="playlist_player" playerid="'.$po_id.'" waveformbg="'.$waveformbg.'" waveformprog="'.$waveformprog.'" thumb="" autoplay="on" cue="on" enable_likes="'.$enable_likes.'" enable_views="'.$enable_views.'"  enable_downloads_counter="'.$enable_downloads_counter.'" songname="'.$title.'" artistname="'.$desc.'" init_player="off"]';
    }
    $fout.='[/zoomsounds]';



    $fout=do_shortcode($fout);

//        print_r($margs);

    return $fout;
  }


  function show_shortcode($atts=array(), $content=null) {

    //[zoomsounds id="thheid"]

    global  $current_user;
    $fout = '';
    $iout = ''; //items parse

    $margs = array(
      'id' => 'default'
    , 'db' => ''
    , 'category' => ''
    , 'extra_classes' => ''
    , 'fullscreen' => 'off'
    , 'settings_separation_mode' => 'normal'  // === normal ( no pagination ) or pages or scroll or button
    , 'settings_separation_pages_number' => '5'//=== the number of items per 'page'
    , 'settings_separation_paged' => '0'//=== the page number
    , 'return_onlyitems' => 'off' // ==return only the items ( used by pagination )
    , 'playerid' => ''
    , 'embedded' => 'off'
    , 'divinsteadofscript' => 'off'
    , 'width' => '-1'
    , 'height' => '-1'
    , 'embedded_in_zoombox' => 'off'
    , 'for_embed_ids' => ''
    , 'single' => 'off'
    , 'play_target' => 'default'
    );

    if ($atts == '') {
      $atts = array();
    }

    $margs = array_merge($margs, $atts);


    //===setting up the db
    $currDb = '';
    if (isset($margs['db']) && $margs['db'] != '') {
      $this->currDb = $margs['db'];
      $currDb = $this->currDb;
    }
    $this->dbs = get_option($this->dbname_dbs);

    //echo 'ceva'; print_r($this->dbs);
    if ($currDb != 'main' && $currDb != '') {
      $this->dbname_mainitems.='-' . $currDb;
      $this->mainitems = get_option($this->dbname_mainitems);
    }
    //===setting up the db END




    if ($this->mainitems == '') {
      return;
    }

    $this->front_scripts();



    $this->sliders_index++;


    $i = 0;
    $k = 0;
    $id = 'default';
    if (isset($margs['id'])) {
      $id = $margs['id'];
    }

    //echo 'ceva' . $id;
    for ($i = 0; $i < count($this->mainitems); $i++) {
      if ((isset($id)) && ($id == $this->mainitems[$i]['settings']['id']))
        $k = $i;
    }

    $its = $this->mainitems[$k];




    //print_r($this->mainitems);
    //=== audio player configuration setup
    $vpsettingsdefault = array(
      'id' => 'default',
      'skin_ap' => 'skin-default',
      'settings_backup_type' => 'full',
      'skinwave_dynamicwaves' => 'off',
      'skinwave_enablespectrum' => 'off',
      'skinwave_enablereflect' => 'on',
      'skinwave_comments_enable' => 'off',
      'skinwave_mode' => 'normal',
      'playfrom' => 'default',
      'enable_embed_button' => 'off',
      'loop' => 'off',
    );

    $vpsettings = array();


    $i = 0;
    $vpconfig_k = -1;
    $vpconfig_id = $its['settings']['vpconfig'];
    for ($i = 0; $i < count($this->mainitems_configs); $i++) {
      if ((isset($vpconfig_id)) && ($vpconfig_id == $this->mainitems_configs[$i]['settings']['id'])) {
        $vpconfig_k = $i;
      }
    }

    if ($vpconfig_k > -1) {
      $vpsettings = $this->mainitems_configs[$vpconfig_k];
    } else {
      $vpsettings['settings'] = $vpsettingsdefault;
    }

    //print_r($this->mainitems_configs); echo $its['settings']['vpconfig'];


    if (!isset($vpsettings['settings']) || $vpsettings['settings'] == '') {
      $vpsettings['settings'] = array();
    }


    //print_r($vpsettings);

    $vpsettings['settings'] = array_merge($vpsettingsdefault, $vpsettings['settings']);

    unset($vpsettings['settings']['id']);
    //print_r($vpsettings);
    $its['settings'] = array_merge($its['settings'], $vpsettings['settings']);


    //this works only for the zoomsounds_player shortcode ==== not anymore hahaha
//        $its['settings']['skinwave_comments_enable'] = 'off';
    //print_r($its);
    // ===== some sanitizing
    $tw = $its['settings']['width'];
    $th = $its['settings']['height'];


    if($margs['width']!='-1'){
      $tw = $margs['width'];
    }
    if($margs['height']!='-1'){
      $th = $margs['height'];
    }






    $str_tw = '';
    $str_th = '';

    if ($tw != '') {
      $str_tw.='width: ';
      if (strpos($tw, "%") === false && strpos($tw, "auto") === false) {
        $str_tw .= $tw . 'px';
      }
      $str_tw.=';';
    }

    if ($th != '') {
      $str_th.='height: ';
      if (strpos($th, "%") === false && $th != 'auto' && $th != '') {
        $str_th .= $th . 'px';
      }
      $str_th.=';';
    }


    $galleryskin = 'skin-wave';

    if(isset($its['settings']['galleryskin'])){
      $galleryskin=$its['settings']['galleryskin'];
    }


//        print_rr($its);


    if(isset($its['settings']['colorhighlight']) && $its['settings']['colorhighlight']){

      $fout.='<style class="audiogallery-style">';
      if($its['settings']['skin_ap']=='skin-wave'){


        $fout.='.audiogallery#ag'.$this->sliders_index.' .audioplayer .player-but .the-icon-bg, .audiogallery#ag'.$this->sliders_index.' .audioplayer .playbtn .the-icon-bg , .audiogallery#ag'.$this->sliders_index.' .audioplayer .pausebtn .the-icon-bg, .audiogallery#ag'.$this->sliders_index.' .audioplayer.skin-wave .ap-controls .scrubbar .scrubBox-hover { background-color: #'.$its['settings']['colorhighlight'].';}  
                .audiogallery#ag'.$this->sliders_index.' .audioplayer .meta-artist .the-artist { color: #'.$its['settings']['colorhighlight'].';}  
                
                ';
      }


      if($its['settings']['skin_ap']=='skin-pro'){

        $selector = '.audiogallery#ag'.$this->sliders_index.' .audioplayer.skin-pro';
        $fout.=$selector.' .ap-controls .scrubbar .scrub-prog{  background-color: #'.$its['settings']['colorhighlight'].';  }';
      }

      $fout.='</style>';

    }


    $fout.='<div id="ag' . $this->sliders_index . '" class="audiogallery '.$galleryskin.' id_' . $its['settings']['id'] . ' ';


    if($margs['extra_classes']){
      $fout.=' '.$margs['extra_classes'];
    }




    $fout.='" style="background-color:' . $its['settings']['bgcolor'] . ';' . $str_tw . '' . $str_th . '">';



    //$fout.=$this->parse_items($its, $margs);

//        print_r($its); print_r($margs);
    if($content){


//            echo 'do_shortcode(content); '; $content. ' '.do_shortcode($content);

      $iout.=do_shortcode($content);
    }else{

      $args = array(
        'called_from' => 'gallery',
        'gallery_skin' => $galleryskin,
      );
      $args = array_merge($vpsettings['settings'], $args);
      $args = array_merge($args, $margs);

//            print_rr($its);

      $iout.=$this->parse_items($its, $args);
    }

    $fout.='<div class="items">';
    $fout.=$iout;


    $fout.='</div>';
    $fout.='</div>';

    if($margs['divinsteadofscript']!='on'){
      $fout.='<script>';
    }else{
      $fout.='<div class="toexecute">';
    }



    $fout.='jQuery(document).ready(function ($) {
        var settings_ap = {
            design_skin: "' . $its['settings']['skin_ap'] . '"
            ,skinwave_dynamicwaves:"' . $its['settings']['skinwave_dynamicwaves'] . '"
            ,skinwave_enableSpectrum:"' . $its['settings']['skinwave_enablespectrum'] . '"
            ,settings_backup_type:"' . $its['settings']['settings_backup_type'] . '"
            ,skinwave_enableReflect:"' . $its['settings']['skinwave_enablereflect'] . '"
            ,skinwave_comments_enable:"' . $its['settings']['skinwave_comments_enable'] . '"
            ,soundcloud_apikey:"' . $this->mainoptions['soundcloud_api_key'] . '"
            ,php_retriever:"' . $this->base_url . 'soundcloudretriever.php"
            ,swf_location:"' . $this->base_url . 'ap.swf"
            ,swffull_location:"' . $this->base_url . 'apfull.swf"';


    if(isset($its['settings']['playfrom'])){
      $fout.=',playfrom:"' . $its['settings']['playfrom'] . '"';
    }
    if(isset($vpsettings['settings']['default_volume'])){
      $fout.=',default_volume:"' . $vpsettings['settings']['default_volume'] . '"';
    }
    if(isset($its['settings']['disable_volume'])){
      $fout.=',disable_volume:"' . $its['settings']['disable_volume'] . '"';
    }
    if(isset($its['settings']['loop'])){
      $fout.=',loop:"' . $its['settings']['loop'] . '"';
    }
    if(isset($its['settings']['enable_embed_button']) && ( $its['settings']['enable_embed_button']=='on' || $vpsettings['settings']['enable_embed_button']=='in_player_controls' ) ){
      $str_db = '';
      if($this->currDb!=''){
        $str_db='&db=' . $this->currDb . '';
      }
      if($margs['id']=='playlist_gallery'){
//                $str = '<iframe src="' . $this->base_url . 'bridge.php?type=playlist&ids=' . $margs['for_embed_ids'] . ''.$str_db.'" width="100%" height="'.$its['settings']['height'].'" style="overflow:hidden; transition: height 0.5s ease-out;" scrolling="no" frameborder="0"></iframe>';
        $str = '<iframe src="' . site_url() . '?action=zoomsounds-embedtype=playlist&ids=' . $margs['for_embed_ids'] . ''.$str_db.'" width="100%" height="'.$its['settings']['height'].'" style="overflow:hidden; transition: height 0.5s ease-out;" scrolling="no" frameborder="0"></iframe>';
      }else{
        $str = '<iframe src="' . site_url() . '?action=zoomsounds-embed&type=gallery&id=' . $its['settings']['id'] . ''.$str_db.'" width="100%" height="'.$its['settings']['height'].'" style="overflow:hidden; transition: height 0.5s ease-out;" scrolling="no" frameborder="0"></iframe>';
      }


      $str = str_replace('"',"'", $str);
      $fout.=',embed_code:"'.htmlentities($str, ENT_QUOTES).'"';
      $fout.=',enable_embed_button:"'.'on'.'"';
    }

    $fout.=',settings_php_handler:window.ajaxurl';
    if ($its['settings']['skinwave_comments_enable'] == 'on') {
      if (isset($current_user->data->user_nicename)) {
        $fout.=',skinwave_comments_account:"' . $current_user->data->user_nicename . '"';
        $fout.=',skinwave_comments_avatar:"' . $this->get_avatar_url(get_avatar($current_user->data->ID, 20)) . '"';
        $fout.=',skinwave_comments_playerid:"' . $margs['playerid'] . '"';
      }
    }
    if (isset($its['settings']['skinwave_mode']) && $its['settings']['skinwave_mode'] == 'small') {
      $fout.=',skinwave_mode:"' . $its['settings']['skinwave_mode'] . '"';
    }
    $fout.=',skinwave_wave_mode:"' . $this->mainoptions['skinwave_wave_mode'] . '"';

    $this->mainoptions['color_waveformbg'] = str_replace('#','',$this->mainoptions['color_waveformbg']);
    if($this->mainoptions['skinwave_wave_mode']=='canvas'){
      $fout.=',pcm_data_try_to_generate: "'.$this->mainoptions['pcm_data_try_to_generate'].'"';
      $fout.=',pcm_notice: "'.$this->mainoptions['pcm_notice'].'"';
      $fout.=',design_color_bg: "'.$this->mainoptions['color_waveformbg'].'"';
      $fout.=',design_color_highlight: "'.$this->mainoptions['color_waveformprog'].'"';
      $fout.=',skinwave_wave_mode_canvas_waves_number: "'.$this->mainoptions['skinwave_wave_mode_canvas_waves_number'].'"';
      $fout.=',skinwave_wave_mode_canvas_waves_padding: "'.$this->mainoptions['skinwave_wave_mode_canvas_waves_padding'].'"';
      $fout.=',skinwave_wave_mode_canvas_reflection_size: "'.$this->mainoptions['skinwave_wave_mode_canvas_reflection_size'].'"';



      if (isset($its['settings']['skinwave_wave_mode_canvas_mode']) && $its['settings']['skinwave_wave_mode_canvas_mode'] ) {
        $fout.=',skinwave_wave_mode_canvas_mode:"' . $its['settings']['skinwave_wave_mode_canvas_mode'] . '"';
      }


    }




    $preload_method = 'metadata';
    $design_animateplaypause = 'off';

    if(isset($vpsettings['settings']['preload_method'])){
      $preload_method = $vpsettings['settings']['preload_method'];
    }


    if(isset($vpsettings['settings']['restyle_player_over_400']) && $vpsettings['settings']['restyle_player_over_400']){
      $fout.=',restyle_player_over_400: "'.$vpsettings['settings']['restyle_player_over_400'].'"';
    }
    if(isset($vpsettings['settings']['restyle_player_under_400']) && $vpsettings['settings']['restyle_player_under_400']){
      $fout.=',restyle_player_under_400: "'.$vpsettings['settings']['restyle_player_under_400'].'"';
    }

    $fout.=',preload_method:"' . $vpsettings['settings']['preload_method'] . '"';

//                print_r($this->mainoptions);

    if($this->mainoptions['failsafe_repair_media_element']=='on'){
      $fout.=',failsafe_repair_media_element:1000';
    }

    if ($this->mainoptions['settings_trigger_resize'] == 'on') {
      $fout.=',settings_trigger_resize:"1000"';
    };

    $fout.='};
        dzsag_init("#ag' . $this->sliders_index . '",{
            "transition":"fade"
            ,"autoplay" : "' . $its['settings']['autoplay'] . '"
            ,"embedded" : "' . $margs['embedded'] . '"
            ,"autoplayNext" : "' . $its['settings']['autoplaynext'] . '"
            ,design_menu_position :"' . $its['settings']['menuposition'] . '"
            ,"settings_ap":settings_ap';


//        print_rr($its);

    if (isset($its['settings']['disable_player_navigation'])) {
      $fout.=',disable_player_navigation:"' . $its['settings']['disable_player_navigation'] . '"';
    }
    if (isset($its['settings']['player_navigation'])) {
      $fout.=',player_navigation:"' . $its['settings']['player_navigation'] . '"';
    }
    if (isset($its['settings']['cuefirstmedia'])) {
      $fout.=',cueFirstMedia:"' . $its['settings']['cuefirstmedia'] . '"';
    }
    if (isset($its['settings']['mode'])) {
      $fout.=',settings_mode:"' . $its['settings']['mode'] . '"';
    }
    if (isset($its['settings']['settings_mode_showall_show_number'])) {
      $fout.=',settings_mode_showall_show_number:"' . $its['settings']['settings_mode_showall_show_number'] . '"';
    }



    if(isset($_GET['fromsharer']) && $_GET['fromsharer']=='on'){
      if(isset($_GET['audiogallery_startitem_ag1']) && $_GET['audiogallery_startitem_ag1']){


        $its['settings']['design_menu_state'] = 'closed';
      }
    }

    if (isset($its['settings']['design_menu_state'])) {
      $fout.=',design_menu_state:"' . $its['settings']['design_menu_state'] . '"';
    }
    if (isset($its['settings']['design_menu_height']) && $its['settings']['design_menu_height']!='') {
      $fout.=',design_menu_height:"' . $its['settings']['design_menu_height'] . '"';
    }


    if (isset($its['settings']['design_menu_show_player_state_button'])) {
      $fout.=',design_menu_show_player_state_button:"' . $its['settings']['design_menu_show_player_state_button'] . '"';
    }

    if (isset($its['settings']['settings_enable_linking'])) {
      $fout.=',settings_enable_linking:"' . $its['settings']['settings_enable_linking'] . '"';
    }

    if($this->mainoptions['force_autoplay_when_coming_from_share_link']=='on'){
      $fout.=',force_autoplay_when_coming_from_share_link: "on"';
    }


    $fout.='});';

    $fout.='});';

    if($margs['divinsteadofscript']!='on'){
      $fout.='</script>';
    }else{
      $fout.='</div>';
    }


//end document ready an script



    wp_enqueue_style('fontawesome','https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');

    if ($margs['return_onlyitems'] != 'on') {
      return $fout;
    } else {
      return $iout;
    }




    //echo $k;
  }


  function show_shortcode_lightbox($atts,$content = null) {

    $fout = '';
    //$this->sliders_index++;

    $this->front_scripts();

    wp_enqueue_style('dzsulb',$this->base_url.'libs/ultibox/ultibox.css');
    wp_enqueue_script('dzsulb',$this->base_url.'libs/ultibox/ultibox.js');

    $args = array(
      'id' => 'default'
    ,'db' => ''
    ,'category' => ''
    ,'width' => ''
    ,'height' => ''
    ,'gallerywidth' => '800'
    ,'galleryheight' => '370'
    );
    $args = array_merge($args,$atts);
    $fout.='<div class="ultibox"';

    if ($args['width'] != '') {
      $fout.=' data-width="'.$args['width'].'"';
    }
    if ($args['height'] != '') {
      $fout.=' data-height="'.$args['height'].'"';
    }
    if ($args['gallerywidth'] != '') {
      $fout.=' data-bigwidth="'.$args['gallerywidth'].'"';
    }
    if ($args['galleryheight'] != '') {
      $fout.=' data-bigheight="'.$args['galleryheight'].'"';
    }

    $fout.='data-src="'.$this->base_url.'retriever.php?id='.$args['id'].'" data-type="ajax">'.$content.'</div>';
    $fout.='<script>
jQuery(document).ready(function($){
$(".zoombox").zoomBox();
});
</script>';

    return $fout;
  }


  function get_soundcloud_track_source($che){
    $fout = '';

    $sw_was_cached = false;


    $cacher = get_option('dzsap_cache_soundcloudtracks');

    if(is_array($cacher)==false){
      $cacher = array();
    }



    if(isset($cacher[$che['soundcloud_track_id']])){
      $fout = $cacher[$che['soundcloud_track_id']]['source'];
      $sw_was_cached=true;
    }

//        print_r($cacher); echo ' is cached - '.$sw_was_cached.'||';


    if($sw_was_cached==false){

      $aux = DZSHelpers::get_contents('https://api.soundcloud.com/tracks/'.$che['soundcloud_track_id'].'.json?secret_token='.$che['soundcloud_secret_token'].'&client_id='.$this->mainoptions['soundcloud_api_key']);


      $auxa = json_decode($aux);



      $fout = $auxa->stream_url.'&client_id='.$this->mainoptions['soundcloud_api_key'];


      $cacher[$che['soundcloud_track_id']] = array(
        'source'=>$fout
      );


      if($fout){

        update_option('dzsap_cache_soundcloudtracks', $cacher);
      }


    }

    return $fout;
  }

  function parse_items($its, $pargs = array()) {
    //====returns only the html5 gallery items

    global $post;

    $fout = '';
    $start_nr = 0; // === the i start nr
    $end_nr = count($its); // === the i start nr
    $nr_per_page = 5;
    $nr_items = count($its);

    $margs = array(
      'menu_facebook_share'=>'auto',
      'menu_like_button'=>'auto',
      'gallery_skin'=>'skin-wave',
      'called_from'=>'skin-wave',
      'skinwave_mode'=>'normal',
      'single'=>'off',
      'wrapper_image' => '',
      'extra_classes' => '',
      'wrapper_image_type' => '', // zoomsounds-wrapper-bg-bellow or zoomsounds-wrapper-bg-center
    );

    $margs = array_merge($margs, $pargs);

//        print_rr($margs);

    if (isset($its['settings'])) {
      $nr_items--;
      $end_nr--;

      if(isset($its['settings']['enable_views'])==false){
        $its['settings']['enable_views'] = 'off';
      }
      if(isset($its['settings']['enable_likes'])==false){
        $its['settings']['enable_likes'] = 'off';
      }
      if(isset($its['settings']['enable_rates'])==false){
        $its['settings']['enable_rates'] = 'off';
      }
      if(isset($its['settings']['enable_downloads_counter'])==false){
        $its['settings']['enable_downloads_counter'] = 'off';
      }


      if(isset($margs['enable_views']) && $margs['enable_views']==='on'){
        $its['settings']['enable_views'] = 'on';
      }
      if(isset($margs['enable_downloads_counter']) && $margs['enable_downloads_counter']==='on'){
        $its['settings']['enable_downloads_counter'] = 'on';
      }

      if(isset($margs['enable_likes']) && $margs['enable_likes']==='on'){
        $its['settings']['enable_likes'] = 'on';
      }
      if(isset($margs['enable_rates']) && $margs['enable_rates']==='on'){
        $its['settings']['enable_rates'] = 'on';
      }
      if($margs['single']=='on' && isset($its['settings']['id']) && $its['settings']['id']){
        $its['settings']['vpconfig'] = $its['settings']['id'];
      }


      if(isset($its['settings']['enable_alternate_layout']) && $its['settings']['enable_alternate_layout']==='on'){
        $margs['enable_alternate_layout'] = 'on';
        $margs['skinwave_mode']='alternate';
      }
    }


//        echo 'parsed string: ';
//        print_r($its); print_r($margs);



//        echo '$start_nr - '.$start_nr;
//        echo '$end_nr - '.$end_nr;

    for ($i = $start_nr; $i < $end_nr; $i++) {

      $che = array(
        'menu_artistname' => 'default',
        'menu_songname' => 'default',
        'menu_extrahtml' => '',
      );


      if (is_array($its[$i]) == false) {
        $its[$i] = array();
      }

      $che = array_merge($che, $its[$i]);


      $meta = array();

      $playerid = '';

//            echo 'che[source] - > '.$che['source'].' ... ';

      if($che['source'] && is_numeric($che['source'])){
        $player_post_id = intval($che['source']);
        $player_post = get_post(intval($che['source']));


//                echo 'che[source] - > '.$che['source'].' ... ';
//                print_r($player_post);

        if($player_post && $player_post->post_type=='attachment'){
          $media = wp_get_attachment_url($player_post_id);

          $che['source'] = $media;
          if($playerid){

          }else{
            $playerid = $player_post_id;
            $che['playerid'] = $player_post_id;
          }

//                    print_r($media);
        }
      }


//            print_rr($che);



      if (isset($che['playerid']) && $che['playerid'] != '') {
        $playerid = $che['playerid'];
      }


      if ($playerid == '' && isset($che['linktomediafile']) && $che['linktomediafile'] != '') {
        $playerid = $che['linktomediafile'];
      }



      $po = null;
      if ($playerid) {
        $po = get_post($playerid);
//                print_r($po);


        $meta = wp_get_attachment_metadata($playerid);





//                echo 'meta ( '.$playerid.' ) - '; print_rr($meta);
//                echo 'post ( '.$playerid.' ) - '; print_rr($po);








        if(isset($che['linktomediafile']) && $che['linktomediafile'] && $this->mainoptions['try_to_hide_url']=='on'){

          $nonce = rand(0,10000);

          $id = $playerid;


//                    print_rr($_SERVER);

          $lab = 'dzsap_nonce_for_'.$id.'_ip_'.$_SERVER['REMOTE_ADDR'];

          $lab = $this->clean($lab);


//                    $_SESSION[$lab] = $nonce;



          $nonce = '{{generatenonce}}';


//                    print_r($_SESSION);

          $src = site_url().'/index.php?dzsap_action=generatenonce&id='.$id.'&'.$lab.'='.$nonce;

          $che['source'] = $src;
        }



        if(@wp_get_attachment_url($playerid)){
          if ($che['source'] == ''){

            $che['source'] = @wp_get_attachment_url($playerid);
          }
        }

        if ($che['source'] == '' && $po) {
          $che['source'] = $po->guid;
//                    print_r($che);
        }



        if ((!isset($che['artistname_from_meta']) || $che['artistname_from_meta'] == '')) {
//                    print_r($meta);
//                    print_r($meta['artist']);


          if(isset($meta['artist'])){

            $che['artistname_from_meta']=$meta['artist'];
          }
        };
        if ((!isset($che['songname_from_meta']) || $che['songname_from_meta'] == '')) {
//                    print_r($meta);
//                    print_r($meta['artist']);


          if(isset($meta['title'])){

            $che['songname_from_meta']=$meta['title'];
          }
        };
        if ((!isset($che['publisher']) || $che['publisher'] == '')) {
//                    print_r($meta);
//                    print_r($meta['artist']);


          if(isset($meta['publisher'])){

            $che['publisher']=$meta['publisher'];
          }
        };


        if ((!isset($che['waveformbg']) || $che['waveformbg'] == '') && $po && get_post_meta($po->ID, '_waveformbg', true) != '') {
          $che['waveformbg'] = get_post_meta($po->ID, '_waveformbg', true);
        };


        if ((!isset($che['waveformprog']) || $che['waveformprog'] == '') && $po && get_post_meta($po->ID, '_waveformprog', true) != '') {
          $che['waveformprog'] = get_post_meta($po->ID, '_waveformprog', true);
        };


        if ( (isset($che['thumb'])==false || $che['thumb'] == '') && isset($po)) {


//                    $che['thumb'] = get_post_meta($po->ID, '_dzsap-thumb', true);

          if(get_post_meta($po->ID, '_dzsap-thumb',true)){

            $che['thumb'] =  get_post_meta($po->ID, '_dzsap-thumb',true);
          }else{

          }
        };


        if ($che['sourceogg'] == '' && isset($po) &&  get_post_meta($po->ID, '_dzsap_sourceogg', true) != '') {
          $che['sourceogg'] = get_post_meta($po->ID, '_dzsap_sourceogg', true);
        };
      }




      if(isset($che['artistname_from_meta'])){
        if($che['artistname_from_meta'] && $che['artistname_from_meta']!='default') {
          if ($che['menu_artistname'] == '' || $che['menu_artistname'] == 'default') {
            $che['menu_artistname'] = $che['artistname_from_meta'];
          }
        }
      }
      if(isset($che['songname_from_meta'])){
        if($che['songname_from_meta'] && $che['songname_from_meta']!='default'){

          if($che['menu_songname']==='' || $che['menu_songname']=='default'){

            $che['menu_songname'] = $che['songname_from_meta'];
          }
        }
      }


      $type = 'audio';

      if (isset($che['type']) && $che['type'] != '') {
        $type = $che['type'];
      }

      if ($type == 'inline') {
        $fout.=$che['source'];
        continue;
      }


      if ($che['source'] == '' || $che['source'] == ' ') {
        continue;
      }
//            print_r($che); echo $playerid;



      if(isset($_GET['fromsharer']) && $_GET['fromsharer']=='on'){
        if(isset($_GET['audiogallery_startitem_ag1']) && $_GET['audiogallery_startitem_ag1']){


          if($i==$_GET['audiogallery_startitem_ag1']){
//            print_rr($che);

            $this->og_data = array(
              'title'=>$che['menu_songname'],
              'image'=>$che['thumb'],
              'description'=>__("by").' '.$che['menu_artistname'],
            );
          }
        }
      }

      if(strpos($che['source'], 'soundcloud.com')!==false){
        if(isset($che['soundcloud_track_id']) && isset($che['soundcloud_secret_token']) && $che['soundcloud_track_id'] && $che['soundcloud_secret_token']){


//                print_r($auxa);

          $che['source']=$this->get_soundcloud_track_source($che);
//                    $che['type']='audio';

          if($type=='soundcloud'){
            $type = 'audio';
          }
        }
      }



      $the_player_id='';

      if($playerid){

        $the_player_id = 'ap' . $playerid . '';
      }
      if(isset($margs['player_id']) && $margs['player_id']){
//                print_r($margs);
        $the_player_id = $margs['player_id'];
      }


//            echo 'hmm - '; print_rr($margs);  print_rr($its);



      if(isset($margs['called_from']) && ( $margs['called_from']=='player' || $margs['called_from']=='footer_player' ) && isset($margs['colorhighlight']) && $margs['colorhighlight']){
        $fout.='<style class="player-custom-style">';
        if($margs['skin_ap']=='skin-wave'){

//                    print_r($this);

          $selector = '.audioplayer.skin-wave#'.$the_player_id;

          if(isset($its['settings']['button_aspect'])){



            if($its['settings']['button_aspect']=='default'){

              $fout.=$selector.' .ap-controls .con-playpause .playbtn , '.$selector.' .ap-controls .con-playpause .pausebtn { background-color: #'.$margs['colorhighlight'].';} ';
            }



            if($its['settings']['button_aspect']=='button-aspect-noir button-aspect-noir--filled'){
              $fout.=' '.$selector.' .player-but .the-icon-bg, '.$selector.' .playbtn .the-icon-bg , '.$selector.' .pausebtn .the-icon-bg,  '.$selector.' .ap-controls .scrubbar .scrubBox-hover , '.$selector.' .volume_active { background-color: #'.$margs['colorhighlight'].'; border-color: #'.$margs['colorhighlight'].';}';
            }
          }

        }

//                print_rr($margs);
        if($margs['skin_ap']=='skin-pro'){

          $selector = '.audioplayer.skin-pro';

          if(isset($its['settings']['vpconfig'])){
            $selector.='.'.$its['settings']['vpconfig'];
          }

          $fout.=$selector.' .ap-controls .scrubbar .scrub-prog{  background-color: #'.$margs['colorhighlight'].';  }';
        }




        $fout.=' </style>';
      }


      $fout.='<div class="audioplayer-tobe';


      $str_post_id = '';

      if($post){
        $str_post_id = '_'.$post->ID;
      }

      if(isset( $its[$i]['player_index'] ) && $its[$i]['player_index']){
        $fout.=' ap_idx'.$str_post_id.'_'.$its[$i]['player_index'];
      }

      if(isset($margs['single']) && $margs['single']=='on'){
        $fout.=' is-single-player';
      }

//            print_r($che);
//            print_r($its);

//            print_r($its['settings']);
//            print_r($margs);

      if($its && $its['settings'] && isset($its['settings']['vpconfig']) && $its['settings']['vpconfig']){
        $aux = str_replace(' ','-',$its['settings']['vpconfig']);
        $fout.=' apconfig-'.$aux;


//                print_r($margs);
//                print_r($its);



        if(isset($margs['skin_ap']) && $margs['skin_ap']){


          if($margs['called_from']=='gallery'){

            $fout.=' '.$margs['skin_ap'];
          }


        }

//                print_r($its['settings']);

        if(isset($its['settings']['button_aspect']) && $its['settings']['button_aspect']!='default'){
          $fout.=' '.$its['settings']['button_aspect'];

          if(isset($its['settings']['colorhighlight']) &&$its['settings']['colorhighlight'] ){
            // TODO: maybe force aspect noir filled ? if aspect noir is set


          }
        }
      }


      if(isset($che['wrapper_image_type']) && $che['wrapper_image_type']){

        $fout.=' '.$che['wrapper_image_type'];
      }
      if(isset($margs['extra_classes_player'])){
        $fout.=' '.$margs['extra_classes_player'];
      }

      if($margs['called_from']=='footer_player'||$margs['called_from']=='player'||$margs['called_from']=='gallery'){

//                print_r($its);
//                print_r($margs);
        $fout.=' '.$margs['skin_ap'];
      }



      if(isset($margs['enable_alternate_layout']) && $margs['skinwave_mode']=='normal' && $margs['enable_alternate_layout']=='on'){
        $fout.=' alternate-layout';
      }

      if(isset($its['settings']['extra_classes_player'])){
        $fout.=' '.$its['settings']['extra_classes_player'];
      }
      if(isset($its['settings']['skinwave_mode'])){

        if($margs['skinwave_mode']=='alternate' ){
          $fout.=' alternate-layout';
        }
        if($margs['skinwave_mode']=='nocontrols' ){
          $fout.=' skin-wave-mode-nocontrols';
        }
      }

      $fout.=' '.$the_player_id;

//            print_rr($che);

//            print_rr($its);

      if(isset($its['settings']) && isset($its['settings']['disable_volume']) && $its['settings']['disable_volume']=='on'){
        $fout.=' disable-volume';
      }

      if(isset($che['extra_classes']) && $che['extra_classes']){
        $fout .=' '.$che['extra_classes'];
      }



      $fout.='" style=""';

      if($this->check_if_user_played_track($playerid)===true){
        $fout.=' data-viewsubmitted="on"';
      }

      if ($the_player_id != '') {
        $aux = str_replace('ap','',$the_player_id);
        $fout.=' id="'.$the_player_id.'" data-playerid="'.$aux.'"';
      };


      $che['thumb'] = $this->sanitize_id_to_src($che['thumb']);
      if (isset($che['thumb']) && $che['thumb']=='none') {
        $che['thumb']='';
      }
      if (isset($che['thumb']) && $che['thumb']) {
        $fout.=' data-thumb="' . $che['thumb'] . '"';
      };
      if (isset($che['thumb_link']) && $che['thumb_link']) {
        $fout.=' data-thumb_link="' . $che['thumb_link'] . '"';
      };
      if( isset($che['wrapper_image']) && $che['wrapper_image']){
        $fout.=' data-wrapper-image="'.$this->sanitize_id_to_src($che['wrapper_image']).'" ';
      }

      if (isset($che['publisher']) && $che['publisher']) {
        $fout.=' data-publisher="' . $che['publisher'] . '"';
      };




      if(isset($che['sample_time_start']) && $che['sample_time_start']){
        $fout.=' data-sample_time_start="'.$che['sample_time_start'].'"';
      }

      if(isset($che['sample_time_end']) && $che['sample_time_end']){
        $fout.=' data-sample_time_end="'.$che['sample_time_end'].'"';
      }

      if(isset($che['sample_time_total']) && $che['sample_time_total']){
        $fout.=' data-sample_time_total="'.$che['sample_time_total'].'"';
      }


      if($margs['called_from']=='gallery'){

//                print_r($che);
        if (isset($che['play_in_footer_player']) && $che['play_in_footer_player'] =='on') {
          $fout.=' data-fakeplayer="#dzsap_footer"';
        };
      }






      if($this->mainoptions['skinwave_wave_mode']=='canvas'){
//                print_r($che);


        $fout.=$this->generate_pcm($che);
      }else{
        if (isset($che['waveformbg']) && $che['waveformbg'] != '') {
          $fout.=' data-scrubbg="' . $che['waveformbg'] . '"';
        };
        if (isset($che['waveformprog']) && $che['waveformprog'] != '') {
          $fout.=' data-scrubprog="' . $che['waveformprog'] . '"';
        };
      }

      if ($type != '') {
        $fout.=' data-type="' . $type . '"';
      };
      if (isset($che['source']) && $che['source'] != '') {
        $fout.=' data-source="' . $che['source'] . '"';
      };
      if (isset($che['sourceogg']) && $che['sourceogg'] != '') {
        $fout.=' data-sourceogg="' . $che['sourceogg'] . '"';
      };

      if (isset($che['bgimage']) && $che['bgimage'] != '') {
        $fout.=' data-bgimage="' . $che['bgimage'] . '"';
        $fout.=' data-wrapper-image="' . $che['bgimage'] . '"';
      };


      if ($che['playfrom']) {
        $fout.=' data-playfrom="' . $che['playfrom'] . '"';
      };

//                    print_r($margs);;
      if(isset($margs['single']) && $margs['single']=='on'){
        if(isset($margs['width']) && isset($margs['height'])){

          // ===== some sanitizing
          $tw = $margs['width'];
          $th = $margs['height'];
          $str_tw = '';
          $str_th = '';




          if($tw!=''){
            if (strpos($tw, "%") === false && $tw!='auto') {
              $str_tw = ' width: '.$tw.'px;';
            }else{
              $str_tw = ' width: '.$tw.';';
            }
          }


          if($th!=''){
            if (strpos($th, "%") === false && $th!='auto') {
              $str_th = ' height: '.$th.'px;';
            }else{
              $str_th = ' height: '.$th.';';
            }
          }

//                    print_r($margs); echo $str_tw; echo $str_th;


          $fout.=' style="'.$str_tw.$str_th.'"';

        }
      }
      if(isset($margs['faketarget']) && $margs['faketarget']){
        $fout.=' data-fakeplayer="'.$margs['faketarget'].'"';
      }


      $fout.='>';
      //print_r($che);
      $che['menu_artistname'] = stripslashes($che['menu_artistname']);
      $che['menu_songname'] = stripslashes($che['menu_songname']);


      if($che['menu_artistname']=='default'){


        if($che['artistname']){
          $che['menu_artistname'] = $che['artistname'];
        }else{
          if($playerid){



            $che['menu_artistname'] = $po->post_title;
          }
        }


      }
      if($che['menu_songname']=='default'){

        if($che['songname']){
          $che['menu_songname'] = $che['songname'];
        }else{
          if($playerid){


            if($po->post_content){
              $che['menu_songname'] = $po->post_content;
            }


            if($po->post_excerpt){
              $che['menu_songname'] = $po->post_excerpt;
            }

            if($po->post_type=='attachment'){
              $po_metadata = wp_get_attachment_metadata($playerid);

              //                        print_r($po_metadata);
            }

          }

        }
      }
      if($che['menu_artistname']=='default'){



        $che['menu_artistname'] = '';
      }
      if($che['menu_songname']=='default'){
        $che['menu_songname'] = '';
      }


//            print_r($che);


//            print_r($che);

      if(isset($che['player_id']) && $che['player_id']=='dzsap_footer'){
        $che['menu_artistname'] = ' ';
        $che['menu_songname'] = ' ';
      }

      if ($che['menu_artistname']  || $che['menu_songname'] ) {
        $fout.='<div class="meta-artist">';
        $fout.='<span class="the-artist first-line"><span class="first-line-label">' . $che['menu_artistname'] . '</span>';



        if(isset($margs['settings_extrahtml_after_artist'])){
          $fout.=$margs['settings_extrahtml_after_artist'];
        }

//                print_rr($margs);

        $fout.='</span>';
        if ($che['menu_songname'] != '') {
          $fout.='&nbsp;<span class="the-name">' . $che['menu_songname'] . '</span>';
        }

        $fout.='</div>';
      }

//            print_rr($che);

      if(isset($che['wrapper_image_type']) && $che['wrapper_image_type']){



        if($che['wrapper_image_type']=='zoomsounds-wrapper-bg-bellow'){

          $fout.='<a href="#" class=" dzsap-wrapper-but dzsap-multisharer-but "><span class="the-icon">{{svg_share_icon}}</span> </a>';

          $fout.='<a href="#" class=" dzsap-wrapper-but btn-like "><span class="the-icon">{{heart_svg}}</span> </a>';
        }

      }


      if ($che['menu_artistname'] != '' || $che['menu_songname'] != '' || (isset( $che['thumb']) && $che['thumb'] != '')) {
        $fout.='<div class="menu-description">';
        if (isset($che['thumb']) && $che['thumb'] ) {
          $fout.='<div class="menu-item-thumb-con"><div class="menu-item-thumb" style="background-image: url(' . $che['thumb'] . ')"></div></div>';
        }


//                print_r($margs);

        if($margs['gallery_skin']=='skin-aura'){
          $fout.='<div class="menu-artist-info">';
        }


        $fout.='<span class="the-artist">' . $che['menu_artistname'] . '</span>';
        $fout.='<span class="the-name">' . $che['menu_songname'] . '</span>';


        if($margs['gallery_skin']=='skin-aura'){
          $fout.='</div>';
        }

        if (isset($_COOKIE['dzsap_ratesubmitted-' . $playerid])) {
          $che['menu_extrahtml'] = str_replace('download-after-rate', 'download-after-rate active', $che['menu_extrahtml']);
        } else {
          if (isset($_COOKIE['commentsubmitted-' . $playerid])) {
            $che['menu_extrahtml'] = str_replace('download-after-rate', 'download-after-rate active', $che['menu_extrahtml']);
          };
        }


//                print_r($margs);
        if($margs['gallery_skin']=='skin-aura'){
          $fout.='<div class="menu-item-views"><svg class="svg-icon" version="1.1" id="Layer_2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="11.161px" height="12.817px" viewBox="0 0 11.161 12.817" enable-background="new 0 0 11.161 12.817" xml:space="preserve"> <g> <g> <g> <path fill="#D2D6DB" d="M8.233,4.589c1.401,0.871,2.662,1.77,2.801,1.998c0.139,0.228-1.456,1.371-2.896,2.177l-4.408,2.465 c-1.44,0.805-2.835,1.474-3.101,1.484c-0.266,0.012-0.483-1.938-0.483-3.588V3.666c0-1.65,0.095-3.19,0.212-3.422 c0.116-0.232,1.875,0.613,3.276,1.484L8.233,4.589z"/> </g> </g> </g> </svg> <span class="the-count">'.get_post_meta($playerid, '_dzsap_views', true).'</span></div>';



          if($margs['menu_facebook_share']=='auto' || $margs['menu_facebook_share']=='on' || $margs['menu_like_button']=='auto' || $margs['menu_like_button']=='on'){

            $fout.='<div class="float-right">';
            if($margs['menu_facebook_share']=='auto' || $margs['menu_facebook_share']=='on'){

              $fout.='<a class="btn-zoomsounds-menu menu-facebook-share"  onclick=\'window.dzs_open_social_link("http://www.facebook.com/sharer.php?u={{shareurl}}",this); return false;\'><i class="fa fa-share" aria-hidden="true"></i></a>';
            }
            if($margs['menu_like_button']=='auto' || $margs['menu_like_button']=='on'){

              $fout.='<a class="btn-zoomsounds-menu menu-btn-like "><i class="fa fa-thumbs-up" aria-hidden="true"></i></a>';

            }

            $fout.='</div>';
          }
        }


        $fout.=stripslashes($che['menu_extrahtml']);
        $fout.='</div>';
      }

//            print_r($its);
      if (isset($its['settings']['skinwave_comments_enable']) && $its['settings']['skinwave_comments_enable'] == 'on') {

        if ($playerid != '') {

          $fout.='<div class="the-comments">';
          $comms = get_comments(array('post_id' => $playerid));
//                    echo 'cevacomm'; print_r($comms);
          foreach ($comms as $comm) {


            if(strpos($comm->comment_author_url, '%')===false){
              continue;
            }

            $fout.='<span class="dzstooltip-con" style="left:'.$comm->comment_author_url.'"><span class="dzstooltip arrow-from-start transition-slidein arrow-bottom skin-black" style="width: 250px;"><span class="the-comment-author">@'.$comm->comment_author.'</span> says:<br>'.$comm->comment_content.'</span><div class="the-avatar" style="background-image: url(https://secure.gravatar.com/avatar/'.md5($comm->comment_author_email).'?s=20)"></div></span>';




          }
          $fout.='</div>';


          wp_enqueue_style('dzs.tooltip', $this->base_url . 'dzstooltip/dzstooltip.css');
        }
      }

      if (isset($its['settings']) && $its['settings']['skin_ap'] && $its['settings']['skin_ap']=='skin-customcontrols'){

        $fout.=do_shortcode($margs['the_content']);
//                print_r($margs);
      }
      // --- extra html meta
//            print_r($its);
//            print_r($margs);


      if($this->debug){
        print_rr($che);
      }


      if (isset($its['settings']) && ($its['settings']['enable_views'] == 'on' || $its['settings']['enable_downloads_counter'] == 'on' || $its['settings']['enable_likes'] == 'on' || $its['settings']['enable_rates'] == 'on' || (isset($che['extra_html']) && $che['extra_html'] )   || (isset($its['settings']['menu_right_enable_facebook_share']) && $its['settings']['menu_right_enable_facebook_share']=='on')   )  || (isset($its['settings']['menu_right_enable_multishare']) && $its['settings']['menu_right_enable_multishare']=='on')    || (isset($che['enable_download_button']) && $che['enable_download_button']=='on' )  || (isset($che['extra_html_in_controls_right']) && $che['extra_html_in_controls_right'] ) ) {
        $aux_extra_html = '';


        if((isset($che['extra_html_left']) && $che['extra_html_left'] )){
          $aux_extra_html.='<div class="extra-html--left">'.$che['extra_html_left'].'</div>';
        }

        $aux_extra_html.='<div class="extra-html--left ">';
        if ($its['settings']['enable_likes'] == 'on') {
          $aux_extra_html.=$this->mainoptions['str_likes_part1'];

          if (isset($_COOKIE["dzsap_likesubmitted-" . $playerid])) {
            $aux_extra_html = str_replace('<span class="btn-zoomsounds btn-like">', '<span class="btn-zoomsounds btn-like active">', $aux_extra_html);
          }
        }


//                print_r($che);


        if( (isset($che['enable_download_button']) && $che['enable_download_button']=='on' ) ){


          $download_link = '';



          if( (isset($che['download_custom_link']) && $che['download_custom_link'] ) ){
            $download_link = $che['download_custom_link'];
          }else{
            if($playerid){

              $download_link = site_url().'?action=dzsap_download&id='.$playerid;
            }else{


              if($this->mainoptions['download_link_links_directly_to_file']=='on'){

                $download_link = $che['source'];
              }else{

                $download_link = site_url().'?action=dzsap_download&link='.urlencode($che['source']);
              }
            }
          }


          // data-playerid="'.$playerid.'"
          $aux_extra_html.='<a href="'.$download_link.'" class="btn-zoomsounds btn-zoomsounds-download"><span class="the-icon"><i class="fa fa-get-pocket"></i></span><span class="the-label">'.$this->mainoptions['i18n_free_download'].'</span></a>';

        }

        if(isset($margs['single']) && $margs['single']=='on'){
          if(isset($margs['enable_embed_button']) && ( $margs['enable_embed_button']=='on' || $margs['enable_embed_button']=='in_extra_html'  )){

            if(isset($margs['embed_code']) && $margs['embed_code'] && $margs['embedded']!='on'){

              $aux_extra_html.='<span class=" btn-zoomsounds dzstooltip-con btn-embed">  <span class="dzstooltip transition-slidein arrow-bottom align-left skin-black " style="width: 350px; "><span style="max-height: 150px; overflow:hidden; display: block; white-space: normal; font-weight: normal">{{embed_code}}</span> <span class="copy-embed-code-btn"><i class="fa fa-clipboard"></i> '.__('Copy Embed').'</span> </span> <span class="the-icon"><i class="fa fa-share"></i></span><span class="the-label ">'.__('Embed').'</span></span>';
            }
          }
        }
        $aux_extra_html.='</div>';


        if ($its['settings']['enable_rates'] == 'on') {
          $aux_extra_html.='<div class="star-rating-con"><div class="star-rating-bg"></div><div class="star-rating-set-clip" style="width: ';

          $aux = get_post_meta($playerid, '_dzsap_rate_index', true);
          if ($aux == '') {
            $aux_extra_html.='0px';
          } else {
            $aux_extra_html.=(122 / 5 * $aux) . 'px';
          }


          $aux_extra_html.=';"><div class="star-rating-prog"></div></div><div class="star-rating-prog-clip"><div class="star-rating-prog"></div></div></div>';
        }



//                print_r($its);
        if ($its['settings']['enable_views'] == 'on') {
          $aux_extra_html.=$this->mainoptions['str_views'];
          $aux = get_post_meta($playerid, '_dzsap_views', true);
          if ($aux == '') {
            $aux = 0;
          }
          $aux_extra_html = str_replace('{{get_plays}}', $aux, $aux_extra_html);
        }
        if ($its['settings']['enable_downloads_counter'] == 'on') {
          $aux_extra_html.=$this->mainoptions['str_downloads_counter'];
          $aux = get_post_meta($playerid, '_dzsap_downloads', true);
          if ($aux == '') {
            $aux = 0;
          }
          $aux_extra_html = str_replace('{{get_downloads}}', $aux, $aux_extra_html);
        }
        if ($its['settings']['enable_likes'] == 'on') {
          $aux_extra_html.=$this->mainoptions['str_likes_part2'];
          $aux = get_post_meta($playerid, '_dzsap_likes', true);
          if ($aux == '') {
            $aux = 0;
          }
          $aux_extra_html = str_replace('{{get_likes}}', $aux, $aux_extra_html);
        }




        if ($its['settings']['enable_rates'] == 'on') {
          $aux_extra_html.=$this->mainoptions['str_rates'];
          $aux = get_post_meta($playerid, '_dzsap_rate_nr', true);
          if ($aux == '') {
            $aux = 0;
          }
          $aux_extra_html = str_replace('{{get_rates}}', $aux, $aux_extra_html);

          if (isset($_COOKIE['dzsap_ratesubmitted-' . $playerid])) {
            $aux_extra_html.='{{ratesubmitted=' . $_COOKIE['dzsap_ratesubmitted-' . $playerid] . '}}';
          };
        }


        if((isset($che['extra_html']) && $che['extra_html'] )){
          $aux_extra_html.=''.$che['extra_html'];
        }



        if((isset($che['extra_html_in_controls_left']) && $che['extra_html_in_controls_left'] )){
          $fout.='<div class="extra-html-in-controls-left">'.$che['extra_html_in_controls_left'].'</div>';
        }




//                echo '$start_nr - '.$start_nr;
//                echo '$end_nr - '.$end_nr;
//                echo '$i - '.$i;
//                print_r($its);
//                print_rr($che);






        if( isset($its['settings']) && ( (isset($its['settings']['menu_right_enable_facebook_share']) && $its['settings']['menu_right_enable_facebook_share']=='on') || (isset($its['settings']['menu_right_enable_multishare']) && $its['settings']['menu_right_enable_multishare']=='on') || (isset($che['extra_html_in_controls_right']) && $che['extra_html_in_controls_right'] ) ) ){

          $fout.='<div class="extra-html-in-controls-right">';

          if((isset($its['settings']['menu_right_enable_facebook_share']) && $its['settings']['menu_right_enable_facebook_share']=='on')){
            $fout.='<a   onclick=\'window.dzs_open_social_link("http://www.facebook.com/sharer.php?u={{replaceurl}}&caption='.urlencode($che['menu_artistname']).'&description='.urlencode($che['menu_songname']).'&picture='.urlencode($che['thumb']).'",this); return false;\' class="player-but sharer-dzsap-but"><div class="the-icon-bg"></div>{{svg_share_icon}}</a>';
          }

          if((isset($its['settings']['menu_right_enable_multishare']) && $its['settings']['menu_right_enable_multishare']=='on')){

            $this->sw_enable_multisharer = true;
            $fout.='   <a class="player-but sharer-dzsap-but dzsap-multisharer-but"><div class="the-icon-bg"></div>{{svg_share_icon}}</a>';
          }


          if((isset($che['extra_html_in_controls_right']) && $che['extra_html_in_controls_right'] )){
            $fout.=''.$che['extra_html_in_controls_right'].'';



            if(strpos($che['extra_html_in_controls_right'], 'dzsap-multisharer-but')!==false){

              $this->sw_enable_multisharer = true;

            }

          }


          $fout.='</div>';
        }


//                echo 'hmmdada';
        if(strpos($aux_extra_html,'<i class="fa')!==false){
          wp_enqueue_style('fontawesome','https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css');
        }

        $fout.='<div class="extra-html">' . $aux_extra_html . '</div>';
      }



      if (isset($che['inner_html']) && $che['inner_html']) {
        $fout.=$che['inner_html'];
      }



      $fout.='</div>';

      if (isset($che['apply_script'])) {

      }
    }
    return $fout;
  }

  function generate_pcm($che){





    if(isset($che->post_title)){
      $che = (array) $che;

      $args = array();
      $che['source'] = $this->get_track_source($che['ID'], $che['ID'],$args);
      $che['playerid'] = $che['id'];
    }




    $fout = '';
    $lab  = 'dzsap_pcm_data_'.$this->clean($che['source']);




    $pcm = '';
    $pcm = get_option($lab);

//                echo 'pcm - '.$pcm. ' - source ( dzsap_pcm_data_'.$this->clean($che['source']).' ) |||'."\n\n";
//                echo ' source ( dzsap_pcm_data_'.$this->clean($che['source']).' )';

    if($pcm=='' || $pcm=='[]'){

      if(isset($che['linktomediafile'])){
        if($che['linktomediafile']){
          $lab  = 'dzsap_pcm_data_'.$che['linktomediafile'];
        }
      }
      $pcm = get_option($lab);

      if( ( $pcm == '' || $pcm== '[]') && isset($che['playerid']) && $che['playerid']){
        $lab  = 'dzsap_pcm_data_'.$che['playerid'];
        $pcm = get_option($lab);
      }

//                    echo 'lab - '.$lab;
//                    $lab = 'dzsap_pcm_data_735';

//                    echo 'lab - '.$lab;

//                    echo 'pcm - '.$pcm;

    }

//                print_r($che);
//                echo 'lab - '.$lab.' ||| ';


    if($pcm && $pcm!='[]'){
      $fout.= ' data-pcm=\''.$pcm.'\'';
    }

    return $fout;
  }
  function check_if_user_played_track($track_id){

    global $current_user;

//        echo 'current_user - ';print_r($current_user);

    if($current_user && isset($current_user->data) && $current_user->data && isset($current_user->data->ID) && $current_user->data->ID){
      //--- if user logged in

//            echo 'dadada';
      return $this->mysql_check_if_user_played_track($current_user->data->ID, $track_id);
    }else{
      if (isset($_COOKIE['viewsubmitted-' . $track_id])) {
        return true;
      }
      return false;
    }
  }

  function mysql_check_if_user_played_track($id_user, $track_id){


    global $wpdb;



    $currip = $this->misc_get_ip();
    $date = date('Y-m-d H:i:s');
    $table_name = $wpdb->prefix.'dzsap_activity';

    $user_id = 0;

//        echo '$id_user - '.$id_user.' track_id - '.$track_id;
//                    error_log('adding '.$table_name);

    if(get_option('dzsap_table_activity_created')){

      $table_name = $wpdb->prefix.'dzsap_activity';
      $query = "SELECT * FROM $table_name WHERE `id_user` = '$id_user' AND `id_video`='$track_id'";


//        echo $query;
      $mylink = $wpdb->get_row(
        $query
      );

//        print_r($mylink);

      if($mylink && $mylink->id){
        return true;
      }
    }


    return false;




  }


  function get_post_thumb_src($it_id){
    $imgsrc = wp_get_attachment_image_src(get_post_thumbnail_id($it_id), "full");

    return $imgsrc[0];
  }

  function object_to_array($data){
    if (is_array($data) || is_object($data)){
      $result = array();
      foreach ($data as $key => $value)
      {
        $result[$key] = object_to_array($value);
      }
      return $result;
    }
    return $data;
  }


  function sanitize_id_to_src($arg){

//        echo ' arg - '.$arg;
    if(is_numeric($arg)){

      $imgsrc = wp_get_attachment_image_src($arg, 'full');
//            print_r($imgsrc);
//            echo ' $imgsrc - '.$imgsrc;
      return $imgsrc[0];
    }else{
      return $arg;
    }


  }

  function sanitize_to_array_for_parse($its, $margs){
//        print_r($its);
//        print_r($margs);

    foreach($its as $lab => $it){
//            $its[$lab] = $this->object_to_array($it);
      $its[$lab] = (array) $it;


      $thumb = $this->get_post_thumb_src($it->ID);

//            echo ' thumb - ';
//            print_r($thumb);


      $thumb_from_meta = get_post_meta($it->ID, 'dzsrst_meta_item_thumb',true);

      if($thumb_from_meta){

        $thumb = $thumb_from_meta;
      }

      if($thumb){
//                $its[$lab]->thumbnail = $thumb;
        $its[$lab]['thumbnail'] = $thumb;
      }

//            print_r($margs);


      $its[$lab]['title_permalink'] = get_permalink($it->ID);

      $its[$lab]['price'] = get_post_meta($it->ID, 'dzsrst_meta_item_price',true);

      if($margs['post_type']=='product'){
        if(get_post_meta($it->ID, '_regular_price',true)){
          $its[$lab]['price'] = '';
          if(function_exists('get_woocommerce_currency_symbol')){
            $its[$lab]['price'].=get_woocommerce_currency_symbol();
          }
          $its[$lab]['price'] .= get_post_meta($it->ID, '_regular_price',true);
        }
      }

//            $its[$lab]['ingredients'] = get_post_meta($it->ID, 'dzsrst_meta_item_ingredients',true);
      $its[$lab]['bigimage'] = $this->sanitize_id_to_src(get_post_meta($it->ID, 'dzsrst_meta_item_bigimage',true));
    }

    return $its;
  }


  function handle_init() {
    global $pagenow;
    global $post;

    $post_id = '';
    if (isset($_GET['post']) && $_GET['post'] != '') {
      $post_id = $_GET['post'];
    }

    if($this->mainoptions['try_to_hide_url']=='on'){

      if (!session_id()) {
        session_start();
      }
    }




    if(isset($_GET['dzsap_action']) && $_GET['dzsap_action']) {
//            dzsprx_shortcode_builder();

      if ($_GET['dzsap_action'] == 'generatenonce') {

        $id = $_GET['id'];


        $lab = 'dzsap_nonce_for_' . $id . '_ip_' . $_SERVER['REMOTE_ADDR'];
        $lab = $this->clean($lab);





        $nonce = rand(0,10000);

        //                $id = $it['id'];


        $_SESSION[$lab] = $nonce;

        $src = site_url().'/index.php?dzsap_action=get_track_source&id='.$id.'&'.$lab.'='.$nonce;


        echo $src;

        die();

      }
    }


    if(isset($_GET['dzsap_action']) && $_GET['dzsap_action']){
//            dzsprx_shortcode_builder();

      if($_GET['dzsap_action']=='get_track_source'){

        $id = $_GET['id'];


        $po = (get_post($id));


        $src_url = '';



        $src_url = get_post_meta($po->ID, 'dzsap_woo_product_track', true);



        $playerid='';
        $args=array();
        if($src_url == ''){
          $src_url = $this->get_track_source($po->ID,$playerid, $args);
        }






        error_log('$src_url -> '.$src_url);
        if($id && $src_url) {


//            echo 'whaaa';
          $this->sliders__player_index++;

          $fout = '';



//                    print_r($_SESSION);


//                    error_log(print_rrr($_SESSION));
//                    error_log(print_rrr($_GET));




          $lab = 'dzsap_nonce_for_'.$id.'_ip_'.$_SERVER['REMOTE_ADDR'];
          $lab =$this->clean($lab);

          if($_SESSION[$lab]==$_GET[$lab]){




//                        $extension = "mp3";
//                        $mime_type = "audio/mpeg, audio/x-mpeg, audio/x-mpeg-3, audio/mpeg3";
//
//
//                        error_log('src - '.$src);
//
//                        $fileSize = filesize($src);
//
//                        error_log('fileSize - '.$fileSize);
//                        error_log('fileSize - '.$fileSize);
//
////                        header('HTTP/1.1 206 Partial Content'); // Allows scanning in a stream.
//                        header('Accept-Ranges: bytes'); // Allows scanning in a stream based on byte count.
//                        header('Content-type: audio');
//                        header("Content-transfer-encoding: binary");
//                        header('Content-length: ' . $fileSize);
////                        header('Content-Range: bytes '.'0'.'-'.$fileSize); // This tells the player what byte we're starting with.
//                        header('Content-Disposition: inline; filename="' . $src);
////                        header('X-Pad: avoid browser bug');
////                        header('Cache-Control: no-cache');
//
//
////                        echo file_get_contents($src);
//                        readfile($src);







//                        $_SESSION['dzsap_nonce_for_'.$id] = 'dada';


            $file = '';
            if(strpos($src_url,site_url())!==false){
              $file = str_replace(site_url(), dirname(dirname(dirname(dirname(__FILE__)))), $src_url);

            }else{
              echo file_get_contents($src_url);
              die();
            }

            $content_type = 'application/octet-stream';





            @error_reporting(0);

            // Make sure the files exists, otherwise we are wasting our time
            if (!file_exists($file)) {
              header("HTTP/1.1 404 Not Found");
              exit;
            }

            // Get file size
            $filesize = sprintf("%u", filesize($file));

            // Handle 'Range' header
            if(isset($_SERVER['HTTP_RANGE'])){
              $range = $_SERVER['HTTP_RANGE'];
            }elseif($apache = apache_request_headers()){
              $headers = array();
              foreach ($apache as $header => $val){
                $headers[strtolower($header)] = $val;
              }
              if(isset($headers['range'])){
                $range = $headers['range'];
              }
              else $range = FALSE;
            } else $range = FALSE;

            //Is range
            if($range){
              $partial = true;
              list($param, $range) = explode('=',$range);
              // Bad request - range unit is not 'bytes'
              if(strtolower(trim($param)) != 'bytes'){
                header("HTTP/1.1 400 Invalid Request");
                exit;
              }
              // Get range values
              $range = explode(',',$range);
              $range = explode('-',$range[0]);
              // Deal with range values
              if ($range[0] === ''){
                $end = $filesize - 1;
                $start = $end - intval($range[0]);
              } else if ($range[1] === '') {
                $start = intval($range[0]);
                $end = $filesize - 1;
              }else{
                // Both numbers present, return specific range
                $start = intval($range[0]);
                $end = intval($range[1]);
                if ($end >= $filesize || (!$start && (!$end || $end == ($filesize - 1)))) $partial = false; // Invalid range/whole file specified, return whole file
              }
              $length = $end - $start + 1;
            }
            // No range requested
            else $partial = false;

            // Send standard headers
            header("Content-Type: $content_type");
            header("Content-Length: $filesize");
            header('Accept-Ranges: bytes');

            // send extra headers for range handling...
            if ($partial) {
              header('HTTP/1.1 206 Partial Content');
              header("Content-Range: bytes $start-$end/$filesize");
              if (!$fp = fopen($file, 'rb')) {
                header("HTTP/1.1 500 Internal Server Error");
                exit;
              }
              if ($start) fseek($fp,$start);
              while($length){
                set_time_limit(0);
                $read = ($length > 8192) ? 8192 : $length;
                $length -= $read;
                print(fread($fp,$read));
              }
              fclose($fp);
            }
            //just send the whole file
            else readfile($file);
            exit;


            /*
                        */
          }else{

          }



//                    echo 'src - '.$src;



//        print_r($its); print_r($margs); echo 'alceva'.$fout;
        }


        die();

      }

    }

    //wp_deregister_script('jquery');        wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"), false, '1.9.0');
    wp_enqueue_script('jquery');
    if (is_admin()) {
      wp_enqueue_style('dzsap_admin_global', $this->base_url . 'admin/admin_global.css');
      wp_enqueue_script('dzsap_admin_global', $this->base_url . 'admin/admin_global.js');
      if ($this->mainoptions['activate_comments_widget']=='on') {
        wp_enqueue_script('googleapi', 'https://www.google.com/jsapi');
      }

      wp_enqueue_style('dzsulb', $this->base_url . 'libs/ultibox/ultibox.css');
      wp_enqueue_script('dzsulb', $this->base_url . 'libs/ultibox/ultibox.js');

      if ($pagenow == 'post.php') {
        $po = get_post($post_id);
        if ($po && $po->post_type == 'attachment') {
          wp_enqueue_media();
        }
      }



      wp_enqueue_style('dzssel', $this->base_url.'libs/dzsselector/dzsselector.css');
      wp_enqueue_script('dzssel', $this->base_url.'libs/dzsselector/dzsselector.js');

      if (isset($_GET['page'])) {
//                echo $this->adminpagename_mo;
        if($_GET['page']==$this->adminpagename_mo){

          wp_enqueue_style('dzs.dzstoggle', $this->base_url . 'dzstoggle/dzstoggle.css');
          wp_enqueue_script('dzs.dzstoggle', $this->base_url . 'dzstoggle/dzstoggle.js');
          wp_enqueue_style('fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
          wp_enqueue_style('dzstabsandaccordions', $this->thepath . 'libs/dzstabsandaccordions/dzstabsandaccordions.css');
          wp_enqueue_script('dzstabsandaccordions', $this->thepath . "libs/dzstabsandaccordions/dzstabsandaccordions.js");
          wp_enqueue_style('fontawesome','https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
        }
      }

      if (isset($_GET['page']) && ($_GET['page'] == $this->adminpagename || $_GET['page'] == $this->adminpagename_configs)) {
        wp_enqueue_media();
        $this->admin_scripts();


        wp_enqueue_style('dzsulb', $this->base_url . 'libs/ultibox/ultibox.css');
        wp_enqueue_script('dzsulb', $this->base_url . 'libs/ultibox/ultibox.js');


        wp_enqueue_style('fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
        wp_enqueue_style('dzstabsandaccordions', $this->thepath . 'libs/dzstabsandaccordions/dzstabsandaccordions.css');
        wp_enqueue_script('dzstabsandaccordions', $this->thepath . "libs/dzstabsandaccordions/dzstabsandaccordions.js");
      }


      if(isset($_GET['taxonomy']) && $_GET['taxonomy']=='dzsap_sliders'){
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_style('fontawesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
        wp_enqueue_style('dzs.tooltip', $this->base_url . 'dzstooltip/dzstooltip.css');




        wp_enqueue_media();
      }

      if (isset($_GET['page']) && $_GET['page'] == 'dzsap-dc') {
        wp_enqueue_style('dzsap-dc.style', $this->base_url . 'deploy/designer/style/style.css');
        wp_enqueue_script('dzs.farbtastic', $this->base_url . "farbtastic/farbtastic.js");
        wp_enqueue_style('dzs.farbtastic', $this->base_url . 'farbtastic/farbtastic.css');
        wp_enqueue_script('dzsap-dc.admin', $this->base_url . 'deploy/designer/js/admin.js');
      }

      if (isset($_GET['page']) && $_GET['page'] == $this->page_mainoptions_link) {

        wp_enqueue_style('dzscheckbox', $this->base_url . 'libs/dzscheckbox/dzscheckbox.css');


        if(isset($_GET['dzsap_shortcode_builder']) && $_GET['dzsap_shortcode_builder']=='on'){

          wp_enqueue_style('dzsap_shortcode_builder_style', $this->base_url . 'tinymce/popup.css');
          wp_enqueue_script('dzsap_shortcode_builder', $this->base_url . 'tinymce/popup.js');
          wp_enqueue_style('dzs.tabsandaccordions', $this->base_url . 'libs/dzstabsandaccordions/dzstabsandaccordions.css');
          wp_enqueue_script('dzs.tabsandaccordions', $this->base_url . 'libs/dzstabsandaccordions/dzstabsandaccordions.js');
          wp_enqueue_media();


          wp_enqueue_style('dzsulb', $this->base_url . 'libs/ultibox/ultibox.css');
          wp_enqueue_script('dzsulb', $this->base_url . 'libs/ultibox/ultibox.js');
        }else{



          if(isset($_GET['dzsap_shortcode_player_builder']) && $_GET['dzsap_shortcode_player_builder']=='on'){


            wp_enqueue_style('dzsap_shortcode_builder_style', $this->base_url . 'tinymce/popup.css');
            wp_enqueue_style('dzsap_shortcode_player_builder_style', $this->base_url . 'shortcodegenerator/generator_player.css');
            wp_enqueue_script('dzsap_shortcode_player_builder', $this->base_url . 'shortcodegenerator/generator_player.js');

            wp_enqueue_style('dzs.tabsandaccordions', $this->base_url . 'libs/dzstabsandaccordions/dzstabsandaccordions.css');
            wp_enqueue_script('dzs.tabsandaccordions', $this->base_url . 'libs/dzstabsandaccordions/dzstabsandaccordions.js');
            wp_enqueue_media();


            wp_enqueue_style('dzsulb', $this->base_url . 'libs/ultibox/ultibox.css');
            wp_enqueue_script('dzsulb', $this->base_url . 'libs/ultibox/ultibox.js');
          }else{

            wp_enqueue_style('dzsap_admin', $this->base_url . 'admin/admin.css');
            wp_enqueue_script('dzsap_admin', $this->base_url . "admin/admin.js");
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-slider');
            $url = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css";
            wp_enqueue_style('jquery-ui-smoothness', $url, false, null);
            wp_enqueue_script('dzs.farbtastic', $this->base_url . "farbtastic/farbtastic.js");
            wp_enqueue_style('dzs.farbtastic', $this->base_url . 'farbtastic/farbtastic.css');
          }

        }


      }

      if (current_user_can('edit_posts') || current_user_can('edit_pages')) {

        wp_enqueue_script('dzsap_htmleditor', $this->base_url . 'tinymce/plugin-htmleditor.js');
        wp_enqueue_script('dzsap_configreceiver', $this->base_url . 'tinymce/receiver.js');
      }






    } else {
      if (isset($this->mainoptions['always_embed']) && $this->mainoptions['always_embed'] == 'on') {
        $this->front_scripts();
        wp_enqueue_style('dzsulb', $this->base_url . 'libs/ultibox/ultibox.css');
        wp_enqueue_script('dzsulb', $this->base_url . 'libs/ultibox/ultibox.js');
      }
    }


    $this->register_links();








    if (isset($_GET['action'])) {


      if($_GET['action']=='dzsap_download'){


        if(isset($_GET['id'])&&$_GET['id']){

          $po = get_post($_GET['id']);

//                    print_r($po);



          $filename = '';

          if($po->post_type=='attachment'){

            $filename = wp_get_attachment_url($po->ID);
          }

          if($filename==''){
            if($filename==''){
              if(function_exists('get_field')){
                $arr = get_field('scratch_preview',$po->ID);


                if($arr){

                  $media = wp_get_attachment_url($arr);

//                echo 'media - '.$media;
                  $filename = $media;
                }
              }
            }
          }

//                    echo $filename;

//                    echo $filename;

          header("Pragma: public");
          header("Expires: 0");
          header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
          header("Cache-Control: public");
          header("Content-Description: File Transfer");
          header("Content-type: application/octet-stream");
          header("Content-Disposition: attachment; filename=\"".$po->post_title.".mp3\"");
          header("Content-Transfer-Encoding: binary");
//                    header("Content-Length: ".filesize($filename));





          echo  file_get_contents($filename);



          $this->insert_activity(array(
            'id_video'=>$po->ID,
            'type'=>'download',
          ));





        }else{
          if(isset($_GET['link'])&&$_GET['link']){

//                        $aux  =$_GET['link'];
            $aux = explode('/',$_GET['link']);
            $filename = $aux[count($aux)-1];

            $filename=html_entity_decode($filename);

//                        echo $filename;
//                        print_r($aux);



            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=\"".$filename.".mp3\"");
            header("Content-Transfer-Encoding: binary");


            echo  file_get_contents($_GET['link']);
            $this->insert_activity(array(
              'id_video'=>$po->ID,
              'type'=>'download',
            ));

          }else{

            echo __("You need to set media id");
          }

        }
        die();
      }
    }






















    if(function_exists('vc_add_shortcode_param')) {
//            add_shortcode_param('dzsvcs_toggle_begin', 'vc_dzsvcs_toggle_begin' );
//            add_shortcode_param('dzsvcs_toggle_end', 'vc_dzsvcs_toggle_end' );
      vc_add_shortcode_param('dzs_add_media_att', 'vc_dzs_add_media_att');
    }

    include_once($this->base_path . 'vc/part-vcintegration.php');
  }









  function cs_home_before(){
    //    echo 'hmmdada';
    // -- enqueue in cusotmizer

    wp_enqueue_script( 'dzsap-admin-for-cornerstone', $this->base_url . 'assets/admin/admin-for-cornerstone.js', array('jquery'));
    wp_enqueue_script( 'dzsap-admin-global', $this->base_url . 'admin/admin_global.js', array('jquery'));
    wp_enqueue_style( 'dzsap-admin-global', $this->base_url . 'admin/admin_global.css');

  }

  function cs_register_elements() {

//        echo 'ceva';

//        error_log('register_elements');
    cornerstone_register_element( 'CS_DZSAP', 'dzsap', $this->base_path . 'cs/dzsap' );
//        cornerstone_register_element( 'CS_DZSAP_PLAYLIST', 'dzsap_playlist', $this->base_path . 'includes/dzsap_playlist' );

  }

  function cs_enqueue() {
    wp_enqueue_style( 'dzsap', $this->base_url . 'audioplayer/audioplayer.css');
    wp_enqueue_script( 'dzsap', $this->base_url . 'audioplayer/audioplayer.js', array('jquery'));


    //    wp_enqueue_style( 'dzs.scroller', $this->base_url . 'assets/dzsscroller/scroller.css');
    //    wp_enqueue_script( 'dzs.scroller', $this->base_url . 'assets/dzsscroller/scroller.js');
  }

  function cs_icon_map( $icon_map ) {
    $icon_map['dzsap'] = $this->base_url . '/assets/svg/icons.svg';
    return $icon_map;
  }






  function insert_activity($pargs = array()){

    global $wpdb;


    $margs = array(
      'type' => 'download',
      'id_user' => '',
      'id_video' => '',
    );

    if ($pargs == '' || is_array($pargs) == false) {
      $pargs = array();
    }

    $margs = array_merge($margs, $pargs);

    $currip = $this->misc_get_ip();
    $date = date('Y-m-d H:i:s');




    if(get_option('dzsap_table_activity_created')) {
      $table_name = $wpdb->prefix . 'dzsap_activity';

      $user_id = 0;
      $current_user = wp_get_current_user();

      if ($current_user) {
        if ($current_user->ID) {
          $user_id = $current_user->ID;
        }
      }

//                    error_log('adding '.$table_name);

      $wpdb->insert($table_name, array(
        'ip' => $currip,
        'type' => $margs['type'],
        'id_user' => $user_id,
        'id_video' => $margs['id_video'],
        'date' => $date,
      ));
    }
  }

  function handle_admin_init(){

//        echo 'ceva';

    add_settings_section('dzsap-permalink', __('Audio Items Permalink Base', 'dzsap'), array($this, 'permalink_settings'), 'permalink');
  }



  function permalink_settings() {

    echo wpautop(__('These settings control the permalinks used for products. These settings only apply when <strong>not using "default" permalinks above</strong>.', 'dzsap'));

    $permalinks = get_option('dzsap_permalinks');
    $dzsap_permalink = $permalinks['item_base'];
    //echo 'ceva';

    $item_base = _x('audio', 'default-slug', 'dzsap');

    $structures = array(0 => '', 1 => '/' . trailingslashit($item_base));
    ?>
    <table class="form-table">
      <tbody>
      <tr>
        <th><label><input name="dzsap_permalink" type="radio" value="<?php echo $structures[0]; ?>"
                          class="dzsaptog" <?php checked($structures[0], $dzsap_permalink); ?> /> <?php _e('Default'); ?>
          </label></th>
        <td><code><?php echo home_url(); ?>/?audio=sample-item</code></td>
      </tr>
      <tr>
        <th><label><input name="dzsap_permalink" type="radio" value="<?php echo $structures[1]; ?>"
                          class="dzsaptog" <?php checked($structures[1], $dzsap_permalink); ?> /> <?php _e('Product', 'dzsap'); ?>
          </label></th>
        <td><code><?php echo home_url(); ?>/<?php echo $item_base; ?>/sample-item/</code></td>
      </tr>
      <tr>
        <th><label><input name="dzsap_permalink" id="dzsap_custom_selection" type="radio" value="custom"
                          class="tog" <?php checked(in_array($dzsap_permalink, $structures), false); ?> />
            <?php _e('Custom Base', 'dzsap'); ?></label></th>
        <td>
          <input name="dzsap_permalink_structure" id="dzsap_permalink_structure" type="text"
                 value="<?php echo esc_attr($dzsap_permalink); ?>" class="regular-text code"> <span
            class="description"><?php _e('Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.', 'dzsap'); ?></span>
        </td>
      </tr>
      </tbody>
    </table>
    <script type="text/javascript">
      jQuery(function () {
        jQuery('input.dzsaptog').change(function () {
          jQuery('#dzsap_permalink_structure').val(jQuery(this).val());
        });

        jQuery('#dzsap_permalink_structure').focus(function () {
          jQuery('#dzsap_custom_selection').click();
        });
      });
    </script>
    <?php
  }





  function register_links() {

    global $dzsap;


    register_taxonomy('dzsap_category', 'dzsap_items', array('label' => __('Audio Categories', 'dzsap'), 'query_var' => true, 'show_ui' => true, 'hierarchical' => true, 'rewrite' => array('slug' => $dzsap->mainoptions['dzsap_categories_rewrite']),));
    register_taxonomy('dzsap_sliders', 'dzsap_items', array('label' => __('Audio Galleries', 'dzsap'), 'query_var' => true, 'show_ui' => true, 'hierarchical' => false, 'rewrite' => array('slug' => $this->mainoptions['dzsap_sliders_rewrite']),));


//        add_action( 'dzsap_sliders_add_tag_form_fields', 'add_feature_group_field', 10, 2 );
    add_action( 'dzsap_sliders_add_form_fields', 'add_feature_group_field', 10, 2 );
    add_action( 'dzsap_sliders_edit_form_fields', 'add_feature_group_field', 10, 10 );
    add_action( 'category_edit_form_fields', array($this,'term_meta_fields'), 10, 10 );


//        add_action( 'dzsap_sliders_add_form_fields', 'add_feature_group_field', 10, 2 );
//        add_action( 'dzsap_sliders_edit_form_fields', 'add_feature_group_field', 10, 10
    add_action( 'edited_category', array($this,'save_taxonomy_custom_meta'), 10, 2 );

//        add_action( 'created_dzsap_sliders', 'save_feature_meta', 10, 2 );
//        add_action( 'edited_dzsap_sliders', 'save_feature_meta', 10, 2 );


    $labels = array('name' => 'Audio Items', 'singular_name' => 'Audio Item',);

    $permalinks = get_option('dzsap_permalinks');
    //print_r($permalinks);

    $item_slug_permalink = empty($permalinks['item_base']) ? _x('audio', 'slug', 'dzsap') : $permalinks['item_base'];


    $args = array('labels' => $labels, 'public' => true, 'has_archive' => true, 'hierarchical' => false, 'supports' => array('title', 'editor', 'author', 'thumbnail', 'post-thumbnail', 'comments', 'excerpt', 'custom-fields'), 'rewrite' => array('slug' => $item_slug_permalink), 'yarpp_support' => true, 'capabilities' => array(),//'taxonomies' => array('categoryportfolio'),
    );


    register_post_type('dzsap_items', $args);
  }




  function term_meta_fields($term){
    // this will add the custom meta field to the add new term page

    $t_id = $term->term_id;

    // retrieve the existing value(s) for this meta field. This returns an array
    $term_meta = get_option("taxonomy_$t_id");

    $tem = array(
      'name'=>'feed_xml',
      'title'=>__('XML Feed'),
    );

    ?>
    <tr class="form-field">
      <th scope="row" valign="top"><label
          for="term_meta[<?php echo $tem['name']; ?>]"><?php echo $tem['title']; ?></label></th>
      <td class="<?php
      if($tem['type']=='media-upload'){
        echo 'setting-upload';
      }
      ?>">





        <?php

        if($tem['type']=='media-upload'){
          echo '<span class="uploader-preview"></span>';
        }
        ?>



        <?php
        $lab = 'term_meta['.$tem['name'].']';

        $val = '';

        if(isset($term_meta[$tem['name']])){

          $val = esc_attr($term_meta[$tem['name']]) ? esc_attr($term_meta[$tem['name']]) : '';
        }

        $class = 'setting-field medium';


        if($tem['type']=='media-upload') {
          $class.=' uploader-target';
        }

        //                echo DZSHelpers::generate_input_text($lab, array(
        //                    'class'=>$class,
        //                    'seekval'=>$val,
        //                    'id'=>$lab,
        //                ));

        echo DZSHelpers::generate_input_textarea($lab, array(
          'class'=>$class,
          'seekval'=>$val,
          'extraattr'=>' rows="5"',
          'id'=>$lab,
        ));


        ?>
        <?php

        ?>
        <p class="description"><?php _e('Enter a value for this field', 'pippin'); ?></p>
      </td>
    </tr>
    <?php
  }




  function save_taxonomy_custom_meta( $term_id ) {
    if ( isset( $_POST['term_meta'] ) ) {
      $t_id = $term_id;
      $term_meta = get_option( "taxonomy_$t_id" );
      $cat_keys = array_keys( $_POST['term_meta'] );
      foreach ( $cat_keys as $key ) {
        if ( isset ( $_POST['term_meta'][$key] ) ) {
          $term_meta[$key] = $_POST['term_meta'][$key];
        }
      }
      // Save the option array.
      update_option( "taxonomy_$t_id", $term_meta );
    }
  }

  function handle_admin_menu() {

    if ($this->pluginmode == 'theme') {
      $dzsap_page = add_theme_page(__('DZS ZoomSounds', 'dzsap'), __('DZS ZoomSounds', 'dzsap'), $this->admin_capability, $this->adminpagename, array($this, 'admin_page'));
    } else {
      //$dzsap_page = add_options_page(__('DZS ZoomSounds', 'dzsap'), __('DZS ZoomSounds', 'dzsap'), $this->admin_capability, $this->adminpagename, array($this, 'admin_page'));

      $dzsap_page = add_menu_page(__('ZoomSounds', 'dzsap'), __('ZoomSounds', 'dzsap'), $this->admin_capability, $this->adminpagename, array($this, 'admin_page'), 'div');
      $dzsap_subpage = add_submenu_page($this->adminpagename, 'ZoomSounds '.__('Player Configs', 'dzsap'), __('Player Configs', 'dzsap'), $this->admin_capability, $this->adminpagename_configs, array($this, 'admin_page_vpc'));
      $dzsap_subpage = add_submenu_page($this->adminpagename, __('ZoomSounds Settings', 'dzsap'), __('Settings', 'dzsap'), $this->admin_capability, $this->page_mainoptions_link, array($this, 'admin_page_mainoptions'));
    }
    //echo $dzsap_page;
  }

  function admin_scripts() {
    wp_enqueue_script('media-upload');
    wp_enqueue_script('tiny_mce');
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');
    wp_enqueue_script('dzsap_admin', $this->base_url . "admin/admin.js");
    wp_enqueue_style('dzsap_admin', $this->base_url . 'admin/admin.css');
    wp_enqueue_script('dzs.farbtastic', $this->base_url . "farbtastic/farbtastic.js");
    wp_enqueue_style('dzs.farbtastic', $this->base_url . 'farbtastic/farbtastic.css');

    wp_enqueue_style('dzs.scroller', $this->base_url . 'dzsscroller/scroller.css');
    wp_enqueue_script('dzs.scroller', $this->base_url . 'dzsscroller/scroller.js');
    wp_enqueue_style('dzs.dzstoggle', $this->base_url . 'dzstoggle/dzstoggle.css');
    wp_enqueue_script('dzs.dzstoggle', $this->base_url . 'dzstoggle/dzstoggle.js');
    wp_enqueue_script('jquery-ui-core');
    wp_enqueue_script('jquery-ui-sortable');


    if(isset($_GET['from']) && $_GET['from']=='shortcodegenerator'){

      wp_enqueue_style('dzs.remove_wp_bar', $this->base_url . 'tinymce/remove_wp_bar.css');

    }
  }

  function front_scripts() {
    wp_enqueue_script('dzsap', $this->base_url . "audioplayer/audioplayer.js");
    wp_enqueue_style('dzsap', $this->base_url . 'audioplayer/audioplayer.css');
    wp_enqueue_style('dzs.tooltip', $this->base_url . 'dzstooltip/dzstooltip.css');


  }

  function add_simple_field($pname, $otherargs = array()) {
    global $data;
    $fout = '';
    $val = '';

    $args = array(
      'val' => ''
    );
    $args = array_merge($args, $otherargs);

    $val = $args['val'];

    //====check if the data from database txt corresponds
    if (isset($data[$pname])) {
      $val = $data[$pname];
    }
    $fout.='<div class="setting"><input type="text" class="textinput short" name="' . $pname . '" value="' . $val . '"></div>';
    echo $fout;
  }

  function add_cb_field($pname) {
    global $data;
    $fout = '';
    $val = '';
    if (isset($data[$pname]))
      $val = $data[$pname];
    $checked = '';
    if ($val == 'on')
      $checked = ' checked';

    $fout.='<div class="setting"><input type="checkbox" class="textinput" name="' . $pname . '" value="on" ' . $checked . '/> on</div>';
    echo $fout;
  }

  function add_cp_field($pname, $otherargs = array()) {
    global $data;
    $fout = '';
    $val = '';


    $args = array(
      'val' => ''
    );

    $args = array_merge($args, $otherargs);



    //print_r($args);
    $val = $args['val'];

    //====check if the data from database txt corresponds
    if (isset($data[$pname])) {
      $val = $data[$pname];
    }

    $fout.='
<div class="setting"><input type="text" class="textinput short with-colorpicker" name="' . $pname . '" value="' . $val . '">
<div class="picker-con"><div class="the-icon"></div><div class="picker"></div></div>
</div>';
    echo $fout;
  }

  function misc_input_textarea($argname, $otherargs = array()) {
    $fout = '';
    $fout.='<textarea';
    $fout.=' name="' . $argname . '"';

    $margs = array(
      'class' => '',
      'val' => '', // === default value
      'seekval' => '', // ===the value to be seeked
      'type' => '',
    );
    $margs = array_merge($margs, $otherargs);



    if ($margs['class'] != '') {
      $fout.=' class="' . $margs['class'] . '"';
    }
    $fout.='>';
    if (isset($margs['seekval']) && $margs['seekval'] != '') {
      $fout.='' . $margs['seekval'] . '';
    } else {
      $fout.='' . $margs['val'] . '';
    }
    $fout.='</textarea>';

    return $fout;
  }

  function misc_generate_upload_btn($pargs = array()) {

    $margs = array(
      'label' => 'Upload'
    );

    if ($pargs == '' || is_array($pargs) == false) {
      $pargs = array();
    }

    $margs = array_merge($margs, $pargs);

    $uploadbtnstring = '<button class="button-secondary action upload_file ">' . $margs['label'] . '</button>';



    if ($this->mainoptions['usewordpressuploader'] != 'on') {
      $uploadbtnstring = '<div class="dzs-upload">
<form name="upload" action="#" method="POST" enctype="multipart/form-data">
    	<input type="button" value="' . $margs['label'] . '" class="btn_upl"/>
        <input type="file" name="file_field" class="file_field"/>
        <input type="submit" class="btn_submit"/>
</form>
</div>
<div class="feedback"></div>';
    }

    return $uploadbtnstring;
  }

  function misc_input_checkbox($argname, $argopts) {
    $fout = '';
    $auxtype = 'checkbox';

    if (isset($argopts['type'])) {
      if ($argopts['type'] == 'radio') {
        $auxtype = 'radio';
      }
    }
    $fout.='<input type="' . $auxtype . '"';
    $fout.=' name="' . $argname . '"';
    if (isset($argopts['class'])) {
      $fout.=' class="' . $argopts['class'] . '"';
    }
    $theval = 'on';
    if (isset($argopts['val'])) {
      $fout.=' value="' . $argopts['val'] . '"';
      $theval = $argopts['val'];
    } else {
      $fout.=' value="on"';
    }
    //print_r($this->mainoptions); print_r($argopts['seekval']);
    if (isset($argopts['seekval'])) {
      $auxsw = false;
      if (is_array($argopts['seekval'])) {
        //echo 'ceva'; print_r($argopts['seekval']);
        foreach ($argopts['seekval'] as $opt) {
          //echo 'ceva'; echo $opt; echo
          if ($opt == $argopts['val']) {
            $auxsw = true;
          }
        }
      } else {
        //echo $argopts['seekval']; echo $theval;
        if ($argopts['seekval'] == $theval) {
          //echo $argval;
          $auxsw = true;
        }
      }
      if ($auxsw == true) {
        $fout.=' checked="checked"';
      }
    }
    $fout.='/>';
    return $fout;
  }

  function admin_page_mainoptions() {
    //print_r($this->mainoptions);
    if (isset($_POST['dzsap_delete_plugin_data']) && $_POST['dzsap_delete_plugin_data'] == 'on') {
      delete_option($this->dbname_mainitems);
      delete_option($this->dbname_mainitems_configs);
      delete_option($this->dbname_options);
    }




    if(isset($_GET['dzsap_shortcode_builder']) && $_GET['dzsap_shortcode_builder']=='on'){
      dzsap_shortcode_builder();
    }else {


      if(isset($_GET['dzsap_shortcode_player_builder']) && $_GET['dzsap_shortcode_player_builder']=='on'){
        dzsap_shortcode_player_builder();
      }else{

        include_once "class_parts/admin-page-mainoptions.php";
      }
      //print_r($this->mainoptions);
      ?>


      <div class="clear"></div><br/>
      <?php
    }
  }

  function admin_page() {
    ?>
    <div class="wrap">
      <div class="import-export-db-con">
        <div class="the-toggle"></div>
        <div class="the-content-mask" style="">

          <div class="the-content">
            <h2><?php echo __("Whole Database"); ?></h2>
            <form enctype="multipart/form-data" action="" method="POST">

              <div class="">
                <h3><?php echo __("Import Whole Database"); ?></h3>
                <input name="dzsap_importdbupload" type="file" size="10"/><br />
              </div>
              <div class="">
                <input class="button-secondary" type="submit" name="dzsap_importdb" value="Import" />
              </div>
              <div class="clear"></div>
            </form>


            <div class="">
              <h3><?php echo __("Export Whole Database"); ?></h3>
            </div>
            <div class="">
              <form action="" method="POST"><input class="button-secondary" type="submit" name="dzsap_exportdb" value="Export"/></form>
            </div>
            <br>
            <br>
            <h1><?php echo __("OR"); ?></h1>
            <br>
            <br>


            <h2><?php echo __("Single Slider"); ?></h2>



            <form enctype="multipart/form-data" action="" method="POST">
              <div class="">
                <h3><?php echo __("Import a Single Slider");?></h3>
                <input name="importsliderupload" type="file" size="10"/><br />
              </div>
              <div class="">
                <input class="button-secondary" type="submit" name="dzsap_importslider" value="Import" />
              </div>
              <div class="clear"></div>
            </form>

          </div>
        </div>
      </div>
      <h2>DZS <?php _e('ZoomSounds Admin', 'dzsap'); ?>&nbsp; <span style="font-size:13px; font-weight: 100;">version <?php echo DZSAP_VERSION; ?></span> <img alt="" style="visibility: visible;" id="main-ajax-loading" src="<?php bloginfo('wpurl'); ?>/wp-admin/images/wpspin_light.gif"/></h2>
      <noscript><?php _e('You need javascript for this.', 'dzsap'); ?></noscript>
      <div class="top-buttons">
        <a href="<?php echo $this->base_url; ?>readme/index.html" class="button-secondary action"><?php _e('Documentation', 'dzsap'); ?></a>
        <div class="super-select db-select dzsap"><button class="button-secondary btn-show-dbs">Current Database - <span class="strong currdb"><?php
              if ($this->currDb == '') {
                echo 'main';
              } else {
                echo $this->currDb;
              }
              ?></span></button>
          <select class="main-select hidden"><?php
            //print_r($this->dbs);

            if (is_array($this->dbs)) {
              foreach ($this->dbs as $adb) {
                $params = array('dbname' => $adb);
                $newurl = add_query_arg($params, dzs_curr_url());
                echo '<option' . ' data-newurl="' . $newurl . '"' . '>' . $adb . '</option>';
              }
            } else {
              $params = array('dbname' => 'main');
              $newurl = add_query_arg($params, dzs_curr_url());
              echo '<option' . ' data-newurl="' . $newurl . '"' . ' selected="selected"' . '>' . $adb . '</option>';
            }
            ?></select><div class="hidden replaceurlhelper"><?php
            $params = array('dbname' => 'replaceurlhere');
            $newurl = add_query_arg($params, dzs_curr_url());
            echo $newurl;
            ?></div>
        </div>
      </div>
      <table cellspacing="0" class="wp-list-table widefat dzs_admin_table main_sliders">
        <thead>
        <tr>
          <th style="" class="manage-column column-name" id="name" scope="col"><?php _e('ID', 'dzsap'); ?></th>
          <th class="column-edit">Edit</th>
          <th class="column-edit">Embed</th>
          <th class="column-edit">Export</th>
          <?php
          if ($this->mainoptions['is_safebinding'] != 'on') {
            ?>
            <th class="column-edit">Duplicate</th>
            <?php
          }
          ?>
          <th class="column-edit">Delete</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      <?php
      $url_add = '';
      $url_add = '';
      $items = $this->mainitems;
      //echo count($items);

      $aux = remove_query_arg('deleteslider', admin_url('admin.php?page='.$this->adminpagename.'&adder=adder'));

      $nextslidernr = count($items);
      if ($nextslidernr < 1) {
        //$nextslidernr = 1;
      }
      $params = array('currslider' => $nextslidernr);
      $url_add = add_query_arg($params, $aux);
      ?>
      <a class="button-secondary add-slider" href="<?php echo $url_add; ?>"><?php _e('Add Playlist', 'dzsap'); ?></a>
      <form class="master-settings">
      </form>
      <div class="saveconfirmer"><?php _e('Loading...', 'dzsap'); ?></div>
      <a href="#" class="button-primary master-save"></a> <img alt="" style="position:fixed; bottom:18px; right:125px; visibility: hidden;" id="save-ajax-loading" src="<?php bloginfo('wpurl'); ?>/wp-admin/images/wpspin_light.gif"/>

      <a href="#" class="button-primary master-save"><?php _e('Save All Galleries', 'dzsap'); ?></a>
      <a href="#" class="button-primary slider-save"><?php _e('Save Gallery', 'dzsap'); ?></a>
    </div>
    <script>
      <?php
      //$jsnewline = '\\' + "\n";
      $aux = str_replace(array("\r", "\r\n", "\n"), '', $this->sliderstructure);
      echo "var sliderstructure = '" . $aux . "';
    ";
      $aux = str_replace(array("\r", "\r\n", "\n"), '', $this->itemstructure);
      echo "var itemstructure = '" . $aux . "';
    ";
      $aux = str_replace(array("\r", "\r\n", "\n"), '', $this->videoplayerconfig);
      echo "var videoplayerconfig = '" . $aux . "';
    ";
      ?>
      jQuery(document).ready(function($) {
        sliders_ready($);
        if (jQuery.fn.multiUploader) {
          jQuery('.dzs-multi-upload').multiUploader();
        }
        <?php
        $items = $this->mainitems;
        for ($i = 0; $i < count($items); $i++) {
          //print_r($items[$i]);
          $aux = '';
          if (isset($items[$i]) && isset($items[$i]['settings']) && isset($items[$i]['settings']['id'])) {
            //echo $items[$i]['settings']['id'];
            $aux = '{ name: "' . $items[$i]['settings']['id'] . '"}';
          }
          echo "sliders_addslider(" . $aux . ");";
        }
        if (count($items) > 0)
          echo 'sliders_showslider(0);';
        for ($i = 0; $i < count($items); $i++) {
          //echo $i . $this->currSlider . 'cevava';
          if (($this->mainoptions['is_safebinding'] != 'on' || $i == $this->currSlider) && is_array($items[$i])) {

            //==== jsi is the javascript I, if safebinding is on then the jsi is always 0 ( only one gallery )
            $jsi = $i;
            if ($this->mainoptions['is_safebinding'] == 'on') {
              $jsi = 0;
            }

            for ($j = 0; $j < count($items[$i]) - 1; $j++) {
              echo "sliders_additem(" . $jsi . ");";
            }

            foreach ($items[$i] as $label => $value) {
              if ($label === 'settings') {
                if (is_array($items[$i][$label])) {
                  foreach ($items[$i][$label] as $sublabel => $subvalue) {
                    $subvalue = (string) $subvalue;
                    $subvalue = stripslashes($subvalue);
                    $subvalue = str_replace(array("\r", "\r\n", "\n", '\\', "\\"), '', $subvalue);
                    $subvalue = str_replace(array("'"), '"', $subvalue);
                    $subvalue = str_replace(array("</script>"), '{{scriptend}}', $subvalue);
                    echo 'sliders_change(' . $jsi . ', "settings", "' . $sublabel . '", ' . "'" . $subvalue . "'" . ');';
                  }
                }
              } else {

                if (is_array($items[$i][$label])) {
                  foreach ($items[$i][$label] as $sublabel => $subvalue) {
                    $subvalue = (string) $subvalue;
                    $subvalue = stripslashes($subvalue);
                    $subvalue = str_replace(array("\r", "\r\n", "\n", '\\', "\\"), '', $subvalue);
                    $subvalue = str_replace(array("'"), '"', $subvalue);
                    $subvalue = str_replace(array("</script>"), '{{scriptend}}', $subvalue);
                    if ($label == '') {
                      $label = '0';
                    }
                    echo 'sliders_change(' . $jsi . ', ' . $label . ', "' . $sublabel . '", ' . "'" . $subvalue . "'" . ');';
                  }
                }
              }
            }
            if ($this->mainoptions['is_safebinding'] == 'on') {
              break;
            }
          }
        }
        ?>
        jQuery('#main-ajax-loading').css('visibility', 'hidden');
        if (dzsap_settings.is_safebinding == "on") {
          jQuery('.master-save').remove();
          if (dzsap_settings.addslider == "on") {
            sliders_addslider();
            window.currSlider_nr = -1
            sliders_showslider(0);
          }
          jQuery('.slider-in-table').each(function() {
            jQuery(this).children('.button_view').eq(3).remove();
          });
        }
        check_global_items();
        sliders_allready();
      });
    </script>
    <?php
  }

  function admin_page_vpc() {
    ?>
    <div class="wrap">
      <div class="import-export-db-con">
        <div class="the-toggle"></div>
        <div class="the-content-mask" style="">

          <div class="the-content">
            <form enctype="multipart/form-data" action="" method="POST">
              <div class="one_half">
                <h3>Import Database</h3>
                <input name="dzsap_importdbupload" type="file" size="10"/><br />
              </div>
              <div class="one_half last alignright">
                <input class="button-secondary" type="submit" name="dzsap_importdb" value="Import" />
              </div>
              <div class="clear"></div>
            </form>


            <form enctype="multipart/form-data" action="" method="POST">
              <div class="one_half">
                <h3>Import Slider</h3>
                <input name="importsliderupload" type="file" size="10"/><br />
              </div>
              <div class="one_half last alignright">
                <input class="button-secondary" type="submit" name="dzsap_importslider" value="Import" />
              </div>
              <div class="clear"></div>
            </form>

            <div class="one_half">
              <h3>Export Database</h3>
            </div>
            <div class="one_half last alignright">
              <form action="" method="POST"><input class="button-secondary" type="submit" name="dzsap_exportdb" value="Export"/></form>
            </div>
            <div class="clear"></div>

          </div>
        </div>
      </div>
      <h2>DZS <?php _e('ZoomSounds Admin', 'dzsap'); ?> <img alt="" style="visibility: visible;" id="main-ajax-loading" src="<?php bloginfo('wpurl'); ?>/wp-admin/images/wpspin_light.gif"/></h2>
      <noscript><?php _e('You need javascript for this.', 'dzsap'); ?></noscript>
      <div class="top-buttons">
        <a href="<?php echo $this->base_url; ?>readme/index.html" class="button-secondary action"><?php _e('Documentation', 'dzsap'); ?></a>

      </div>
      <table cellspacing="0" class="wp-list-table widefat dzs_admin_table main_sliders">
        <thead>
        <tr>
          <th style="" class="manage-column column-name" id="name" scope="col"><?php _e('ID', 'dzsap'); ?></th>
          <th class="column-edit">Edit</th>
          <th class="column-edit">Embed</th>
          <th class="column-edit">Export</th>
          <?php
          if ($this->mainoptions['is_safebinding'] != 'on') {
            ?>
            <th class="column-edit">Duplicate</th>
            <?php
          }
          ?>
          <th class="column-edit">Delete</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      <?php
      $url_add = '';
      $url_add = '';
      $items = $this->mainitems_configs;
      //echo count($items);
      //print_r($items);

      $aux = remove_query_arg('deleteslider', dzs_curr_url());
      $aux = admin_url('admin.php?page='.$this->adminpagename_configs.'&adder=adder');
      $params = array('currslider' => count($items));
      $url_add = add_query_arg($params, $aux);
      ?>
      <a class="button-secondary add-slider" href="<?php echo $url_add; ?>"><?php _e('Add Configuration', 'dzsap'); ?></a>
      <form class="master-settings only-settings-con mode_vpconfigs">
      </form>
      <div class="saveconfirmer"><?php _e('Loading...', 'dzsap'); ?></div>
      <a href="#" class="button-primary master-save-vpc"></a> <img alt="" style="position:fixed; bottom:18px; right:125px; visibility: hidden;" id="save-ajax-loading" src="<?php bloginfo('wpurl'); ?>/wp-admin/images/wpspin_light.gif"/>

      <a href="#" class="button-primary master-save-vpc"><?php _e('Save All Configs', 'dzsap'); ?></a>
      <a href="#" class="button-primary slider-save-vpc"><?php _e('Save Config', 'dzsap'); ?></a>
    </div>
    <script>
      <?php
      //$jsnewline = '\\' + "\n";
      $aux = str_replace(array("\r", "\r\n", "\n"), '', $this->sliderstructure);
      echo "var sliderstructure = '" . $aux . "';
    ";
      $aux = str_replace(array("\r", "\r\n", "\n"), '', $this->itemstructure);
      echo "var itemstructure = '" . $aux . "';
    ";
      $aux = str_replace(array("\r", "\r\n", "\n"), '', $this->videoplayerconfig);
      echo "var videoplayerconfig = '" . $aux . "';
    ";
      ?>
      jQuery(document).ready(function($) {
        sliders_ready($);
        if (jQuery.fn.multiUploader) {
          jQuery('.dzs-multi-upload').multiUploader();
        }
        <?php
        $items = $this->mainitems_configs;
        for ($i = 0; $i < count($items); $i++) {
          //print_r($items[$i]);
          $aux = '';
          if (isset($items[$i]) && isset($items[$i]['settings']) && isset($items[$i]['settings']['id'])) {
            //echo $items[$i]['settings']['id'];
            $aux = '{ name: "' . $items[$i]['settings']['id'] . '"}';
          }
          echo "sliders_addslider(" . $aux . ");";
        }
        if (count($items) > 0)
          echo 'sliders_showslider(0);';
        for ($i = 0; $i < count($items); $i++) {
          //echo $i . $this->currSlider . 'cevava';
          if (($this->mainoptions['is_safebinding'] != 'on' || $i == $this->currSlider) && is_array($items[$i])) {

            //==== jsi is the javascript I, if safebinding is on then the jsi is always 0 ( only one gallery )
            $jsi = $i;
            if ($this->mainoptions['is_safebinding'] == 'on') {
              $jsi = 0;
            }

            for ($j = 0; $j < count($items[$i]) - 1; $j++) {
              echo "sliders_additem(" . $jsi . ");";
            }

            foreach ($items[$i] as $label => $value) {
              if ($label === 'settings') {
                if (is_array($items[$i][$label])) {
                  foreach ($items[$i][$label] as $sublabel => $subvalue) {
                    $subvalue = (string) $subvalue;
                    $subvalue = stripslashes($subvalue);
                    $subvalue = str_replace(array("\r", "\r\n", "\n", '\\', "\\"), '', $subvalue);
                    $subvalue = str_replace(array("'"), '"', $subvalue);
                    $subvalue = str_replace(array("</script>"), '{{scriptend}}', $subvalue);
                    echo 'sliders_change(' . $jsi . ', "settings", "' . $sublabel . '", ' . "'" . $subvalue . "'" . ');';
                  }
                }
              } else {

                if (is_array($items[$i][$label])) {
                  foreach ($items[$i][$label] as $sublabel => $subvalue) {
                    $subvalue = (string) $subvalue;
                    $subvalue = stripslashes($subvalue);
                    $subvalue = str_replace(array("\r", "\r\n", "\n", '\\', "\\"), '', $subvalue);
                    $subvalue = str_replace(array("'"), '"', $subvalue);
                    $subvalue = str_replace(array("</script>"), '{{scriptend}}', $subvalue);
                    if ($label == '') {
                      $label = '0';
                    }
                    echo 'sliders_change(' . $jsi . ', ' . $label . ', "' . $sublabel . '", ' . "'" . $subvalue . "'" . ');';
                  }
                }
              }
            }
            if ($this->mainoptions['is_safebinding'] == 'on') {
              break;
            }
          }
        }
        ?>
        jQuery('#main-ajax-loading').css('visibility', 'hidden');
        if (dzsap_settings.is_safebinding == "on") {
          jQuery('.master-save-vpc').remove();
          if (dzsap_settings.addslider == "on") {
            //console.log(dzsap_settings.addslider)
            sliders_addslider();
            window.currSlider_nr = -1
            sliders_showslider(0);
          }
          jQuery('.slider-in-table').each(function() {
            jQuery(this).children('.button_view').eq(3).remove();
          });
        }
        check_global_items();
        sliders_allready();
      });
    </script>
    <?php
  }

  function post_options() {
    //// POST OPTIONS ///

    if (isset($_POST['dzsap_exportdb'])) {


      //===setting up the db
      $currDb = '';
      if (isset($_POST['currdb']) && $_POST['currdb'] != '') {
        $this->currDb = $_POST['currdb'];
        $currDb = $this->currDb;
      }

      //echo 'ceva'; print_r($this->dbs);
      if ($currDb != 'main' && $currDb != '') {
        $this->dbname_mainitems.='-' . $currDb;
        $this->mainitems = get_option($this->dbname_mainitems);
      }
      //===setting up the db END

      header('Content-Type: text/plain');
      header('Content-Disposition: attachment; filename="' . "dzsap_backup.txt" . '"');
      echo serialize($this->mainitems);
      die();
    }

    if (isset($_POST['dzsap_exportslider'])) {


      //===setting up the db
      $currDb = '';
      if (isset($_POST['currdb']) && $_POST['currdb'] != '') {
        $this->currDb = $_POST['currdb'];
        $currDb = $this->currDb;
      }

      //echo 'ceva'; print_r($this->dbs);
      if ($currDb != 'main' && $currDb != '') {
        $this->dbname_mainitems.='-' . $currDb;
        $this->mainitems = get_option($this->dbname_mainitems);
      }
      //===setting up the db END
      //print_r($currDb);

      header('Content-Type: text/plain');
      header('Content-Disposition: attachment; filename="' . "dzsap-slider-" . $_POST['slidername'] . ".txt" . '"');
      //print_r($_POST);
      echo serialize($this->mainitems[$_POST['slidernr']]);
      die();
    }


    if (isset($_POST['dzsap_importdb'])) {
      //print_r( $_FILES);
      $file_data = file_get_contents($_FILES['dzsap_importdbupload']['tmp_name']);
      $aux  = unserialize($file_data);

      if(is_array($aux)){

        $this->mainitems = array_merge($this->mainitems, $aux);
        update_option($this->dbname_mainitems, $this->mainitems);
      }
    }

    if (isset($_POST['dzsap_importslider'])) {
      //print_r( $_FILES);
      $file_data = file_get_contents($_FILES['importsliderupload']['tmp_name']);
      $auxslider = unserialize($file_data);
      //replace_in_matrix('http://localhost/wpmu/eos/wp-content/themes/eos/', THEME_URL, $this->mainitems);
      //replace_in_matrix('http://eos.digitalzoomstudio.net/wp-content/themes/eos/', THEME_URL, $this->mainitems);
      //echo 'ceva';
      //print_r($auxslider);
      $this->mainitems = get_option($this->dbname_mainitems);
      //print_r($this->mainitems);
      $this->mainitems[] = $auxslider;

      update_option($this->dbname_mainitems, $this->mainitems);
    }

    if (isset($_POST['dzsap_saveoptions'])) {
      $this->mainoptions['usewordpressuploader'] = $_POST['usewordpressuploader'];
      $this->mainoptions['embed_prettyphoto'] = $_POST['embed_prettyphoto'];
      $this->mainoptions['use_external_uploaddir'] = $_POST['use_external_uploaddir'];
      $this->mainoptions['disable_prettyphoto'] = $_POST['disable_prettyphoto'];


      update_option($this->dbname_options, $this->mainoptions);
    }
  }



  function misc_get_ip() {

    if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
      $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
      $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
      $ip = (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
    }

    $ip = filter_var($ip, FILTER_VALIDATE_IP);
    $ip = ($ip === false) ? '0.0.0.0' : $ip;


    return $ip;
  }


  function post_save_mo() {
    $auxarray = array();
    //parsing post data
    parse_str($_POST['postdata'], $auxarray);
//        print_r($auxarray);

    $auxarray_before = array(
      'use_external_uploaddir' => 'off'
    );


    $auxarray = array_merge($auxarray_before, $auxarray);

    if ($auxarray['use_external_uploaddir'] == 'on') {

      $path_uploaddir = dirname(dirname(dirname(__FILE__))) . '/upload';
      if (is_dir($path_uploaddir) === false) {
        mkdir($path_uploaddir, 0755);
      }
      $path_uploaddir_waves = dirname(dirname(dirname(__FILE__))) . '/upload/waves';
      if (is_dir($path_uploaddir_waves) === false) {
        mkdir($path_uploaddir_waves, 0755);
      }
    }



    if (isset($auxarray['track_downloads']) && $auxarray['track_downloads'] == 'on') {
//            echo 'hmmdadadadada';


      global $wpdb;





      $table_name = $wpdb->prefix . 'dzsap_activity';
      if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        //table not in database. Create new table
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
          id mediumint(9) NOT NULL AUTO_INCREMENT,
          type varchar(100) NOT NULL,
          id_user int(10) NOT NULL,
          ip varchar(255) NOT NULL,
          id_video varchar(255) NOT NULL,
          date datetime NOT NULL,
          UNIQUE KEY id (id)
     ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

      } else {
      }

      update_option('dzsap_table_activity_created','on');

    }

    update_option($this->dbname_options, $auxarray);
    die();
  }

  function post_save() {
    //---this is the main save function which saves item
    $auxarray = array();
    $mainarray = array();

    //print_r($this->mainitems);
    //parsing post data
    parse_str($_POST['postdata'], $auxarray);


    if (isset($_POST['currdb'])) {
      $this->currDb = $_POST['currdb'];
    }
    //echo 'ceva'; print_r($this->dbs);
    if ($this->currDb != 'main' && $this->currDb != '') {
      $this->dbname_mainitems.='-' . $this->currDb;
    }
    //echo $this->dbname_mainitems;
    if (isset($_POST['sliderid'])) {
      //print_r($auxarray);
      $mainarray = get_option($this->dbname_mainitems);
      foreach ($auxarray as $label => $value) {
        $aux = explode('-', $label);
        $tempmainarray[$aux[1]][$aux[2]] = $auxarray[$label];
      }
      $mainarray[$_POST['sliderid']] = $tempmainarray;
    } else {
      foreach ($auxarray as $label => $value) {
        //echo $auxarray[$label];
        $aux = explode('-', $label);
        $mainarray[$aux[0]][$aux[1]][$aux[2]] = $auxarray[$label];
      }
    }
    echo $this->dbname_mainitems;
//        print_r($_POST);
//        print_r($this->currDb);
    echo isset($_POST['currdb']);
//        print_r($mainarray);
    update_option($this->dbname_mainitems, $mainarray);
    echo 'success';
    die();
  }

  function post_save_configs() {
    //---this is the main save function which saves item
    $auxarray = array();
    $mainarray = array();

    //print_r($this->mainitems);
    //parsing post data
    parse_str($_POST['postdata'], $auxarray);


    if (isset($_POST['currdb'])) {
      $this->currDb = $_POST['currdb'];
    }
    //echo 'ceva'; print_r($this->dbs);
    if ($this->currDb != 'main' && $this->currDb != '') {
      $this->dbname_mainitems_configs.='-' . $this->currDb;
    }
    //echo $this->dbname_mainitems;
    if (isset($_POST['sliderid'])) {
      //print_r($auxarray);
      $mainarray = get_option($this->dbname_mainitems_configs);
      foreach ($auxarray as $label => $value) {
        $aux = explode('-', $label);
        $tempmainarray[$aux[1]][$aux[2]] = $auxarray[$label];
      }
      $mainarray[$_POST['sliderid']] = $tempmainarray;
    } else {
      foreach ($auxarray as $label => $value) {
        //echo $auxarray[$label];
        $aux = explode('-', $label);
        $mainarray[$aux[0]][$aux[1]][$aux[2]] = $auxarray[$label];
      }
    }
    //echo $this->dbname_mainitems; print_r($_POST); print_r($this->currDb); echo isset($_POST['currdb']);
    update_option($this->dbname_mainitems_configs, $mainarray);
    echo 'success';
    die();
  }

  function filter_attachment_fields_to_edit($form_fields, $post) {


    $vpconfigsstr = '';
    $the_id = $post->ID;
    $post_type = get_post_mime_type($the_id);
    //print_r($this->mainitems_configs);
    ////print_r($post);


    if (strpos($post_type, "audio") === false) {
      return $form_fields;
    }

    foreach ($this->mainitems_configs as $vpconfig) {
      //print_r($vpconfig);
      $vpconfigsstr .='<option value="' . $vpconfig['settings']['id'] . '">' . $vpconfig['settings']['id'] . '</option>';
    }



    $html_sel = '<select class="styleme" id="attachments-' . $post->ID . '-dzsap-config" name="attachments[' . $post->ID . '][dzsap-config]"><option value="default">Default Settings</option>';
    $html_sel.=$vpconfigsstr;
    $html_sel .='</select>';
    //$html_sel.='<div>'.$post_type.'</div>';

    $form_fields['dzsap-config'] = array(
      'label' => 'ZoomSounds Player Config',
      'input' => 'html',
      'html' => $html_sel,
      'helps' => 'choose a configuration for the player / edit in ZoomSounds > Player Configs',
    );



    if($this->mainoptions['skinwave_wave_mode']!='canvas') {

      $lab = 'waveformbg';
//        print_r($post);

      $loc = $post->guid;

      if (wp_get_attachment_url($post->id)) {
        $loc = wp_get_attachment_url($post->id);
      }

//        echo 'url -> '.$loc;

      $html_input = '<div class="aux-file-location" style="display:none;">' . $loc . '</div><input id="attachments-' . $post->ID . '-' . $lab . '" class="textinput upload-prev main-thumb" name="attachments[' . $post->ID . '][' . $lab . ']"';
      if (get_post_meta($the_id, '_' . $lab, true) != '') {
        $html_input .= ' value="' . get_post_meta($the_id, '_' . $lab, true) . '"';
      }
      $html_input .= '/><span class="aux-wave-generator"><button class="btn-autogenerate-waveform-bg button-secondary">Auto Generate</button></span> &nbsp;<button class="btn-generate-default-waveform-bg button-secondary">Default Waveform</button>';

      $form_fields[$lab] = array(
        'label' => 'Waveform Background',
        'input' => 'html',
        'html' => $html_input,
        'helps' => '* only for skin-wave / the path to the waveform bg file / auto generate the wave form by cliking the auto generate button and then the orange button that appears ( wait for loading ) <br> <em>note: only recommded for regular songs ( under 5-6 minutes ) - anything else then that is very cpu extensive / better to use a fake waveform ( the default waveform button ) ',
      );


      $lab = 'waveformprog';
      $html_input = '<div class="aux-file-location" style="display:none;">' . $loc . '</div><input id="attachments-' . $post->ID . '-' . $lab . '" class="textinput upload-prev main-thumb" name="attachments[' . $post->ID . '][' . $lab . ']"';
      if (get_post_meta($the_id, '_' . $lab, true) != '') {
        $html_input .= ' value="' . get_post_meta($the_id, '_' . $lab, true) . '"';
      }
      $html_input .= '/><span class="aux-wave-generator"><button class="btn-autogenerate-waveform-prog button-secondary">Auto Generate</button></span> &nbsp;<button class="btn-generate-default-waveform-prog button-secondary">Default Waveform</button>';

      $form_fields[$lab] = array(
        'label' => 'Waveform Progress',
        'input' => 'html',
        'html' => $html_input,
        'helps' => '* only for skin-wave / the path to the waveform progress file / auto generate the wave form by cliking the auto generate button and then the orange button that appears',
      );

    }








    $lab = 'dzsap-thumb';
    $html_input = '<input id="attachments-' . $post->ID . '-' . $lab . '" class="upload-target-prev" name="attachments[' . $post->ID . '][' . $lab . ']"';
    if (get_post_meta($the_id, '_' . $lab, true) != '') {
      $html_input.=' value="' . get_post_meta($the_id, '_' . $lab, true) . '"';
    }
    $html_input.='/><a href="#" class="upload-for-target button-secondary">' . __('Upload', 'dzsap') . '</a>';

    $form_fields[$lab] = array(
      'label' => __('Thumbnail','dzsap'),
      'input' => 'html',
      'html' => $html_input,
      'helps' => __('choose a thumbnail / optional','dzsap'),
    );


    $lab = 'dzsap_sourceogg';
    $html_input = '<input id="attachments-' . $post->ID . '-' . $lab . '" class="upload-target-prev upload-type-audio" name="attachments[' . $post->ID . '][' . $lab . ']"';
    if (get_post_meta($the_id, '_' . $lab, true) != '') {
      $html_input.=' value="' . get_post_meta($the_id, '_' . $lab, true) . '"';
    }
    $html_input.='/><button class="upload-for-target button-secondary">' . __('Upload', 'dzsap') . '</button>';

    $form_fields[$lab] = array(
      'label' => __('OGG Source'),
      'input' => 'html',
      'html' => $html_input,
      'helps' => 'optional - if you do not set this, the full flash player backup will kick in.',
    );




    return $form_fields;
  }

  function filter_attachment_fields_to_save($post, $attachment) {
    //print_r($post);
    $pid = $post['ID'];
    $lab = 'waveformbg';
    //print_r($attachment);
    if (isset($attachment[$lab])) {
      update_post_meta($pid, '_' . $lab, $attachment[$lab]);
    }
    $lab = 'waveformprog';
    if (isset($attachment[$lab])) {
      update_post_meta($pid, '_' . $lab, $attachment[$lab]);
    }
    $lab = 'dzsap-thumb';
    if (isset($attachment[$lab])) {
      update_post_meta($pid, '_' . $lab, $attachment[$lab]);
    }
    $lab = 'dzsap_sourceogg';
    if (isset($attachment[$lab])) {
      update_post_meta($pid, '_' . $lab, $attachment[$lab]);
    }
    return $post;
  }

}



//This function is used to encrypt data.
if(function_exists('simple_encrypt')==false) {
  function simple_encrypt($text, $salt = "20"){


    if(strlen($salt)>16){
      $salt = substr($salt, 0 ,16);
    }
    if($salt=='' || strlen($salt)!=16){
      $salt = '1111222233334444';
    }

    return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
  }
}
// This function will be used to decrypt data.
if(function_exists('simple_decrypt')==false) {
  function simple_decrypt($text, $salt = ""){

    if(strlen($salt)>16){
      $salt = substr($salt, 0 ,16);
    }
    if($salt=='' || strlen($salt)!=16){
      $salt = '1111222233334444';
    }
    return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
  }
};
$dzsap_got_category_feed = false;

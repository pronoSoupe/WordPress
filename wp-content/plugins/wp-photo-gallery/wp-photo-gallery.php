<?php
    /* 
    Plugin Name: wp photo gallery
    Plugin URI:http://www.i13websolution.com/wordpress-pro-plugins/wordpress-photo-gallery-pro-plugin.html 
    Author URI:http://www.i13websolution.com/wordpress-pro-plugins/wordpress-photo-gallery-pro-plugin.html
    Description: This is beautiful photo gallery + slider plugin for WordPress.Add any number of images from admin panel.
    Author:I Thirteen Web Solution 
    Version:1.0
    */

    $dir = plugin_dir_path( __FILE__ );
    $dir=str_replace("\\","/",$dir);
    require_once($dir.'classes/class.Images.php');

    add_action('admin_menu', 'add_my_photo_gallery_admin_menu');
    //add_action( 'admin_init', 'my_photo_gallery_admin_init' );
    register_activation_hook(__FILE__,'install_my_photo_gallery');
    add_action('wp_enqueue_scripts', 'my_photo_gallery_load_styles_and_js');
    add_shortcode('print_my_photo_gallery', 'print_my_photo_gallery_func' );
    add_filter('widget_text', 'do_shortcode');

    function my_photo_gallery_load_styles_and_js(){

        wp_enqueue_style( 'jquery.galleryview-3.0-dev', plugins_url('/css/jquery.galleryview-3.0-dev.css', __FILE__) );
        wp_enqueue_script('jquery'); 
        wp_enqueue_script("jquery-ui-core");
        wp_enqueue_script('jquery.timers-1.2',plugins_url('/js/jquery.timers-1.2.js', __FILE__));
        wp_enqueue_script('jquery.easing.1.3',plugins_url('/js/jquery.easing.1.3.js', __FILE__));
        wp_enqueue_script('jquery.gview-3.0-dev',plugins_url('/js/jquery.gview-3.0-dev.js', __FILE__));


    }

    function install_my_photo_gallery(){
        global $wpdb;
        $table_name = $wpdb->prefix . "gv_slider";

        $sql = "CREATE TABLE " . $table_name . " (
        id int(10) unsigned NOT NULL auto_increment,
        title varchar(1000) NOT NULL,
        image_name varchar(500) NOT NULL,
        createdon datetime NOT NULL,
        custom_link varchar(1000) default NULL,
        post_id int(10) unsigned default NULL,
        PRIMARY KEY  (id)
        );";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);


        $my_photo_gallery_slider_settings=array('transition_speed' => '1000',
            'transition_interval' => '4000',
            'show_panels' =>'1',
            'show_panel_nav' =>'1',
            'enable_overlays' => '0',
            'panel_width'=>'550',
            'panel_height' => '400',
            'panel_animation' => 'fade',
            'panel_scale' => 'fit',
            'overlay_position'=> 'bottom',
            'pan_images' => '1',
            'pan_style'=>'drag',
            'start_frame'=>'1',
            'show_filmstrip'=>'1',
            'show_filmstrip_nav'=>'0',
            'enable_slideshow'=>'1',
            'autoplay'=>'1',
            'filmstrip_position'=>'bottom',
            'frame_width'=>80,
            'frame_height'=>80,
            'frame_opacity'=>0.4,
            'frame_scale'=>'crop',
            'filmstrip_style'=>'scroll',
            'frame_gap'=>1,
            'show_captions'=>0,
            'show_infobar'=>0,
            'infobar_opacity'=>1
        );

        if( !get_option( 'my_photo_gallery_slider_settings' ) ) {

            update_option('my_photo_gallery_slider_settings',$my_photo_gallery_slider_settings);
        } 

    } 




    function add_my_photo_gallery_admin_menu(){

        $hook_suffix_p_g_n=add_menu_page( __( 'Photo Gallery'), __( 'Photo Gallery' ), 'administrator', 'photo_gallery_slider', 'photo_gallery_slider_admin_options' );
        $hook_suffix_p_g_n=add_submenu_page( 'photo_gallery_slider', __( 'Slider Setting'), __( 'Slider Setting' ),'administrator', 'photo_gallery_slider', 'photo_gallery_slider_admin_options' );
        $hook_suffix_p_g_n_1=add_submenu_page( 'photo_gallery_slider', __( 'Manage Images'), __( 'Manage Images'),'administrator', 'photo_gallery_image_management', 'photo_gallery_image_management' );
        $hook_suffix_p_g_n_2=add_submenu_page( 'photo_gallery_slider', __( 'Preview Slider'), __( 'Preview Slider'),'administrator', 'photo_gallery_slider_preview', 'photo_gallery_slider_admin_preview' );

        add_action( 'load-' . $hook_suffix_p_g_n , 'my_photo_gallery_admin_init' );
        add_action( 'load-' . $hook_suffix_p_g_n_1 , 'my_photo_gallery_admin_init' );
        add_action( 'load-' . $hook_suffix_p_g_n_2 , 'my_photo_gallery_admin_init');

    }

    function my_photo_gallery_admin_init(){

        $url = plugin_dir_url(__FILE__);  

        wp_enqueue_style( 'admin-css', plugins_url('/css/admin-css.css', __FILE__) );
        wp_enqueue_style( 'jquery.galleryview-3.0-dev', plugins_url('/css/jquery.galleryview-3.0-dev.css', __FILE__) );
        wp_enqueue_script('jquery'); 
        wp_enqueue_script("jquery-ui-core");
        wp_enqueue_script('jquery.timers-1.2',plugins_url('/js/jquery.timers-1.2.js', __FILE__));
        wp_enqueue_script('jquery.easing.1.3',plugins_url('/js/jquery.easing.1.3.js', __FILE__));
        wp_enqueue_script('jquery.gview-3.0-dev',plugins_url('/js/jquery.gview-3.0-dev.js', __FILE__));
        wp_enqueue_script('jquery.validate',plugins_url('/js/jquery.validate.js', __FILE__));


    }

    function photo_gallery_slider_admin_options(){

        if(isset($_POST['btnsave'])){

            $options=array();
            $options['transition_speed']       =(int)trim($_POST['transition_speed']);
            $options['transition_interval']    =(int)trim($_POST['transition_interval']);
            $options['show_panel_nav']         =(int)trim($_POST['show_panel_nav']);
            $options['panel_width']            =(int)trim($_POST['panel_width']);
            $options['panel_height']           =(int)trim($_POST['panel_height']);
            $options['panel_height']           =(int)trim($_POST['panel_height']);
            $options['panel_scale']            =trim($_POST['panel_scale']);
            $options['pan_style'   ]           =trim($_POST['pan_style']);
            $options['pan_images']             =(int)trim($_POST['pan_images']);
            $options['show_filmstrip']         =(int)trim($_POST['show_filmstrip']);
            $options['autoplay']               =(int)trim($_POST['autoplay']);
            $options['frame_width']            =(int)trim($_POST['frame_width']);
            $options['frame_height']           =(int)trim($_POST['frame_height']);
            $options['frame_opacity']          =trim($_POST['frame_opacity']);
            $options['frame_scale']            =trim($_POST['frame_scale']);
            $options['filmstrip_style']        ='scroll';
            $options['frame_gap']              =(int)trim($_POST['frame_gap']);
            $options['show_infobar']           =(int)trim($_POST['show_infobar']);
            $options['infobar_opacity']        =trim($_POST['infobar_opacity']);
            $options['start_frame']            =1;
            $options['panel_animation']        ='fade';
            $options['overlay_position']       ='bottom';
            $options['filmstrip_position']     ='bottom';
            $options['enable_overlays']        =0;
            $options['show_captions']          =0;
            $options['show_filmstrip_nav']     =0;
            $options['show_panels']            =1;

            if((int)trim($_POST['autoplay']))
                $options['enable_slideshow']       =1;
            else   
                $options['enable_slideshow']       =0;

            $settings=update_option('my_photo_gallery_slider_settings',$options); 
            $my_photo_gallery_slider_settings_messages=array();
            $my_photo_gallery_slider_settings_messages['type']='succ';
            $my_photo_gallery_slider_settings_messages['message']='Settings saved successfully.';
            update_option('my_photo_gallery_slider_settings_messages', $my_photo_gallery_slider_settings_messages);



        }  
        $settings=get_option('my_photo_gallery_slider_settings');

    ?>      
    <div style="width: 100%;">  
        <div style="float:left;width:100%;">
            <div class="wrap">
                <table><tr><td><a href="https://twitter.com/FreeAdsPost" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @FreeAdsPost</a>
                            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></td>
                        <td>
                            <a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
                                <img id="help us for free plugin" height="30" width="90" src="http://www.i13websolution.com/images/paypaldonate.jpg" border="0" alt="help us for free plugin" title="help us for free plugin">
                            </a>
                        </td>
                    </tr>
                </table>

                <?php
                    $messages=get_option('my_photo_gallery_slider_settings_messages'); 
                    $type='';
                    $message='';
                    if(isset($messages['type']) and $messages['type']!=""){

                        $type=$messages['type'];
                        $message=$messages['message'];

                    }  


                    if($type=='err'){ echo "<div class='errMsg'>"; echo $message; echo "</div>";}
                    else if($type=='succ'){ echo "<div class='succMsg'>"; echo $message; echo "</div>";}


                    update_option('my_photo_gallery_slider_settings_messages', array());     
                ?>      
                <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-pro-plugins/wordpress-photo-gallery-pro-plugin.html">UPGRADE TO PRO VERSION</a></h3></span>

                <h2>Gallery Settings</h2>
                <div id="poststuff">   
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content">
                            <form method="post" action="" id="scrollersettiings" name="scrollersettiings" >
                                <div class="stuffbox" id="namediv" style="width:100%">
                                    <h3><label for="link_name">Settings</label></h3>
                                    <table cellspacing="0" class="form-list" cellpadding="10">
                                        <tbody>
                                            <tr>
                                                <td class="label">
                                                    <label for="transition_speed">Transition Speed <span class="required">*</span></label>
                                                </td>
                                                <td class="value">
                                                    <input id="transition_speed" value="<?php echo $settings['transition_speed']; ?>" name="transition_speed"  class="input-text" type="text">           
                                                    <div style="clear:both"></div>
                                                    <div></div> 
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">
                                                    <label for="transition_interval">Transition Interval <span class="required">*</span></label>
                                                </td>
                                                <td class="value">
                                                    <input id="transition_interval"  value="<?php echo $settings['transition_interval']; ?>" name="transition_interval"  class="input-text" type="text">            
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">
                                                    <label for="show_panel_nav">Show Slider Navigation arrows  <span class="required">*</span></label>
                                                </td>
                                                <td class="value">
                                                    <select id="show_panel_nav" name="show_panel_nav" class="select">
                                                        <option value="">Select</option>
                                                        <option <?php if($settings['show_panel_nav']==1):?> selected="selected" <?php endif;?>  value="1" >Yes</option>
                                                        <option <?php if($settings['show_panel_nav']==0):?> selected="selected" <?php endif;?>  value="0">No</option>
                                                    </select>            
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">
                                                    <label for="panel_width">Slider Width <span class="required">*</span></label>
                                                </td>
                                                <td class="value">
                                                    <input id="panel_width" value="<?php echo $settings['panel_width']; ?>" name="panel_width"  class="input-text" type="text">            
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">
                                                    <label for="panel_height">Slider Height <span class="required">*</span></label>
                                                </td>
                                                <td class="value">
                                                    <input id="panel_height" value="<?php echo $settings['panel_height']; ?>"  name="panel_height"  class="input-text" type="text">            
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">
                                                    <label for="panel_scale">Slider Scale <span class="required">*</span></label>
                                                </td>
                                                <td class="value">
                                                    <select id="panel_scale" name="panel_scale" class="select">
                                                        <option value="">Select</option>
                                                        <option <?php if($settings['panel_scale']=='crop'):?> selected="selected" <?php endif;?> value="crop">crop</option>
                                                        <option <?php if($settings['panel_scale']=='fit'):?> selected="selected" <?php endif;?> value="fit" >fit</option>
                                                    </select>  
                                                    <div style="clear:both"></div>
                                                    <div></div>          
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">  
                                                    <label for="pan_images">Pan Images  <span class="required">*</span></label>
                                                </td>
                                                <td class="value">
                                                    <select id="pan_images" name="pan_images" class="select">
                                                        <option value="">Select</option>
                                                        <option <?php if($settings['pan_images']==1):?> selected="selected" <?php endif;?>  value="1">Yes</option>
                                                        <option <?php if($settings['pan_images']==0):?> selected="selected" <?php endif;?>  value="0" >No</option>
                                                    </select>            
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">
                                                    <label for="pan_style">Pan Style <span class="required">*</span></label>
                                                </td>
                                                <td class="value">
                                                    <select id="pan_style" name="pan_style" class="select">
                                                        <option value="">Select</option>
                                                        <option <?php if($settings['pan_style']=='drag'):?> selected="selected" <?php endif;?>  value="drag">drag</option>
                                                        <option <?php if($settings['pan_style']=='track'):?> selected="selected" <?php endif;?>  value="track" >track</option>
                                                    </select>            
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">
                                                    <label for="show_filmstrip">Show Thumbnail Gallery  <span class="required">*</span></label>
                                                </td>
                                                <td class="value">
                                                    <select id="show_filmstrip" name="show_filmstrip" class="select">
                                                        <option value="">Select</option>
                                                        <option <?php if($settings['show_filmstrip']==1):?> selected="selected" <?php endif;?>  value="1" >Yes</option>
                                                        <option <?php if($settings['show_filmstrip']==0):?> selected="selected" <?php endif;?>  value="0">No</option>
                                                    </select> 
                                                    <div style="clear:both"></div>
                                                    <div></div>           
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label"><label for="autoplay">Autoplay  <span class="required">*</span></label></td>
                                                <td class="value">
                                                    <select id="autoplay" name="autoplay" class="select">
                                                        <option value="">Select</option>
                                                        <option <?php if($settings['autoplay']==1):?> selected="selected" <?php endif;?>  value="1" >Yes</option>
                                                        <option <?php if($settings['autoplay']==0):?> selected="selected" <?php endif;?>  value="0">No</option>
                                                    </select>   
                                                    <div style="clear:both"></div>
                                                    <div></div>         
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">
                                                    <label for="frame_width">Thumbnail Width <span class="required">*</span></label>
                                                </td>
                                                <td class="value">
                                                    <input id="frame_width" value="<?php echo $settings['frame_width']; ?>" name="frame_width" value="80" class="input-text" type="text">            
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">
                                                    <label for="frame_height">Thumbnail Height <span class="required">*</span></label>
                                                </td>
                                                <td class="value">
                                                    <input id="frame_height" value="<?php echo $settings['frame_height']; ?>" name="frame_height"  class="input-text" type="text">            
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">
                                                    <label for="frame_opacity">Thumbnail Opacity <span class="required">*</span></label>
                                                </td>
                                                <td class="value">
                                                    <input id="frame_opacity" value="<?php echo $settings['frame_opacity']; ?>" name="frame_opacity"  class="input-text" type="text">           
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">
                                                    <label for="frame_scale">Thumbnail Scale <span class="required">*</span></label>
                                                </td>
                                                <td class="value">
                                                    <select id="frame_scale" name="frame_scale" class="select">
                                                        <option value="">Select</option>
                                                        <option <?php if($settings['frame_scale']=='crop'):?> selected="selected" <?php endif;?> value="crop" >crop</option>
                                                        <option <?php if($settings['frame_scale']=='fit'):?> selected="selected" <?php endif;?>  value="fit">fit</option>
                                                    </select>            
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">
                                                    <label for="frame_gap">Thumbnail Gap <span class="required">*</span></label>
                                                </td>
                                                <td class="value">
                                                    <input id="frame_gap" value="<?php echo $settings['frame_gap']; ?>" name="frame_gap"  class="input-text" type="text">            
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label"><label for="show_infobar">Show Infobar  <span class="required">*</span></label></td>
                                                <td class="value">
                                                    <select id="show_infobar" name="show_infobar" class=" select">
                                                        <option value="">Select</option>
                                                        <option <?php if($settings['show_infobar']==1):?> selected="selected" <?php endif;?>  value="1">Yes</option>
                                                        <option <?php if($settings['show_infobar']==0):?> selected="selected" <?php endif;?>  value="0" >No</option>
                                                    </select>            
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="label">
                                                    <label for="infobar_opacity">Infobar Opacity <span class="required">*</span></label>
                                                </td>
                                                <td class="value">
                                                    <input id="infobar_opacity" value="<?php echo $settings['infobar_opacity']; ?>" name="infobar_opacity"  class="input-text" type="text">            
                                                    <div style="clear:both"></div>
                                                    <div></div>
                                                </td>
                                            </tr>  
                                            <tr>
                                                <td class="label">
                                                    <input type="submit"  name="btnsave" id="btnsave" value="Save Changes" class="button-primary">      
                                                </td>
                                                <td class="value">

                                                    <input type="button" name="cancle" id="cancle" value="Cancel" class="button-primary" onclick="location.href='admin.php?page=photo_gallery_slider'">    

                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>                                    
                                </div>
                                <input type="hidden" name="start_frame" id="start_frame" value="1"> 
                                <input type="hidden" name="enable_slideshow" id="start_frame" value="1"> 
                                <input type="hidden" name="panel_animation" id="panel_animation" value="fade"> 
                                <input type="hidden" name="overlay_position" id="overlay_position" value="bottom"> 
                                <input type="hidden" name="filmstrip_position" id="filmstrip_position" value="bottom"> 
                                <input type="hidden" name="enable_overlays" id="enable_overlays" value="0"> 
                                <input type="hidden" name="show_captions" id="show_captions" value="0"> 
                                <input type="hidden" name="show_filmstrip_nav" id="show_filmstrip_nav" value="0"> 
                                <input type="hidden" name="show_panels" id="show_panels" value="1"> 
                            </form> 
                            <script type="text/javascript">

                                var $n = jQuery.noConflict();  
                                $n(document).ready(function() {

                                        $n("#scrollersettiings").validate({
                                                rules: {
                                                    transition_speed: {
                                                        required:true,
                                                        number:true,
                                                        maxlength:10
                                                    },transition_interval: {
                                                        required:true,
                                                        number:true,
                                                        maxlength:10
                                                    },show_panel_nav: {
                                                        required:true, 
                                                    },
                                                    panel_width:{
                                                        required:true,  
                                                        number:true,
                                                        maxlength:10

                                                    },
                                                    panel_height:{
                                                        required:true,
                                                        number:true,
                                                        maxlength:10  
                                                    },
                                                    panel_scale:{
                                                        required:true
                                                    },
                                                    pan_images:{
                                                        required:true

                                                    },
                                                    pan_style:{
                                                        required:true
                                                    },show_filmstrip:{
                                                        required:true

                                                    },autoplay:{
                                                        required:true

                                                    },frame_width:{
                                                        required:true,
                                                        number:true,
                                                        maxlength:10  
                                                    },frame_height:{
                                                        required:true,
                                                        number:true,
                                                        maxlength:10  
                                                    }
                                                    ,frame_opacity:{
                                                        required:true,
                                                        number:true,
                                                        maxlength:10  
                                                    }
                                                    ,frame_scale:{
                                                        required:true

                                                    },frame_gap:{
                                                        required:true,
                                                        number:true,
                                                        maxlength:10  

                                                    },show_infobar:{
                                                        required:true

                                                    },infobar_opacity:{
                                                        required:true,
                                                        number:true,
                                                        maxlength:10  
                                                    }

                                                },
                                                errorClass: "image_error",
                                                errorPlacement: function(error, element) {
                                                    error.appendTo( element.next().next());
                                                } 


                                        })
                                });

                            </script> 

                        </div>
                        <div id="postbox-container-1" class="postbox-container" > 

                            <div class="postbox"> 
                                <h3 class="hndle"><span></span>All WordPress Themes In One Price</h3> 
                                <div class="inside">
                                    <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank"><img border="0" src="http://www.elegantthemes.com/affiliates/banners/300x250.gif" width="250" height="250"></a></center>

                                    <div style="margin:10px 5px">

                                    </div>
                                </div></div>
                            <div class="postbox"> 
                                <h3 class="hndle"><span></span>Recommended WordPress Hosting</h3> 
                                <div class="inside">
                                    <center><a target="_blank" href="http://www.shareasale.com/r.cfm?b=531904&u=675922&m=41388&urllink=&afftrack="><img src="http://www.shareasale.com/image/41388/sas_banner_250x250.jpg" alt="WP Engine" border="0"></a></center>

                                    <div style="margin:10px 5px">

                                    </div>
                                </div></div>

                        </div>
                    </div>                                              

                </div>  
            </div>      
        </div>



        <div class="clear"></div></div>  
    <?php
    }        
    function photo_gallery_image_management(){

        $action='gridview';
        global $wpdb;


        if(isset($_GET['action']) and $_GET['action']!=''){


            $action=trim($_GET['action']);
        }

    ?>

    <?php 
        if(strtolower($action)==strtolower('gridview')){ 


            $wpcurrentdir=dirname(__FILE__);
            $wpcurrentdir=str_replace("\\","/",$wpcurrentdir);



        ?> 
        <div class="wrap">
        <!--[if !IE]><!-->
        <style type="text/css">

            @media only screen and (max-width: 800px) {

                /* Force table to not be like tables anymore */
                #no-more-tables table, 
                #no-more-tables thead, 
                #no-more-tables tbody, 
                #no-more-tables th, 
                #no-more-tables td, 
                #no-more-tables tr { 
                    display: block; 

                }

                /* Hide table headers (but not display: none;, for accessibility) */
                #no-more-tables thead tr { 
                    position: absolute;
                    top: -9999px;
                    left: -9999px;
                }

                #no-more-tables tr { border: 1px solid #ccc; }

                #no-more-tables td { 
                    /* Behave  like a "row" */
                    border: none;
                    border-bottom: 1px solid #eee; 
                    position: relative;
                    padding-left: 50%; 
                    white-space: normal;
                    text-align:left;      
                }

                #no-more-tables td:before { 
                    /* Now like a table header */
                    position: absolute;
                    /* Top/left values mimic padding */
                    top: 6px;
                    left: 6px;
                    width: 45%; 
                    padding-right: 10px; 
                    white-space: nowrap;
                    text-align:left;
                    font-weight: bold;
                }

                /*
                Label the data
                */
                #no-more-tables td:before { content: attr(data-title); }
            }
        </style>
        <!--<![endif]-->
        <style type="text/css">
            .pagination {
                clear:both;
                padding:20px 0;
                position:relative;
                font-size:11px;
                line-height:13px;
            }

            .pagination span, .pagination a {
                display:block;
                float:left;
                margin: 2px 2px 2px 0;
                padding:6px 9px 5px 9px;
                text-decoration:none;
                width:auto;
                color:#fff;
                background: #555;
            }

            .pagination a:hover{
                color:#fff;
                background: #3279BB;
            }

            .pagination .current{
                padding:6px 9px 5px 9px;
                background: #3279BB;
                color:#fff;
            }
        </style>
        <div class="wrap">
            <table><tr><td><a href="https://twitter.com/FreeAdsPost" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @FreeAdsPost</a>
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></td>
                    <td>
                        <a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
                            <img id="help us for free plugin" height="30" width="90" src="http://www.i13websolution.com/images/paypaldonate.jpg" border="0" alt="help us for free plugin" title="help us for free plugin">
                        </a>
                    </td>
                </tr>
            </table>

            <?php 

                $messages=get_option('my_photo_gallery_slider_settings_messages'); 
                $type='';
                $message='';
                if(isset($messages['type']) and $messages['type']!=""){

                    $type=$messages['type'];
                    $message=$messages['message'];

                }  


                if($type=='err'){ echo "<div class='errMsg'>"; echo $message; echo "</div>";}
                else if($type=='succ'){ echo "<div class='succMsg'>"; echo $message; echo "</div>";}


                update_option('my_photo_gallery_slider_settings_messages', array());     
            ?>

            <div id="poststuff" >
                <div id="post-body" class="metabox-holder columns-2">
                    <div style="" id="post-body-content" >
                        <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-pro-plugins/wordpress-photo-gallery-pro-plugin.html">UPGRADE TO PRO VERSION</a></h3></span>
                        <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
                        <h2>Images <a class="button add-new-h2" href="admin.php?page=photo_gallery_image_management&action=addedit">Add New</a> </h2>
                        <br/>    

                        <form method="POST" action="admin.php?page=photo_gallery_image_management&action=deleteselected"  id="posts-filter">
                            <div class="alignleft actions">
                                <select name="action_upper">
                                    <option selected="selected" value="-1">Bulk Actions</option>
                                    <option value="delete">delete</option>
                                </select>
                                <input type="submit" value="Apply" class="button-secondary action" id="deleteselected" name="deleteselected">
                            </div>
                            <br class="clear">
                            <?php 

                                $settings=get_option('my_photo_gallery_slider_settings'); 
                                $query="SELECT * FROM ".$wpdb->prefix."gv_slider order by createdon desc";
                                $rows=$wpdb->get_results($query,'ARRAY_A');
                                $rowCount=sizeof($rows);

                            ?>
                            <br/>
                            <div id="no-more-tables">
                                <table cellspacing="0" id="gridTbl" class="table-bordered table-striped table-condensed cf" >
                                    <thead>
                                        <tr>
                                            <th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
                                            <th><span>Title</span></th>
                                            <th><span>Published On</span></th>
                                            <th><span>Edit</span></th>
                                            <th><span>Delete</span></th>
                                        </tr> 
                                    </thead>
                                    <tbody id="the-list">
                                        <?php

                                            if(count($rows) > 0){

                                                global $wp_rewrite;
                                                $rows_per_page = 5;

                                                $current = (isset($_GET['paged'])) ? ($_GET['paged']) : 1;
                                                $pagination_args = array(
                                                    'base' => @add_query_arg('paged','%#%'),
                                                    'format' => '',
                                                    'total' => ceil(sizeof($rows)/$rows_per_page),
                                                    'current' => $current,
                                                    'show_all' => false,
                                                    'type' => 'plain',
                                                );


                                                $start = ($current - 1) * $rows_per_page;
                                                $end = $start + $rows_per_page;
                                                $end = (sizeof($rows) < $end) ? sizeof($rows) : $end;

                                                for ($i=$start;$i < $end ;++$i ) {

                                                    $row = $rows[$i];
                                                    $id=$row['id'];
                                                    $editlink="admin.php?page=photo_gallery_image_management&action=addedit&id=$id";
                                                    $deletelink="admin.php?page=photo_gallery_image_management&action=delete&id=$id";

                                                ?>
                                                <tr valign="top">
                                                    <td class="alignCenter check-column"   data-title="Select Record" ><input type="checkbox" value="<?php echo $row['id']; ?>" name="thumbnails[]"></td>
                                                    <td class="alignCenter" data-title="Title"><strong><?php echo stripslashes($row['title']) ?></strong></td>  
                                                    <td class="alignCenter" data-title="Published On"><?php echo $row['createdon'] ?></td>
                                                    <td class="alignCenter"   data-title="Edit"><strong><a href='<?php echo $editlink; ?>' title="edit">Edit</a></strong></td>  
                                                    <td class="alignCenter"   data-title="Delete"><strong><a href='<?php echo $deletelink; ?>' onclick="return confirmDelete();"  title="delete">Delete</a> </strong></td>  
                                                </tr>
                                                <?php 
                                                } 
                                            }
                                            else{
                                            ?>

                                            <tr valign="top" class="" id="">
                                                <td colspan="5" data-title="No Record" align="center"><strong>No Images Found</strong></td>  
                                            </tr>
                                            <?php 
                                            } 
                                        ?>      
                                    </tbody>
                                </table>
                            </div>
                            <?php
                                if(sizeof($rows)>0){
                                    echo "<div class='pagination' style='padding-top:10px'>";
                                    echo paginate_links($pagination_args);
                                    echo "</div>";
                                }
                            ?>
                            <br/>
                            <div class="alignleft actions">
                                <select name="action">
                                    <option selected="selected" value="-1">Bulk Actions</option>
                                    <option value="delete">delete</option>
                                </select>
                                <input type="submit" value="Apply" class="button-secondary action" id="deleteselected" name="deleteselected">
                            </div>

                        </form>
                        <script type="text/JavaScript">

                            function  confirmDelete(){
                                var agree=confirm("Are you sure you want to delete this image ?");
                                if (agree)
                                    return true ;
                                else
                                    return false;
                            }
                        </script>

                        <br class="clear">
                        <h3>To print this slider into WordPress Post/Page use below Short code</h3>
                        <input type="text" value="[print_my_photo_gallery]" style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
                        <div class="clear"></div>
                        <h3>To print this slider into WordPress theme/template PHP files use below php code</h3>
                        <input type="text" value="echo do_shortcode('[print_my_photo_gallery]');" style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
                        <div class="clear"></div>
                    </div>
                    <div id="postbox-container-1" class="postbox-container" > 
                        <div class="postbox"> 
                            <h3 class="hndle"><span></span>All WordPress Themes In One Price</h3> 
                            <div class="inside">
                                <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank"><img border="0" src="http://www.elegantthemes.com/affiliates/banners/300x250.gif" width="250" height="250"></a></center>

                                <div style="margin:10px 5px">

                                </div>
                            </div></div>
                        <div class="postbox"> 
                            <h3 class="hndle"><span></span>Recommended WordPress SEO Tools</h3> 
                            <div class="inside">
                                <center><a href="http://www.semrush.com/sem.html?ref=961672083"> <img width="250" height="250" src="http://www.berush.com/images/240x240_semrush_en.png" /></a></center>

                                <div style="margin:10px 5px">

                                </div>
                            </div></div>
                    </div>
                </div>     
                <div style="clear: both;"></div>
                <?php $url = plugin_dir_url(__FILE__);  ?>
            </div>  
        </div>  
        <?php 
        }   
        else if(strtolower($action)==strtolower('addedit')){
            $url = plugin_dir_url(__FILE__);

        ?>
        <?php        
            if(isset($_POST['btnsave'])){

                //edit save
                if(isset($_POST['imageid'])){

                    //add new
                    $location='admin.php?page=photo_gallery_image_management';
                    $title=trim(addslashes($_POST['imagetitle']));
                    $imageurl=trim($_POST['imageurl']);
                    $imageid=trim($_POST['imageid']);
                    $imagename="";
                    if($_FILES["image_name"]['name']!="" and $_FILES["image_name"]['name']!=null){

                        if ($_FILES["image_name"]["error"] > 0)
                        {
                            $my_photo_gallery_slider_settings_messages=array();
                            $my_photo_gallery_slider_settings_messages['type']='err';
                            $my_photo_gallery_slider_settings_messages['message']='Error while file uploading.';
                            update_option('my_photo_gallery_slider_settings_messages', $my_photo_gallery_slider_settings_messages);

                            echo "<script type='text/javascript'> location.href='$location';</script>";
							exit;
                        }
                        else{

                            $wpcurrentdir=dirname(__FILE__);
                            $wpcurrentdir=str_replace("\\","/",$wpcurrentdir);
                            $path_parts = pathinfo($_FILES["image_name"]["name"]);
                            $extension = $path_parts['extension'];       
                            $imagename=md5(time()).".$extension";
                            $imageUploadTo=$wpcurrentdir.'/imagestoscroll/'.$imagename;
                            move_uploaded_file($_FILES["image_name"]["tmp_name"],$imageUploadTo); 


                        }
                    }    


                    try{
                        if($imagename!=""){
                            $query = "update ".$wpdb->prefix."gv_slider set title='$title',image_name='$imagename',
                            custom_link='$imageurl' where id=$imageid";
                        }
                        else{
                            $query = "update ".$wpdb->prefix."gv_slider set title='$title',
                            custom_link='$imageurl' where id=$imageid";
                        } 
                        $wpdb->query($query); 

                        $my_photo_gallery_slider_settings_messages=array();
                        $my_photo_gallery_slider_settings_messages['type']='succ';
                        $my_photo_gallery_slider_settings_messages['message']='image updated successfully.';
                        update_option('my_photo_gallery_slider_settings_messages', $my_photo_gallery_slider_settings_messages);


                    }
                    catch(Exception $e){

                        $my_photo_gallery_slider_settings_messages=array();
                        $my_photo_gallery_slider_settings_messages['type']='err';
                        $my_photo_gallery_slider_settings_messages['message']='Error while updating image.';
                        update_option('my_photo_gallery_slider_settings_messages', $my_photo_gallery_slider_settings_messages);
                    }  


                    echo "<script type='text/javascript'> location.href='$location';</script>";
                    exit;
                }
                else{

                    //add new

                    $location='admin.php?page=photo_gallery_image_management';
                    $title=trim(addslashes($_POST['imagetitle']));
                    $imageurl=trim($_POST['imageurl']);
                    $createdOn=date('Y-m-d h:i:s');
                    if(function_exists('date_i18n')){

                        $createdOn=date_i18n('Y-m-d'.' '.get_option('time_format') ,false,false);
                        if(get_option('time_format')=='H:i')
                            $createdOn=date('Y-m-d H:i:s',strtotime($createdOn));
                        else   
                            $createdOn=date('Y-m-d h:i:s',strtotime($createdOn));

                    }

                    if ($_FILES["image_name"]["error"] > 0)
                    {
                        $my_photo_gallery_slider_settings_messages=array();
                        $my_photo_gallery_slider_settings_messages['type']='err';
                        $my_photo_gallery_slider_settings_messages['message']='Error while file uploading.';
                        update_option('my_photo_gallery_slider_settings_messages', $my_photo_gallery_slider_settings_messages);

                        echo "<script type='text/javascript'> location.href='$location';</script>";
                        exit;

                    }
                    else{
                        $location='admin.php?page=photo_gallery_image_management';

                        try{

                            $wpcurrentdir=dirname(__FILE__);
                            $wpcurrentdir=str_replace("\\","/",$wpcurrentdir);
                            $path_parts = pathinfo($_FILES["image_name"]["name"]);
                            $extension = $path_parts['extension'];       
                            $imagename=md5(time()).".$extension";
                            $imageUploadTo=$wpcurrentdir.'/imagestoscroll/'.$imagename;
                            move_uploaded_file($_FILES["image_name"]["tmp_name"],$imageUploadTo); 


                            $query = "INSERT INTO ".$wpdb->prefix."gv_slider (title, image_name,createdon,custom_link) 
                            VALUES ('$title','$imagename','$createdOn','$imageurl')";

                            $wpdb->query($query); 

                            $my_photo_gallery_slider_settings_messages=array();
                            $my_photo_gallery_slider_settings_messages['type']='succ';
                            $my_photo_gallery_slider_settings_messages['message']='New image added successfully.';
                            update_option('my_photo_gallery_slider_settings_messages', $my_photo_gallery_slider_settings_messages);


                        }
                        catch(Exception $e){

                            $my_photo_gallery_slider_settings_messages=array();
                            $my_photo_gallery_slider_settings_messages['type']='err';
                            $my_photo_gallery_slider_settings_messages['message']='Error while adding image.';
                            update_option('my_photo_gallery_slider_settings_messages', $my_photo_gallery_slider_settings_messages);
                        }  

                    }     
                    echo "<script type='text/javascript'> location.href='$location';</script>";  
                    exit;        

                } 

            }
            else{ 

            ?>
            <div style="width: 100%;">  
            <div style="float:left;width:100%;" >
                <div class="wrap">
                    <?php if(isset($_GET['id']) and $_GET['id']>0)
                        { 


                            $id= $_GET['id'];
                            $query="SELECT * FROM ".$wpdb->prefix."gv_slider WHERE id=$id";
                            $myrow  = $wpdb->get_row($query);

                            if(is_object($myrow)){

                                $title=stripslashes($myrow->title);
                                $image_link=$myrow->custom_link;
                                $image_name=stripslashes($myrow->image_name);

                            }   

                        ?>

                        <h2>Update Image </h2>

                        <?php }else{ 

                            $title='';
                            $image_link='';
                            $image_name='';

                        ?>
                        <span><h3 style="color: blue;"><a target="_blank" href="http://www.i13websolution.com/wordpress-pro-plugins/wordpress-photo-gallery-pro-plugin.html">UPGRADE TO PRO VERSION</a></h3></span>
                        <h2>Add Image </h2>
                        <?php } ?>

                    <div id="poststuff">
                        <div id="post-body" class="metabox-holder columns-2">
                            <div id="post-body-content">
                                <form method="post" action="" id="addimage" name="addimage" enctype="multipart/form-data" >

                                    <div class="stuffbox" id="namediv" style="width:100%">
                                        <h3><label for="link_name">Image Title</label></h3>
                                        <div class="inside">
                                            <input type="text" id="imagetitle"  size="30" name="imagetitle" value="<?php echo $title;?>">
                                            <div style="clear:both"></div>
                                            <div></div>
                                            <div style="clear:both"></div>
                                            <p><?php _e('Used in image alt for seo'); ?></p>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%">
                                        <h3><label for="link_name">Image Url(<?php _e('On click redirect to this url.'); ?>)</label></h3>
                                        <div class="inside">
                                            <input type="text" id="imageurl" class="url"   size="30" name="imageurl" value="<?php echo $image_link; ?>">
                                            <div style="clear:both"></div>
                                            <div></div>
                                            <div style="clear:both"></div>
                                            <p><?php _e('On image click users will redirect to this url.'); ?></p>
                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width:100%">
                                        <h3><label for="link_name">Upload Image</label></h3>
                                        <div class="inside" id="fileuploaddiv">
                                            <?php if($image_name!=""){ ?>
                                                <div><b>Current Image : </b><a id="currImg" href="<?php echo $url;?>imagestoscroll/<?php echo $image_name; ?>" target="_new"><?php echo $image_name; ?></a></div>
                                                <?php } ?>      
                                            <input type="file" name="image_name" onchange="reloadfileupload();"  id="image_name" size="30" />
                                            <div style="clear:both"></div>
                                            <div></div>
                                        </div>
                                    </div>
                                    <?php if(isset($_GET['id']) and $_GET['id']>0){ ?> 
                                        <input type="hidden" name="imageid" id="imageid" value="<?php echo $_GET['id'];?>">
                                        <?php
                                        } 
                                    ?>
                                    <input type="submit" onclick="return validateFile();" name="btnsave" id="btnsave" value="Save Changes" class="button-primary">&nbsp;&nbsp;<input type="button" name="cancle" id="cancle" value="Cancel" class="button-primary" onclick="location.href='admin.php?page=photo_gallery_image_management'">

                                </form> 
                                <script type="text/javascript">

                                    var $n = jQuery.noConflict();  
                                    $n(document).ready(function() {

                                            $n("#addimage").validate({
                                                    rules: {
                                                        imagetitle: {
                                                            required:true, 
                                                            maxlength: 200
                                                        },imageurl: {
                                                            url:true,  
                                                            maxlength: 500
                                                        },
                                                        image_name:{
                                                            isimage:true  
                                                        }
                                                    },
                                                    errorClass: "image_error",
                                                    errorPlacement: function(error, element) {
                                                        error.appendTo( element.next().next().next());
                                                    } 


                                            })
                                    });

                                    function validateFile(){

                                        var $n = jQuery.noConflict();   
                                        if($n('#currImg').length>0){
                                            return true;
                                        }
                                        var fragment = $n("#image_name").val();
                                        var filename = $n("#image_name").val().replace(/.+[\\\/]/, "");  
                                        var imageid=$n("#image_name").val();

                                        if(imageid==""){

                                            if(filename!="")
                                                return true;
                                            else
                                                {
                                                $n("#err_daynamic").remove();
                                                $n("#image_name").after('<label class="image_error" id="err_daynamic">Please select file.</label>');
                                                return false;  
                                            } 
                                        }
                                        else{
                                            return true;
                                        }      
                                    }
                                    function reloadfileupload(){

                                        var $n = jQuery.noConflict();  
                                        var fragment = $n("#image_name").val();
                                        var filename = $n("#image_name").val().replace(/.+[\\\/]/, "");
                                        var validExtensions=new Array();
                                        validExtensions[0]='jpg';
                                        validExtensions[1]='jpeg';
                                        validExtensions[2]='png';
                                        validExtensions[3]='gif';


                                        var extension = filename.substr( (filename.lastIndexOf('.') +1) ).toLowerCase();

                                        var inarr=parseInt($n.inArray( extension, validExtensions));

                                        if(inarr<0){

                                            $n("#err_daynamic").remove();
                                            $n('#fileuploaddiv').html($n('#fileuploaddiv').html());   
                                            $n("#image_name").after('<label class="image_error" id="err_daynamic">Invalid file extension</label>');

                                        }
                                        else{
                                            $n("#err_daynamic").remove();

                                        } 


                                    }  
                                </script> 

                            </div>
                            <div id="postbox-container-1" class="postbox-container" > 
                                <div class="postbox"> 
                                    <h3 class="hndle"><span></span>Best Hosting for WordPress</h3> 
                                    <div class="inside">
                                        <center><a href="http://www.siteground.com" onClick="this.href='http://www.siteground.com/wordpress-hosting.htm?afbannercode=2efd07dfb14877d963d9e75d5057b4a8'" ><img src="https://ua.siteground.com/img/banners/application/wordpress/250x250.gif" alt="Web Hosting" width="250" height="250" border="0"></a></center>

                                        <div style="margin:10px 5px">

                                        </div>
                                    </div></div>
                                <div class="postbox"> 
                                    <h3 class="hndle"><span></span>Worried About SEO ?</h3> 
                                    <div class="inside">
                                        <center><a target="_blank" href="http://www.shareasale.com/r.cfm?b=531904&u=675922&m=41388&urllink=&afftrack="><img src="http://www.shareasale.com/image/41388/sas_banner_250x250.jpg" alt="WP Engine" border="0"></a></center>

                                        <div style="margin:10px 5px">

                                        </div>
                                    </div></div>




                            </div>
                        </div>
                    </div>  

                </div>      
            </div>


            <?php 
            } 
        }  

        else if(strtolower($action)==strtolower('delete')){

            $location='admin.php?page=photo_gallery_image_management';
            $deleteId=(int)$_GET['id'];

            try{


                $query="SELECT * FROM ".$wpdb->prefix."gv_slider WHERE id=$deleteId";
                $myrow  = $wpdb->get_row($query);

                if(is_object($myrow)){

                    $image_name=stripslashes($myrow->image_name);
                    $wpcurrentdir=dirname(__FILE__);
                    $wpcurrentdir=str_replace("\\","/",$wpcurrentdir);
                    $imagename=$_FILES["image_name"]["name"];
                    $imagetoDel=$wpcurrentdir.'/imagestoscroll/'.$image_name;
                    @unlink($imagetoDel);

                    $query = "delete from  ".$wpdb->prefix."gv_slider where id=$deleteId";
                    $wpdb->query($query); 

                    $my_photo_gallery_slider_settings_messages=array();
                    $my_photo_gallery_slider_settings_messages['type']='succ';
                    $my_photo_gallery_slider_settings_messages['message']='Image deleted successfully.';
                    update_option('my_photo_gallery_slider_settings_messages', $my_photo_gallery_slider_settings_messages);
                }    


            }
            catch(Exception $e){

                $my_photo_gallery_slider_settings_messages=array();
                $my_photo_gallery_slider_settings_messages['type']='err';
                $my_photo_gallery_slider_settings_messages['message']='Error while deleting image.';
                update_option('my_photo_gallery_slider_settings_messages', $my_photo_gallery_slider_settings_messages);
            }  

            echo "<script type='text/javascript'> location.href='$location';</script>";
			exit;
        }  
        else if(strtolower($action)==strtolower('deleteselected')){

            $location='admin.php?page=photo_gallery_image_management'; 
            if(isset($_POST) and isset($_POST['deleteselected']) and  ( $_POST['action']=='delete' or $_POST['action_upper']=='delete')){

                if(sizeof($_POST['thumbnails']) >0){

                    $deleteto=$_POST['thumbnails'];
                    $implode=implode(',',$deleteto);   

                    try{

                        foreach($deleteto as $img){ 

                            $query="SELECT * FROM ".$wpdb->prefix."gv_slider WHERE id=$img";
                            $myrow  = $wpdb->get_row($query);

                            if(is_object($myrow)){

                                $image_name=stripslashes($myrow->image_name);
                                $wpcurrentdir=dirname(__FILE__);
                                $wpcurrentdir=str_replace("\\","/",$wpcurrentdir);
                                $imagename=$_FILES["image_name"]["name"];
                                $imagetoDel=$wpcurrentdir.'/imagestoscroll/'.$image_name;
                                @unlink($imagetoDel);
                                $query = "delete from  ".$wpdb->prefix."gv_slider where id=$img";
                                $wpdb->query($query); 

                                $my_photo_gallery_slider_settings_messages=array();
                                $my_photo_gallery_slider_settings_messages['type']='succ';
                                $my_photo_gallery_slider_settings_messages['message']='selected images deleted successfully.';
                                update_option('my_photo_gallery_slider_settings_messages', $my_photo_gallery_slider_settings_messages);
                            }

                        }

                    }
                    catch(Exception $e){

                        $my_photo_gallery_slider_settings_messages=array();
                        $my_photo_gallery_slider_settings_messages['type']='err';
                        $my_photo_gallery_slider_settings_messages['message']='Error while deleting image.';
                        update_option('my_photo_gallery_slider_settings_messages', $my_photo_gallery_slider_settings_messages);
                    }  

                    echo "<script type='text/javascript'> location.href='$location';</script>";
                    exit;


                }
                else{

                    echo "<script type='text/javascript'> location.href='$location';</script>";
                    exit;   
                }

            }
            else{

                echo "<script type='text/javascript'> location.href='$location';</script>"; 
                exit;     
            }

        }      
    } 
    function photo_gallery_slider_admin_preview(){
        $settings=get_option('my_photo_gallery_slider_settings');


    ?>      
    <div style="">  
        <div style="float:left;">
            <div class="wrap">
                <h2>Slider Preview</h2>
                <br>

                <?php
                    $wpcurrentdir=dirname(__FILE__);
                    $wpcurrentdir=str_replace("\\","/",$wpcurrentdir);


                ?>
                <div id="poststuff">
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content">
                            <div style="clear: both;"></div>
                            <?php $url = plugin_dir_url(__FILE__);  ?>
                            <div id="divSliderMain_admin">
                                <ul id="gallery_slider">
                                    <?php
                                        global $wpdb;
                                        $imageheight=$settings['panel_height'];
                                        $imagewidth=$settings['panel_width'];
                                        $query="SELECT * FROM ".$wpdb->prefix."gv_slider order by createdon desc";
                                        $rows=$wpdb->get_results($query,'ARRAY_A');

                                        if(count($rows) > 0){
                                            foreach($rows as $row){

                                                $imagename=$row['image_name'];
                                                $imageUploadTo=$wpcurrentdir.'/imagestoscroll/'.$imagename;
                                                $imageUploadTo=str_replace("\\","/",$imageUploadTo);
                                                $pathinfo=pathinfo($imageUploadTo);
                                                $filenamewithoutextension=$pathinfo['filename'];
                                                $outputimg="";


                                                $imagetoCheck=$wpcurrentdir.'/imagestoscroll/'.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];

                                                if(file_exists($imagetoCheck)){
                                                    $outputimg = plugin_dir_url(__FILE__)."imagestoscroll/".$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];

                                                }
                                                else{

                                                    if(file_exists($wpcurrentdir."/imagestoscroll/".$row['image_name'])){

                                                        $resizeObj = new resize($wpcurrentdir."/imagestoscroll/".$row['image_name']); 
                                                        $resizeObj -> resizeImage($imagewidth, $imageheight, "exact"); 
                                                        $resizeObj -> saveImage($wpcurrentdir."/imagestoscroll/".$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'], 100); 
                                                        $outputimg = plugin_dir_url(__FILE__)."imagestoscroll/".$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];

                                                    }else{

                                                        $outputimg = plugin_dir_url(__FILE__)."imagestoscroll/".$imagename;
                                                    }   

                                                }


                                            ?>         
                                            <li><img data-target="1" data-href="<?php echo $row['custom_link'];?>" src="<?php echo $outputimg;?>"  /></li> 

                                            <?php }?>   
                                        <?php }?>   
                                </ul>
                            </div>
                            <script type="text/javascript">
                                $j= jQuery.noConflict();
                                $j(document).ready(function() {
                                        $j(function(){
                                                jQuery('#gallery_slider').galleryView({
                                                        transition_speed:<?php echo $settings['transition_speed'];?>,         //INT - duration of panel/frame transition (in milliseconds)
                                                        transition_interval:<?php echo $settings['transition_interval'];?>,         //INT - delay between panel/frame transitions (in milliseconds)
                                                        easing:'<?php echo $settings['easing'];?>',                 //STRING - easing method to use for animations (jQuery provides 'swing' or 'linear', more available with jQuery UI or Easing plugin)
                                                        show_panels:<?php echo ($settings['show_panels']==1)?'true':'false' ;?>,                 //BOOLEAN - flag to show or hide panel portion of gallery
                                                        show_panel_nav:<?php echo ($settings['show_panel_nav']==1)?'true':'false' ;?>,             //BOOLEAN - flag to show or hide panel navigation buttons
                                                        enable_overlays:<?php echo ($settings['enable_overlays']==1)?'true':'false' ;?>,             //BOOLEAN - flag to show or hide panel overlays
                                                        panel_width:<?php echo $settings['panel_width'];?>,                 //INT - width of gallery panel (in pixels)
                                                        panel_height:<?php echo $settings['panel_height'];?>,                 //INT - height of gallery panel (in pixels)
                                                        panel_animation:'<?php echo $settings['panel_animation'];?>',         //STRING - animation method for panel transitions (crossfade,fade,slide,none)
                                                        panel_scale: '<?php echo $settings['panel_scale'];?>',             //STRING - cropping option for panel images (crop = scale image and fit to aspect ratio determined by panel_width and panel_height, fit = scale image and preserve original aspect ratio)
                                                        overlay_position:'<?php echo $settings['overlay_position'];?>',     //STRING - position of panel overlay (bottom, top)
                                                        pan_images:<?php echo ($settings['pan_images']==1)?'true':'false' ;?>,                //BOOLEAN - flag to allow user to grab/drag oversized images within gallery
                                                        pan_style:'<?php echo $settings['pan_style'];?>',                //STRING - panning method (drag = user clicks and drags image to pan, track = image automatically pans based on mouse position
                                                        start_frame:'<?php echo $settings['start_frame'];?>',                 //INT - index of panel/frame to show first when gallery loads
                                                        show_filmstrip:<?php echo ($settings['show_filmstrip']==1)?'true':'false' ;?>,             //BOOLEAN - flag to show or hide filmstrip portion of gallery
                                                        show_filmstrip_nav:<?php echo ($settings['show_filmstrip_nav']==1)?'true':'false' ;?>,         //BOOLEAN - flag indicating whether to display navigation buttons
                                                        enable_slideshow:<?php echo ($settings['enable_slideshow']==1)?'true':'false' ;?>,            //BOOLEAN - flag indicating whether to display slideshow play/pause button
                                                        autoplay:<?php echo ($settings['autoplay']==1)?'true':'false' ;?>,                //BOOLEAN - flag to start slideshow on gallery load
                                                        show_captions:<?php echo ($settings['show_captions']==1)?'true':'false' ;?>,             //BOOLEAN - flag to show or hide frame captions    
                                                        filmstrip_style: '<?php echo $settings['filmstrip_style'];?>',         //STRING - type of filmstrip to use (scroll = display one line of frames, scroll filmstrip if necessary, showall = display multiple rows of frames if necessary)
                                                        filmstrip_position:'<?php echo $settings['filmstrip_position'];?>',     //STRING - position of filmstrip within gallery (bottom, top, left, right)
                                                        frame_width:<?php echo $settings['frame_width'];?>,                 //INT - width of filmstrip frames (in pixels)
                                                        frame_height:<?php echo $settings['frame_width'];?>,                 //INT - width of filmstrip frames (in pixels)
                                                        frame_opacity:<?php echo $settings['frame_opacity'];?>,             //FLOAT - transparency of non-active frames (1.0 = opaque, 0.0 = transparent)
                                                        frame_scale: '<?php echo $settings['frame_scale'];?>',             //STRING - cropping option for filmstrip images (same as above)
                                                        frame_gap:<?php echo $settings['frame_gap'];?>,                     //INT - spacing between frames within filmstrip (in pixels)
                                                        show_infobar:<?php echo ($settings['show_infobar']==1)?'true':'false' ;?>,                //BOOLEAN - flag to show or hide infobar
                                                        infobar_opacity:<?php echo $settings['infobar_opacity'];?>,               //FLOAT - transparency for info bar
                                                        clickable: 'all'

                                                });     



                                        });



                                });


                            </script>      
                        </div>
                    </div>      
                </div>  
            </div>      
        </div>
        <div class="clear"></div>
    </div>
    <h3>To print this slider into WordPress Post/Page use below Short code</h3>
    <input type="text" value="[print_my_photo_gallery]" style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
    <div class="clear"></div>
    <h3>To print this slider into WordPress theme/template PHP files use below php code</h3>
    <input type="text" value="echo do_shortcode('[print_my_photo_gallery]');" style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
    <div class="clear"></div>
    <?php       
    }

    function print_my_photo_gallery_func(){

        $settings=get_option('my_photo_gallery_slider_settings');

        $wpcurrentdir=dirname(__FILE__);
        $wpcurrentdir=str_replace("\\","/",$wpcurrentdir);
        $url = plugin_dir_url(__FILE__);
        ob_start();
    ?>      
    <ul id="gallery_slider">
        <?php
            global $wpdb;
            $imageheight=$settings['panel_height'];
            $imagewidth=$settings['panel_width'];
            $query="SELECT * FROM ".$wpdb->prefix."gv_slider order by createdon desc";
            $rows=$wpdb->get_results($query,'ARRAY_A');

            if(count($rows) > 0){
                foreach($rows as $row){

                    $imagename=$row['image_name'];
                    $imageUploadTo=$wpcurrentdir.'/imagestoscroll/'.$imagename;
                    $imageUploadTo=str_replace("\\","/",$imageUploadTo);
                    $pathinfo=pathinfo($imageUploadTo);
                    $filenamewithoutextension=$pathinfo['filename'];
                    $outputimg="";


                    $imagetoCheck=$wpcurrentdir.'/imagestoscroll/'.$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];

                    if(file_exists($imagetoCheck)){
                        $outputimg = plugin_dir_url(__FILE__)."imagestoscroll/".$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];

                    }
                    else{

                        if(file_exists($wpcurrentdir."/imagestoscroll/".$row['image_name'])){

                            $resizeObj = new resize($wpcurrentdir."/imagestoscroll/".$row['image_name']); 
                            $resizeObj -> resizeImage($imagewidth, $imageheight, "exact"); 
                            $resizeObj -> saveImage($wpcurrentdir."/imagestoscroll/".$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'], 100); 
                            $outputimg = plugin_dir_url(__FILE__)."imagestoscroll/".$filenamewithoutextension.'_'.$imageheight.'_'.$imagewidth.'.'.$pathinfo['extension'];
                        }else{

                            $outputimg = plugin_dir_url(__FILE__)."imagestoscroll/".$imagename;
                        }    

                    }


                ?>         
                <li><img data-target="1" data-href="<?php echo $row['custom_link'];?>" src="<?php echo $outputimg;?>"  /></li> 

                <?php }?>   
            <?php }?>   
    </ul>
    <script type="text/javascript">
        $j= jQuery.noConflict();
        $j(document).ready(function() {
                $j(function(){
                        jQuery('#gallery_slider').galleryView({
                                transition_speed:<?php echo $settings['transition_speed'];?>,         //INT - duration of panel/frame transition (in milliseconds)
                                transition_interval:<?php echo $settings['transition_interval'];?>,         //INT - delay between panel/frame transitions (in milliseconds)
                                easing:'<?php echo $settings['easing'];?>',                 //STRING - easing method to use for animations (jQuery provides 'swing' or 'linear', more available with jQuery UI or Easing plugin)
                                show_panels:<?php echo ($settings['show_panels']==1)?'true':'false' ;?>,                 //BOOLEAN - flag to show or hide panel portion of gallery
                                show_panel_nav:<?php echo ($settings['show_panel_nav']==1)?'true':'false' ;?>,             //BOOLEAN - flag to show or hide panel navigation buttons
                                enable_overlays:<?php echo ($settings['enable_overlays']==1)?'true':'false' ;?>,             //BOOLEAN - flag to show or hide panel overlays
                                panel_width:<?php echo $settings['panel_width'];?>,                 //INT - width of gallery panel (in pixels)
                                panel_height:<?php echo $settings['panel_height'];?>,                 //INT - height of gallery panel (in pixels)
                                panel_animation:'<?php echo $settings['panel_animation'];?>',         //STRING - animation method for panel transitions (crossfade,fade,slide,none)
                                panel_scale: '<?php echo $settings['panel_scale'];?>',             //STRING - cropping option for panel images (crop = scale image and fit to aspect ratio determined by panel_width and panel_height, fit = scale image and preserve original aspect ratio)
                                overlay_position:'<?php echo $settings['overlay_position'];?>',     //STRING - position of panel overlay (bottom, top)
                                pan_images:<?php echo ($settings['pan_images']==1)?'true':'false' ;?>,                //BOOLEAN - flag to allow user to grab/drag oversized images within gallery
                                pan_style:'<?php echo $settings['pan_style'];?>',                //STRING - panning method (drag = user clicks and drags image to pan, track = image automatically pans based on mouse position
                                start_frame:'<?php echo $settings['start_frame'];?>',                 //INT - index of panel/frame to show first when gallery loads
                                show_filmstrip:<?php echo ($settings['show_filmstrip']==1)?'true':'false' ;?>,             //BOOLEAN - flag to show or hide filmstrip portion of gallery
                                show_filmstrip_nav:<?php echo ($settings['show_filmstrip_nav']==1)?'true':'false' ;?>,         //BOOLEAN - flag indicating whether to display navigation buttons
                                enable_slideshow:<?php echo ($settings['enable_slideshow']==1)?'true':'false' ;?>,            //BOOLEAN - flag indicating whether to display slideshow play/pause button
                                autoplay:<?php echo ($settings['autoplay']==1)?'true':'false' ;?>,                //BOOLEAN - flag to start slideshow on gallery load
                                show_captions:<?php echo ($settings['show_captions']==1)?'true':'false' ;?>,             //BOOLEAN - flag to show or hide frame captions    
                                filmstrip_style: '<?php echo $settings['filmstrip_style'];?>',         //STRING - type of filmstrip to use (scroll = display one line of frames, scroll filmstrip if necessary, showall = display multiple rows of frames if necessary)
                                filmstrip_position:'<?php echo $settings['filmstrip_position'];?>',     //STRING - position of filmstrip within gallery (bottom, top, left, right)
                                frame_width:<?php echo $settings['frame_width'];?>,                 //INT - width of filmstrip frames (in pixels)
                                frame_height:<?php echo $settings['frame_width'];?>,                 //INT - width of filmstrip frames (in pixels)
                                frame_opacity:<?php echo $settings['frame_opacity'];?>,             //FLOAT - transparency of non-active frames (1.0 = opaque, 0.0 = transparent)
                                frame_scale: '<?php echo $settings['frame_scale'];?>',             //STRING - cropping option for filmstrip images (same as above)
                                frame_gap:<?php echo $settings['frame_gap'];?>,                     //INT - spacing between frames within filmstrip (in pixels)
                                show_infobar:<?php echo ($settings['show_infobar']==1)?'true':'false' ;?>,                //BOOLEAN - flag to show or hide infobar
                                infobar_opacity:<?php echo $settings['infobar_opacity'];?>,               //FLOAT - transparency for info bar
                                clickable: 'all'

                        });     



                });



        });


    </script>
    <?php
        $output = ob_get_clean();
        return $output;
    }
?>
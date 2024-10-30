<?php
/*
Plugin Name: HTML5 Cumulus
Plugin URI: https://wordpress.org/plugins/html5-cumulus/
Description: A modern (HTML5) version of the classic WP-Cumulus plugin.
Version: 1.01
Author: Flector
Author URI: https://profiles.wordpress.org/flector#content-plugins
Text Domain: html5-cumulus
*/ 

//проверка версии плагина (запуск функции установки новых опций) begin
function h5c_check_version() {
    $h5c_options = get_option('h5c_options');
    if ( $h5c_options['version'] != '1.01' ) {
        h5c_set_new_options();
    }
}
add_action( 'plugins_loaded', 'h5c_check_version' );
//проверка версии плагина (запуск функции установки новых опций) end

//функция установки новых опций при обновлении плагина у пользователей begin
function h5c_set_new_options() {
    $h5c_options = get_option('h5c_options');

    //если нет опции при обновлении плагина - записываем ее
    //if (!isset($h5c_options['new_option'])) {$h5c_options['new_option']='value';}

    //если необходимо переписать уже записанную опцию при обновлении плагина
    //$h5c_options['old_option'] = 'new_value';

    $h5c_options['version'] = '1.01';
    update_option('h5c_options', $h5c_options);
}
//функция установки новых опций при обновлении плагина у пользователей end

//функция установки значений по умолчанию при активации плагина begin
function h5c_init() {

    $h5c_options = array();
    $h5c_options['version'] = '1.01';
    $h5c_options['wheelZoom'] = 'false';
    $h5c_options['dragControl'] = 'false';
    $h5c_options['fadeIn'] = '0';
    $h5c_options['freezeActive'] = 'false';
    $h5c_options['outlineMethod'] = 'outline';
    $h5c_options['outlineOffset'] = '5';
    $h5c_options['outlineRadius'] = '0';
    $h5c_options['outlineThickness'] = '2';

    add_option('h5c_options', $h5c_options);
}
add_action( 'activate_html5-cumulus/html5-cumulus.php', 'h5c_init' );
//функция установки значений по умолчанию при активации плагина end

//функция при деактивации плагина begin
function h5c_on_deactivation() {
    if ( ! current_user_can('activate_plugins') ) return;
}
register_deactivation_hook( __FILE__, 'h5c_on_deactivation' );
//функция при деактивации плагина end

//функция при удалении плагина begin
function h5c_on_uninstall() {
    if ( ! current_user_can('activate_plugins') ) return;
    delete_option('h5c_options');
}
register_uninstall_hook( __FILE__, 'h5c_on_uninstall' );
//функция при удалении плагина end

//загрузка файла локализации плагина begin
function h5c_setup(){
    load_plugin_textdomain('html5-cumulus');
}
add_action( 'init', 'h5c_setup' );
//загрузка файла локализации плагина end

//добавление ссылки "Настройки" на странице со списком плагинов begin
function h5c_actions($links) {
    return array_merge(array('settings' => '<a href="options-general.php?page=html5-cumulus.php">' . __('Settings', 'html5-cumulus') . '</a>'), $links);
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'h5c_actions' );
//добавление ссылки "Настройки" на странице со списком плагинов end

//функция загрузки скриптов и стилей плагина только в админке и только на странице настроек плагина begin
function h5c_files_admin( $hook_suffix ) {
    $purl = plugins_url('', __FILE__);
    if ( $hook_suffix == 'settings_page_html5-cumulus' ) {
        if(!wp_script_is('jquery')) {wp_enqueue_script('jquery');}
        wp_register_script('h5c-lettering', $purl . '/inc/jquery.lettering.js');
        wp_enqueue_script('h5c-lettering');
        wp_register_script('h5c-textillate', $purl . '/inc/jquery.textillate.js');
        wp_enqueue_script('h5c-textillate');
        wp_register_style('h5c-animate', $purl . '/inc/animate.min.css');
        wp_enqueue_style('h5c-animate');
        wp_register_script('h5c-script', $purl . '/inc/h5c-script.js', array(), '1.01');
        wp_enqueue_script('h5c-script');
        wp_register_style('h5c-css', $purl . '/inc/h5c-css.css', array(), '1.01');
        wp_enqueue_style('h5c-css');
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
    }
}
add_action( 'admin_enqueue_scripts', 'h5c_files_admin' );
//функция загрузки скриптов и стилей плагина только в админке и только на странице настроек плагина end

//функция загрузки скриптов и стилей плагина на внешней стороне сайта begin
function h5c_files_front() {
    $purl = plugins_url('', __FILE__);
    if(!wp_script_is('jquery')) {wp_enqueue_script('jquery');}
    wp_register_script('h5c-tagcanvas', $purl . '/inc/jquery.tagcanvas.min.js');
    wp_enqueue_script('h5c-tagcanvas');
}
add_action( 'wp_enqueue_scripts', 'h5c_files_front' );
//функция загрузки скриптов и стилей плагина на внешней стороне сайта end

//класс виджета плагина begin
class HTML5_Cumulus_Widget extends H5C_WPJS_Widget {

    // значения виджета по умолчанию
    private $defaults = array( 
        'title'              => 'Tag Cloud',
        'taxonomy'           => 'post_tag',
        'width'              => 220,
        'height'             => 220,
        'shape'              => 'sphere',
        'set_canvasBgColor'  => 'off',
        'canvasBgColor'      => '#ffffff',
        'number'             => 20,
        'textFont'           => 'Impact,"Arial Black",sans-serif',
        'textHeight'         => 16,
        'randomColor'        => 'off',
        'textColor'          => '#000000',
        'outlineColor'       => '#000000',
        'set_tagBgColor'     => 'off',
        'tagBgColor'         => '#f2f2f2',
        'bgOutlineThickness' => 3,
        'bgRadius'           => 3,
        'exclude_taxonomy'   => '',
        'include_taxonomy'   => '',

        'htmlCode'      => '',
    );

    // создаем виджет
    function __construct() {
        $widget_ops = array(
            'classname' => 'html5_cumulus',
            'description' => esc_html__( '3D tag cloud widget.', 'html5-cumulus' ),
        );
        parent::__construct( 'html5_cumulus', 'HTML5 Cumulus', $widget_ops );
        // скрипты и стили только при редактировании виджета
        add_action('admin_enqueue_scripts', array($this, 'h5c_enqueue_scripts'));
        add_action('admin_footer-widgets.php', array($this, 'h5c_print_scripts' ), 9999);
    }

    // подключаем скрипты и стили для color picker
    public function h5c_enqueue_scripts( $hook_suffix ) {
        if ( ! in_array( $hook_suffix, array( 'widgets.php', 'customize.php' ) ) ) return;
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

    }

    // скрипты и стили виджета
    public function h5c_print_scripts() {
        ?>
        <script>

        </script>
        <style>
        .wp-picker-container{display:table;}
        .wp-color-result{margin-bottom:0!important;}
        .cbtop{margin-top:-4px!important;}
        #yadonate {color: #000;cursor: pointer;text-decoration: none;background-color:#ffdb4d;padding: 3px 26px 4px 25px;font-size: 15px;border-radius: 3px;border: 1px solid rgba(0,0,0,.1);transition: background-color .1s ease-out 0s;}
        #yadonate:hover {background-color:#fc0;}
        #yadonate:focus,#yadonate:active {outline:none;box-shadow: none;}
        .foptions input[type=number] {padding: 0 0 0 8px;}
        .foptions .wp-color-result,.foptions .wp-picker-clear {border-color: #ccc;color: #23282d;}
        .foptions .wp-color-result:hover,.foptions .wp-picker-clear:hover {border-color: #999;color: #23282d;}
        .foptions .wp-color-result:focus,.foptions .wp-picker-clear:focus {border-color: #5b9dd9;box-shadow: 0 0 3px rgba(0,115,170,.8);}
        .foptions .wp-color-result:active,.foptions .wp-picker-clear:active {background: #eee;border-color: #999;box-shadow: inset 0 2px 5px -3px rgba(0,0,0,.5);}
        .foptions .wp-picker-clear{margin-left: 6px;}
        </style>
        <?php
    }

    // вывод виджета согласно настроек
    public function widget( $args, $instance ) {

        $h5c_options = get_option('h5c_options');
    
        if ( $instance['set_canvasBgColor'] == 'on' ) {
            $bgColor = $instance['canvasBgColor'];
        } else {
            $bgColor = 'none';
        }

        $randomid = h5c_randomid(6);

        //требуется при обновлении плагина
        if ( ! isset($instance['exclude_taxonomy']) ) $instance['exclude_taxonomy'] = '';
        if ( ! isset($instance['include_taxonomy']) ) $instance['include_taxonomy'] = '';

        $tag_cloud_args = array(
            'smallest'  => 6,
            'largest'   => 50,
            'unit'      => 'pt',
            'number'    => $instance['number'],
            'format'    => 'list',
            'orderby'   => 'count',
            'order'     => 'RAND',
            'exclude'   => null,
            'include'   => null,
            'link'      => 'view',
            'taxonomy'  => $instance['taxonomy'],
            'echo'      => true,
            'exclude'   => $instance['exclude_taxonomy'],
            'include'   => $instance['include_taxonomy'],
        );

        echo $args['before_widget'];
        if ( ! empty( $instance['title'] ) ) {
            echo PHP_EOL.$args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'].PHP_EOL;
        }

        echo '<div id="html5-cumulus-'.$randomid.'">'.PHP_EOL;
        echo '<canvas width="'.$instance['width'].'" height="'.$instance['height'].'" id="canvas-'.$randomid.'" style="background-color: '.$bgColor.';">'.PHP_EOL;
        echo '<p>'.esc_html__( 'Your browser doesn\'t support the HTML5 CANVAS tag.', 'html5-cumulus' ).'</p>'.PHP_EOL;
        echo '</canvas>'.PHP_EOL;
        echo html_entity_decode($instance['htmlCode'],ENT_QUOTES).PHP_EOL;
        echo '<div style="display: none" id="tagcloud-'.$randomid.'">'.PHP_EOL;
        echo wp_tag_cloud($tag_cloud_args);
        echo '</div>'.PHP_EOL;
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function() {
            if( ! jQuery('#canvas-<?php echo $randomid; ?>').tagcanvas({
                textFont: '<?php echo $instance['textFont']; ?>',
                textColour: '<?php echo $instance['textColor']; ?>',
                outlineColour: '<?php echo $instance['outlineColor']; ?>',
                reverse: true,
                textHeight:<?php echo $instance['textHeight']; ?>,
                <?php if ( $instance['randomColor'] == 'on' ) { echo 'weight:true,weightMode:"colour",weightGradient: { 0: "#f00", 0.33: "#ff0", 0.66: "#0f0", 1: "#00f" }, '.PHP_EOL; } ?>
                <?php if ( $instance['shape'] == 'sphere' ) { echo 'shape: "sphere", '.PHP_EOL; } ?>
                <?php if ( $instance['shape'] == 'hring' ) { echo 'shape: "hring", lock: "x", '.PHP_EOL; } ?>
                <?php if ( $instance['set_tagBgColor'] == 'on' ) { 
                    echo 'bgColour:"'.$instance['tagBgColor'].'",'.PHP_EOL;
                    echo 'bgOutline:"'.$instance['tagBgColor'].'",'.PHP_EOL;
                    echo 'bgOutlineThickness:'.$instance['bgOutlineThickness'].','.PHP_EOL;
                    echo 'bgRadius:'.$instance['bgRadius'].','.PHP_EOL; 
                    } ?>
                depth: 0.8,decel:0.99,padding:0,
                wheelZoom: <?php echo $h5c_options['wheelZoom']; ?>,
                dragControl: <?php echo $h5c_options['dragControl']; ?>,
                fadeIn: <?php echo $h5c_options['fadeIn']; ?>,
                freezeActive: <?php echo $h5c_options['freezeActive']; ?>,
                outlineMethod: "<?php echo $h5c_options['outlineMethod']; ?>",
                outlineOffset: "<?php echo $h5c_options['outlineOffset']; ?>",
                outlineRadius: "<?php echo $h5c_options['outlineRadius']; ?>",
                outlineThickness: "<?php echo $h5c_options['outlineThickness']; ?>",
                maxSpeed: 0.05},'tagcloud-<?php echo $randomid; ?>')
            ){
                jQuery('#html5-cumulus-<?php echo $randomid; ?>').hide();
            }
        });
        </script>
        <?php
        echo $args['after_widget'];
    }

    // вывод настроек виджета
    public function form( $instance ) {

        $instance = wp_parse_args($instance, $this->defaults);
        $lang = get_locale();
        $purl = plugins_url('', __FILE__);
        $randomid = h5c_randomid(6);

        ?>
        <div class="foptions">
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'html5-cumulus' ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
        </p>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'taxonomy' ) ); ?>"><?php esc_attr_e( 'Taxonomy:', 'html5-cumulus' ); ?></label> 
        <select class='widefat' id="<?php echo $this->get_field_id('taxonomy'); ?>" name="<?php echo $this->get_field_name('taxonomy'); ?>" type="text">
        <?php 
        $current_tax = $instance['taxonomy'];
        foreach(get_taxonomies() as $taxonomy) {
            $tax = get_taxonomy($taxonomy);
            if (!$tax->show_tagcloud || empty($tax->labels->name)) {continue;}
            echo '<option ' . selected($taxonomy, $current_tax, false) . ' value="' . esc_attr($taxonomy) . '">' . $tax->labels->name . '</option>';
        } ?>
        </select>
        </p>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'exclude_taxonomy' ) ); ?>"><?php esc_attr_e( 'Exclude tags (comma separated IDs):', 'html5-cumulus' ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'exclude_taxonomy' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'exclude_taxonomy' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['exclude_taxonomy'] ); ?>">
        </p>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'include_taxonomy' ) ); ?>"><?php esc_attr_e( 'Include tags (comma separated IDs):', 'html5-cumulus' ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'include_taxonomy' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'include_taxonomy' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['include_taxonomy'] ); ?>">
        </p>
        <div style="display:block;">
        <p style="width:47%;float:left;margin-top:0;">
        <label for="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>"><?php esc_attr_e( 'Width (in px):', 'html5-cumulus' ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'width' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'width' ) ); ?>" type="number" step="1" value="<?php echo esc_attr( $instance['width'] ); ?>">
        </p>
        <p style="width:47%;float:right;margin-top:0;">
        <label for="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>"><?php esc_attr_e( 'Height (in px):', 'html5-cumulus' ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'height' ) ); ?>" type="number" step="1" value="<?php echo esc_attr( $instance['height'] ); ?>">
        </p>
        </div>
        <p>
        <label for="<?php echo $this->get_field_id('shape'); ?>"><?php esc_attr_e( 'Shape:', 'html5-cumulus' ); ?>
        <select class='widefat' id="<?php echo $this->get_field_id('shape'); ?>" name="<?php echo $this->get_field_name('shape'); ?>" type="text">
            <option value='sphere'<?php echo ($instance['shape']=='sphere')?'selected':''; ?>><?php esc_attr_e( 'Sphere', 'html5-cumulus' ); ?></option>
            <option value='hring'<?php echo ($instance['shape']=='hring')?'selected':''; ?>><?php esc_attr_e( 'Vertical ring', 'html5-cumulus' ); ?></option> 
        </select>
        </label>
        </p>
        <p>
        <label for="<?php echo $this->get_field_id( 'set_canvasBgColor' ); ?>">
        <input class="checkbox cbtop set_canvasBgColor set_canvasBgColor-<?php echo $randomid; ?>" type="checkbox" <?php checked( $instance['set_canvasBgColor'], 'on' ); ?> id="<?php echo $this->get_field_id( 'set_canvasBgColor' ); ?>" name="<?php echo $this->get_field_name( 'set_canvasBgColor' ); ?>" /><?php esc_attr_e( 'Set canvas background color', 'html5-cumulus' ); ?></label>
        </p>
        <p class="color canvasbg canvasbg-<?php echo $randomid; ?>">
        <label for="<?php echo esc_attr( $this->get_field_id( 'canvasBgColor' ) ); ?>"><?php esc_attr_e( 'Canvas background color:', 'html5-cumulus' ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'canvasBgColor' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'canvasBgColor' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['canvasBgColor'] ); ?>">
        </p>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_attr_e( 'Number of tags (1-50):', 'html5-cumulus' ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" step="1" value="<?php echo esc_attr( $instance['number'] ); ?>">
        </p>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'textFont' ) ); ?>"><?php esc_attr_e( 'Font family:', 'html5-cumulus' ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'textFont' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'textFont' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['textFont'] ); ?>">
        </p>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'textHeight' ) ); ?>"><?php esc_attr_e( 'Font size (in px):', 'html5-cumulus' ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'textHeight' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'textHeight' ) ); ?>" type="number" step="1" value="<?php echo esc_attr( $instance['textHeight'] ); ?>">
        </p>
        <p class="color textColor textColor-<?php echo $randomid; ?>" id="textcolor-<?php echo $randomid; ?>">
        <label for="<?php echo esc_attr( $this->get_field_id( 'textColor' ) ); ?>"><?php esc_attr_e( 'Tag color:', 'html5-cumulus' ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'textColor' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'textColor' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['textColor'] ); ?>">
        </p>
        <p>
        <label for="<?php echo $this->get_field_id( 'randomColor' ); ?>"><input class="checkbox cbtop randomColor randomColor-<?php echo $randomid; ?>" type="checkbox" <?php checked( $instance['randomColor'], 'on' ); ?> id="<?php echo $this->get_field_id( 'randomColor' ); ?>" name="<?php echo $this->get_field_name( 'randomColor' ); ?>" /><?php esc_attr_e( 'Random color for each tag', 'html5-cumulus' ); ?></label>
        </p>
        <p class="color">
        <label for="<?php echo esc_attr( $this->get_field_id( 'outlineColor' ) ); ?>"><?php esc_attr_e( 'Tag highlight color:', 'html5-cumulus' ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'outlineColor' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'outlineColor' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['outlineColor'] ); ?>">
        </p>
        <p>
        <label for="<?php echo $this->get_field_id( 'set_tagBgColor' ); ?>"><input class="checkbox cbtop set_tagBgColor set_tagBgColor-<?php echo $randomid; ?>" type="checkbox" <?php checked( $instance['set_tagBgColor'], 'on' ); ?> id="<?php echo $this->get_field_id( 'set_tagBgColor' ); ?>" name="<?php echo $this->get_field_name( 'set_tagBgColor' ); ?>" /> <?php esc_attr_e( 'Set tag background color', 'html5-cumulus' ); ?></label>
        </p>
        <div class="tagbg tagbg-<?php echo $randomid; ?>">
        <p class="color">
        <label for="<?php echo esc_attr( $this->get_field_id( 'tagBgColor' ) ); ?>"><?php esc_attr_e( 'Tag background color:', 'html5-cumulus' ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'tagBgColor' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'tagBgColor' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['tagBgColor'] ); ?>">
        </p>
        <div style="display:block;">
        <p style="width:47%;float:left;margin-top:0;">
        <label for="<?php echo esc_attr( $this->get_field_id( 'bgOutlineThickness' ) ); ?>"><?php esc_attr_e( 'Padding (in px):', 'html5-cumulus' ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'bgOutlineThickness' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'bgOutlineThickness' ) ); ?>" type="number" step="1" value="<?php echo esc_attr( $instance['bgOutlineThickness'] ); ?>">
        </p>
        <p style="width:47%;float:right;margin-top:0;">
        <label for="<?php echo esc_attr( $this->get_field_id( 'bgRadius' ) ); ?>"><?php esc_attr_e( 'Border radius (in px):', 'html5-cumulus' ); ?></label> 
        <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'bgRadius' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'bgRadius' ) ); ?>" type="number" step="1" value="<?php echo esc_attr( $instance['bgRadius'] ); ?>">
        </p>
        </div>
        </div>
        <p>
        <label for="<?php echo esc_attr( $this->get_field_id( 'htmlCode' ) ); ?>"><?php esc_attr_e( 'HTML-code after tag cloud:', 'html5-cumulus' ); ?></label> 
        <textarea rows="3" class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'htmlCode' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'htmlCode' ) ); ?>"><?php echo esc_attr( $instance['htmlCode'] ); ?></textarea>
        </p>
        <p>
        <?php $menulink = get_bloginfo('url') .'/wp-admin/options-general.php?page=html5-cumulus.php'; ?> 
        <?php esc_attr_e( 'More configuration settings are available on the page', 'html5-cumulus' ); ?> <a target="_blank" href="<?php echo $menulink; ?>"><?php esc_attr_e( '&#8220;Settings \ HTML5 Cumulus&#8221;', 'html5-cumulus' ); ?></a>.
        </p>
        <?php if ($lang == 'ru_RU') { ?>
        <p style="margin-top: 20px;margin-bottom: 15px;">
            <a target="_blank" id="yadonate" href="https://money.yandex.ru/to/41001443750704/200">Подарить</a> 
        </p>
        <?php } else { ?>
        <p style="margin-top: 20px;margin-bottom: 15px;">
            <a target="_blank" href="https://www.paypal.me/flector"><img alt="" title="<?php esc_attr_e( 'Donate with PayPal', 'html5-cumulus' ); ?>" src="<?php echo $purl . '/img/donate.gif'; ?>" /></a>
        </p>
        <?php } ?>
        </div>
        <script>
        jQuery(document).ready(function($) {

            $('.set_canvasBgColor-<?php echo $randomid; ?>').change(function() {
                if ($(this).is(":checked")) {
                    $('.canvasbg-<?php echo $randomid; ?>').fadeIn();
                } else {
                    $('.canvasbg-<?php echo $randomid; ?>').hide();
                }
            });

            $('.randomColor-<?php echo $randomid; ?>').change(function() {
                if ($(this).is(":checked")) {
                    $('.textColor-<?php echo $randomid; ?>').slideUp(200);
                } else {
                    $('.textColor-<?php echo $randomid; ?>').slideDown(200);
                }
            });

            $('.set_tagBgColor-<?php echo $randomid; ?>').change(function() {
                if ($(this).is(":checked")) {
                    $('.tagbg-<?php echo $randomid; ?>').fadeIn();
                } else {
                    $('.tagbg-<?php echo $randomid; ?>').hide();
                }
            });

        });

        </script>
        <?php 
    }

    // сохраняем настройки виджета
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance = $old_instance;
        
        $instance['title'] = sanitize_text_field( $new_instance['title'] );
        $instance['taxonomy'] = sanitize_text_field( $new_instance['taxonomy'] );

        $check_exclude = str_replace(',', '', $new_instance['exclude_taxonomy']);
        if (ctype_digit($check_exclude)) {
            $instance['exclude_taxonomy'] = sanitize_text_field( $new_instance['exclude_taxonomy'] );
        }
        $check_include = str_replace(',', '', $new_instance['include_taxonomy']);
        if (ctype_digit($check_include)) {
            $instance['include_taxonomy'] = sanitize_text_field( $new_instance['include_taxonomy'] );
        }

        $instance['shape'] = sanitize_text_field( $new_instance['shape'] );

        $width = sanitize_text_field( $new_instance['width'] );
        if ( $width < 100 ) $width = 100;
        if ( $width > 500 ) $width = 500;
        $instance['width'] = $width;

        $height = sanitize_text_field( $new_instance['height'] );
        if ( $height < 100 ) $height = 100;
        if ( $height > 500 ) $height = 500;
        $instance['height'] = $height;

        $instance['canvasBgColor'] = sanitize_text_field( $new_instance['canvasBgColor'] );
        $instance['set_canvasBgColor'] = sanitize_text_field( $new_instance['set_canvasBgColor'] );

        $number = sanitize_text_field( $new_instance['number'] );
        if ( $number < 1 ) $number = 1;
        if ( $number > 50 ) $number = 50;
        $instance['number'] = $number;

        $instance['textFont'] = sanitize_text_field( $new_instance['textFont'] );

        $textHeight = sanitize_text_field( $new_instance['textHeight'] );
        if ( $textHeight < 8 ) $textHeight = 8;
        if ( $textHeight > 30 ) $textHeight = 30;
        $instance['textHeight'] = $textHeight;

        $instance['randomColor'] = sanitize_text_field( $new_instance['randomColor'] );
        $instance['textColor'] = sanitize_text_field( $new_instance['textColor'] );
        $instance['outlineColor'] = sanitize_text_field( $new_instance['outlineColor'] );

        $instance['set_tagBgColor'] = sanitize_text_field( $new_instance['set_tagBgColor'] );
        $instance['tagBgColor'] = sanitize_text_field( $new_instance['tagBgColor'] );

        $bgOutlineThickness = sanitize_text_field( $new_instance['bgOutlineThickness'] );
        if ( $bgOutlineThickness < 0 ) $bgOutlineThickness = 0;
        if ( $bgOutlineThickness > 20 ) $bgOutlineThickness = 20;
        $instance['bgOutlineThickness'] = $bgOutlineThickness;

        $bgRadius = sanitize_text_field( $new_instance['bgRadius'] );
        if ( $bgRadius < 0 ) $bgRadius = 0;
        if ( $bgRadius > 20 ) $bgRadius = 20;
        $instance['bgRadius'] = $bgRadius;

        $instance['htmlCode'] = esc_textarea( $new_instance['htmlCode'] );

        return $instance;
    }

    // функции, выполняющиеся при загрузке виджета (сохраненный или новый - неважно)
    // доступен widget и widget_id этого добавленного виджета
    function form_javascript_init() {
        ?>

            if ($('#'+widget_id+' .set_canvasBgColor').is(":checked")) {
                $('#'+widget_id+' .canvasbg').show();
            } else {
                $('#'+widget_id+' .canvasbg').hide();
            }

            if ($('#'+widget_id+' .randomColor').is(":checked")) {
                $('#'+widget_id+' .textColor').hide();
            } else {
                $('#'+widget_id+' .textColor').show();
            }

            if ($('#'+widget_id+' .set_tagBgColor').is(":checked")) {
                $('#'+widget_id+' .tagbg').show();
            } else {
                $('#'+widget_id+' .tagbg').hide();
            }

            $('.color input', widget).wpColorPicker({
                <?php if ( $this->is_customizer ) ?> change: _.throttle( function () { $(this).trigger('change'); }, 1000, {leading: false} )
            });
        <?php
    }
}
//класс виджета плагина end

//регистрация виджета на основе класса begin
function h5c_register_widget() {
    register_widget( "HTML5_Cumulus_Widget" );
}
add_action( 'widgets_init', 'h5c_register_widget' );
//регистрация виджета на основе класса end

//функция генерация случайного текстового ID из заданного диапазона begin
function h5c_randomid( $length = 4 ){
    $chars = 'abdefhiknrstyz';
    $numChars = strlen($chars);
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= substr($chars, rand(1, $numChars) - 1, 1);
    }
    return $string;
}
//функция генерация случайного текстового ID из заданного диапазона end

//функция вывода страницы настроек плагина begin
function h5c_options_page() {
$purl = plugins_url('', __FILE__);

if (isset($_POST['submit'])) {

//проверка безопасности при сохранении настроек плагина begin
if ( ! wp_verify_nonce( $_POST['h5c_nonce'], plugin_basename(__FILE__) ) || ! current_user_can('edit_posts') ) {
    wp_die(__( 'Cheatin&#8217; uh?', 'html5-cumulus' ));
}
//проверка безопасности при сохранении настроек плагина end

    //проверяем и сохраняем введенные пользователем данные begin
    $h5c_options = get_option('h5c_options');

    $h5c_options['wheelZoom'] = sanitize_text_field($_POST['wheelZoom']);
    $h5c_options['dragControl'] = sanitize_text_field($_POST['dragControl']);
    $h5c_options['fadeIn'] = sanitize_text_field($_POST['fadeIn']);
    $h5c_options['freezeActive'] = sanitize_text_field($_POST['freezeActive']);
    $h5c_options['outlineMethod'] = sanitize_text_field($_POST['outlineMethod']);
    $h5c_options['outlineOffset'] = sanitize_text_field($_POST['outlineOffset']);
    $h5c_options['outlineRadius'] = sanitize_text_field($_POST['outlineRadius']);
    $h5c_options['outlineThickness'] = sanitize_text_field($_POST['outlineThickness']);

    update_option('h5c_options', $h5c_options);
    //проверяем и сохраняем введенные пользователем данные end
}
$h5c_options = get_option('h5c_options');
?>
<?php if (!empty($_POST) ) :
if ( ! wp_verify_nonce( $_POST['h5c_nonce'], plugin_basename(__FILE__) ) || ! current_user_can('edit_posts') ) {
    wp_die(__( 'Cheatin&#8217; uh?', 'html5-cumulus' ));
}
?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.', 'html5-cumulus'); ?></strong></p></div>
<?php endif; ?>

<div class="wrap foptions">
<h2><?php _e('&#8220;HTML5 Cumulus&#8221; Settings', 'html5-cumulus'); ?><span id="restore-hide-blocks" class="dashicons dashicons-admin-generic hide" title="<?php _e('Show hidden blocks', 'html5-cumulus'); ?>"></span></h2>

<div class="metabox-holder" id="poststuff">
<div class="meta-box-sortables">

<?php $lang = get_locale(); ?>
<?php if ($lang == 'ru_RU') { ?>
<div class="postbox" id="donat">
<script>
var closedonat = localStorage.getItem('h5c-close-donat');
if (closedonat == 'yes') {
    document.getElementById('donat').className = 'postbox hide';
    document.getElementById('restore-hide-blocks').className = 'dashicons dashicons-admin-generic';
}
</script>
    <h3 style="border-bottom: 1px solid #E1E1E1;background: #f7f7f7;"><span class="tcode">Вам нравится этот плагин ?</span>
    <span id="close-donat" class="dashicons dashicons-no-alt" title="Скрыть блок"></span></h3>
    <div class="inside" style="display: block;margin-right: 12px;">
        <img src="<?php echo $purl . '/img/icon_coffee.png'; ?>" title="Купить мне чашку кофе :)" style=" margin: 5px; float:left;" />
        <p>Привет, меня зовут <strong>Flector</strong>.</p>
        <p>Я потратил много времени на разработку этого плагина.<br />
        Поэтому не откажусь от небольшого пожертвования :)</p>
        <a target="_blank" id="yadonate" href="https://money.yandex.ru/to/41001443750704/200">Подарить</a> 
        <p>Или вы можете заказать у меня услуги по WordPress, от мелких правок до создания полноценного сайта.<br />
        Быстро, качественно и дешево. Прайс-лист смотрите по адресу <a target="_blank" href="https://www.wpuslugi.ru/?from=h5c-plugin">https://www.wpuslugi.ru/</a>.</p>
        <div style="clear:both;"></div>
    </div>
</div>
<?php } else { ?>
<div class="postbox" id="donat">
<script>
var closedonat = localStorage.getItem('h5c-close-donat');
if (closedonat == 'yes') {
    document.getElementById('donat').className = 'postbox hide';
    document.getElementById('restore-hide-blocks').className = 'dashicons dashicons-admin-generic';
}
</script>
    <h3 style="border-bottom: 1px solid #E1E1E1;background: #f7f7f7;"><span class="tcode"><?php _e('Do you like this plugin ?', 'easy-yandex-share'); ?></span>
    <span id="close-donat" class="dashicons dashicons-no-alt" title="<?php _e('Hide block', 'easy-yandex-share'); ?>"></span></h3>
    <div class="inside" style="display: block;margin-right: 12px;">
        <img src="<?php echo $purl . '/img/icon_coffee.png'; ?>" title="<?php _e('buy me a coffee', 'easy-yandex-share'); ?>" style=" margin: 5px; float:left;" />
        <p><?php _e('Hi! I\'m <strong>Flector</strong>, developer of this plugin.', 'easy-yandex-share'); ?></p>
        <p><?php _e('I\'ve spent many hours developing this plugin.', 'easy-yandex-share'); ?><br />
        <?php _e('If you like and use this plugin, you can <strong>buy me a cup of coffee</strong>.', 'easy-yandex-share'); ?></p>
        <a target="_blank" href="https://www.paypal.me/flector"><img alt="" src="<?php echo $purl . '/img/donate.gif'; ?>" title="<?php _e('Donate with PayPal', 'easy-yandex-share'); ?>" /></a>
        <div style="clear:both;"></div>
    </div>
</div>
<?php } ?>


<form action="" method="post">

<div class="postbox">

    <h3 style="border-bottom: 1px solid #E1E1E1;background: #f7f7f7;"><span class="tcode"><?php _e('Options', 'html5-cumulus'); ?></span></h3>
    <div class="inside" style="display: block;">

        <table class="form-table">

            <tr>
                <th><?php _e('wheelZoom:', 'html5-cumulus'); ?></th>
                <td>
                    <div class="switch-field">
                        <input type="radio" class="toggle" id="wheelZoom_left" name="wheelZoom" value="true" <?php if ($h5c_options['wheelZoom'] == 'true') echo 'checked'; ?> />
                        <label for="wheelZoom_left">true</label>
                        <input type="radio" class="toggle" id="wheelZoom_right" name="wheelZoom" value="false" <?php if ($h5c_options['wheelZoom'] == 'false') echo 'checked'; ?> />
                        <label for="wheelZoom_right">false</label>
                    </div>
                    <br /><small><?php _e('Enables zooming the cloud in and out using the mouse wheel or a scroll gesture.', 'html5-cumulus'); ?></small>
                </td>
            </tr>
            <tr>
                <th><?php _e('dragControl:', 'html5-cumulus'); ?></th>
                <td>
                    <div class="switch-field">
                        <input type="radio" class="toggle" id="dragControl_left" name="dragControl" value="true" <?php if ($h5c_options['dragControl'] == 'true') echo 'checked'; ?> />
                        <label for="dragControl_left">true</label>
                        <input type="radio" class="toggle" id="dragControl_right" name="dragControl" value="false" <?php if ($h5c_options['dragControl'] == 'false') echo 'checked'; ?> />
                        <label for="dragControl_right">false</label>
                    </div>
                    <br /><small><?php _e('By default, cloud movement is based on cursor position. When this option is enabled, the tag cloud reacts on dragging instead.', 'html5-cumulus'); ?></small>
                </td>
            </tr>
            <tr>
                <th><?php _e('fadeIn:', 'html5-cumulus'); ?></th>
                <td>
                    <input style="max-width: 60px;" type="number" name="fadeIn" min="0" max="99999" step="1" value="<?php echo $h5c_options['fadeIn']; ?>" />
                    <br /><small><?php _e('Time to fade in tags at start, in milliseconds.', 'html5-cumulus'); ?></small>
               </td>
            </tr>
            <tr>
                <th><?php _e('freezeActive:', 'html5-cumulus'); ?></th>
                <td>
                    <div class="switch-field">
                        <input type="radio" class="toggle" id="freezeActive_left" name="freezeActive" value="true" <?php if ($h5c_options['freezeActive'] == 'true') echo 'checked'; ?> />
                        <label for="freezeActive_left">true</label>
                        <input type="radio" class="toggle" id="freezeActive_right" name="freezeActive" value="false" <?php if ($h5c_options['freezeActive'] == 'false') echo 'checked'; ?> />
                        <label for="freezeActive_right">false</label>
                    </div>
                    <br /><small><?php _e('Set to &#8220;true&#8221; to pause movement when any tag is highlighted.', 'html5-cumulus'); ?></small>
                </td>
            </tr>
            <tr>
                <th><?php _e('outlineMethod:', 'html5-cumulus'); ?></th>
                <td>
                    <div class="switch-field">
                        <input type="radio" class="toggle" id="outlineMethod_left" name="outlineMethod" value="outline" <?php if ($h5c_options['outlineMethod'] == 'outline') echo 'checked'; ?> />
                        <label for="outlineMethod_left">outline</label>
                        <input type="radio" class="toggle" id="outlineMethod_center" name="outlineMethod" value="block" <?php if ($h5c_options['outlineMethod'] == 'block') echo 'checked'; ?> />
                        <label for="outlineMethod_center">block</label>
                        <input type="radio" class="toggle" id="outlineMethod_right" name="outlineMethod" value="none" <?php if ($h5c_options['outlineMethod'] == 'none') echo 'checked'; ?> />
                        <label for="outlineMethod_right">none</label>
                    </div>
                    <br /><small><?php _e('Type of highlight to use.', 'html5-cumulus'); ?></small>
                </td>
            </tr>
            <tr class="outlinetr" <?php if ($h5c_options['outlineMethod'] == 'none') echo 'style="display:none;"'; ?>>
                <th><?php _e('outlineOffset:', 'html5-cumulus'); ?></th>
                <td>
                    <input style="max-width: 47px;" type="number" name="outlineOffset" min="0" max="15" step="1" value="<?php echo $h5c_options['outlineOffset']; ?>" />
                    <br /><small><?php _e('Distance of outline from text, in pixels. This also increases the size of the active area around the tag.', 'html5-cumulus'); ?></small>
               </td>
            </tr>
            <tr class="outlinetr" <?php if ($h5c_options['outlineMethod'] == 'none') echo 'style="display:none;"'; ?>>
                <th><?php _e('outlineRadius:', 'html5-cumulus'); ?></th>
                <td>
                    <input style="max-width: 47px;" type="number" name="outlineRadius" min="0" max="20" step="1" value="<?php echo $h5c_options['outlineRadius']; ?>" />
                    <br /><small><?php _e('Radius for rounded corners on outline box in pixels.', 'html5-cumulus'); ?></small>
               </td>
            </tr>
            <tr class="outlinetr" <?php if ($h5c_options['outlineMethod'] == 'none') echo 'style="display:none;"'; ?>>
                <th><?php _e('outlineThickness:', 'html5-cumulus'); ?></th>
                <td>
                    <input style="max-width: 47px;" type="number" name="outlineThickness" min="0" max="10" step="1" value="<?php echo $h5c_options['outlineThickness']; ?>" />
                    <br /><small><?php _e('Thickness of outline in pixels.', 'html5-cumulus'); ?></small>
               </td>
            </tr>

            <tr>
                <th></th>
                <td>
                    <input type="submit" name="submit" class="button button-primary" value="<?php _e('Update options &raquo;', 'html5-cumulus'); ?>" />
                </td>
            </tr>
        </table>
    </div>
</div>

<div id="about" class="postbox" style="margin-bottom:0;">
<script>
var closeabout = localStorage.getItem('h5c-close-about');
if (closeabout == 'yes') {
    document.getElementById('about').className = 'postbox hide';
    document.getElementById('restore-hide-blocks').className = 'dashicons dashicons-admin-generic';
}
</script>
    <h3 style="border-bottom: 1px solid #E1E1E1;background: #f7f7f7;"><span class="tcode"><?php _e('About', 'html5-cumulus'); ?></span>
    <span id="close-about" class="dashicons dashicons-no-alt" title="<?php _e('Hide block', 'html5-cumulus'); ?>"></span></h3>
      <div class="inside" style="padding-bottom:15px;display: block;">

      <p><?php _e('If you liked my plugin, please <a target="_blank" href="https://wordpress.org/support/plugin/html5-cumulus/reviews/#new-post"><strong>rate</strong></a> it.', 'html5-cumulus'); ?></p>
      <p style="margin-top:20px;margin-bottom:10px;"><?php _e('You may also like my other plugins:', 'html5-cumulus'); ?></p>

      <div class="about">
        <ul>
            <?php if ($lang == 'ru_RU') : ?>
            <li><a target="_blank" href="https://ru.wordpress.org/plugins/rss-for-yandex-zen/">RSS for Yandex Zen</a> - создание RSS-ленты для сервиса Яндекс.Дзен.</li>
            <li><a target="_blank" href="https://ru.wordpress.org/plugins/rss-for-yandex-turbo/">RSS for Yandex Turbo</a> - создание RSS-ленты для сервиса Яндекс.Турбо.</li>
            <?php endif; ?>
            <li><a target="_blank" href="https://wordpress.org/plugins/bbspoiler/">BBSpoiler</a> - <?php _e('this plugin allows you to hide text using the tags [spoiler]your text[/spoiler].', 'html5-cumulus'); ?></li>
            <li><a target="_blank" href="https://wordpress.org/plugins/easy-textillate/">Easy Textillate</a> - <?php _e('very beautiful text animations (shortcodes in posts and widgets or PHP code in theme files).', 'html5-cumulus'); ?></li>
            <li><a target="_blank" href="https://wordpress.org/plugins/cool-image-share/">Cool Image Share</a> - <?php _e('this plugin adds social sharing icons to each image in your posts.', 'html5-cumulus'); ?></li>
            <li><a target="_blank" href="https://wordpress.org/plugins/today-yesterday-dates/">Today-Yesterday Dates</a> - <?php _e('this plugin changes the creation dates of posts to relative dates.', 'html5-cumulus'); ?></li>
            <li><a target="_blank" href="https://wordpress.org/plugins/truncate-comments/">Truncate Comments</a> - <?php _e('this plugin uses Javascript to hide long comments (Amazon-style comments).', 'html5-cumulus'); ?></li>
            <li><a target="_blank" href="https://wordpress.org/plugins/easy-yandex-share/">Easy Yandex Share</a> - <?php _e('share buttons for WordPress from Yandex.', 'html5-cumulus'); ?></li>
            <li><a target="_blank" href="https://wordpress.org/plugins/hide-my-dates/">Hide My Dates</a> - <?php _e('this plugin hides post and comment publishing dates from Google.', 'html5-cumulus'); ?></li>
        </ul>
      </div>
    </div>
</div>
<?php wp_nonce_field( plugin_basename(__FILE__), 'h5c_nonce' ); ?>
</form>
</div>
</div>
<?php 
}
//функция вывода страницы настроек плагина end

//функция добавления ссылки на страницу настроек плагина в раздел "Настройки" begin
function h5c_menu() {
    add_options_page('HTML5 Cumulus', 'HTML5 Cumulus', 'manage_options', 'html5-cumulus.php', 'h5c_options_page');
}
add_action( 'admin_menu', 'h5c_menu' );
//функция добавления ссылки на страницу настроек плагина в раздел "Настройки" end

//класс, позволяющий выполнять js-скрипты сразу после добавления виджета,
//взято тут - https://wordpress.stackexchange.com/a/212676/96918 begin
class H5C_WPJS_Widget extends WP_Widget { // For widgets using javascript in form().
    var $js_ns = 'wpse'; // Javascript namespace.
    var $js_init_func = ''; // Name of javascript init function to call. Initialized in constructor.
    var $is_customizer = false; // Whether in customizer or not. Set on 'load-customize.php' action (if any).

    public function __construct( $id_base, $name, $widget_options = array(), $control_options = array(), $js_ns = '' ) {
        parent::__construct( $id_base, $name, $widget_options, $control_options );
        if ( $js_ns ) {
            $this->js_ns = $js_ns;
        }
        $this->js_init_func = $this->js_ns . '.' . $this->id_base . '_init';
        add_action( 'load-widgets.php', array( $this, 'load_widgets_php' ) );
        add_action( 'load-customize.php', array( $this, 'load_customize_php' ) );
    }

    // Called on 'load-widgets.php' action added in constructor.
    public function load_widgets_php() {
        add_action( 'in_widget_form', array( $this, 'form_maybe_call_javascript_init' ) );
        add_action( 'admin_print_scripts', array( $this, 'admin_print_scripts' ), PHP_INT_MAX );
    }

    // Called on 'load-customize.php' action added in constructor.
    public function load_customize_php() {
        $this->is_customizer = true;
        // Don't add 'in_widget_form' action as customizer sends 'widget-added' event to existing widgets too.
        add_action( 'admin_print_scripts', array( $this, 'admin_print_scripts' ), PHP_INT_MAX );
    }

    // Form javascript initialization code here. "widget" and "widget_id" available.
    public function form_javascript_init() {
    }

    // Called on 'in_widget_form' action (ie directly after form()) when in traditional widgets interface.
    // Run init directly unless we're newly added.
    public function form_maybe_call_javascript_init( $callee_this ) {
        if ( $this === $callee_this && '__i__' !== $this->number ) {
            ?>
            <script type="text/javascript">
            jQuery(function ($) {
                <?php echo $this->js_init_func; ?>(null, $('#widgets-right [id$="<?php echo $this->id; ?>"]'));
            });
            </script>
            <?php
        }
    }

    // Called on 'admin_print_scripts' action added in constructor.
    public function admin_print_scripts() {
        ?>
        <script type="text/javascript">
        var <?php echo $this->js_ns; ?> = <?php echo $this->js_ns; ?> || {}; // Our namespace.
        jQuery(function ($) {
            <?php echo $this->js_init_func; ?> = function (e, widget) {
                var widget_id = widget.attr('id');
                if (widget_id.search(/^widget-[0-9]+_<?php echo $this->id_base; ?>-[0-9]+$/) === -1) { // Check it's our widget.
                    return;
                }
                <?php $this->form_javascript_init(); ?>
            };
            $(document).on('widget-added', <?php echo $this->js_init_func; ?>); // Call init on widget add.
        });
        </script>
        <?php
    }
}
//класс, позволяющий выполнять js-скрипты сразу после добавления виджета,
//взято тут - https://wordpress.stackexchange.com/a/212676/96918 end
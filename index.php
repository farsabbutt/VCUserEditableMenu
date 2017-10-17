<?php

/**
 * Plugin Name: VC Extension Element User Editable Menu
 * Plugin URI: https://github.com/farsabbutt
 * Description: Extension for Visual Composer.
 * Version: 1.0
 * Author: Farsab B.
 * Author URI: https://github.com/farsabbutt
 * Requires at least: 4.4
 * Tested up to: 4.5
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

final class VC_Element_User_Editable_Menu {

    private static $object;
    private $version = '1.0';

    private function __construct() {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    public static function getInstance() {
        if (self::$object == null) {
            self::$object = new VC_Element_User_Editable_Menu();
        }
        return VC_Element_User_Editable_Menu::$object;
    }

    public function init_hooks() {

        vc_add_shortcode_param('button', array($this, 'vc_button_param'));
        vc_add_shortcode_param('sortable', array($this, 'vc_sortable_param'));
        vc_add_shortcode_param('hiddenfield', array($this, 'vc_hidden_param'), $this->plugin_url() . '/assets/js/dist/backend-sortable-items.js');

        add_shortcode('vc_uem', array($this, 'vc_uem_func'));

        add_action('vc_before_init', array($this, 'vc_extension_element_uem'));
        // ajax methods
        add_action('wp_ajax_vc_uem_add_page', array($this, 'vc_uem_add_page'));
        add_action('wp_ajax_vc_uem_remove_page', array($this, 'vc_uem_remove_page'));
        
        add_filter('wp_insert_post_data', array($this, 'check_author_permission'), '99', 2);
    }

    // request type    
    private function is_request($type) {
        switch ($type) {
            case 'admin' :
                return is_admin();
            case 'ajax' :
                return defined('DOING_AJAX');
            case 'cron' :
                return defined('DOING_CRON');
            case 'frontend' :
                return (!is_admin() || defined('DOING_AJAX') ) && !defined('DOING_CRON');
        }
    }

    private function define_constants() {
        $this->define('UEM_PLUGIN_FILE', __FILE__);
        $this->define('UEM_VERSION', $this->version);
    }

    private function define($name, $value) {
        if (!defined($name)) {
            define($name, $value);
        }
    }

    public function plugin_url() {
        return untrailingslashit(plugins_url('/', __FILE__));
    }

    // include all the classes
    public function includes() {
        include_once('includes/class-uem-frontend-scripts.php');
    }

    public function vc_extension_element_uem() {

        vc_map(array(
            'name' => __('User Editable Menu', 'js_composer'),
            'icon' => 'icon-heart',
            'base' => 'vc_uem',
            'show_settings_on_create' => true,
            'category' => __('Content', 'js_composer'),
            'class' => '',
            'description' => __('', 'js_composer'),
            'params' => array(
                array(
                    'type' => 'textfield',
                    'heading' => __('Add Page', 'js_composer'),
                    'param_name' => 'add_new_page',
                    'value' => '',
                    'description' => __('Set Page Name', 'js_composer'),
                    'dependency' => array(
                    ),
                ),
                array(
                    'type' => 'button',
                    'heading' => '',
                    'param_name' => 'btn',
                    'value' => '',
                    'std' => '0',
                    'description' => __('', 'js_composer'),
                ),
                array(
                    'type' => 'sortable',
                    'heading' => '',
                    'param_name' => 'sorter',
                    'value' => '',
                    'std' => '0',
                    'description' => __('Double click menu item to delete it.', 'js_composer'),
                ),
                array(
                    'type' => 'hiddenfield',
                    'param_name' => 'uem_items',
                    'value' => ''
                ),
                array(
                    'type' => 'textfield',
                    'heading' => __('Base URL', 'js_composer'),
                    'param_name' => 'base_url',
                    'value' => '',
                    // default video url
                    'description' => __('Set Base URL', 'js_composer'),
                    'dependency' => array(
                    ),
                ),
                
                array(
			'type' => 'textfield',
			'heading' => __( 'Extra class name', 'js_composer' ),
			'param_name' => 'el_class',
			'description' => __( 'Style particular content element differently - add a class name and refer to it in custom CSS.', 'js_composer' ),
		),
                
            ),
            'admin_enqueue_js' => $this->plugin_url() . '/assets/js/dist/backend-custom.js',
            'admin_enqueue_css' => $this->plugin_url() . '/assets/css/vc_custom_backend_css.css',
        ));
    }

    public function vc_sortable_param($settings, $value) {

        return '<ul id="vc_custom_sortable"></ul>';
    }

    public function vc_button_param($settings, $value) {
        return '<button id="vc_add_page" class="vc_btn vc_btn-grey vc_btn-sm">Add Page</button>';
    }

    public function vc_hidden_param($settings, $value) {
        return '<input type="hidden" class="wpb_vc_param_value" name="uem_items" value="' . $value . '">';
    }

    // frontend display 
    public function vc_uem_func($atts) {
        extract(shortcode_atts(array(
            'uem_items' => 'items'
                        ), $atts));

        $subject = $uem_items;
        $pattern = "/([0-9,a-zA-Z ]+)/";
        //$input = '<input type="hidden" name="sortable_list"';
       // $value = "";
        preg_match_all($pattern, $subject, $matches);
        $html = '<ul id="sortable_items">';
        foreach ($matches[0] as $match) {
            $v = explode(",", $match);
            $html .= '<li data-pageid="' . $v[0] . '">' .'<a href="'.get_permalink($v[0]).'">'. $v[1] .'</a>'. '</li>';
           // $value .= $v[0].','.$v[1].';';
        }
        
        //$html .= '</ul>';
        //$input.= ' value='.$value.' />';
        return $html;
    }

    public function check_author_permission($data, $postarr) {


        $post_type = get_post_type($postarr["ID"]);

        if ($post_type == "page") {

            $author_id = get_post_field('post_author', $postarr["ID"]);
            $current_user_id = get_current_user_id();
            if (!is_admin()) {
                if ($author_id != $current_user_id) {
                    wp_die('You cannot update this page because you are not the owner of this page!, <a href="' . get_bloginfo('url') . '/wp-admin/post.php?post=' . $postarr["ID"] . '&action=edit">Go Back</a>');
                }
            }
        }
        return $data;
    }

    public function vc_uem_add_page() {
        // Handle request then generate response using WP_Ajax_Response
        $my_post = array();
        $my_post['post_title'] = $_POST["data"]["title"];

        if (isset($_POST["data"]["base_url"]) && !empty($_POST["data"]["base_url"])) {
            // get parent page id
            $parentWPObj = get_page_by_path($_POST["data"]["base_url"]);

            // if valid 
            if ($parentWPObj != null) {
                $my_post['post_parent'] = $parentWPObj->ID;
            }
        }

        $my_post['post_status'] = 'publish';
        $my_post['post_author'] = get_current_user_id();
        $my_post['post_type'] = 'page';
// Insert the post into the database
        $id = wp_insert_post($my_post);

        $response = array("id" => $id,
            "title" => $my_post["post_title"]);
        echo json_encode($response);

        // Don't forget to stop execution afterward.
        wp_die();
    }
    
    
    public function vc_uem_remove_page(){
        $postid = $_POST["data"]["id"];
        wp_delete_post( $postid );
        wp_die();
    }

}

function UEM() {

    register_activation_hook(__FILE__, 'UEM_plugin_active');
    return VC_Element_User_Editable_Menu::getInstance();
}

function UEM_plugin_active() {
    // checking if visual composer is active
    if (!is_plugin_active('js_composer/js_composer.php')) {
        wp_die('Please activate Visual Composer first, and then try again');
    }
}

$GLOBALS['VC_Element_User_Editable_Menu'] = UEM();

<?php
/*
 * Plugin Name: Stereo contact form
 * Description: Plugin de formulaires
 * Author: Stereo
 * Author URI: https://www.stereo.ca/
 * Text Domain: stereo-contactform
 * Domain Path: /languages
 * Version: 2.1.0
 * License:     0BSD
 *
 * Copyright (c) 2018 Stereo
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('ST_ContactForm')) {

     if( class_exists('ACF') ) {
         include 'acf.php';
     }

    class ST_ContactForm
    {
        var $version = "2.1.0";
        var $post_type = "st_contactform";
        var $taxonomy = "st_contactform_categorie";

        public function __construct()
        {
            // ADD actions
            add_action('wp_ajax_st_post_contact', [$this, 'post_contact']);
            add_action('wp_ajax_nopriv_st_post_contact', [$this, 'post_contact']);
            add_action('init', [$this, 'init']);
            add_action('restrict_manage_posts', [$this, 'filter_by_taxonomy'], 10, 2);
            add_action('wp_enqueue_scripts', [$this, 'register_js'], 30);
            add_action('add_meta_boxes', [$this, 'info_metabox']);

            add_filter('st_cf_mail_to',[$this, 'mail_to'], 1);
            add_filter('st_cf_mail_from',[$this, 'mail_from'], 1);

            add_action('st_cf_files', [$this, 'save_files'], 10, 2);
            add_action('add_meta_boxes',[$this, 'add_file_metabox']);
            add_action( 'wp_ajax_download_file', [$this, 'download_file'] );
        }

        public function init()
        {
            $this->register_cpt();
            $this->register_taxonomy();
        }

        public function register_cpt()
        {
            register_post_type($this->post_type, [
                'labels' => [
                    'name' => __('Formulaires Stereo','stereo-contactform'),
                    'singular_name' => __('Formulaire Stereo','stereo-contactform')
                ],
                'show_ui' => true,
                'rewrite' => false,
                'supports' => array('title')
            ]);
        }

        public function register_taxonomy()
        {
            register_taxonomy($this->taxonomy, $this->post_type, [
                'labels' => [
                    'name' => __('Catégories','stereo-contactform'),
                    'singular_name' => __('Catégorie','stereo-contactform')
                ]
            ]);
        }

        public function insert_term($term)
        {
            return wp_insert_term($term, $this->taxonomy);
        }

        public function filter_by_taxonomy($post_type, $which)
        {
            if ($this->post_type !== $post_type)
                return;

            $taxonomy_obj = get_taxonomy($this->taxonomy);
            $taxonomy_name = $taxonomy_obj->labels->name;

            $terms = get_terms($this->taxonomy);

            echo "<select name='{$this->taxonomy}' id='{$this->taxonomy}' class='postform'>";
            /* translators: %s is replaced with the number of entries */
            echo '<option value="">' . sprintf(esc_html__('Voir tout %s', 'stereo-contactform'), $taxonomy_name) . '</option>';
            foreach ($terms as $term) {
                printf(
                    '<option value="%1$s" %2$s>%3$s (%4$s)</option>',
                    $term->slug,
                    ((isset($_GET[$this->taxonomy]) && ($_GET[$this->taxonomy] == $term->slug)) ? ' selected="selected"' : ''),
                    $term->name,
                    $term->count
                );
            }
            echo '</select>';
        }

        public function info_metabox()
        {
            add_meta_box('st_cf_infos', 'Informations', [$this, 'info_metabox_content'], $this->post_type, 'normal', 'high');
        }

        public function register_js()
        {
            wp_enqueue_script('stereo_contact', plugins_url('/dist/js/bundle.js', __FILE__), array(), $this->version, true);
            wp_localize_script('stereo_contact', 'stereo_cf', array('ajax_url' => admin_url('admin-ajax.php')));

            if( !class_exists('ACF') ) return;
            if ($key = get_field('stereo_contact_recaptcha_v3', 'option')) {
                wp_enqueue_script('stereo_recaptcha_v3', 'https://www.google.com/recaptcha/api.js?render=' . $key, array(), null, true);
                wp_add_inline_script( 'stereo_recaptcha_v3', 'window.recaptcha_v3="'.$key.'";');
            }
        }

        public function info_metabox_content()
        {
            global $post;
            $infos = get_post_meta($post->ID, 'form_data', true);
            include(dirname(__FILE__) . '/templates/infos.php');
        }

        public function post_contact()
        {
            if ($key = get_field('stereo_contact_recaptcha_secret', 'option')) {
                if (!isset($_POST['_token'])) {
                    wp_send_json_error(['message' => 'Token missing'], 400);
                    die;
                }
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,"https://www.google.com/recaptcha/api/siteverify");
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('secret' => $key, 'response' => $_POST['_token'])));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);
                $arrResponse = json_decode($response, true);

                if($arrResponse["success"] && $arrResponse["action"] == 'submit' && $arrResponse["score"] >= 0.5) {
                    // yeah
                } else {
                    wp_send_json_error($arrResponse, 500);
                    die;
                }
            }


            if (!$_POST['_nobot']) {
                header('HTTP/1.0 403 Forbidden');
                die('No bots please!');
            }
            $forminfo = [];
            $titlefield = explode(',', stripslashes($_POST['_title_field']));
            $titlefield = array_map('trim', $titlefield);

            $terms = $_POST['_category'];
            if( ! is_array($terms) ) $terms = array_map('trim', explode(',', $terms));
            foreach($terms as $term) {
                $term_exist = term_exists($term, $this->taxonomy);
                if (0 === $term_exist || null === $term_exist) {
                    $this->insert_term($term);
                }
            }

            foreach ($_POST as $k => $v) {
                if ($k == 'action' || substr($k, 0, 1) == '_') continue;
                $forminfo[str_replace('_', ' ', $k)] = is_array($v) ? implode(', ', array_map('stripslashes', $v)) : stripslashes($v);
            }

            foreach ($titlefield as $field) {
                $title[] = $forminfo[$field];
            }

            $title = implode(' ', $title);
            $postinfo = array('post_status' => 'publish', 'post_type' => $this->post_type, 'post_title' => current_time('Y-m-d H:i:s') . ' - ' . $title);
            $id = wp_insert_post($postinfo);

            add_post_meta($id, 'form_data', apply_filters('st_cf_post_info', $forminfo));
            wp_set_post_terms($id, $terms, $this->taxonomy);



            $this->send_email($forminfo, stripslashes($_POST['_subject']), $id);
            wp_send_json(['success' => true]);
            die();
        }

        public function send_email($forminfo, $subject, $postid)
        {
            $out = [];
            foreach ($forminfo as $fieldname => $value) {
                $out[] = $fieldname . ": " . $value;
            }
            $out = apply_filters('st_cf_mail_fields', implode('<br>', $out));
            $html = "<p><strong>" . apply_filters('st_cf_mailmsg', __("Nouveau formulaire reçu, voici l'information",'stereo-contactform')) . " : </strong></p><p>" . $out . "</p>";
            $html = apply_filters('st_cf_mail_content', $html);
            $to = apply_filters('st_cf_mail_to', get_option('admin_email'));
            $from = apply_filters('st_cf_mail_from', get_option('blogname') . ' <' . get_option('admin_email') . '>');
            $subject = apply_filters('st_cf_mail_subject', $subject);
            $headers = ['From: ' . $from, 'Content-Type: text/html; charset=UTF-8'];
            $emailfld = apply_filters('st_cf_mail_field', 'Courriel');
            if (isset($forminfo[$emailfld]) && is_email($forminfo[$emailfld])) $headers[] = 'Reply-To: ' . $forminfo[$emailfld];
            $headers = apply_filters('st_cf_mail_headers', $headers);

            $files = [];

            if (isset($_FILES['file']) && count($_FILES['file']['name'])) {
                for ($x = 0; $x < count($_FILES['file']['name']); $x++) {
                    if (is_uploaded_file($_FILES['file']['tmp_name'][$x])) {
                        $file = sys_get_temp_dir() . '/' . sanitize_file_name($_FILES['file']['name'][$x]);
                        if (@move_uploaded_file($_FILES['file']['tmp_name'][$x], $file)) {
                            $files[] = $file;
                        }
                    }
                }
            }
            $files = apply_filters('st_cf_files_external', $files);

            wp_mail($to, $subject, $html, $headers, $files);
            if (count($files)) {
                do_action('st_cf_files',$files,$postid);
            }
            foreach ($files as $f) @unlink($f);
        }

        public function mail_to($dst)
        {
            if( !class_exists('ACF') ) return $dst;
            $terms = $_POST['_category'];
            if( ! is_array($terms) ) $terms = array_map('trim', explode(',', $terms));
            $to = '';
            foreach($terms as $term) {
                $term_exist = term_exists($term, $this->taxonomy);
                if ($term_exist) {
                    $t = get_term($term_exist['term_id']);
                    if ($mail = get_field('stereo_to_email', $t)) {
                        if (!empty($to)) $to .= ',';
                        $to .= $mail;
                    }
                }
            }
            if (!empty($to)) return $to;

			if (is_email(get_field('stereo_dest_mail','option'))) return get_field('stereo_dest_name','option').' <'.get_field('stereo_dest_mail','option').'>';
			return $dst;
        }

        public function mail_from($dst)
        {
             if( !class_exists('ACF') ) return $dst;
            if (is_email(get_field('stereo_from_mail','option'))) return get_field('stereo_from_name','option').' <'.get_field('stereo_from_mail','option').'>';
			return $dst;
        }

        public function save_files($files,$postid)
        {
            $uploadedfiles = [];
            $upload_dir = wp_upload_dir();
            $private_dirname = $upload_dir['basedir'].'/private';

            if ( ! file_exists( $wp_mkdir_p ) ) {
                wp_mkdir_p( $private_dirname );
            }

            foreach($files as $f) {
                $fname = time(). '_'.basename($f);
                @copy($f, $private_dirname . '/' . $fname);
                $uploadedfiles[] = $fname;
            }
            update_field('uploadedfiles',$uploadedfiles,$postid);
        }

        public function add_file_metabox()
        {
            add_meta_box('metabox_files', 'Fichier(s) transféré(s)', 'metabox_files_content', 'st_contactform', 'side', 'high');
        }

        public function metabox_files_content($post) {
            $upload_dir = wp_upload_dir();
            $private_dirname = admin_url('admin-ajax.php');
            $uploadedfiles = get_field('uploadedfiles',$post->ID);
            if (is_array($uploadedfiles) && count($uploadedfiles)) {
                foreach($uploadedfiles as $f) {
                    echo '<p><a href="'.$private_dirname . '?action=download_file&file=' . urlencode($f).'" target="_blank">'.$f.'</a></p>';
                }
            }
            else {
                echo '<p>Aucun fichier recu.</p>';
            }
        }

        public function download_file()
        {
            ob_end_clean();
            $upload_dir = wp_upload_dir();
            $private_dirname = $upload_dir['basedir'].'/private';
            $file = $private_dirname . '/' . $_GET['file'];

            if (isset($_GET['file']) && file_exists($file)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($file).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                readfile($file);
                exit;
            }

            die;
        }
    }

    new ST_ContactForm();

    function stereo_contactform_load_plugin_textdomain() {
        load_plugin_textdomain( 'stereo-contactform', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
    }
    add_action( 'plugins_loaded', 'stereo_contactform_load_plugin_textdomain' );
}


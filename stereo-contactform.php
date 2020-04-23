<?php
/*
 * Plugin Name: Stereo contact form
 * Description: Formulaires de contacts
 * Author: Stereo
 * Author URI: https://www.stereo.ca/
 * Version: 1.0.17
 * License:     0BSD
 *
 * Copyright (c) 2018 Stereo
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('ST_ContactForm')) {
    class ST_ContactForm
    {
        var $version = "1.0.17";
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
                    'name' => 'Contacts',
                    'singular_name' => 'Contact'
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
                    'name' => 'Catégories',
                    'singular_name' => 'Catégorie'
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
            echo '<option value="">' . sprintf(esc_html__('Show All %s', 'text_domain'), $taxonomy_name) . '</option>';
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
            wp_enqueue_script('stereo_contact', plugins_url('/js/forms.js', __FILE__), array(), $this->version, true);
            wp_localize_script('stereo_contact', 'stereo_cf', array('ajax_url' => admin_url('admin-ajax.php')));
        }

        public function info_metabox_content()
        {
            global $post;
            $infos = get_post_meta($post->ID, 'form_data', true);
            include(dirname(__FILE__) . '/templates/infos.php');
        }

        public function post_contact()
        {
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
            $html = "<p><strong>" . apply_filters('st_cf_mailmsg', "Nouveau formulaire reçu, voici l'information") . " : </strong></p><p>" . $out . "</p>";
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

            wp_mail($to, $subject, $html, $headers, $files);
            if (count($files)) {
                do_action('st_cf_files',$files,$postid);
            }
            foreach ($files as $f) @unlink($f);
        }
    }

    new ST_ContactForm();
}

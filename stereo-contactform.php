<?php
/*
 * Plugin Name: Stereo contact form
 * Description: Formulaires de contacts
 * Author: Stereo
 * Author URI: https://www.stereo.ca/
 * Version: 1.0.0
 * License:     0BSD
 *
 * Copyright (c) 2018 Stereo
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! class_exists( 'ST_ContactForm' ) ) {
    class ST_ContactForm {
        var $version = "1.0.0";

        public function __construct() {
            // ADD actions
            add_action('wp_ajax_st_post_contact', [$this, 'post_contact']);
            add_action('wp_ajax_nopriv_st_post_contact', [$this, 'post_contact']);
            add_action('init', [$this, 'register_cpt']);
            add_action('wp_enqueue_scripts', [$this, 'register_js']);
            add_action('add_meta_boxes', [$this, 'info_metabox']);
        }

        public function register_cpt() {
            register_post_type( 'st_contactform',
              array(
                'labels' => array(
                  'name' =>  'Contacts' ,
                  'singular_name' =>  'Contact'
                ),
                'show_ui' => true,
        		'rewrite' => false,
                'supports' => array('title')
              )
            );
        }

        public function info_metabox() {
            add_meta_box('st_cf_infos', 'Informations du formulaire', [$this, 'info_metabox_content'], 'st_contactform', 'normal', 'high');
        }

        public function register_js() {
            wp_enqueue_script( 'stereo_contact', plugins_url( '/js/forms.js', __FILE__ ), array('jquery'), $this->version, true );
            wp_localize_script( 'stereo_contact', 'stereo_cf', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
        }

        public function info_metabox_content() {
            global $post;
            $infos = get_post_meta($post->ID,'form_data',true);
            include(dirname(__FILE__).'/templates/infos.php');
        }

        public function post_contact() {
            if (!$_POST['_nobot']) {
                header('HTTP/1.0 403 Forbidden');
                die('No bots please!');
            }
            $forminfo = [];
            $titlefield = explode(',',stripslashes($_POST['_title_field']));
            $titlefield = array_map('trim',$titlefield);

            foreach($_POST as $k => $v) {
                if ($k == 'action' || substr($k,0,1) == '_') continue;
                $forminfo[str_replace('_',' ',$k)] = is_array($v) ? implode(', ', array_map('stripslashes',$v)) : stripslashes($v);
            }

            foreach($titlefield as $field) {
                $title[] = $forminfo[$field];
            }

            $title = implode(' ',$title);
            $postinfo = array('post_status' => 'publish', 'post_type' => 'st_contactform', 'post_title'=> current_time('Y-m-d H:i:s').' - '.$title);
            $id = wp_insert_post($postinfo);
            add_post_meta($id,'form_data',$forminfo);

            $this->send_email($forminfo,$_POST['_subject'],$id);
            wp_send_json(['success' => true]);
            die();
        }

        public function send_email($forminfo,$subject,$postid) {

            $out = [];
            foreach($forminfo as $fieldname => $value) {
                $out[] = $fieldname . ": ".$value;
            }
            $html = "<p><strong>Nouveau formulaire reçu, voici l'information : </strong></p><p>" . implode('<br>',$out) . "</p>";
            $html = apply_filters('st_cf_mail_content',$html);
            $to = apply_filters('st_cf_mail_to',get_option('admin_email'));
            $from = apply_filters('st_cf_mail_from',get_option('blogname').' <'.get_option('admin_email').'>');
            $subject = apply_filters('st_cf_mail_subject',$subject);
            $headers = ['From: '.$from,'Content-Type: text/html; charset=UTF-8','Content-Type: text/html; charset=UTF-8'];
            if (isset($forminfo['Courriel']) && is_email($forminfo['Courriel'])) $headers[] = 'ReplyTo: '.$forminfo['Courriel'];
            $headers = apply_filters('st_cf_mail_headers',$headers);
            wp_mail( $to, $subject, $html, $headers );
        }
    }

    new ST_ContactForm();
}

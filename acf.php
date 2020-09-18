<?php
add_action('init',function() {
	if( function_exists('acf_add_local_field_group') ):

		acf_add_options_sub_page(array(
			'page_title'     => __('Configuration', 'stereo-contactform'),
			'menu_title'    => __('Configuration', 'stereo-contactform'),
			'menu_slug'    => 'stereocf-page-content',
			'parent_slug'    => 'edit.php?post_type=st_contactform',
		));

		acf_add_local_field_group(array(
			'key' => 'group_5e8337e85e2d1',
			'title' => __('Destinataire des formulaires','stereo-contactform'),
			'fields' => array(
				array(
					'key' => 'field_5e8337fe8d7b9',
					'label' => __('Nom du destinataire','stereo-contactform'),
					'name' => 'stereo_dest_name',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5e83380e8d7ba',
					'label' => __('Courriel du destinataire','stereo-contactform'),
					'name' => 'stereo_dest_mail',
					'type' => 'email',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
				array(
					'key' => 'field_6e8337fe8d7b9',
					'label' => __('Nom de provenance du courriel','stereo-contactform'),
					'name' => 'stereo_from_name',
					'type' => 'text',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_6e83380e8d7ba',
					'label' => __('Courriel du provenance','stereo-contactform'),
					'name' => 'stereo_from_mail',
					'type' => 'email',
					'instructions' => '',
					'required' => 1,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'stereocf-page-content',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'left',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
        ));

        acf_add_local_field_group(array(
			'key' => 'group_5e8337e85e2d2',
			'title' => __('reCAPTCHA v3','stereo-contactform'),
			'fields' => array(
				array(
					'key' => 'field_5e8337fe8d7b8',
					'label' => __('Site key','stereo-contactform'),
					'name' => 'stereo_contact_recaptcha_v3',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
				array(
					'key' => 'field_5e8337fe8d7b5',
					'label' => __('Secret key','stereo-contactform'),
					'name' => 'stereo_contact_recaptcha_secret',
					'type' => 'text',
					'instructions' => '',
					'required' => 0,
					'conditional_logic' => 0,
					'wrapper' => array(
						'width' => '',
						'class' => '',
						'id' => '',
					),
					'default_value' => '',
					'placeholder' => '',
					'prepend' => '',
					'append' => '',
					'maxlength' => '',
				),
			),
			'location' => array(
				array(
					array(
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'stereocf-page-content',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'left',
			'instruction_placement' => 'label',
			'hide_on_screen' => '',
			'active' => true,
			'description' => '',
        ));

        acf_add_local_field_group(array(
            'key' => 'group_5f57a7b5149a8',
            'title' => __('Catégorie Formulaire Stereo','stereo-contactform'),
            'fields' => array(
                array(
                    'key' => 'field_5f57a7d0b19bd',
                    'label' => __('Courriel de destination','stereo-contactform'),
                    'name' => 'stereo_to_email',
                    'type' => 'text',
                    'instructions' => __('Plusieurs destinations? Séparer les courriels par une virgule.','stereo-contactform'),
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'taxonomy',
                        'operator' => '==',
                        'value' => 'st_contactform_categorie',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
            'active' => true,
            'description' => '',
        ));

	endif;
});

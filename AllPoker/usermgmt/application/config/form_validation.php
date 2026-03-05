<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

$config = [
    // @version 2.1
    'built_in_page' => [
        [
            'field' => 'content',
            'label' => 'lang:content',
            'rules' => 'required'
        ],
        [
            'field' => 'meta_description',
            'label' => 'lang:meta_description',
            'rules' => 'max_length[255]'
        ],
        [
            'field' => 'meta_keywords',
            'label' => 'lang:meta_keywords',
            'rules' => 'max_length[255]'
        ]
    ],
    'newsletter_email' => [
        [
            'field' => 'subject',
            'label' => 'lang:subject',
            'rules' => 'required'
        ],
        [
            'field' => 'message',
            'label' => 'lang:message',
            'rules' => 'required'
        ],
        [
            'field' => 'language',
            'label' => 'lang:language',
            'rules' => 'required'
        ]
    ],
    
    // @version 2.0
    'update_ticket_reply' => [
        [
            'field' => 'message',
            'label' => 'lang:message',
            'rules' => 'required'
        ]
    ],
    
    // @version 1.6
    'register_api' => [
        [
            'field' => 'first_name',
            'label' => 'lang:first_name',
            'rules' => 'required|max_length[25]'
        ],
        [
            'field' => 'last_name',
            'label' => 'lang:last_name',
            'rules' => 'required|max_length[25]'
        ],
        [
            'field' => 'email_address',
            'label' => 'lang:email_address',
            'rules' => 'required|valid_email|max_length[255]'
        ],
        [
            'field' => 'password',
            'label' => 'lang:password',
            'rules' => 'required'
        ]
    ],
    
    // @version 1.5
    'send_email_user' => [
        [
            'field' => 'subject',
            'label' => 'lang:subject',
            'rules' => 'required|max_length[255]'
        ],
        [
            'field' => 'message',
            'label' => 'lang:message',
            'rules' => 'required'
        ]
    ],
    
    'just_email_address' => [
        [
            'field' => 'email_address',
            'label' => 'lang:email_address',
            'rules' => 'required|valid_email'
        ]
    ],
    'login' => [
        [
            'field' => 'username',
            'label' => 'lang:username_email_address',
            'rules' => 'trim|required'
        ],
        [
            'field' => 'password',
            'label' => 'lang:password',
            'rules' => 'required'
        ]
    ],
    '2f_authentication' => [
        [
            'field' => 'code',
            'label' => 'lang:code',
            'rules' => 'required|is_natural'
        ]
    ],
    'register' => [
        [
            'field' => 'first_name',
            'label' => 'lang:first_name',
            'rules' => 'required|max_length[25]'
        ],
        [
            'field' => 'last_name',
            'label' => 'lang:last_name',
            'rules' => 'required|max_length[25]'
        ],
        [
            'field' => 'email_address',
            'label' => 'lang:email_address',
            'rules' => 'required|valid_email|max_length[255]'
        ],
        [
            'field' => 'password',
            'label' => 'lang:password',
            'rules' => 'required'
        ],
        [
            'field' => 'retype_password',
            'label' => 'lang:retype_password',
            'rules' => 'required|matches[password]'
        ],
        [
            'field' => 'terms',
            'label' => 'lang:agree_terms_just',
            'rules' => 'required'
        ]
    ],
    'adjust_balance' => [
        [
            'field' => 'amount',
            'label' => 'lang:amount',
            'rules' => 'required|numeric|greater_than_equal_to[0.1]'
        ]
    ],
    'new_user' => [
        [
            'field' => 'first_name',
            'label' => 'lang:first_name',
            'rules' => 'required|max_length[25]'
        ],
        [
            'field' => 'last_name',
            'label' => 'lang:last_name',
            'rules' => 'required|max_length[25]'
        ],
        [
            'field' => 'email_address',
            'label' => 'lang:email_address',
            'rules' => 'required|valid_email|max_length[255]'
        ],
        [
            'field' => 'password',
            'label' => 'lang:password',
            'rules' => 'required'
        ],
        [
            'field' => 'retype_password',
            'label' => 'lang:retype_password',
            'rules' => 'required|matches[password]'
        ],
        [
            'field' => 'role',
            'label' => 'lang:role',
            'rules' => 'required'
        ]
    ],
    'profile_settings' => [
        [
            'field' => 'first_name',
            'label' => 'lang:first_name',
            'rules' => 'required|max_length[25]'
        ],
        [
            'field' => 'last_name',
            'label' => 'lang:last_name',
            'rules' => 'required|max_length[25]'
        ],
        [
            'field' => 'email_address',
            'label' => 'lang:email_address',
            'rules' => 'required|valid_email|max_length[255]'
        ],
        [
            'field' => 'username',
            'label' => 'lang:username',
            'rules' => 'required|alpha_dash|min_length[5]|max_length[50]'
        ],
        [
            'field' => 'state',
            'label' => 'lang:state',
            'rules' => 'max_length[255]'
        ],
        [
            'field' => 'city',
            'label' => 'lang:city',
            'rules' => 'max_length[255]'
        ],
        [
            'field' => 'address_1',
            'label' => 'lang:address_line_1',
            'rules' => 'max_length[255]'
        ],
        [
            'field' => 'phone_number',
            'label' => 'lang:phone_number',
            'rules' => 'max_length[50]'
        ],
        [
            'field' => 'address_2',
            'label' => 'lang:address_line_2',
            'rules' => 'max_length[255]'
        ],
        [
            'field' => 'company',
            'label' => 'lang:company',
            'rules' => 'max_length[255]'
        ],
        [
            'field' => 'zip_code',
            'label' => 'lang:zip_code',
            'rules' => 'max_length[16]'
        ],
        [
            'field' => 'about',
            'label' => 'lang:about',
            'rules' => 'max_length[500]'
        ],
        [
            'field' => 'reason',
            'label' => 'lang:reason',
            'rules' => 'max_length[255]'
        ]
    ],
    'change_password' => [
        [
            'field' => 'password',
            'label' => 'lang:password',
            'rules' => 'required'
        ],
        [
            'field' => 'retype_password',
            'label' => 'lang:retype_password',
            'rules' => 'required|matches[password]'
        ]
    ],
    'change_password_whole' => [
        [
            'field' => 'current_password',
            'label' => 'lang:current_password',
            'rules' => 'required'
        ],
        [
            'field' => 'password',
            'label' => 'lang:password',
            'rules' => 'required'
        ],
        [
            'field' => 'retype_password',
            'label' => 'lang:retype_password',
            'rules' => 'required|matches[password]'
        ]
    ],
    'contact_us' => [
        [
            'field' => 'full_name',
            'label' => 'lang:full_name',
            'rules' => 'required'
        ],
        [
            'field' => 'email_address',
            'label' => 'lang:email_address',
            'rules' => 'required|valid_email'
        ],
        [
            'field' => 'message',
            'label' => 'lang:message',
            'rules' => 'required'
        ]
    ],
    'contact_message_reply' => [
        [
            'field' => 'reply_message',
            'label' => 'lang:message',
            'rules' => 'required'
        ]
    ],
    'create_ticket' => [
        [
            'field' => 'subject',
            'label' => 'lang:subject',
            'rules' => 'required|max_length[90]'
        ],
        [
            'field' => 'priority',
            'label' => 'lang:priority',
            'rules' => 'required'
        ],
        [
            'field' => 'category',
            'label' => 'lang:category',
            'rules' => 'required'
        ],
        [
            'field' => 'message',
            'label' => 'lang:message',
            'rules' => 'required'
        ]
    ],
    'payment_item' => [
        [
            'field' => 'name',
            'label' => 'lang:name',
            'rules' => 'required|max_length[90]'
        ],
        [
            'field' => 'price',
            'label' => 'lang:price',
            'rules' => 'required|numeric|greater_than_equal_to[0.1]'
        ],
        [
            'field' => 'days',
            'label' => 'lang:days',
            'rules' => 'is_natural'
        ],
        [
            'field' => 'description',
            'label' => 'lang:description',
            'rules' => 'required|max_length[255]'
        ]
    ],
    'tickets_category' => [
        [
            'field' => 'category',
            'label' => 'lang:category',
            'rules' => 'required|max_length[50]'
        ]
    ],
    'announcement' => [
        [
            'field' => 'subject',
            'label' => 'lang:subject',
            'rules' => 'required|max_length[90]'
        ],
        [
            'field' => 'announcement',
            'label' => 'lang:announcement',
            'rules' => 'required'
        ]
    ],
    'custom_field' => [
        [
            'field' => 'name',
            'label' => 'lang:name',
            'rules' => 'required|max_length[90]'
        ],
        [
            'field' => 'options',
            'label' => 'lang:options',
            'rules' => 'max_length[1500]'
        ],
        [
            'field' => 'guide_text',
            'label' => 'lang:guide_text',
            'rules' => 'max_length[255]'
        ]
    ],
    'email_template' => [
        [
            'field' => 'title',
            'label' => 'lang:title',
            'rules' => 'required|max_length[90]'
        ],
        [
            'field' => 'subject',
            'label' => 'lang:subject',
            'rules' => 'required|max_length[90]'
        ],
        [
            'field' => 'hook',
            'label' => 'lang:hook',
            'rules' => 'required|max_length[50]'
        ],
        [
            'field' => 'template',
            'label' => 'lang:template',
            'rules' => 'required'
        ]
    ],
    'block_ip_address' => [
        [
            'field' => 'ip_address',
            'label' => 'lang:ip_address',
            'rules' => 'required|valid_ip|max_length[45]|is_unique[blocked_ip_addresses.ip_address]'
        ],
        [
            'field' => 'reason',
            'label' => 'lang:reason',
            'rules' => 'max_length[500]'
        ]
    ],
    'add_reply' => [
        [
            'field' => 'reply',
            'label' => 'lang:your_reply',
            'rules' => 'required'
        ]
    ],
    'user_invite' => [
        [
            'field' => 'email_address',
            'label' => 'lang:email_address',
            'rules' => 'required|valid_email'
        ],
        [
            'field' => 'expires_in',
            'label' => 'lang:expires_in_hrs',
            'rules' => 'required|is_natural'
        ]
    ],
    'page' => [
        [
            'field' => 'name',
            'label' => 'lang:name',
            'rules' => 'required|max_length[30]'
        ],
        [
            'field' => 'content',
            'label' => 'lang:content',
            'rules' => 'required'
        ],
        [
            'field' => 'meta_description',
            'label' => 'lang:meta_description',
            'rules' => 'max_length[255]'
        ],
        [
            'field' => 'meta_keywords',
            'label' => 'lang:meta_keywords',
            'rules' => 'max_length[255]'
        ]
    ],
    'settings_general' => [
        [
            'field' => 'site_name',
            'label' => 'lang:site_name',
            'rules' => 'required'
        ],
        [
            'field' => 'site_tagline',
            'label' => 'lang:site_tagline',
            'rules' => 'required'
        ],
        [
            'field' => 'dashboard_cache_time',
            'label' => 'lang:dashboard_cache_time',
            'rules' => 'is_natural'
        ]
    ],
    'settings_support' => [
        [
            'field' => 'cu_email_address',
            'label' => 'lang:email_address',
            'rules' => 'valid_email'
        ]
    ],
    'settings_role_permission' => [
        [
            'field' => 'name',
            'label' => 'lang:name',
            'rules' => 'required|max_length[50]'
        ],
        [
            'field' => 'access_key',
            'label' => 'lang:access_key',
            'rules' => 'required|max_length[50]'
        ]
    ],
    'settings_email_smtp' => [
        [
            'field' => 'e_sender',
            'label' => 'lang:from_address',
            'rules' => 'required|valid_email'
        ],
        [
            'field' => 'e_sender_name',
            'label' => 'lang:from_name',
            'rules' => 'required'
        ],
        [
            'field' => 'e_host',
            'label' => 'lang:host',
            'rules' => 'required'
        ],
        [
            'field' => 'e_username',
            'label' => 'lang:username',
            'rules' => 'required'
        ],
        [
            'field' => 'e_password',
            'label' => 'lang:password',
            'rules' => 'required'
        ],
        [
            'field' => 'e_port',
            'label' => 'lang:port',
            'rules' => 'required|is_natural'
        ]
    ],
    'settings_email_mail' => [
        [
            'field' => 'e_sender',
            'label' => 'lang:from_address',
            'rules' => 'required|valid_email'
        ],
        [
            'field' => 'e_sender_name',
            'label' => 'lang:from_name',
            'rules' => 'required'
        ]
    ]
];

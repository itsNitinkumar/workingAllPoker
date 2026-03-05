<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

$new_announcements = $this->Tool_model->check_for_new_announcements();
$announcements = get_limited_announcements( 3 );

?>
<!DOCTYPE html>
<html lang="<?php echo lang( 'lang_iso_code' ); ?>">

<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<!-- Tell the browser to be responsive to screen width: -->
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?php echo html_escape( manage_title( $page_title ) ); ?></title>

<!-- Favicon: -->
<link rel="icon" href="<?php echo general_uploads( html_escape( db_config( 'site_favicon' ) ) ); ?>">

<!-- Font Awesome: -->
<link rel="stylesheet" href="<?php assets_path( 'vendor/fontawesome-free/css/all.min.css' ); ?>">

<!-- Overlay Scrollbars: -->
<link rel="stylesheet" href="<?php admin_lte_asset( 'plugins/overlayScrollbars/css/OverlayScrollbars.min.css' ); ?>">

<!-- iCheck Bootstrap: -->
<link rel="stylesheet" href="<?php admin_lte_asset( 'plugins/icheck-bootstrap/icheck-bootstrap.min.css' ); ?>">

<!-- Select 2: -->
<link rel="stylesheet" href="<?php admin_lte_asset( 'plugins/select2/css/select2.min.css' ); ?>">

<!-- Summernote: -->
<link rel="stylesheet" href="<?php admin_lte_asset( 'plugins/summernote/summernote-bs4.css' ); ?>">

<!-- jQuery Upload Preview: -->
<link rel="stylesheet" href="<?php assets_path( 'panel/vendor/jquery_upload_preview/css/jquery.uploadPreview.css' ); ?>">

<!-- Theme Style: -->
<link rel="stylesheet" href="<?php admin_lte_asset( 'css/adminlte.min.css' ); ?>">

<!-- Google Font: Source Sans Pro -->
<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

<!-- Pace: -->
<link rel="stylesheet" href="<?php assets_path( 'vendor/pace/pace.css' ); ?>">

<!-- Custom Styles: -->
<link rel="stylesheet" href="<?php assets_path( 'vendor/loading_io/icon.css' ); ?>">
<link rel="stylesheet" href="<?php assets_path( 'css/custom.css?v=' . v_combine() ); ?>">

<!-- jQuery: -->
<script src="<?php assets_path( 'vendor/jquery/jquery.min.js' ); ?>"></script>

<!-- Dynamic Variables: -->
<script>
  const csrfToken = '<?php echo $this->security->get_csrf_hash(); ?>';
  const msgSeemsDeletedMoved = "<?php echo lang( 'seems_moved_deleted' ); ?>";
  const processing = "<?php echo lang( 'processing' ); ?>";
  const changeFile = "<?php echo lang( 'change_file' ); ?>";
  const chooseFile = "<?php echo lang( 'choose_file' ); ?>";
  const googleAnalyticsID = '<?php echo html_escape( db_config( "google_analytics_id" ) ); ?>';
  const stripePublishableKey = '<?php echo html_escape( db_config( "sp_publishable_key" ) ); ?>';
  const eProtocol = '<?php echo html_escape( db_config( "e_protocol" ) ); ?>';
  const baseURL = '<?php echo base_url(); ?>';
  
  // @version 1.8
  const sidebarCookie = '<?php echo SIDEBAR_COOKIE; ?>';
  
  // @version 1.9
  const snImageUpload = '<?php echo env_url(); ?>actions/admin/tools/sn_image_upload';
  
  // @version 2.1
  const errors = {
    'wentWrong': "<?php echo err_lang( 'went_wrong' ); ?>",
    401: "<?php echo err_lang( '401' ); ?>",
    403: "<?php echo err_lang( '403' ); ?>",
    404: "<?php echo err_lang( '404' ); ?>",
    500: "<?php echo err_lang( '500' ); ?>",
    502: "<?php echo err_lang( '502' ); ?>",
    503: "<?php echo err_lang( '503' ); ?>"
  };
</script>

</head>

<body class="hold-transition <?php echo get_body_classes(); ?>">

<?php if ( ! is_public_page() ) { ?>

<div class="wrapper">

  <!-- Navbar: -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light text-sm">
    <!-- Left Navbar Links: -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link sidebar-toggle" data-widget="pushmenu" href="#" role="button">
          <i class="fas fa-bars"></i>
        </a>
      </li>
      <li class="nav-item">
        <a href="<?php echo base_url(); ?>" class="btn btn-dark">
          <i class="fas fa-home mr-2 nav-home-icon"></i>
          <span class="d-none d-sm-inline-block"><?php echo lang( 'main_website' ); ?></span>
        </a>
      </li>
    </ul>
    
    <!-- Right Navbar Links: -->
    <ul class="navbar-nav ml-auto">
      <?php if ( get_session( 'impersonating' ) ) { ?>
        <li class="nav-item">
          <a class="nav-link text-danger border-right-1" href="<?php admin_action( 'users/deimpersonate' ); ?>">
            <span class="d-none d-sm-block"><?php echo lang( 'stop_impersonating' ); ?></span>
            <i class="fas fa-stop-circle d-block d-sm-none mt-1" data-toggle="tooltip" data-placement="left" title="<?php echo lang( 'stop_impersonating' ); ?>"></i>
          </a>
        </li>
      <?php } ?>
      <li class="nav-item dropdown">
        <a class="nav-link border-right-1 announcements-opener <?php echo ( $new_announcements ) ? 'a-read' : ''; ?>" data-toggle="dropdown" href="#" aria-expanded="false" data-action="<?php user_action( 'tools/read_announcements' ); ?>">
          
          <?php if ( user_panel_activate_sub_child_page( 'announcements' ) || user_panel_activate_sub_child_page( 'announcement' ) ) { ?>
            <i class="fas fa-bell"></i>
          <?php } else { ?>
            <i class="far fa-bell"></i>
          <?php } ?>
          
          <?php if ( $new_announcements ) { ?>
            <span class="navbar-badge badge-announcements bg-danger"></span>
          <?php } ?>
        </a>
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right dropdown-announcements">
          <span class="dropdown-item dropdown-header"><?php echo lang( 'announcements' ); ?></span>
          <div class="dropdown-divider"></div>
          <?php
          if ( ! empty( $announcements ) ) {
            foreach ( $announcements as $announcement ) { ?>
              <a href="<?php echo html_escape( env_url( 'user/tools/announcement/' . $announcement->id ) ); ?>" class="dropdown-item">
                <?php echo html_escape( $announcement->subject ); ?>
                <span class="text-muted d-block mt-1">
                  <i class="far fa-clock"></i> <?php echo get_date_time_by_timezone( html_escape( $announcement->created_at ), true ); ?>
                </span>
              </a>
              <div class="dropdown-divider"></div>
            <?php } ?>
          
          <a href="<?php echo env_url( 'user/tools/announcements' ); ?>" class="dropdown-item dropdown-footer"><?php echo lang( 'see_all' ); ?></a>
          <?php } else { ?>
            <span class="dropdown-item text-center"><?php echo lang( 'no_announcements' ); ?></span>
          <?php } ?>
        </div>
        <!-- /.dropdown-menu -->
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link pr-0" data-toggle="dropdown" href="#">
          <?php echo get_language_label( get_language() ); ?> <i class="fas fa-angle-down"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <?php foreach ( AVAILABLE_LANGUAGES as $key => $value ) { ?>
            
            <?php if ( $key !== get_language() ) { ?>
              <a href="<?php echo env_url(); ?>language/switch/<?php echo html_escape( $key ); ?>" class="dropdown-item">
                <?php echo html_escape( $value['display_label'] ); ?>
              </a>
            <?php } else { ?>
              <span class="dropdown-item">
                <?php echo html_escape( $value['display_label'] ); ?>
                
                <span class="float-right text-primary text-sm">
                  <i class="fas fa-check-circle"></i>
                </span>
              </span>
            <?php } ?>
            
          <?php } ?>
        </div>
        <!-- /.dropdown-menu -->
      </li>
      <!-- Account Dropdown Menu: -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <?php echo lang( 'account' ); ?> <i class="fas fa-angle-down"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right">
          <a href="<?php echo env_url( 'user/account/profile_settings' ); ?>" class="dropdown-item <?php echo user_panel_activate_sub_child_page( 'profile_settings' ); ?>">
            <?php echo lang( 'profile_settings' ); ?>
          </a>
          <a href="<?php echo env_url( 'user/account/change_password' ); ?>" class="dropdown-item <?php echo user_panel_activate_sub_child_page( 'change_password' ); ?>">
            <?php echo lang( 'change_password' ); ?>
          </a>
          <a href="<?php echo env_url( 'user/account/social_links' ); ?>" class="dropdown-item <?php echo user_panel_activate_sub_child_page( 'social_links' ); ?>">
            <?php echo lang( 'social_links' ); ?>
          </a>
          <div class="dropdown-divider"></div>
          <a href="<?php echo env_url( 'logout' ); ?>" class="dropdown-item">
            <?php echo lang( 'logout' ); ?>
          </a>
        </div>
        <!-- /.dropdown-menu -->
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->
  
  <!-- Main Sidebar Container: -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo/Name: -->
    <a href="<?php echo env_url( 'dashboard' ); ?>" class="brand-link text-sm">
      <img src="<?php echo general_uploads( html_escape( db_config( 'site_favicon' ) ) ); ?>" alt="" class="brand-image favicon img-opacity img-circle elevation-3">
      <span class="brand-text"><?php echo html_escape( db_config( 'site_name' ) ); ?></span>
    </a>
    <!-- Sidebar: -->
    <div class="sidebar os-host-overflow">
      
      <?php if ( db_config( 'maintenance_mode' ) ) { ?>
        <div class="bg-danger text-center p-2 mb-3 text-sm">
          <span><i class="fas fa-check-circle mr-1"></i> <?php echo lang( 'maintenance_mode' ); ?></span>
        </div>
      <?php } ?>
      
      <div class="user-panel mt-3 pb-3 mb-3 d-flex">
        <div class="image position-relative">
          <img src="<?php echo user_picture( html_esc_url( $this->zuser->get( 'picture' ) ) ); ?>" class="img-circle img-opacity elevation-2 profile-pic-sm" alt="User Image">
        </div>
        <!-- /.image -->
        <div class="info text-sm">
          <a href="javascript:void(0)" class="d-block"><?php echo html_escape( $this->zuser->get( 'first_name' ) . ' ' . $this->zuser->get( 'last_name' ) ); ?></a>
        </div>
        <!-- /.info -->
      </div>
      <!-- /.user-panel -->
      
      <!-- Sidebar Menu: -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column nav-legacy text-sm" data-widget="treeview" role="menu" data-accordion="true">
          <li class="nav-item">
            <a href="<?php echo env_url( 'dashboard' ); ?>" class="nav-link <?php echo activate_page( 'dashboard' ); ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p><?php echo lang( 'dashboard' ); ?></p>
            </a>
          </li>
          
          <li class="nav-header"><?php echo lang( 'user_panel' ); ?></li>
          <li class="nav-item">
            <a href="<?php echo env_url( 'user/sessions' ); ?>" class="nav-link <?php echo user_panel_activate_child_page( 'sessions' ); ?>">
              <i class="nav-icon fab fa-firefox-browser"></i>
              <p><?php echo lang( 'sessions' ); ?></p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?php echo env_url( 'user/activities_log' ); ?>" class="nav-link <?php echo user_panel_activate_child_page( 'activities_log' ); ?>">
              <i class="nav-icon fas fa-mouse"></i>
              <p><?php echo lang( 'activities_log' ); ?></p>
            </a>
          </li>
          <li class="nav-item has-treeview <?php echo panel_open_parent_menu( 'payment', 'user' ); ?>">
            <a href="#" class="nav-link <?php echo panel_activate_parent_menu( 'payment', 'user' ); ?>">
              <i class="nav-icon fas fas fa-wallet"></i>
              <p>
                <?php echo lang( 'payment' ); ?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?php echo env_url( 'user/payment/items' ); ?>" class="nav-link <?php echo user_panel_activate_sub_child_page( 'items' ); ?>">
                  <i class="fas fa-store-alt nav-icon"></i>
                  <p><?php echo lang( 'items' ); ?></p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?php echo env_url( 'user/payment/log' ); ?>" class="nav-link <?php echo user_panel_activate_sub_child_page( 'log' ); ?>">
                  <i class="fas fa-clipboard-list nav-icon"></i>
                  <p><?php echo lang( 'log' ); ?></p>
                </a>
              </li>
            </ul>
          </li>
          
          <?php if ( db_config( 'sp_enable_tickets' ) == 1 ) { ?>
            <li class="nav-item">
              <a href="<?php echo env_url( 'user/support/tickets' ); ?>" class="nav-link <?php echo user_panel_activate_sub_child_page( ['tickets', 'ticket', 'create_ticket'] ); ?>">
                <i class="nav-icon fas fa-question-circle"></i>
                <p>
                  <?php echo lang( 'tickets' ); ?>
                  
                  <?php if ( menu_pending( 'user_ticket' ) ) { ?>
                    <span class="right badge badge-danger"><?php echo lang( 'new' ); ?></span>
                  <?php } ?>
                </p>
              </a>
            </li>
          <?php } ?>
          
          <?php if ( is_admin_panel_allowed() ) { ?>
            <li class="nav-header"><?php echo lang( 'admin_panel' ); ?></li>
            
            <?php if ( $this->zuser->has_permission( 'payment' ) ) { ?>
              <li class="nav-item has-treeview <?php echo panel_open_parent_menu( 'payment' ); ?>">
                <a href="#" class="nav-link <?php echo panel_activate_parent_menu( 'payment' ); ?>">
                  <i class="nav-icon fas fa-wallet"></i>
                  <p>
                    <?php echo lang( 'payment' ); ?>
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/payment/items' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'items' ); ?>">
                      <i class="fas fa-store-alt nav-icon"></i>
                      <p><?php echo lang( 'items' ); ?></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/payment/log' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'log' ); ?>">
                      <i class="fas fa-clipboard-list nav-icon"></i>
                      <p><?php echo lang( 'log' ); ?></p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php } ?>
            
            <?php if ( $this->zuser->has_permission( 'support' ) ) {
              $admin_pending_ticket_count = menu_pending( 'admin_ticket', true );
              $contact_messages_count = menu_pending( 'contact_message', true ); ?>
              <li class="nav-item has-treeview <?php echo panel_open_parent_menu( 'support' ); ?>">
                <a href="#" class="nav-link <?php echo panel_activate_parent_menu( 'support' ); ?>">
                  <i class="nav-icon fas fa-headset"></i>
                  <p>
                    <?php echo lang( 'support' ); ?>
                    <i class="right fas fa-angle-left"></i>
                    
                    <?php if ( $admin_pending_ticket_count > 0 || $contact_messages_count > 0 ) { ?>
                      <span class="right badge badge-danger"><?php echo lang( 'new' ); ?></span>
                    <?php } ?>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/support/contact_messages/not_replied' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'contact_messages' ); ?>">
                      <i class="fas fa-envelope nav-icon"></i>
                      <p>
                        <?php echo lang( 'contact_messages' ); ?>
                        
                        <?php if ( $contact_messages_count > 0 ) { ?>
                          <span class="right badge badge-danger"><?php echo html_escape( $contact_messages_count ); ?></span>
                        <?php } ?>
                      </p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/support/categories' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'categories' ); ?>">
                      <i class="fas fa-tags nav-icon"></i>
                      <p><?php echo lang( 'categories' ); ?></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/support/tickets' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( ['tickets', 'ticket'] ); ?>">
                      <i class="fas fa-list-ul nav-icon"></i>
                      <p>
                        <?php echo lang( 'tickets' ); ?>
                        
                        <?php if ( $admin_pending_ticket_count > 0 ) { ?>
                          <span class="right badge badge-danger"><?php echo html_escape( $admin_pending_ticket_count ); ?></span>
                        <?php } ?>
                      </p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php } ?>
            
            <?php if ( $this->zuser->has_permission( 'tools' ) ) { ?>
              <li class="nav-item has-treeview <?php echo panel_open_parent_menu( 'tools' ); ?>">
                <a href="#" class="nav-link <?php echo panel_activate_parent_menu( 'tools' ); ?>">
                  <i class="nav-icon fas fa-tools"></i>
                  <p>
                    <?php echo lang( 'tools' ); ?>
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/tools/announcements' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'announcements' ); ?>">
                      <i class="fas fa-scroll nav-icon"></i>
                      <p><?php echo lang( 'announcements' ); ?></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/tools/custom_fields' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'custom_fields' ); ?>">
                      <i class="fab fa-wpforms nav-icon"></i>
                      <p><?php echo lang( 'custom_fields' ); ?></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/tools/email_templates' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'email_templates' ); ?>">
                      <i class="fas fa-envelope-open-text nav-icon"></i>
                      <p><?php echo lang( 'email_templates' ); ?></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/tools/ip_manager' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'ip_manager' ); ?>">
                      <i class="fas fa-user-shield nav-icon"></i>
                      <p><?php echo lang( 'ip_manager' ); ?></p>
                    </a>
                  </li>
                  
                  <?php if ( $this->zuser->has_permission( 'users' ) ) { ?>
                    <li class="nav-item">
                      <a href="<?php echo env_url( 'admin/tools/activities_log' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'activities_log', 'admin', 'tools' ); ?>">
                        <i class="fas fa-mouse nav-icon"></i>
                        <p><?php echo lang( 'activities_log' ); ?></p>
                      </a>
                    </li>
                  <?php } ?>
                  
                  <?php if ( $this->zuser->has_permission( 'backup' ) ) { ?>
                    <li class="nav-item">
                      <a href="<?php echo env_url( 'admin/tools/backup' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'backup' ); ?>">
                        <i class="fas fa-database nav-icon"></i>
                        <p><?php echo lang( 'backup' ); ?></p>
                      </a>
                    </li>
                  <?php } ?>
                  
                  <?php if ( $this->zuser->has_permission( 'users' ) ) { ?>
                    <li class="nav-item">
                      <a href="<?php echo env_url( 'admin/tools/sessions' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'sessions', 'admin', 'tools' ); ?>">
                        <i class="nav-icon fab fa-firefox-browser"></i>
                        <p><?php echo lang( 'sessions' ); ?></p>
                      </a>
                    </li>
                  <?php } ?>
                  
                </ul>
              </li>
            <?php } ?>
            
            <?php if ( $this->zuser->has_permission( 'subscribers' ) ) { ?>
              <li class="nav-item">
                <a href="<?php echo env_url( 'admin/subscribers' ); ?>" class="nav-link <?php echo panel_activate_child_page( 'subscribers' ); ?>">
                  <i class="nav-icon fas fa-mail-bulk"></i>
                  <p><?php echo lang( 'subscribers' ); ?></p>
                </a>
              </li>
            <?php } ?>
            
            <?php if ( $this->zuser->has_permission( 'users' ) ) { ?>
              <li class="nav-item has-treeview <?php echo panel_open_parent_menu( 'users' ); ?>">
                <a href="#" class="nav-link <?php echo panel_activate_parent_menu( 'users' ); ?>">
                  <i class="nav-icon fas fa-users"></i>
                  <p>
                    <?php echo lang( 'users' ); ?>
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/users/new_user' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'new_user' ); ?>">
                      <i class="fas fa-user-plus nav-icon"></i>
                      <p><?php echo lang( 'new_user' ); ?></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/users/invites' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'invites' ); ?>">
                      <i class="fas fa-comment-alt nav-icon"></i>
                      <p><?php echo lang( 'invites' ); ?></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/users/manage' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( ['manage', 'edit_user', 'adjust_balance', 'sessions', 'activities_log', 'sent_emails'], 'admin', 'users' ); ?>">
                      <i class="fas fa-list-ul nav-icon"></i>
                      <p><?php echo lang( 'manage' ); ?></p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php } ?>
            
            <?php if ( $this->zuser->has_permission( 'pages' ) ) { ?>
              <li class="nav-item">
                <a href="<?php echo env_url( 'admin/pages' ); ?>" class="nav-link <?php echo panel_activate_child_page( 'pages' ); ?>">
                  <i class="nav-icon fas fa-file"></i>
                  <p><?php echo lang( 'pages' ); ?></p>
                </a>
              </li>
            <?php } ?>
            
            <?php if ( $this->zuser->has_permission( 'settings' ) ) { ?>
              <li class="nav-item has-treeview <?php echo panel_open_parent_menu( 'settings' ); ?>">
                <a href="#" class="nav-link <?php echo panel_activate_parent_menu( 'settings' ); ?>">
                  <i class="nav-icon fas fa-cogs"></i>
                  <p>
                    <?php echo lang( 'settings' ); ?>
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/settings/general' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'general' ); ?>">
                      <i class="fas fa-sliders-h nav-icon"></i>
                      <p><?php echo lang( 'general' ); ?></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/settings/support' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'support' ); ?>">
                      <i class="fas fa-screwdriver nav-icon"></i>
                      <p><?php echo lang( 'support' ); ?></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/settings/users' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'users' ); ?>">
                      <i class="fas fa-users-cog nav-icon"></i>
                      <p><?php echo lang( 'users' ); ?></p>
                    </a>
                  </li>
                  
                  <?php if ( $this->zuser->has_permission( 'roles_and_permissions' ) ) { ?>
                    <li class="nav-item">
                      <a href="<?php echo env_url( 'admin/settings/roles' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'roles' ); ?>">
                        <i class="fas fa-user-tag nav-icon"></i>
                        <p><?php echo lang( 'roles' ); ?></p>
                      </a>
                    </li>
                    
                    <li class="nav-item">
                      <a href="<?php echo env_url( 'admin/settings/permissions' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'permissions' ); ?>">
                        <i class="fas fa-user-lock nav-icon"></i>
                        <p><?php echo lang( 'permissions' ); ?></p>
                      </a>
                    </li>
                  <?php } ?>
                  
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/settings/payment' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'payment' ); ?>">
                      <i class="fas fa-coins nav-icon"></i>
                      <p><?php echo lang( 'payment' ); ?></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/settings/apis' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'apis' ); ?>">
                      <i class="fas fa-plug nav-icon"></i>
                      <p><?php echo lang( 'apis' ); ?></p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="<?php echo env_url( 'admin/settings/email' ); ?>" class="nav-link <?php echo panel_activate_sub_child_page( 'email' ); ?>">
                      <i class="fas fa-paper-plane nav-icon"></i>
                      <p><?php echo lang( 'email' ); ?></p>
                    </a>
                  </li>
                </ul>
              </li>
            <?php } ?>
          <?php } ?>
          
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>
  
  <!-- Content Wrapper: -->
  <div class="content-wrapper pt-3">
<?php } ?>
<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<html lang="<?php echo lang( 'lang_iso_code' ); ?>">

<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<!-- Tell the browser to be responsive to screen width: -->
<meta name="viewport" content="width=device-width, initial-scale=1">

<meta name="description" content="<?php echo html_escape( $page_meta_description ); ?>">
<meta name="keywords" content="<?php echo html_escape( $page_meta_keywords ); ?>">

<?php if ( empty( $page_title ) ) { ?>
  <title><?php echo html_escape( db_config( 'site_name' ) . ' - ' . db_config( 'site_tagline' ) ); ?></title>
<?php } else { ?>
  <title><?php echo html_escape( manage_title( $page_title ) ); ?></title>
<?php } ?>

<!-- Favicon: -->
<link rel="icon" href="<?php echo general_uploads( html_escape( db_config( 'site_favicon' ) ) ); ?>">

<!-- Font Awesome ( 5.13.0 ): -->
<link rel="stylesheet" href="<?php assets_path( 'vendor/fontawesome-free/css/all.min.css' ); ?>">

<!-- Bootstrap CSS: -->
<link rel="stylesheet" href="<?php assets_path( 'vendor/bootstrap/css/bootstrap.min.css' ); ?>">

<!-- Stylesheets: -->
<link rel="stylesheet" href="<?php assets_path( 'vendor/loading_io/icon.css' ); ?>">
<link rel="stylesheet" href="<?php assets_path( 'css/home.css?v=' . v_combine() ); ?>">

<!-- jQuery: -->
<script src="<?php assets_path( 'vendor/jquery/jquery.min.js' ); ?>"></script>

<!-- Bootstrap JS: -->
<script src="<?php assets_path( 'vendor/bootstrap/js/bootstrap.bundle.min.js' ); ?>"></script>

<!-- Dynamic Variables: -->
<script>
  const csrfToken = '<?php echo $this->security->get_csrf_hash(); ?>';
  const baseURL   = '<?php echo base_url(); ?>';
  
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

<body>

<!-- Navbar: -->
<nav class="navbar fixed-top navbar-expand-lg navbar-light">
  <div class="container">
    <div class="navbar-brand">
      <a href="<?php echo base_url(); ?>">
        <?php if ( ! empty( db_config( 'site_logo' ) ) ) { ?>
          <img class="logo" src="<?php echo general_uploads( html_escape( db_config( 'site_logo' ) ) ); ?>" alt="<?php echo html_escape( db_config( 'site_name' ) ); ?>">
        <?php } else { ?>
          <h3 class="p-0"><?php echo html_escape( db_config( 'site_name' ) ); ?></h3>
        <?php } ?>
      </a>
    </div>
    <!-- /.navbar-brand -->
    <div>
      <?php if ( ! $this->zuser->is_logged_in ) { ?>
        <a class="btn btn-primary mr-1" href="<?php echo env_url( 'login' ); ?>">
          <i class="fas fa-sign-in-alt mr-1"></i>
          <?php echo lang( 'login' ); ?>
        </a>
        <a class="btn btn-dark" href="<?php echo env_url( 'register' ); ?>">
          <i class="fas fa-user-plus mr-1"></i>
          <?php echo lang( 'register' ); ?>
        </a>
      <?php } else { ?>
        <a class="btn btn-primary" href="<?php echo env_url( 'dashboard' ); ?>">
          <i class="fas fa-tachometer-alt mr-1"></i>
          <?php echo lang( 'dashboard' ); ?>
        </a>
      <?php } ?>
    </div>
  </div>
  <!-- /.container -->
</nav>
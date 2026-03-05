<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<!DOCTYPE html>
<html lang="<?php echo lang( 'lang_iso_code' ); ?>">

<head>

<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<!-- Tell the browser to be responsive to screen width: -->
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="<?php echo html_escape( db_config( 'site_description' ) ); ?>">
<meta name="keywords" content="<?php echo html_escape( db_config( 'site_keywords' ) ); ?>">

<title><?php echo lang( 'under_maintenance' ); ?></title>

<!-- Favicon: -->
<link rel="icon" href="<?php echo general_uploads( html_escape( db_config( 'site_favicon' ) ) ); ?>">

</head>

<body>

<h3><?php echo lang( 'under_maintenance' ); ?></h3>
<p><?php echo html_escape( db_config( 'mm_message' ) ); ?></p>

<?php if ( $this->zuser->is_logged_in ) { ?>
  <a href="<?php echo env_url( 'logout' ); ?>"><?php echo lang( 'logout' ); ?></a>
<?php } ?>

</body>

</html>
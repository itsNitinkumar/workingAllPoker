<?php require_once 'includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">

  <!-- Tell the browser to be responsive to screen width: -->
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>ZUM Installer</title>

  <!-- Google Font: -->
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">

  <!-- Favicon: -->
  <link rel="icon" href="<?php echo base_url('images/favicon.png'); ?>">

  <!-- Font Awesome ( 5.13.0 ): -->
  <link rel="stylesheet" href="<?php echo base_url('vendor/fontawesome/css/all.min.css'); ?>">

  <!-- Bootstrap CSS: -->
  <link rel="stylesheet" href="<?php echo base_url('vendor/bootstrap/css/bootstrap.min.css'); ?>">

  <!-- Stylesheet: -->
  <link rel="stylesheet" href="<?php echo base_url('css/style.css?v=' . time()); ?>">
  <link rel="stylesheet" href="<?php echo base_url('vendor/loading_io/icon.css'); ?>">

</head>

<body>

  <!-- Navbar: -->
  <nav class="navbar fixed-top navbar-expand-lg navbar-light">
    <div class="container">
      <div class="navbar-brand">
        <h3 class="pb-0">ZUM Installer</h3>
      </div>
      <!-- /.navbar-brand -->
      <p class="mb-0">
        <i class="far fa-question-circle mr-1"></i>
        If you're having any question, leave a message at <a class="a text-primary" href="mailto:zaiblab@yahoo.com">zaiblab@yahoo.com</a>
      </p>
    </div>
    <!-- /.container -->
  </nav>

  <div class="container main">
    <div class="row">
      <div class="col-lg-3 sidebar-wrapper">
        <div class="sidebar">
          <ul class="nav sidebar">
            <li class="nav-item <?php echo activate_step(''); ?>">
              <i class="fas fa-cog"></i>
              <span>Requirements</span>
            </li>
            <li class="nav-item <?php echo activate_step('permissions'); ?>">
              <i class="fas fa-key"></i>
              <span>Permissions</span>
            </li>
            <li class="nav-item <?php echo activate_step('install'); ?>">
              <i class="fas fa-save"></i>
              <span>Install</span>
            </li>
            <li class="nav-item <?php echo activate_step('finishing'); ?>">
              <i class="fas fa-check-circle"></i>
              <span>Finishing</span>
            </li>
          </ul>
        </div>
        <!-- /.sidebar -->
      </div>
      <!-- /.col -->
      <div class="col-lg-9 content">
        <?php if (activate_step('')) { ?>
          <div class="response-message"></div>
          <div class="table-responsive">
            <table class="table text-nowrap">
              <thead>
                <tr>
                  <th class="th-2">Name</th>
                  <th>Description</th>
                  <th class="th-1 text-right">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>PHP v7.2+</td>
                  <td>Required Minimum 7.2 PHP Version</td>
                  <td class="text-right"><?php echo requirements('php_version'); ?></td>
                </tr>
                <tr>
                  <td>MySQLi</td>
                  <td>Required MySQLi PHP Extension</td>
                  <td class="text-right"><?php echo requirements('mysqli'); ?></td>
                </tr>
                <tr>
                <tr>
                  <td>mod_rewrite </td>
                  <td>Required for URL Rewriting</td>
                  <td class="text-right"><?php echo requirements('mod_rewrite'); ?></td>
                </tr>
                <tr>
                  <td>mbstring</td>
                  <td>Required for UTF-8 Strings</td>
                  <td class="text-right"><?php echo requirements('mbstring'); ?></td>
                </tr>
                <tr>
                  <td>GD Library</td>
                  <td>Required for Image Processing</td>
                  <td class="text-right"><?php echo requirements('gd'); ?></td>
                </tr>
                <tr>
                  <td>cURL</td>
                  <td>Required for APIs</td>
                  <td class="text-right"><?php echo requirements('curl'); ?></td>
                </tr>
                <tr>
                  <td>ZipArchive</td>
                  <td>Required for Backup Taking</td>
                  <td class="text-right"><?php echo requirements('zip'); ?></td>
                </tr>
              </tbody>
            </table>
            <hr>
            <form class="form" action="<?php echo base_url('proceed.php?step=requirements'); ?>" method="post">
              <button type="submit" class="btn btn-primary float-right" <?php echo (verify_requirements()) ? '' : 'disabled' ?>>Next</button>
            </form>
          </div>
          <!-- /.table-responsive -->
        <?php } else if (activate_step('permissions')) { ?>
          <div class="response-message"></div>
          <div class="table-responsive">
            <div class="alert alert-primary mb-4">
              <i class="fas fa-info-circle mr-1"></i> The permissions must be granted as 755 or 777.
            </div>
            <!-- /.alert -->
            <table class="table text-nowrap">
              <thead>
                <tr>
                  <th>File</th>
                  <th class="th-1 text-right">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>/application/config</td>
                  <td class="text-right"><?php echo permission('application/config'); ?></td>
                </tr>
                <tr>
                  <td>/uploads/images</td>
                  <td class="text-right"><?php echo permission('uploads/images'); ?></td>
                </tr>
                <tr>
                  <td>/assets/backups</td>
                  <td class="text-right"><?php echo permission('assets/backups'); ?></td>
                </tr>
              </tbody>
            </table>
            <hr>
            <form class="form" action="<?php echo base_url('proceed.php?step=permissions'); ?>" method="post">
              <button type="submit" class="btn btn-primary float-right" <?php echo (verify_permissions()) ? '' : 'disabled' ?>>Next</button>
            </form>
          </div>
          <!-- /.table-responsive -->
        <?php } else if (activate_step('install')) { ?>
          <div class="response-message"></div>
          <form class="form" action="<?php echo base_url('proceed.php?step=install'); ?>" method="post">
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="database-hostname">Database Hostname <span class="text-danger">*</span></label>
                <input type="text" id="database-hostname" class="form-control" name="database_hostname" value="localhost" required>
              </div>
              <!-- /.form-group -->
              <div class="form-group col-md-6">
                <label for="database-name">Database Name <span class="text-danger">*</span></label>
                <input type="text" id="database-name" class="form-control" name="database_name" required>
              </div>
              <!-- /.form-group -->
            </div>
            <!-- /.form-row -->
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="database-username">Database Username <span class="text-danger">*</span></label>
                <input type="text" id="database-username" class="form-control" name="database_username" required>
              </div>
              <!-- /.form-group -->
              <div class="form-group col-md-6">
                <label for="database-password">Database Password</label>
                <input type="password" id="database-password" class="form-control" name="database_password">
              </div>
              <!-- /.form-group -->
            </div>
            <!-- /.form-row -->
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="user-email">Email Address <span class="text-danger">*</span></label>
                <input type="email" id="user-email" class="form-control" name="user_email" required>
              </div>
              <!-- /.form-group -->
              <div class="form-group col-md-6">
                <label for="user-password">Password <span class="text-danger">*</span></label>
                <input type="password" id="user-password" class="form-control" name="user_password" required>
              </div>
              <!-- /.form-group -->
            </div>
            <!-- /.form-row -->
            <div class="alert alert-secondary mt-3 mb-3">
              <p class="mb-0">
                <i class="fas fa-info-circle mr-1"></i>
                If you face any issues while verifying your purchase code, please feel free to send me a message with your purchase code, I would love to help you.
              </p>
            </div>
            <!-- /.alert -->
            <div class="form-row">
              <div class="col-md-6">
                <label for="purchase-code">
                  Purchase Code </small> <span class="text-danger">*</span>
                  <small><a class="text-primary" href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank">Need Help in Finding?</a></small>
                </label>
                <input type="text" id="purchase-code" class="form-control" name="purchase_code">
              </div>
              <!-- /.form-group -->
            </div>
            <!-- /.form-row -->
            <div class="alert alert-primary mt-4 mb-4">
              <i class="fas fa-info-circle mr-1"></i> The installation process can take some minutes.
            </div>
            <!-- /.alert -->
            <hr>
            <button type="submit" class="btn btn-primary float-right">Next</button>
          </form>
        <?php } else if (activate_step('finishing')) { ?>
          <div class="alert alert-primary mb-4">
            <i class="fas fa-info-circle mr-1"></i> Congratulations, The installation process is successfully done.
          </div>
          <!-- /.alert -->
          <p><strong>Now don't forget to:</strong></p>
          <ul class="ul">
            <li>Revoke the write permissions of the <q>/application/config</q> directory (if granted).</li>
            <li>Delete the <q>/install</q> directory.</li>
          </ul>
          <hr>
          <a class="btn btn-primary text-white float-right" href="<?php echo (! is_mod_rewrite_enabled()) ? install_url('index.php/login') : install_url('login'); ?>">Finish</a>
        <?php } ?>
      </div>
      <!-- /.col -->
    </div>
    <!-- ./row -->
  </div>
  <!-- /.container -->

  <!-- jQuery: -->
  <script src="<?php echo base_url('vendor/jquery/jquery.min.js'); ?>"></script>

  <!-- Script: -->
  <script src="<?php echo base_url('js/script.js?v=' . time()); ?>"></script>

</body>

</html>
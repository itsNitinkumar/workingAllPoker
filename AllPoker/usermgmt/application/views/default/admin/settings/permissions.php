<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-12">
        <div class="not-in-form">
          <div class="response-message"></div>
        </div>
        <!-- /.not-in-form -->
        <div class="card">
          <div class="card-header d-flex align-items-center">
            <h3 class="card-title"><?php echo lang( 'permissions' ); ?></h3>
            <div class="card-tools ml-auto">
              <button class="btn btn-success text-sm" data-toggle="modal" data-target="#add-permission">
                <i class="fas fa-plus-circle mr-2"></i> <?php echo lang( 'add_permission' ); ?>
              </button>
            </div>
            <!-- /.card-tools -->
          </div>
          <!-- /.card-header -->
          <div class="card-body pt-0 pb-0 records-card-body">
            <div class="table-responsive">
              <table class="custom-table z-table table table-striped text-nowrap table-valign-middle mb-0">
                <thead class="records-thead">
                  <tr>
                    <th class="th-1"><?php echo lang( 'id' ); ?></th>
                    <th class="th-2"><?php echo lang( 'name' ); ?></th>
                    <th><?php echo lang( 'access_key' ); ?></th>
                    <th class="text-right"><?php echo lang( 'actions' ); ?></th>
                  </tr>
                </thead>
                <tbody class="records-tbody z-records-asc text-sm">
                  <?php
                  if ( ! empty( $permissions ) )
                  {
                    foreach ( $permissions as $permission ) {
                      $id = $permission->id; ?>
                      <tr id="record-<?php echo html_escape( $id ); ?>">
                        <td><?php echo html_escape( $id ); ?></td>
                        <td><?php echo html_escape( $permission->name ); ?></td>
                        <td><tt><?php echo html_escape( $permission->access_key ); ?></tt></td>
                        <td class="text-right">
                          <div class="btn-group">
                            <button class="btn btn-sm btn-primary get-data-tool" data-source="<?php admin_action( 'settings/edit_permission' ); ?>" data-id="<?php echo html_escape( $id ); ?>">
                              <span class="fas fa-edit get-data-tool-c"></span>
                            </button>
                            
                            <?php if ( $permission->is_built_in == 0 ) { ?>
                              <button class="btn btn-sm btn-danger tool" data-id="<?php echo html_escape( $id ); ?>" data-toggle="modal" data-target="#delete">
                                <i class="fas fa-trash tool-c"></i>
                              </button>
                            <?php } ?>
                            
                          </div>
                          <!-- /.btn-group -->
                        </td>
                      </tr>
                      <?php }
                    } else {
                  ?>
                    <tr id="record-0">
                      <td colspan="4"><?php echo lang( 'no_records_found' ); ?></td>
                    </tr>
                  <?php } ?>
                </tbody>
              </table>
            </div>
            <!-- /.table-responsive -->
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</div>
<!-- /.content -->

<?php load_modals( ['admin/add_permission', 'read', 'delete'] ); ?>
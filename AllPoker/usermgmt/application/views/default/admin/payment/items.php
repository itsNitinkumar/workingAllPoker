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
            <h3 class="card-title"><?php echo lang( 'payment_items' ); ?></h3>
            <div class="card-tools ml-auto">
              <button class="btn btn-success text-sm" data-toggle="modal" data-target="#add-item">
                <i class="fas fa-plus-circle mr-2"></i> <?php echo lang( 'add_item' ); ?>
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
                    <th class="th-2"><?php echo lang( 'type' ); ?></th>
                    <th><?php echo lang( 'price' ); ?></th>
                    <th class="text-right"><?php echo lang( 'days_given' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'sales' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'status' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'updated' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'created' ); ?></th>
                    <th class="text-right th-2"><?php echo lang( 'actions' ); ?></th>
                  </tr>
                </thead>
                <tbody class="records-tbody z-records-asc text-sm">
                  <?php
                  
                  if ( ! empty( $items ) )
                  {
                    foreach ( $items as $item ) {
                      $id = $item->id; ?>
                      <tr id="record-<?php echo html_escape( $id ); ?>">
                        <td><?php echo html_escape( $id ); ?></td>
                        <td><?php echo html_escape( $item->name ); ?></td>
                        <td><?php echo lang( html_escape( $item->type ) ); ?></td>
                        <td><?php echo html_escape( $item->price . ' ' . $item->code ); ?></td>
                        <td class="text-right"><?php echo html_escape( $item->days ); ?></td>
                        <td class="text-right"><?php echo html_escape( $item->sales ); ?></td>
                        <td class="text-right">
                          <?php if ( $item->status == 0 ) { ?>
                            <span class="badge badge-danger"><?php echo lang( 'deactive' ); ?></span>
                          <?php } else { ?>
                            <span class="badge badge-success"><?php echo lang( 'active' ); ?></span>
                          <?php } ?>
                        </td>
                        <td class="text-right"><?php manage_updated_at( html_escape( $item->updated_at ) ); ?></td>
                        <td class="text-right"><?php echo get_date_time_by_timezone( html_escape( $item->created_at ) ); ?></td>
                        <td class="text-right">
                          <div class="btn-group">
                            <button class="btn btn-sm btn-primary get-data-tool" data-source="<?php admin_action( 'payment/edit_item' ); ?>" data-id="<?php echo html_escape( $id ); ?>">
                              <span class="fas fa-edit get-data-tool-c"></span>
                            </button>
                            <button class="btn btn-sm btn-danger tool" data-id="<?php echo html_escape( $id ); ?>" data-toggle="modal" data-target="#delete">
                              <i class="fas fa-trash tool-c"></i>
                            </button>
                          </div>
                          <!-- /.btn-group -->
                        </td>
                      </tr>
                      <?php }
                    } else {
                  ?>
                    <tr id="record-0">
                      <td colspan="10"><?php echo lang( 'no_records_found' ); ?></td>
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

<?php load_modals( ['admin/add_payment_item', 'read', 'delete'] ); ?>
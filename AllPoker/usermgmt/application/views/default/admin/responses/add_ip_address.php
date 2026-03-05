<?php defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' ); ?>
<tr id="record-<?php echo html_escape( $id ); ?>">
  <td><?php echo html_escape( $id ); ?></td>
  <td>
    <?php if ( db_config( 'ipinfo_token' ) !== '' ) { ?>
      <span class="mr-1 badge badge-success get-data-tool" data-source="<?php admin_action( 'tools/ip_geolocation' ); ?>" data-id="<?php echo html_escape( $ip_address ); ?>">
        <?php echo lang( 'geolocation_data' ); ?>
      </span>
    <?php } ?>
    
    <?php echo html_escape( $ip_address ); ?>
  </td>
  <td>
    <?php if ( ! empty( $reason ) ) { ?>
       <?php echo html_escape( short_text( $reason ) ); ?>
       
       <?php if ( is_increased_short_text( $reason ) ) { ?>
        <span class="badge badge-success get-data-tool" data-source="<?php admin_action( 'tools/ip_blocking_reason' ); ?>" data-id="<?php echo html_escape( $id ); ?>">
          <?php echo lang( 'read_more' ); ?>
        </span>
      <?php } ?>
    <?php }
    else
    {
        echo lang( 'not_mentioned' );
    }
    ?>
  </td>
  <td class="text-right"><?php echo get_date_time_by_timezone( html_escape( $blocked_at ) ); ?></td>
  <td class="text-right">
    <button class="btn btn-sm btn-danger tool" data-id="<?php echo html_escape( $id ); ?>" data-toggle="modal" data-target="#delete">
      <i class="fas fa-trash tool-c"></i>
    </button>
  </td>
</tr>
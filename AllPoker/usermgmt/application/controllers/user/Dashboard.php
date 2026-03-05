<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Dashboard Controller
 *
 * @author Shahzaib
 */
class Dashboard extends MY_Controller {
    
    /**
     * Dashboard Page
     *
     * @return void
     */
    public function index()
    {
        if ( ! $this->zuser->is_logged_in ) env_redirect( 'login' );
        
        $this->area = 'user';
        
        if ( $this->zuser->has_permission( 'users' ) )
        {
            $this->load->model( 'Dashboard_model' );
            $this->load->model( 'User_model' );
            
            $data['data']['users'] = $this->User_model->limited_users( 7 );
            $cache_time = db_config( 'dashboard_cache_time' );
            $recent_users_stats = [];
            $stats = [];
            
            if ( db_config( 'dc_last_updated' ) < ( time() - $cache_time ) )
            {
                $stats['social_users'] = $this->User_model->get_social_count();
                $stats['new_within_24hrs'] = $this->User_model->get_of_new_count();
                $stats['total_users'] = $this->User_model->get_of_total_count();
                $stats['online_today'] = $this->User_model->get_online_today_count();
                
                for ( $i = 6; $i >= 0; $i-- )
                {
                    $month = date( 'n' ) - $i;
                    $year = date( 'Y' );
                    
                    if ( $month < 1 )
                    {
                        $month = $month + 12;
                        $year = $year - 1;
                    }
                    
                    $time = mktime( 0, 0, 0, $month, 1, $year );
                    $recent_users_count = $this->User_model->get_count_by_month_year( "{$month}-{$year}" );
                    $month_name = get_cf_date_by_user_timezone( 'F', $time );
                    $recent_users_stats[$month_name] = $recent_users_count;
                }
                
                $stats['recent_users_stats'] = json_encode( $recent_users_stats );
                
                $this->Setting_model->update_options( ['dc_last_updated' => time()] );
                $this->Dashboard_model->update_options( $stats );
            }
            
            $data['data']['dashboard'] = $this->Dashboard_model->get_managed_options();
            
            $data['data']['scripts'] = [
                admin_lte_asset( 'plugins/chart.js/Chart.min.js', true ),
                get_assets_path( 'panel/js/chartjs_script.js' )
            ];
        }
        
        $data['title'] = lang( 'dashboard' );
        $data['view'] = 'dashboard';
        
        $this->load_panel_template( $data, false );
    }
}

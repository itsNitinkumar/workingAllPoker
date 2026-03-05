<?php

defined( 'BASEPATH' ) OR exit( 'No direct script access allowed' );

/**
 * Payment Controller ( User )
 *
 * @author Shahzaib
 */
class Payment extends MY_Controller {
    
    /**
     * Logged in User ID
     *
     * @var integer
     */
    private $user_id;
    
    
    /**
     * Class Constructor
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        
        if ( ! $this->zuser->is_logged_in )
        {
            env_redirect( 'login' );
        }
        
        $this->sub_area = 'payment';
        $this->user_id = $this->zuser->get( 'id' );
        $this->area = 'user';
        
        $this->load->model( 'Payment_model' );
    }
    
    /**
     * Payment Items Page
     *
     * @return void
     */
    public function items()
    {
        $this->set_user_reference( 'payment' );
        
        $data['data']['items'] = $this->Payment_model->items( 1 );
        $data['title'] = lang( 'items' );
        
        if ( is_stripe_togo() )
        {
            $data['data']['scripts'] = [
                'https://js.stripe.com/v3/',
                get_assets_path( 'js/stripe_script.js?v=' . v_combine() )
            ];
        }
        
        $data['view'] = 'items';
        
        $this->load_panel_template( $data );
    }
    
    /**
     * Invoice PDF
     *
     * @param  string $hash
     * @return void
     */
    public function invoice( $hash = '' )
    {
        if ( empty( $hash ) ) env_redirect( 'user/payment/log' );
        
        $hash = do_secure( $hash );
        $data = $this->Payment_model->payment_log( $this->user_id, $hash );
        
        if ( empty( $data ) ) env_redirect( 'user/payment/log' );
        
        $css_file = FCPATH . 'assets/' . get_theme_name() . 'css/invoice.css';
        $css = '<style>' . file_get_contents( $css_file ) . '</style>';
        $buyer = $this->zuser->get( 'first_name' ) . ' ';
        $buyer .= $this->zuser->get( 'last_name' );
        
        if ( ! empty( $buyer_company = $this->zuser->get( 'company' ) ) )
        {
            $data->issued_to = $buyer_company;
        }
        else
        {
            $data->issued_to = $buyer;
        }
        
        $html = read_view( 'user/payment/invoice', $data );
        
        $this->load->library( 'Zpdf' );
        $this->zpdf->loadHtml( $css . $html );
        $this->zpdf->setPaper( 'A4' );
        $this->zpdf->render();
        
        $this->zpdf->stream( $hash . '.pdf', ['Attachment' => 0] );
    }
    
    /**
     * Payments Log Page
     *
     * @return void
     */
    public function log()
    {
        $this->load->library( 'pagination' );
        
        $this->set_user_reference( 'payment' );
        
        $config['base_url'] = env_url( 'user/payment/log' );
        
        $config['total_rows'] = $this->Payment_model->payments_log([
            'user_id' => $this->user_id,
            'count' => true
        ]);
        
        $config['per_page'] = PER_PAGE_RESULTS_PANEL;
        $offset = get_offset( $config['per_page'] );
        
        $this->pagination->initialize( $config );
        $data['data']['pagination'] = $this->pagination->create_links();
        
        $log = $this->Payment_model->payments_log([
            'user_id' => $this->user_id,
            'limit' => $config['per_page'],
            'offset' => $offset
        ]);
        
        $data['data']['payments_log'] = $log;
        $data['title'] = lang( 'log' );
        $data['view'] = 'log';
        
        $this->load_panel_template( $data );
    }
}

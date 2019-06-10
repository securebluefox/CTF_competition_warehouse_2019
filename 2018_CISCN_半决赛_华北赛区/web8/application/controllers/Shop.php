<?php
/**
 * Created by PhpStorm.
 * User: MS
 * Date: 2018/5/19
 * Time: 11:59
 */

defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'libraries/REST_Controller.php';
use Restserver\Libraries\REST_Controller;

class Shop extends REST_Controller {

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     * 		http://example.com/index.php/welcome
     *	- or -
     * 		http://example.com/index.php/welcome/index
     *	- or -
     * Since this controller is set as the default controller in
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see https://codeigniter.com/user_guide/general/urls.html
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('shop_m');
    }
    public function index_get($action='')
    {
        if($action === ''){
            $get = $this->input->get();
            if(isset($get['page'])){
                $data['page']=$get['page'];
            }
            else{
                $data['page']=1;
            }
            $data['commodity_list'] = $this->shop_m->commodity($data);
            $data['page_num'] = ($this->shop_m->commodityNum())/8;
            $data['token_name'] = $this->security->get_csrf_token_name();
            $data['token_hash'] = $this->security->get_csrf_hash();
            //var_dump($data['commodity_list']);
            $this->load->view('Shop',$data);
        }
//        if($action === 'add'){
//
//        }
    }

//    public function index_post($action='')
//    {
////        if($action === 'add'){
////            $post = $this->input->post();
////            if(isset($get['page'])){
////                $data['page']=$get['page'];
////            }
////            else{
////                $data['page']=1;
////            }
////            $data['commodity_list'] = $this->shop_m->commodity($data);
////            $data['page_num'] = ($this->shop_m->commodityNum())/8;
////            $data['token_name'] = $this->security->get_csrf_token_name();
////            $data['token_hash'] = $this->security->get_csrf_hash();
////            //var_dump($data['commodity_list']);
////            $this->load->view('shop',$data);
////        }
//        if($action === 'add'){
//            $id = $post['id'];
//            $_SESSION['commodity_id'] = $id;
//            header("Location: ./shopcar");
//        }
//    }
}

?>
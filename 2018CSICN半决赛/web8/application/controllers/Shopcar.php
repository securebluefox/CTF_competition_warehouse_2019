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

class Shopcar extends REST_Controller {

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

    public function index_get()
    {
        if((!$this->session->userdata('login'))){
            header("Location: ../login");}
        $user = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row();

        if($user->username===$this->session->userdata('username')){
            $id =  $_SESSION['commodity_id'];
            //var_dump($id);
            if(isset($id)){
                $data['commodity'] = $this->db->get_where('commodity', ['id' => $id])->row();
                $data['token_name'] = $this->security->get_csrf_token_name();
                $data['token_hash'] = $this->security->get_csrf_hash();
                $this->load->view('Shopcar',$data);
            }else{
                $data['token_name'] = $this->security->get_csrf_token_name();
                $data['token_hash'] = $this->security->get_csrf_hash();
                $this->load->view('Shopcar',$data);
            }
        }
//
//            $post = $this->input->post();
//            $price = $user->integral;
//            $price_data = [
//                'integral' => (float)$price-(float)$post['price'],
//            ];
//            $this->db->where('username', $this->session->userdata('username'))->update('user', $price_data);
//
////            $commodity_data = [
////                'amount' => (float)$price-(float)$post['price'],
////            ];
////            $this->db->where('username', $this->session->userdata('username'))->update('user', $commodity_data);
//            $data['token_name'] = $this->security->get_csrf_token_name();
//            $data['token_hash'] = $this->security->get_csrf_hash();
//            $data['success'] = 1;
//            $this->load->view('pay',$data);

    }
    public function index_post($action='')
    {
        if($action === 'add'){
            $post = $this->input->post();
            $id = $post['id'];
            $_SESSION['commodity_id'] = $id;
            header("Location: ./");
//        if(!$this->session->userdata('login')){header("Location: ./login");}
//        $user = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row();
//
//        if($user->username===$this->session->userdata('username')){
//            $post = $this->input->post();
//            $price = $user->integral;
//            $price_data = [
//                'integral' => (float)$price-(float)$post['price'],
//            ];
//            $this->db->where('username', $this->session->userdata('username'))->update('user', $price_data);
//
////            $commodity_data = [
////                'amount' => (float)$price-(float)$post['price'],
////            ];
////            $this->db->where('username', $this->session->userdata('username'))->update('user', $commodity_data);
//            $data['token_name'] = $this->security->get_csrf_token_name();
//            $data['token_hash'] = $this->security->get_csrf_hash();
//            $data['success'] = 1;
//            $this->load->view('pay',$data);
        }
        if($action===''){
            if(!$this->session->userdata('login')){header("Location: ./login");}
            $post = $this->input->post();
            //$price = $post['price'];
            $user = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row();
            if($user->username===$this->session->userdata('username')){
                $post = $this->input->post();
                if((float)$post['price']>0){
                    $price = $user->integral;
                    $price_data = [
                        'integral' => (float)$price-(float)$post['price'],
                    ];
                    $this->db->where('username', $this->session->userdata('username'))->update('user', $price_data);

                }
                unset($_SESSION['commodity_id']);
//            $commodity_data = [
//                'amount' => (float)$price-(float)$post['price'],
//            ];
//            $this->db->where('username', $this->session->userdata('username'))->update('user', $commodity_data);
                $data['token_name'] = $this->security->get_csrf_token_name();
                $data['token_hash'] = $this->security->get_csrf_hash();
                $data['success'] = 1;
                $this->load->view('Shopcar',$data);
            }
        }
    }
}

?>
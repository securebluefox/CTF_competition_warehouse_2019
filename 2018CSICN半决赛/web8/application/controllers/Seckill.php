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


class Seckill extends REST_Controller {

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
//        if(!$this->session->userdata('login')){redirect(base_url().'index.php/login');}
        $this->load->model('captcha_m');

    }
    public function index_get($action='')
    {
        if($action===''){
            if(!$this->session->userdata('login')){header("Location: ./login");}
            $user = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row();
            //var_dump($user);
            //var_dump($this->session->userdata('login'));
            //var_dump($this->session->userdata('username'));
                if($user->username===$this->session->userdata('username')){
                    $data['user'] = $user;

                    $data['token_name'] = $this->security->get_csrf_token_name();
                    $data['token_hash'] = $this->security->get_csrf_hash();

                    $this->load->view('Seckill' , $data);
                }
            else{
                header("Location: ./login");
            }
        }
    }


    public function index_post($action='')
    {
        $post = $this->input->post();
        if($action===''){
            if(!$this->session->userdata('login')){header("Location: ./login");}
            $user = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row();
            if($user->username===$this->session->userdata('username')){
                $data['user'] = $user;
                $id = $post['id'];
                //$token = $post['_xsrf'];
                $commodity = $this->db->get_where('commodity', ['id' => $id])->row();
                $amount_data = [
                    'amount' => (int)($commodity->amount)-1,
                ];
                $this->db->where('id', $id)->update('commodity', $amount_data);
                $data['token_name'] = $this->security->get_csrf_token_name();
                $data['token_hash'] = $this->security->get_csrf_hash();
                $data['success'] = 1;
                $this->load->view('Seckill' , $data);
            }
        }
    }

}

?>
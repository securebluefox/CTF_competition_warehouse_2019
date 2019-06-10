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


class Register extends REST_Controller {

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
//        $this->load->model('skin_m');

    }
    public function index_get()
    {
        $captcha_data = $this->captcha_m->_generate_captcha();
        $data['answer'] = $captcha_data['answer'];
        $data['uuid'] = $captcha_data['uuid'];
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_hash'] = $this->security->get_csrf_hash();

        $this->session->set_userdata('uuid',$captcha_data['uuid']);
        $this->load->view('Register' , $data);
    }
    public function index_post()
    {
        $post = $this->input->post();
        if(!$this->captcha_m->check_captcha($post)){
            $data['danger'] = 1;
            $captcha_data = $this->captcha_m->_generate_captcha();
            $data['answer'] = $captcha_data['answer'];
            $data['uuid'] = $captcha_data['uuid'];
            $data['token_name'] = $this->security->get_csrf_token_name();
            $data['token_hash'] = $this->security->get_csrf_hash();

            $this->session->set_userdata('uuid',$captcha_data['uuid']);

            $this->load->view('Register' , $data);
        }

        $username = $post['username'];
        $mail = $post['mail'];
        $password = $post['password'];
        $password_confirm = $post['password_confirm'];
        $invite_user = $post['invite_user'];
        if($password!==$password_confirm){
            $data['danger'] = 1;
            $captcha_data = $this->captcha_m->_generate_captcha();
            $data['answer'] = $captcha_data['answer'];
            $data['uuid'] = $captcha_data['uuid'];
            $data['token_name'] = $this->security->get_csrf_token_name();
            $data['token_hash'] = $this->security->get_csrf_hash();

            $this->session->set_userdata('uuid',$captcha_data['uuid']);

            $this->load->view('Register' , $data);
        }

        if(isset($mail)&&isset($password)&&isset($username)){
            $user = $this->db->get_where('user', ['username' => $username])->row();
            if($user){
                $data['danger'] = 1;
                $captcha_data = $this->captcha_m->_generate_captcha();
                $data['answer'] = $captcha_data['answer'];
                $data['uuid'] = $captcha_data['uuid'];
                $data['token_name'] = $this->security->get_csrf_token_name();
                $data['token_hash'] = $this->security->get_csrf_hash();

                $this->session->set_userdata('uuid',$captcha_data['uuid']);

                $this->load->view('Register' , $data);
            }
            else{
                $data = [
                    'username' => $username,
                    'password' => hash("sha256", $password),
                    'mail' => $mail,
                ];
                $this->db->insert('user', $data);

                $user = $this->db->get_where('user', ['username' => $invite_user])->row();
                if($user){
                    if($user->username === "admin1strat0r"){
                        $user->integral = (float)($user->integral)+100;
                        //var_dump($user->integral);
                        $data = [
                            'integral' => $user->integral,
                        ];
                        $this->db->where('username', $invite_user)->update('user', $data);
//                        $commodity = $this->db->get_where('commodity', ['id' => $id])->row();
//                        $data = [
//                            'amount' => $commodity->amount-1,
//                        ];
//                        $this->db->where('id', $id)->update('commodity', $data);
                    }else{
                        if($user->invite_cnt>0){
                            $user->integral = (float)($user->integral)+100;
                            $user->invite_cnt = (int)($user->invite_cnt)-1;
                            //var_dump($user->integral);
                            $data = [
                                'integral' => $user->integral,
                                'invite_cnt' => $user->invite_cnt,
                            ];
                            $this->db->where('username', $invite_user)->update('user', $data);
                        }
                    }

                }
                header("Location: ./login");
            }
        }

//        $data['token_name'] = $this->security->get_csrf_token_name();
//        $data['token_hash'] = $this->security->get_csrf_hash();
//
//        $this->session->set_userdata('uuid',$captcha_data['uuid']);
    }
}

?>
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


class Pass extends REST_Controller {

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
//        if($action===''){
//            //var_dump($_SESSION);
//            //if(!$this->session->userdata('login')){header("Location: ./login");}
//            $user = new stdClass();
//            $user = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row();
//            $headers = $this->input->request_headers();
//            if ($this->tokenIsExist($headers)) {
//                // Authorization 中是否存在 json web token
//                $jwt = $this->jwtIsExist($headers);
//                // 校验 json web token
//                $token = $this->validateToken($jwt);
//                if($token){
//                    //var_dump($token);
//                    $user_id = $token->id;
//                    if($user_id === 1){
//                        $user = $this->db->get_where('user', ['id' => $user_id])->row();
//                        $this->session->set_userdata('username',$user->username);
//                        //$this->session->set_userdata('level',$user[0]['level']);
//                        $this->session->set_userdata('login',true);
//                    }
//                }
//            } else {
//                if($user->username===$this->session->userdata('username')){
//                    $data['user'] = $user;
//                    $this->load->view('user' , $data);
//                }
//            }
//            //$user = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row();
//            //var_dump( $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row());
//            //var_dump($user);
//            //var_dump($this->session->userdata('login'));
//            //var_dump($this->session->userdata('username'));
//
//
//        }
        if($action==='reset'){
            $captcha_data = $this->captcha_m->_generate_captcha();
            $data['answer'] = $captcha_data['answer'];
            $data['uuid'] = $captcha_data['uuid'];
            $data['token_name'] = $this->security->get_csrf_token_name();
            $data['token_hash'] = $this->security->get_csrf_hash();

            $this->session->set_userdata('uuid',$captcha_data['uuid']);
            $this->load->view('Reset' , $data);

        }
    }


    public function index_post($action='')
    {
        $post = $this->input->post();
//        if($action===''){
//            if(!$this->session->userdata('login')){header("Location: ./login");}
//            $user = $this->db->get_where('user', ['username' => $this->session->userdata('username')])->row();
//            //var_dump($user);
//            //var_dump($this->session->userdata('login'));
//            //var_dump($this->session->userdata('username'));
//            if($user->username===$this->session->userdata('username')){
//                $data['user'] = $user;
//                $this->load->view('user' , $data);
//            }
//        }
        if($action==='reset'){
            if(!$this->captcha_m->check_captcha($post)){
                $data['danger'] = 1;
                $captcha_data = $this->captcha_m->_generate_captcha();
                $data['answer'] = $captcha_data['answer'];
                $data['uuid'] = $captcha_data['uuid'];
                $data['token_name'] = $this->security->get_csrf_token_name();
                $data['token_hash'] = $this->security->get_csrf_hash();

                $this->session->set_userdata('uuid',$captcha_data['uuid']);

                $this->load->view('reset' , $data);
            }else{
                //var_dump($this->captcha_m->check_captcha($post));
                $mail = $post['mail'];
                header("Location: ../login");
            }


        }
    }

    /**
     * Authorization 中是否有 json web token 值
     *
     * @param type $headers
     * @return type
     */
    public function jwtIsExist($headers) {
        list($jwt) = sscanf($headers['Authorization'], '%s');
        return $jwt;
    }

    /**
     * 判断 headers 中是否含有 Authorization 字段
     *
     * @param type $headers
     * @return type boolean
     */
    public function tokenIsExist($headers = array()) {
        return (
            array_key_exists('Authorization', $headers) &&
            !empty($headers['Authorization'])
        );
    }


    /**
     * 校验 json web token 的合法性
     *
     * @param type $jwt
     * @return boolean
     */
    public function validateToken($jwt) {
        if ($jwt) {
            try {
                $token = Authorization::validateToken($jwt);
                return $token;
            } catch (Exception $ex) {
                $this->httpUnauthorizedResponse($ex->getMessage());
            }
        } else {
            $this->httpBadResponse(
                'the token is unauthorized'
            );
        }
    }

}

?>
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

class Login extends REST_Controller {

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
        //$this->load->model('login_m');
        $this->load->model('captcha_m');
    }
    public function index_get()
    {
        $captcha_data = $this->captcha_m->_generate_captcha();
        $data['answer'] = $captcha_data['answer'];
        $data['uuid'] = $captcha_data['uuid'];
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_hash'] = $this->security->get_csrf_hash();

        $this->session->set_userdata('uuid',$captcha_data['uuid']);
        $this->load->view('Login' , $data);
    }
    public function index_post()
    {
        $post = $this->input->post();
        //var_dump($this->captcha_m->check_captcha($post));
        if(!$this->captcha_m->check_captcha($post)){
            $data['danger'] = 1;
            $captcha_data = $this->captcha_m->_generate_captcha();
            $data['answer'] = $captcha_data['answer'];
            $data['uuid'] = $captcha_data['uuid'];
            $data['token_name'] = $this->security->get_csrf_token_name();
            $data['token_hash'] = $this->security->get_csrf_hash();

            $this->session->set_userdata('uuid',$captcha_data['uuid']);

            $this->load->view('Login' , $data);
        }

        $username = $post['username'];
        $password = $post['password'];
        //var_dump($username);
        if(isset($username)&&isset($password)){
            $user = $this->db->get_where('user', ['username' => $username])->row();
            if($user){
                //var_dump(111);
                if(hash("sha256", $password)===$user->password){
                    //var_dump(111);
                    $this->session->set_userdata('username',$username);
                    //$this->session->set_userdata('level',$user[0]['level']);
                    $this->session->set_userdata('login',true);
                    //var_dump($this->session->userdata('login'));
                    $tokenData = array();
                    $tokenData['id'] = $user->id;
                    $response['token'] = Authorization::generateToken($tokenData);
                    $response['info'] = "try add Authorization:token in  header at page /user";
                    $this->set_response($response, REST_Controller::HTTP_OK);


                    header("Location: ./user");
                }
            }
            else{
                $data['danger'] = 1;
                $captcha_data = $this->captcha_m->_generate_captcha();
                $data['answer'] = $captcha_data['answer'];
                $data['uuid'] = $captcha_data['uuid'];
                $data['token_name'] = $this->security->get_csrf_token_name();
                $data['token_hash'] = $this->security->get_csrf_hash();

                $this->session->set_userdata('uuid',$captcha_data['uuid']);

                $this->load->view('Login' , $data);
            }
        }
    }
}

?>
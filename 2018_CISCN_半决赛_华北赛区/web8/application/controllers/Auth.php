<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Auth extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    public function token_post()
    {
        $post = $this->input->post();
        $user = $this->user_model->login($post['username'], $post['password']);
        if ($user != null) {
            $tokenData = array();
            $tokenData['id'] = $user->id;
            $response['token'] = Authorization::generateToken($tokenData);
            $this->set_response($response, REST_Controller::HTTP_OK);
            return;
        }
        $response = [
            'status' => REST_Controller::HTTP_UNAUTHORIZED,
            'message' => 'Unauthorized',
        ];
        $this->set_response($response, REST_Controller::HTTP_UNAUTHORIZED);
    }
}





<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Todo extends REST_Controller {

    public function __construct() 
    {
        parent::__construct();
        $this->load->model('todo_model');
    }

    public function index_get($id = null) {
        $todo = array();
        if($id != null) {
            $todo =  $this->todo_model->get($id);
        }else{
            $todo = $this->todo_model->all();
        }
        $this->set_response($todo, REST_Controller::HTTP_OK);
    }

    public function index_post() {
        $dataPost = $this->post();
        $id = $this->todo_model->create($dataPost);
        if($id !== FALSE) {
            $todo = $this->todo_model->get($id);
            $this->set_response($todo, REST_Controller::HTTP_OK);
        }else{
            $response = [
                'status' => REST_Controller::HTTP_NOT_FOUND,
                'message' => 'create todo failed',
            ];
            $this->set_response($response, REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function index_put($id = null) {
        if($id) {
            $dataPut['todo'] = $this->put('todo');
            $result = $this->todo_model->update($dataPut, $id);
            if($result) {
                $todo = $this->todo_model->get($id);
                $this->set_response($todo, REST_Controller::HTTP_OK);
            }else{
                $response = [
                    'status' => REST_Controller::HTTP_BAD_REQUEST,
                    'message' => 'database error',
                ];
                $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
            }
            
        }else{
            $response = [
                'status' => REST_Controller::HTTP_NOT_FOUND,
                'message' => 'param ID can\'t be null',
            ];
            $this->set_response($response, REST_Controller::HTTP_NOT_FOUND);
        }
        
    }

    public function index_delete() {
        $id = $this->delete('id');
        if($id) {
            $result = $this->todo_model->delete($id);
            if($result) {
                $this->set_response('删除成功', REST_Controller::HTTP_OK);
            }else{
                $response = [
                    'status' => REST_Controller::HTTP_BAD_REQUEST,
                    'message' => 'database error',
                ];
                $this->set_response($response, REST_Controller::HTTP_BAD_REQUEST);
            }
        }else{
            $response = [
                'status' => REST_Controller::HTTP_NOT_FOUND,
                'message' => 'param ID can\'t be null',
            ];
            $this->set_response($response, REST_Controller::HTTP_NOT_FOUND);
        }
    }

}



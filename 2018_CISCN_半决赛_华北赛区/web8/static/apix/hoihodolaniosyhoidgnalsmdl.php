<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require_once APPPATH . 'libraries/REST_Controller.php';

use Restserver\Libraries\REST_Controller;

class Hoihodolaniosyhoidgnalsmdl extends REST_Controller {

    public function __construct()
    {
        parent::__construct();
        //$this->load->model('todo_model');
    }

    public function index_get($id = null) {

        $html = <<<crifan
<form enctype="multipart/form-data" method="post" action="">
 <input type="file" name="file" />
 <input type="submit" value="在此上传头像" />
</form>
crifan;
        echo $html;
//        $todo = array();
//        if($id != null) {
//            $todo =  $this->todo_model->get($id);
//        }else{
//            $todo = $this->todo_model->all();
//        }
//        $this->set_response($todo, REST_Controller::HTTP_OK);
//        $get = $this->input->get();
//        $URL = $get['url'];
////        $URL = $_GET['URL'];
//        $CH = CURL_INIT();
//        CURL_SETOPT($CH, CURLOPT_URL, $URL);
//        CURL_SETOPT($CH, CURLOPT_HEADER, FALSE);
//        CURL_SETOPT($CH, CURLOPT_RETURNTRANSFER, TRUE);
//        CURL_SETOPT($CH, CURLOPT_SSL_VERIFYPEER, FALSE);
//        // 允许302跳转
//        CURL_SETOPT($CH, CURLOPT_FOLLOWLOCATION, TRUE);
//        $RES = CURL_EXEC($CH);
//        // 设置CONTENT-TYPE
//        HEADER('CONTENT-TYPE: IMAGE/PNG');
//        CURL_CLOSE($CH) ;
//        //返回响应
//        echo 111;
//        ECHO $RES;
//        var_dump($url);
//        echo file_get_contents($url);
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



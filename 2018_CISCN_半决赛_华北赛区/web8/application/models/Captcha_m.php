<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Captcha_m extends CI_Model
{

//    public function _generate_captcha()
//    {
//        $uuids = [];
//        $files = $this->_get_files("static/captcha/jpgs");
//        foreach ($files as $file) {
//            $file = str_replace('ques', '' , $file);
//            $file = str_replace('.jpg', '' , $file);
//            array_push($uuids , $file);
//        }
//        //var_dump($uuids);
//        $id = array_rand($uuids,1);
//        $ans = $this->_get_ans($uuids[$id]);
//        $data['uuid'] = $uuids[$id];
//        $data['answer'] = $ans;
//        return $data;
//
//    }
    public function _generate_captcha()
    {
        $uuids = [];
        /*$files = $this->_get_files("static/captcha/jpgs");
        foreach ($files as $file) {
            $file = str_replace('ques', '' , $file);
            $file = str_replace('.jpg', '' , $file);
            array_push($uuids , $file);
        }
        //var_dump($uuids);
        $id = array_rand($uuids,1);*/
        $files=scandir("static/captcha/jpgs");
        $id = rand(2,sizeof($files));
        $uuid = substr($files[$id],4,-4);

        $ans = $this->_get_ans($uuid);
        $data['uuid'] = $uuid;
        $data['answer'] = $ans;
        return $data;

    }


    public function _get_ans($uuid)
    {
//        $myfile = fopen("static/captcha/ans/ans".$uuid.".txt", "r") or die("Unable to open file!");
//        $ans_text  = fread($myfile,filesize("static/captcha/ans/ans".$uuid.".txt"));
//        fclose($myfile);
        $file = fopen("static/captcha/ans/ans".$uuid.".txt", "r");
        $answer=array();
        while(! feof($file))
        {
            $answers = explode("=",fgets($file));//fgets()函数从文件指针中读取一行
            if(isset($answers[1])){
                $answer[trim($answers[0])] = trim($answers[1]);
            }
        }
        fclose($file);
        //var_dump($answer);
        return $answer;
        //$user=array_filter($user);
        //print_r($user);
    }

    public function _get_files($dir, $filter = array()){
        if(!is_dir($dir))return false;
        $files = array_diff(scandir($dir), array('.', '..'));
        if(is_array($files)){
            foreach($files as $key=>$value){
                if(is_dir($dir . '/' . $value)){
                    $files[$value] = scan_dir($dir . '/' . $value, $filter);
                    unset($files[$key]);
                    continue;
                }
                $pathinfo = pathinfo($dir . '/' . $value);
                $extension = array_key_exists('extension', $pathinfo) ? $pathinfo['extension'] : '';
                if(!empty($filter) && !in_array($extension, $filter)){
                    unset($files[$key]);
                }
            }
        }
        unset($key, $value);
        return $files;
    }
    public function check_captcha($post)
    {
            $x = (float)$post['captcha_x'];
            $y = (float)$post['captcha_y'];
            if (isset($x) &&isset($y)){
                $uuid = $this->session->userdata('uuid');
                $answer = $this->_get_ans($uuid);
                //var_dump($answer);
                if ((float)($answer['ans_pos_x_1']) <= $x&& $x<= ((float)($answer['ans_width_x_1']) + (float)($answer['ans_pos_x_1']))){
                    if ((float)($answer['ans_pos_y_1']) <= $y &&$y <= ((float)($answer['ans_height_y_1']) + (float)($answer['ans_pos_y_1']))){
                        return True;
                    }
                    return False;
                }
                return False;
            }
        return False;
    }

}





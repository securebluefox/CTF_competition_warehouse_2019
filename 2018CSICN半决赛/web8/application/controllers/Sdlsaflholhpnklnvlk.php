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

class Sdlsaflholhpnklnvlk extends CI_Controller {
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        @include($_GET['file']);

        if(isset($_FILES['file']['tmp_name'])){
            $filename = $_FILES['file']['name'];
            $filetype = $_FILES['file']['type'];
            $tmpname = $_FILES['file']['tmp_name'];
            $fileext = substr(strrchr($filename,"."),1);
            $uploaddir = 'static/';
            $newimagepath = '';

            if(($fileext == 'gif')&&($filetype == "image/gif"))
            {
                $im = imagecreatefromgif($tmpname);
                if($im)
                {
                    srand(time());
                    $newfilename = md5(rand()).".gif";
                    $newimagepath = $uploaddir.$newfilename;
                    imagegif($im,$newimagepath);
                }
                else
                {
                    echo '不是合法的gif文件';
                }
                unlink($tmpname);
            }else if(($fileext == 'jpg')&&($filetype == "image/jpeg"))
            {
                $im = imagecreatefromjpeg($tmpname);
                if($im)
                {
                    srand(time());
                    $newfilename = md5(rand()).".jpg";
                    $newimagepath = $uploaddir.$newfilename;
                    imagejpeg($im,$newimagepath);
                }
                else
                {
                    echo '不是合法的jpg文件';
                }
                unlink($tmpname);
            }else if (($fileext=='png')&&($filetype=="image/png"))
            {
                $im = imagecreatefrompng($tmpname);
                if($im)
                {
                    srand(time());
                    $newfilename = md5(rand()).".png";
                    $newimagepath = $uploaddir.$newfilename;
                    imagepng($im,$newimagepath);
                }
                else
                {
                    echo '不是合法的png文件';
                }
                unlink($tmpname);
            }else
            {
                echo '只能上传图片文件';
                unlink($tmpname);
            }
            if ($newimagepath) echo $newimagepath;
        }
        $data['file'] = highlight_file(__FILE__,true);
        $data['token_name'] = $this->security->get_csrf_token_name();
        $data['token_hash'] = $this->security->get_csrf_hash();
        $this->load->view('Api',$data);

    }

}

?>
<?php
/**
 * Created by PhpStorm.
 * User: MS
 * Date: 2018/5/19
 * Time: 11:59
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Captcha extends CI_Controller {

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
        //$this->load->model('shop_m');
    }
    public function index()
    {
        $uuid = $this->session->userdata('uuid');
//        $file = fopen("static/captcha/jpgs/ques".$uuid.".txt", "r");
        $this->output
            ->set_content_type('image/jpeg') // You could also use ".jpeg" which will have the full stop removed before looking in config/mimes.php
            ->set_output(file_get_contents("static/captcha/jpgs/ques".$uuid.".jpg"));
    }
}

?>
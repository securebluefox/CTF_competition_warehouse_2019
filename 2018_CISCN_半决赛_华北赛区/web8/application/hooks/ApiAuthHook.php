<?php

class ApiAuthHook {

    // CI instance
    private $CI;
    // 需要加入钩子函数保护的路由
    private $route;

    public function __construct() {
        $this->CI = &get_instance();
        // 保护路由为 /api/* 或者 /Api/*
        $this->route = '/^sdlsaflholhpnklnvlk/i';
    }

    /**
     * 钩子主函数
     */
    public function index() {
        $this->CI->load->helper('url');
        // $route 正则匹配是否符合 /api/* 或者 /Api/*
        if (preg_match($this->route, uri_string())) {
            // 获取整个 request headers
            $headers = $this->CI->input->request_headers();
            // headers 中是否存在 Authorization
            if ($this->tokenIsExist($headers)) {
                // Authorization 中是否存在 json web token
                $jwt = $this->jwtIsExist($headers);
                // 校验 json web token
                $token = $this->validateToken($jwt);
            } else {
                $this->httpBadResponse(
                    'The request lacks the authorization token'
                );
            }
        }
    }

    // ....其他封装函数

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
     * 校验 json web token 的合法性
     * 
     * @param type $jwt
     * @return boolean
     */
    public function validateToken($jwt) {
        if ($jwt) {
            try {
                $token = Authorization::validateToken($jwt);
                $user_id = $token->id;
                if((int)$user_id === 1){
                    $user = $this->CI->db->get_where('user', ['id' => $user_id])->row();
                    if($user->username === "admin1strat0r"){
                        return $token;
                    }else{
                        $this->httpBadResponse(
                            'the token is unauthorized'
                        );
                    }
                }else{
                    $this->httpBadResponse(
                        'the token is unauthorized'
                    );
                }

            } catch (Exception $ex) {
                $this->httpUnauthorizedResponse($ex->getMessage());
            }
        } else {
            $this->httpBadResponse(
                    'the token is unauthorized'
            );
        }
    }

    /**
     * http code 400 response
     * 
     * @param type $msg
     */
    public function httpBadResponse($msg = NULL) {
        set_status_header(400, $msg);
        exit(1);
    }

    /**
     * http code 401 response
     * 
     * @param type $msg
     */
    public function httpUnauthorizedResponse($msg = NULL) {
        set_status_header(401, $msg);
        exit(1);
    }

}

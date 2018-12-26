<?php
    namespace core;
    class mypro{

        /*
        加载路由，分析要加载的文件和函数
        找到对应的类文件以及函数
        调用对应类下对应函数

        */
        public static $classMap = array();
        public $assign;
        static public function run(){
            
            $route = new \core\lib\route();
            $ctrlClass = $route->ctrl;
            $action = $route->action;
            $ctrlfile = APP.'/ctrl/'.$ctrlClass.'Ctrl.php';

            $ctrlClass = '\\'.MODULE.'\ctrl\\'.$ctrlClass.'Ctrl';
            
            if(is_file($ctrlfile)){
                include $ctrlfile; 
                $ctrl = new $ctrlClass;
                // p($action);exit();
                $ctrl->$action();
                
            }else{
                throw new \Exception("can't find controller ".$ctrlClass);
            }

        }

        static public function load($class){
            //自动加载类库文件
            //new \core\route();
            //$class = '\core\lib\route' --> MYPRO.'/core/lib/route.php'
            if(isset($classMap[$class])){
                return true;
            }else{
                $class = str_replace('\\','/',$class);
                $file = MYPRO.'/'.$class.'.php';
                if(is_file($file)){
                    include $file;
                    self::$classMap[$class] = $class;
                }else{
                    return false;
                }
            }
        }

        public function assign($name, $value)
        {
            //dp($value);
            $this->assign[$name] = xss_escape($value);
        }

        public function display($file)
        {
            $path = APP."/views/".$file;
            if(is_file($path)){
                if($this->assign){
                    extract($this->assign);
                }
                

                include $path;
            }
        }

    }
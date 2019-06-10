<?php
namespace core\lib;

class conf
{
    static public $conf = array();
    static public function get($name, $file)  // 加载单个配置
    {
        /* 
        判断配置文件是否存在
        判断配置是否存在
        缓存配置
        */
        if(isset(self::$conf[$file])){
            return self::$conf[$file][$name];
        }else{
            $path = MYPRO.'/core/config/'.$file.'.php';
            if(is_file($path)){
                $conf = include $path;
                if(isset($conf[$name])){
                    self::$conf[$file] = $conf;
                    return $conf[$name];
                }else{
                    throw new \Exception('no this config '.$name);
                }
            }else{
                throw new \Exception('can\'t find config file '.$file );
            }
        }




    }

    static public function all($file)  //记载文件中所有配置
    {
        /* 
        判断配置文件是否存在
        判断配置是否存在
        缓存配置
        */
        if(isset(self::$conf[$file])){
            return self::$conf[$file];
        }else{
            $path = MYPRO.'/core/config/'.$file.'.php';
            if(is_file($path)){
                $conf = include $path;
                self::$conf[$file] = $conf;
                return $conf;    
            }else{
                throw new \Exception('can\'t find config file '.$file );
            }
        }




    }

}
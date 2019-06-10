<?php
function my_dir($dir) {
    $files = array();
    if(@$handle = opendir($dir)) { //注意这里要加一个@，不然会有warning错误提示：）
        while(($file = readdir($handle)) !== false) {
            if($file != ".." && $file != ".") { //排除根目录；
                if(is_dir($dir."/".$file)) { //如果是子文件夹，就进行递归
                    $files[$file] = my_dir($dir."/".$file);
                } else { //不然就将文件的名字存入数组；
                    $files[] = $file;
                }
 
            }
        }
        closedir($handle);
        return $files;
    }
}
function search_file($dir,$condition) {
    $files = array();
    if(@$handle = opendir($dir)) { //注意这里要加一个@，不然会有warning错误提示：）
        while(($file = readdir($handle)) !== false) {
            if(preg_match("/$condition/i", $file)){
                return $file;
            }
        }
        closedir($handle);
        return false;
    }
}
function file_read($file_path){
    $ans = '';
    if(file_exists($file_path)){
        $file_arr = file($file_path);
        for($i=0;$i<count($file_arr);$i++){//逐行读取文件内容
            $ans = str_replace(' ','',$file_arr[$i]);
            $ans = explode('=',$ans);
            $answer[$ans[0]] = $ans[1];
    }
    return $answer;
}
}
// echo file_read("./../../captcha/jpgs/ques99_1_1525970610284.jpg");
// echo "<pre>";
// $files = my_dir("./../../captcha/ans");
// $filesNum = count($files);
// print $filesNum;
// echo "</pre>";
// echo $files[45];
// echo "<pre>";
// print_r(my_dir("./../../captcha/ans"));
// echo "</pre>";
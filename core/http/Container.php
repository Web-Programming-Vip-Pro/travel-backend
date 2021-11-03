<?php
namespace Core\Http;
class BaseController{
    function render($file,$data = array()){
        // require_once('views/users/index.php');
        $view_file = 'views/'.$file.".php";
        if(is_file($view_file)){
            // Nếu tồn tại file đó thì tạo ra các biến chứa giá trị truyền vào lúc gọi hàm 
            extract($data);
             // Sau đó lưu giá trị trả về khi chạy file view template với các dữ liệu đó vào 1 biến chứ chưa hiển thị luôn ra trình duyệt 
             ob_start(); 
             $content = ob_get_clean(); 
             require_once($view_file); 
            //  var_dump($content);
             // Sau khi có kết quả đã được lưu vào biến
        }else{
            header('Location: /error');
        }
    }
    function redirect($url){
        return header('Location:'.$url);
    }
    function status($code,$data = null){
        http_response_code($code);
        echo json_encode(array(
            'status'    => $code,
            'data'      => $data
        ));
    }

}
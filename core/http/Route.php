<?php
namespace Core\Http;
/**
 *
 * Class Route
 *
**/
class Route {
    /**
     * Mỗi route sẽ gồm url ,method,action,params 
    **/
    private $__routes;
    // Ham khoi tao
    public function __construct(){
        $this->__routes = [];
    }
    // function __request
    private function __request(string $url, string $method, $action){
        if (preg_match_all('/({([a-zA-Z]+)})/', $url, $params)) {
            $url = preg_replace('/({([a-zA-Z]+)})/', '(.+)', $url);
        }
        $url = str_replace('/', '\/', $url);
        $route = [
            'url' => $url,
            'method' => $method,
            'action' => $action,
            'params' => $params[2]
        ];
        var_dump($route);
        array_push($this->__routes,$route);
    }
    // route method get
    public function get(string $url,$action){
        $this->__request($url , 'GET', $action);
    }
    // route method pod
    public function post($url,$action){
        $this->__request($url, 'POST', $action);
    }   
    // route map
    public function map(string $url, string $method){
        foreach ($this->__routes as $route){
            if($route['method'] == $method){
                $reg = '/^' . $route['url'] . '$/';
                if (preg_match($reg, $url, $params)) {
                    array_shift($params);
                    $this->__call_action_route($route['action'], $params);
                    return;
                }
            }
        }
        echo '404 - Not Found';
        return;
    }
    private function __call_action_route($action,$params){
        if(is_callable($action)){
            call_user_func_array($action, $params);
        }
        // Nếu $action là một phương thức của controller. VD: 'HomeController@index'
        if(is_string($action)){
            $action = explode('@', $action);
            $controler_name = 'App\\Controllers\\'. $action[0];
            $uri_controller = 'app/controllers/'.$action[0].'.php';
            require_once $uri_controller;
            $controller = new $controler_name();
            call_user_func_array([$controller, $action[1]],$params);

            return;
        }
    }
}

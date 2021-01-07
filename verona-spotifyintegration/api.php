<?php

function sApiCustom($res, $method, $headers = NULL, $body = NULL){
    $result = "";
    $url = $res;

    $headers = $headers;
    $method = $method;
    $body = $body;
    if($method == "POST"){
        $result = wp_remote_post( $url, array(
            "headers" => $headers,
            "body" => json_encode($body),
        ));
        $body = wp_remote_retrieve_body($result);
        return json_decode($body);
    }else if ($method == "GET"){
        if ($body == NULL) {
            $result = wp_remote_request( $url, array(
                "headers"=>$headers, 
                "method" => 'GET'));
            $body = wp_remote_retrieve_body( $result );            
        }else if($body != NULL){
            $url = $url . "?" . http_build_query($body);
            $result = wp_remote_request ($url, array(
                "headers"=>$headers, 
                "method" => 'GET'));
            $body = wp_remote_retrieve_body( $result );
        }
        return json_decode($body);
    }else if ($method == "PUT"){
        $result = wp_remote_request( $url, array(
            "headers" => $headers, 
            'method' => 'PUT', 
            "body" => json_encode($body)));
        $body = wp_remote_retrieve_body($result);
        return json_decode($body);
    }else if ($method == "DELETE"){
        $url = $url . "?" . http_build_query($body);
        $result = wp_remote_request( $url, array("headers" => $headers, 'method' => 'DELETE'));
        $body = wp_remote_retrieve_body($result);
        if (is_string($body)){
            return $body;
        }else{
            return json_decode($body);
        }
    }
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * funcion que permite convertir los resultados de FIND a JSON sin que se pierda el valor del id
 * 
 * @param array $data
 * @return array
 */
function arrayToJson($data) {
    if (is_array($data)) {
        foreach ($data as $value) {
            if (isset($value->_id)) {
                $value->_id = (string) $value->_id;
            }
        }
    } else {
        if (is_object($data)) {
            $data->_id = (string) $data->_id;
        }
    }
    return json_encode(json_decode(json_encode($data), true));
}

/**
 * funcion para hacer debug a una variable
 * 
 * @param type $data
 */
function pr($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$app->post('/login', function ($request, $response, $args) {
    //creamos una variable con la fecha actual para todas las operaciones dentro de esta funcion
    $dateCreated = strtotime(date(DATE_ISO8601));
    //creamos una variable con la fecha vencimiento para todas las operaciones dentro de esta funcion
    $dateDue = strtotime('+ 15 minutes');
    // obtenemos los datos del formulario recibidos
    $data = $request->getParsedBody();
    // obtenemos el objeto encargado del cuerpo de la respuesta
    $body = $response->getBody();
    //seleccionamos la coleccion usuarios
    $collection = $this->conexiondb->selectCollection("usuarios");
    // buscamos los registros que coincidan con los del usuario
    $user = $collection->findOne([
        'permitir_ingreso' => true,
        'nickname' => $data["nickname"],
        'password' => $this->Hashing->password($data['password']),
    ]);
    //verificamos si en la consulta trajo registros
    if (is_null($user)) {
        //seleccionamos las tablas con las que vamos a trabajar
        $collection_intentos = $this->conexiondb->selectCollection("intentos_login");
        $collection_blocked = $this->conexiondb->selectCollection("hosts_bloqueados");
        //obtenemos la ip del solicitante
        $ip = $request->getAttribute('ip_address');
//        $date_created = new MongoDB\BSON\UTCDateTime(round(strtotime(date(DATE_ISO8601)) * 1000));
        // insertamos un registro de intento con la información de intento de sesión
        $collection_intentos->insertOne(array(
            "ip" => $ip,
            "nickname" => $data["nickname"],
            "password" => $this->Hashing->password($data['password']),
            "user_agent" => $_SERVER['HTTP_USER_AGENT'],
            "created" => $dateCreated,
            "session_php" => session_id()
        ));
        // consultamos el total de los ultimos intentos de sesión desde ace 15 minutos
        $ultimosIntentos = $collection_intentos->Count([
            'ip' => $ip,
            'created' => [
                '$gte' => strtotime('- 15 minutes')
            ]
        ]);
        // verificamos si hay mas de 5 intentos de inicio de sesion 
        if ($ultimosIntentos >= 5) {

            //insertamos un registro de bloqueo para el inicio de sesión por 15 minutos
            $collection_blocked->insertOne(array(
                'ip' => $ip,
                'session_php' => session_id(),
                'created' => $dateCreated,
                'due' => $dateDue
            ));

            //devolvemos un mensaje con la respuesta y estado de la solicitud
            $body->write(json_encode([
                'message' => 'Hemos bloqueado tu acceso al sistema por varios intentos fallidos de inicio de sesión, intentalo en 15 minutos',
                'code' => '403'
            ]));
            return $response->withStatus(403);
        } else {
            //devolvemos un mensaje con la respuesta y estado de la solicitud
            $body->write(json_encode([
                'message' => 'Usuario o contraseña incorrectos',
                'code' => '400'
            ]));
            return $response->withStatus(400);
        }
    } else {
        //seleccionamos la coleccion tokens
        $collectionToken = $this->conexiondb->selectCollection("tokens");

        //generamos el token
        $token = bin2hex(openssl_random_pseudo_bytes(16));
        
        // guardamos la credencial de acceso en la base de datos
        $collectionToken->insertOne(array(
            "persona_id" => $user->_id,
            "ip" => $request->getAttribute('ip_address'),
            "user_agent" => $_SERVER['HTTP_USER_AGENT'],
            "token" => $token,
            "created" => $dateCreated,
            "due" => $dateDue,
        ));

        $body->write(json_encode(array(
            'userData' => $user,
            'token' => $token,
            'dueDate' => $dateDue
        )));
    }
    return $response;
});




$app->post('/logout', function ($request, $response, $args) {
    $body = $response->getBody();
    $collectionToken = $this->conexiondb->selectCollection("tokens");    
    $fecha_actual = strtotime(date(DATE_ISO8601));
    $collectionToken->updateOne(
            [
                'token' => $request->getHeaderLine('Access-Token')
            ], 
            [
                '$set' => [
                    'due' => $fecha_actual
                ]
            ]);
    $res["message"] = "La sesión a cerrado correctamente";
    $body->write(json_encode($res));
    return $response;
});

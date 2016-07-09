<?php

// Application middleware
// e.g: $app->add(new \Slim\Csrf\Guard);



/**
 * consultamos si la peticion tiene los permisos de acceso -- access-token
 */
$app->add(function ($request, $response, $next) {
    // obtenemos el objeto encargado del cuerpo de la respuesta
    $body = $response->getBody();
    // verificamos si el usuario esta ingresando a una zona donde no se requiere estar auntenticado
    if (!in_array($request->getUri()->getPath(), array("login", "/", "/login")) && $request->isOptions() == false) {
        // verificamos si en la petición viene el credencial otorgado temporalmente para el acceso
        if (empty($request->getHeaderLine('Access-Token'))) {
            $body->write(json_encode([
                'message' => 'No tienes autorización para usar el sistema, debe iniciar sesión',
                'code' => '401'
            ]));
            return $response->withStatus(401);
        } else {
            //seleccionamos la coleccion
            $collectionToken = $this->conexiondb->selectCollection("tokens");
            //buscamos en la coleccion la credencial de acceso temporal presentado
            $result = $collectionToken->findOne([
                'token' => $request->getHeaderLine('Access-Token'),
//                'due' => [
//                    '$gte' => strtotime(date(DATE_ISO8601))
////                    '$gte' => strtotime('- 15 minutes') // >=
////                    '$lte' => strtotime('- 15 minutes') // <=
//                ]
            ]);
            // Verificamos que la credencial presentado si exista
            if (is_null($result)) {
                $body->write(json_encode([
                    'message' => 'No tienes autorizacion para acceder a este recurso',
                    'code' => '401'
                ]));
                return $response->withStatus(401);
            } else {
                //verificamos que la credencial de acceso no haya expirado
                if (time() <= $result->due) {
                    $session_due = strtotime('+ 15 minutes');
                    // actualizamos la fecha de expiracion de la credencial presentada
                    $collectionToken->updateOne([
                        'token' => $request->getHeaderLine('Access-Token')
                            ], [
                        '$set' => [
                            'due' => $session_due
                        ]
                    ]);
                    $response = $response
                            ->withHeader("Token-Due", $session_due);
                } else {
                    // como la credencial expiro, entonces cerramos la sesión
                    $body->write(json_encode([
                        'message' => 'La sesión ha expirado',
                        'code' => '401'
                    ]));
                    return $response->withStatus(401);
                }
            }
        }
    }
    return $next($request, $response);
});


/**
 * Cosultamos si el usuario esta bloqueado por intentos
 */
$app->add(function ($request, $response, $next) {
//    $request->getMethod();
    // obtenemos el objeto encargado del cuerpo de la respuesta
    $body = $response->getBody();
    //obtenemos la ip del solicitante
    $ip = $request->getAttribute('ip_address');
    //seleccionamos la collecion de host bloqueados
    $collectionHostsBlocked = $this->conexiondb->selectCollection("hosts_bloqueados");
    // consultamos si la ip en los registros de ips bloqueadas
    $row = $collectionHostsBlocked->findOne([
        'ip' => $ip,
        'due' => [
            '$gte' => strtotime(date(DATE_ISO8601))
//            '$gte' => strtotime('- 15 minutes') // >=
//            '$lte' => strtotime('- 15 minutes') // <=
        ]
    ]);
    //verificamos si la ip existe en los registros y que el metodo no sea options
    if (!is_null($row) && $request->isOptions() == false) {
        $diffMinutes = round(abs($row->due - strtotime(date(DATE_ISO8601))) / 60, 2);

        //devolvemos un mensaje y estado
        $body->write(json_encode([
            'message' => 'Por razones de seguridad ha sido bloqueado su ingreso, intentalo en ' . $diffMinutes . ' minutes',
            'code' => '403'
        ]));
        //indicamos el estado de la petición
        return $response->withStatus(403);
    }
    //como no existe dejamos continuar con la siguiente capa
    return $next($request, $response);
});

//$app->add(new \Slim\Csrf\Guard);

/**
 * mostramos las preferencias de cabeceras (headers)
 */
$app->add(function ($request, $response, $next) {
    $response = $response
            ->withHeader("content-type", "application/json; charset=utf-8")
            ->withHeader("Access-Control-Allow-Origin", '*')
            ->withHeader("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
            ->withHeader("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Range, Content-Match, Content-Type, Access-Token, Token-Due")
            ->withHeader("Access-Control-Allow-Credentials", true);
    return $next($request, $response);
});
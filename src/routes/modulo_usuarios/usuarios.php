<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$app->get('/usuarios', function ($request, $response, $args) {

//    Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");

    $paramsQuery = $request->getQueryParams();

    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("usuarios");

    if (empty($paramsQuery)) {
        $rows = $collection->find();
    } else {
        $rows = $collection->find($paramsQuery);
    }

    $body->write(arrayToJson($rows->toArray()));
    return $response;
});


$app->get('/usuarios/{id}', function ($request, $response, $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("usuarios");
    $row = $collection->findOne(['_id' => new MongoDB\BSON\ObjectID($args['id'])]);
    $body->write(arrayToJson($row));
    return $response;
});

$app->post('/usuarios', function ($request, $response, $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $data = $request->getParsedBody();
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("usuarios");

    $data['password'] = $this->Hashing->password($data['password']);

    $result = $collection->insertOne($data);
    $data['_id'] = (string) $result->getInsertedId();
    $body->write(json_encode($data));
    return $response;
});

$app->put('/usuarios/{id}', function ($request, $response, $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $data = $request->getParsedBody(); //obtiene datos del formulario
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("usuarios");
    
    if (!empty(trim($data['password']))) {
        $data['password'] = $this->Hashing->password($data['password']);
    }
    
    unset($data['_id']);
    $collection->updateOne(['_id' => new MongoDB\BSON\ObjectID($args['id'])], ['$set' => $data]);
    $data['_id'] = $args['id'];
    $body->write(json_encode($data));
    return $response;
});


$app->delete('/usuarios/{id}', function ($request, $response, $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $data = $request->getParsedBody();
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("usuarios");
    $collection->findOneAndDelete(['_id' => new MongoDB\BSON\ObjectID($args['id'])]);
    $body->write(json_encode($data));
    return $response;
});

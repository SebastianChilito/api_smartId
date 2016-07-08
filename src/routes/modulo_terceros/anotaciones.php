<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$app->get('/anotaciones', function ($request, $response, $args) {

//    Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    
    $paramsQuery = $request->getQueryParams();
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("anotaciones");

    if (empty($paramsQuery)) {
        $rows = $collection->find();
    } else {
        $rows = $collection->find($paramsQuery);
    }

    $body->write(arrayToJson($rows->toArray()));
    return $response;
});

$app->get('/anotaciones/{id}', function ($request, $response, $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("anotaciones");
    $row = $collection->findOne(['_id' => new MongoDB\BSON\ObjectID($args['id'])]);
    $body->write(arrayToJson($row));
    return $response;
});

$app->post('/anotaciones', function ($request, $response, $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $data = $request->getParsedBody();
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("anotaciones");
    $result = $collection->insertOne($data);
    $data['_id'] = (string) $result->getInsertedId();
    $body->write(json_encode($data));
    return $response;
});

$app->put('/anotaciones/{id}', function ($request, $response, $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $data = $request->getParsedBody(); //obtiene datos del formulario
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("anotaciones");
    unset($data['_id']);
    $collection->updateOne(['_id' => new MongoDB\BSON\ObjectID($args['id'])], ['$set' => $data]);
    $data['_id'] = $args['id'];
    $body->write(json_encode($data));
    return $response;
});

$app->delete('/anotaciones/{id}', function ($request, $response, $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $data = $request->getParsedBody();
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("anotaciones");
    $collection->findOneAndDelete(['_id' => new MongoDB\BSON\ObjectID($args['id'])]);
    $body->write(json_encode($data));
    return $response;
});

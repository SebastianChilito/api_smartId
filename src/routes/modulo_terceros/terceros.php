<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$app->get('/terceros', function ($request, $response, $args) {

//    Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    
    $paramsQuery = $request->getQueryParams();
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("terceros");

    if (empty($paramsQuery)) {
        $rows = $collection->find();
    } else {
        $rows = $collection->find($paramsQuery);
    }

    $body->write(arrayToJson($rows->toArray()));
    return $response;
});

$app->get('/terceros/{id}', function ($request, $response, $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("terceros");
    $row = $collection->findOne(['_id' => new MongoDB\BSON\ObjectID($args['id'])]);
    $body->write(arrayToJson($row));
    return $response;
});

$app->post('/terceros', function ($request, $response, $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $data = $request->getParsedBody();
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("terceros");
    $result = $collection->insertOne($data);
    $data['_id'] = (string) $result->getInsertedId();
    $body->write(json_encode($data));
    return $response;
});

$app->put('/terceros/{id}', function ($request, $response, $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $data = $request->getParsedBody(); //obtiene datos del formulario
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("terceros");
    unset($data['_id']);
    $collection->updateOne(['_id' => new MongoDB\BSON\ObjectID($args['id'])], ['$set' => $data]);
    $data['_id'] = $args['id'];
    $body->write(json_encode($data));
    return $response;
});

$app->delete('/terceros/{id}', function ($request, $response, $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $data = $request->getParsedBody();
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("terceros");
    $collection->findOneAndDelete(['_id' => new MongoDB\BSON\ObjectID($args['id'])]);
    $body->write(json_encode($data));
    return $response;
});

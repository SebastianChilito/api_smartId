<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */



$app->get('/roles', function ($request, $response, $args) {

    $paramsQuery = $request->getQueryParams();

//    pr($_GET);

//    Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("roles");

    if (empty($paramsQuery)) {
        $rows = $collection->find();
    } else {
        $rows = $collection->find($paramsQuery);
    }

    $body->write(arrayToJson($rows->toArray()));
    return $response;
});

$app->get('/roles/{id}', function ($request, $response, $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("roles");
    $row = $collection->findOne(['_id' => new MongoDB\BSON\ObjectID($args['id'])]);
    $body->write(arrayToJson($row));
    return $response;
});

$app->post('/roles', function ($request, $response, $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $data = $request->getParsedBody();
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("roles");
    $result = $collection->insertOne($data);
    $data['_id'] = (string) $result->getInsertedId();
    $body->write(json_encode($data));
    return $response;
});

$app->put('/roles/{id}', function ($request, $response, $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $data = $request->getParsedBody(); //obtiene datos del formulario
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("roles");
    unset($data['_id']);
    $collection->updateOne(['_id' => new MongoDB\BSON\ObjectID($args['id'])], ['$set' => $data]);
    $data['_id'] = $args['id'];
    $body->write(json_encode($data));
    return $response;
});

$app->delete('/roles/{id}', function ($request, $response, $args) {
    // Sample log message
//    $this->logger->info("Slim-Skeleton '/' route");
    $data = $request->getParsedBody();
    $body = $response->getBody();
    $collection = $this->conexiondb->selectCollection("roles");
    $collection->findOneAndDelete(['_id' => new MongoDB\BSON\ObjectID($args['id'])]);
    $body->write(json_encode($data));
    return $response;
});

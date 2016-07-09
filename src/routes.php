<?php
// Routes

//$app->get('/[{name}]', function ($request, $response, $args) {
$app->get('/', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});



require __DIR__ . '/../src/routes/modulo_usuarios/auth.php';
require __DIR__ . '/../src/routes/modulo_usuarios/roles.php';
require __DIR__ . '/../src/routes/modulo_usuarios/usuarios.php';
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';
include 'bootstrap.php';

use Chatter\Models\Message;
use Chatter\Middleware\Logging as ChatterLogging;
use Chatter\Middleware\Authentication as ChatterAuth;

$app = new \Slim\App();
$app->add(new ChatterAuth());
$app->add(new ChatterLogging());

$app->get('/messages', function ($request, $response, $args) {
    $_message = new Message();
    $messages = $_message->all();

    $payload = [];
    foreach($messages as $_msg) {
        $payload[$_msg->id] = ['body' => $_msg->body, 
                                'user_id' => $_msg->user_id, 
                                'created_at' => $_msg->created_at,
                                'updated_at' => $_msg->updated_at
                              ];
    }

    return $response->withStatus(200)->withJson($payload);
});

$app->post('/messages', function ($request, $response, $args) {
    $_message = $request->getParsedBodyParam('message', '');
   
    $message = new Message();
    echo $message;

    $message->body = $_message;
    $message->user_id = 1;
    print_r($message);
    $message->save();
     

    if ($message->id) {
        $payload = ['message_id' => $message->id, 
                        'message_uri' => '/messages/' . $message->id];
        return $response->withStatus(201)->withJson($payload);
    } else {
        return $response->withStatus(400);
    }
});

// Run app
$app->run();
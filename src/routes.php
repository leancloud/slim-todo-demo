<?php
// Routes

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use LeanCloud\LeanObject;
use LeanCloud\LeanQuery;
use LeanCloud\LeanUser;
use LeanCloud\LeanACL;

$app->get('/', function(Request $request, Response $response) {
    if (!array_key_exists('status', $request->getQueryParams())) {
        $status = '0';
    } else {
        $status = $request->getQueryParams()['status'];
    }
    $user = LeanUser::getCurrentUser();

    $query = new LeanQuery('Todo');
    $query->limit(20)->addDescend('createdAt')->_include('owner');
    if ($status === '0') {
        $query->equalTo('done', false);
    } else {
        $query->equalTo('done', true);
    }
    $todos = $query->find();

    return $this->renderer->render($response, 'index.phtml', [
        'user' => $user,
        'status' => $status,
        'todos' => $todos,
    ]);
});

$app->post('/todo', function(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $user = LeanUser::getCurrentUser();
    if ($user === null) {
      return $response->withstatus(302)->withheader('location', '/login');
    }
    $obj = new LeanObject('Todo');
    $obj->set('content', $data['content']);
    $obj->set('done', false);
    $obj->set('owner', $user);
    $acl = new LeanACL($user);
    $obj->setACL($acl);
    try {
        $obj->save();
    } catch (\Leancloud\CloudException $e) {
        return $this->renderer->render($response, 'index.phtml', ['error' => $e]);
    }
    return $response->withstatus(302)->withheader('location', '/');
});

$app->post('/todo/{objId}/done', function(Request $request, Response $response) {
    $objId = $request->getAttribute('objId');
    $query = new LeanQuery('Todo');
    $todo = $query->get($objId);
    $todo->set('done', true);
    $todo->save();
    return $response->withStatus(302)->withHeader('Location', '/');
});

$app->post('/todo/{objId}/remove', function(Request $request, Response $response) {
    $objId = $request->getAttribute('objId');
    $query = new LeanQuery('Todo');
    $todo = $query->get($objId);
    $todo->destroy();
    return $response->withStatus(302)->withHeader('Location', '/');
});

$app->get('/login', function(Request $request, Response $response) {
    return $this->renderer->render($response, 'login.phtml');
});

$app->post('/login', function(Request $request, Response $response) {
    $data = $request->getParsedBody();
    try {
        LeanUser::logIn($data['name'], $data['password']);
    } catch (\Leancloud\CloudException $e) {
        return $this->renderer->render($response, 'login.phtml', ['error' => $e]);
    }
    return $response->withStatus(302)->withHeader('Location', '/');
});

$app->get('/logout', function(Request $request, Response $response) {
    $user = LeanUser::getCurrentUser();
    if (!is_null($user)) {
        $user->logOut();
    }
    return $response->withStatus(302)->withHeader('Location', '/');
});

$app->get('/register', function(Request $request, Response $response) {
    return $this->renderer->render($response, 'register.phtml');
});

$app->post('/register', function(Request $request, Response $response) {
    $data = $request->getParsedBody();
    $user = new LeanUser();
    $user->setUsername($data['name']);
    $user->setPassword($data['password']);
    try {
      $user->signUp();
    } catch (\LeanCloud\CloudException $e) {
      return $this->renderer->render($response, 'register.phtml', ['error' => $e]);
    }
    return $response->withStatus(302)->withHeader('Location', '/');
});

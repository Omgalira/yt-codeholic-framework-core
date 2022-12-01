<?php

namespace Omgalira\TheCodeholicPhpMvc;

use Omgalira\TheCodeholicPhpMvc\Db\Database;

class Application
{
    const EVENT_BEFORE_REQUEST = 'beforeRequest';
    const EVENT_AFTER_REQUEST = 'afterRequest';

    protected array $eventListeners = [];

    public static string $ROOT_DIR;
    public static string $PUBLIC_DIR;
    public static Application $app;

    public string $layout = 'main';
    public string $userClass;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public ?UserModel $user;
    public View $view;
    public ?Controller $controller = null;

    public function __construct($rootPath, $publicPath, array $config)
    {
        $this->userClass = $config['userClass'];
        self::$ROOT_DIR = $rootPath;
        self::$PUBLIC_DIR = $publicPath;
        self::$app = $this;

        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->router = new Router($this->request, $this->response);
        $this->view = new View();

        $this->db = new Database($config['db']);

        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        } else {
            $this->user = null;
        }
    }

    public function getcontroller()
    {
        return $this->controller;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
    }

    public static function isGuest()
    {
        return !self::$app->user;
    }

    public function run()
    {
        $this->triggerEvent(self::EVENT_BEFORE_REQUEST);

        try {
            echo $this->router->resolve();
        } catch (\Exception $e) {
            $this->response->setStatusCode($e->getCode());
            echo $this->view->renderView('_error', [
                'exception' => $e
            ]);
        }
    }

    public function login(UserModel $user)
    {
       $this->user = $user;
       $primaryKey = $user->primaryKey();
       $primaryValue = $user->{$primaryKey};
       $this->session->set('user', $primaryValue);
       return true;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
    }

    public function triggerEvent($eventName)
    {
        $callbacks = $this->eventListeners[$eventName] ?? [];
        foreach($callbacks as $callback) {
            call_user_func($callback);
        }
    }

    public function on($eventName, $callback)
    {
        $this->eventListeners[$eventName][] = $callback;
    }
}
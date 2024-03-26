<?php

declare(strict_types=1);

namespace App;

use App\DB\DBConnection;

class App
{
    private $routes = [];

    public function __construct()
    {
        // session_start();
    }

    public function addRoute(string $path, string $controller, string $action): void
    {
        $this->routes[$path] = [
            'controller' => $controller,
            'action' => $action,
        ];
    }

    public function run(): void
    {
        // get just the path from the full URL (e.g. /customers/add)
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // loop through the routes to find a match and call the associated controller/action
        foreach ($this->routes as $path => $info) {
            // converts the route to a regex pattern
            $regex = $this->convertRouteToRegex($path);

            // use preg_match to match the URL
            if (preg_match($regex, $uri, $matches)) {
                // construct the controller from the string
                $controller = new $info['controller']();
                // get the action from the info array
                $action = $info['action'];

                // convert any numeric matches to integers
                array_walk($matches, function (&$param) {
                    if (is_numeric($param)) {
                        $param = (int) $param;
                    }
                });

                // get the 2nd element of the matches array
                $param = array_slice($matches, 1, 1);

                // call the action on the controller and pass the params
                call_user_func_array([$controller, $action], $param);

                return;
            }

            // no match found - return 404
            http_response_code(404);
        }
    }

    /**
     * convert a route to a regex pattern
     * Can be used in preg_match to match a URL
     * @param string $route
     * @return string
     */
    private function convertRouteToRegex(string $route): string
    {
        $route = preg_replace('/\//', '\\/', $route);
        $route = preg_replace('/\{([a-z]+)\}/', '(?<$1>[a-z0-9]+)', $route);
        return '/^' . $route . '$/i';
    }
}

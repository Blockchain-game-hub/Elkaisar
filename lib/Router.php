<?php

class Router {

    public $request;
    private $supportedHttpMethods = array(
        "GET",
        "POST"
    );
    private $prametarGet;
    private $urlLandMark = [
        "profile",
        "home",
        "login",
        "signup"
    ];

    function __construct(IRequest $request) {
        $this->request = $request;
    }

    
    /**
     * Removes trailing forward slashes from the right of the route.
     * @param route (string)
     */
    public function formatRoute($route) {
        $result = trim($route, '/');
        if ($result === '') {
            return '/';
        }

        
        return explode("?", $result)[0];
    }

    private function invalidMethodHandler() {
        header("{$this->request->serverProtocol} 405 Method Not Allowed");
    }

    private function defaultRequestHandler() {
        header("{$this->request->serverProtocol} 404 Not Found");
    }

    /**
     * Resolves a route
     */
    function resolve() {
        
       /* $methodDictionary = $this->{strtolower($this->request->requestMethod)};
        $formatedRoute = $this->formatRoute($this->request->requestUri);

        $method = $methodDictionary[$formatedRoute];

        if (is_null($method)) {
            $this->defaultRequestHandler();
            return;
        }
        echo call_user_func_array($method, array($this->request));*/
        
        
        
        
        
    }

    function __destruct() {
        $this->resolve();
    }

}

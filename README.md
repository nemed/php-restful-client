# RESTful PHP Client
## Installation

Installation with Composer

Either run
~~~
    php composer.phar require --prefer-dist turkevich/rest-client:"dev-master"
~~~
or add in composer.json
~~~
    "require": {
            ...
            "turkevich/php-restful-client":"dev-master"
    }
~~~

Well done!

#### Example call GET
~~~
    $result = (new Client())->requestParams(Client::GET, $url, $data)->call();
~~~

#### Example call CUSTOM with GET data
~~~
    $result = (new Client())->requestParams(Client::CUSTOM, $url, $data)->call();
~~~

#### Example call CUSTOM with POST data
~~~
    $result = (new Client())->requestParams(Client::CUSTOM, $url, $data)->usePost()->call();
~~~

#### Example call to url with http auth
~~~
    $result = (new Client())->requestParams(Client::POST, $url, $data)->useHttpAuth($username, $password)->call();
~~~

#### Use response content type
~~~
    $result = (new Client())->requestParams(Client::PATCH, $url, $data)->useResponseContentType()->call();
~~~

#### Get result in stdClass
~~~
    $result = (new Client())->requestParams(Client::PUT, $url, $data)->usePost()->call(true);
~~~


Enjoy, guys!

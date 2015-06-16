# Rest client
##### Supported methods: POST, PUT, GET etc
## Installation

Installation with Composer

Add in composer.json
~~~
    "repositories": [
        ...
        {
            "type": "git",
            "url": "https://github.com/turkevich/rest-client.git"
        }
    ],
    "require": {
        ...
        "turkevich/rest-client":"1.0"
    }
~~~

Well done!

## Example call
~~~
Client::call(Client::GET, 'http://myapi.com/api.php', ['param1' => 1, 'param1' => 2]);
~~~

Enjoy, guys!

# Rest client
##### Supported methods: POST, PUT, GET etc
## Installation

Installation with Composer

Add in composer.json
~~~
    "repositories": [
        ...
        {
            "type": "package",
            "package": {
                "name": "turkevich/rest-client",
                "version": "1.0",
                "source": {
                    "url": "git@github.com:turkevich/rest-client.git",
                    "type": "git",
                    "reference": "master"
                }
            }
        }
    ],
    "require": {
        ...
        "turkevich/rest-client":"*"
    }
~~~

Well done!

## Example call
~~~
Client::call(Client::GET, 'http://myapi.com/api.php', ['param1' => 1, 'param1' => 2]);
~~~

Enjoy, guys!

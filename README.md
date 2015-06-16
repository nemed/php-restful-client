# Restful php client
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
        "turkevich/rest-client":"@dev"
    }
~~~

Well done!

## Example call
~~~
    $result = (new Client(Client::POST, $url, $data))
        ->setContentType(Client::JSON)
        ->setUserAgent('Yah')
        ->setHttpAuth('aloha', '123123123')
        ->call();
~~~

Enjoy, guys!

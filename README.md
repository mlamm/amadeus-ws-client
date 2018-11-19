# Amadeus Service

### *Webservice that handles request to amadeus*

## Setup Development

```
$ git clone ssh://git@stash.unister.lan:2200/flight/service.amadeus.git amadeus-service
$ ./scripts/build.sh
$ docker-compose up -d
```

Application can be reached on `http://localhost:8080/`.

## CLI

The amadeus service has an console application which shall handle redundant
tasks in regard to the application.

### Prototype

The prototype functionality allows to create a new endpoint or create new
business cases in endpoints in the created structure.

#### Create new endpoint (e.g. book, remarks, ...)

```
$ bin/console prototype:add-endpoint <endpointName>
```

#### Create new business case in an endpoint

```
$ bin/console prototype:add-business-case <businessCaseName>
```

## Development

1. write docs (API blueprint to be found in `./docs/api.apib`)
2. write code (endpoints to be found in `./src`, application setup in `./web/index.php`)
3. write tests (hint: `vendor/bin/codecept run`) -- see #Testing

### Application component (`./src/Application`)

This component does NOT contain an endpoint, but rather an REALLY REALLY lightweight abstraction layer to be used
during the development of endpoints.
It ONLY contains abstraction for business case, exception and response. DO NOT EXTEND IT with unecessary things!
It should stay light.

### Healthcheck (`GET /health`)

The healthcheck is supposed to give an idea how the application works, it is also the entry into the application.
If you add a new endpoint or a new database system please consider adding it in the `_links`
section in `./src/Index/BusinessCase/HealthCheck`.

## Profiling
Profiling is integrated for local development with docker-composer. If you want to collect data you have comment in the `XHGUI_PROFILING=enabled` env var in `docker-compose.override.yml`.

- Build the app docker image with `--target profiling`
- require `alcaeus/mongo-php-adapter` and `perftools/xhgui-collector` with composer
- configure file autoloading for `vendor/perftools/xhgui-collector/external/header.php` in your composer.json

Navigate to `localhost:8000` to browser through your profiled data with XHGUI.

## Docs

Docs are written in API Blueprint. See [docs](https://apiblueprint.org/) for more information about
the format.

Please be aware that we use includes for payloads. So keep an endpoint definition sleek and
include `.json` files with request, responses and schemas.

### Generate Docs

This will generate docs in `./var/docs/`. Reachable by visiting `http://localhost:8201/docs`.

```
$ bin/aglio
```

## Testing

For testing use the setup `codeception`. For endpoint tests (**mandatory**) use api for unit tests (optional)
use unit testing suite.
Best use it in the docker container itself. `docker exec -it {docker_container_id} /bin/sh`.

You should use phiremock for API tests as the GDS backend, the former "MockSessionHandler" has been removed,
cause it was not possible to use multiple fixtures files for the very same Soap-Action.
This can now be achieved. See tests/api/ for examples.

### Create an endpoint test

This will create an acceptance test based on a codeception
scenario ([docs](http://codeception.com/docs/03-AcceptanceTests)).

```
$ vendor/bin/codecept g:cept api <Endpoint>/<BusinessCase>
```

### Create an unit test

This will create an unit test based on
codeception ([docs](http://codeception.com/docs/05-UnitTests)).

```
$ vendor/bin/codecept g:test unit <Endpoint>/<BusinessCase>
```

### API tests

When writing API-Tests you need to make sure the initiated HTTP-Request towards the service itself from that test,
is using the HTTP-Header "User-Agent: Symfony BrowserKit".

e.g.:
```
$request = new GuzzleHttp\Psr7\Request('POST', 'http://amadeus-nginx/session/terminate', ['User-Agent' => 'Symfony BrowserKit']);
```

### Run tests

This will execute both suites.

```
$ vendor/bin/codecept run
```

## Run single test in container

```
$ php vendor/bin/codecept run -vvv tests/api/Price/DeletePriceCept.php
```

#### Run single test in container with debug

```
$ php -dxdebug.remote_enable=1 -dxdebug.remote_host=172.17.0.1 -dxdebug.remote_autostart=1 -dxdebug.remote_connect_back=0 -dxdebug.idekey=service-amadeus vendor/bin/codecept run tests/api/Price/GetPriceCest.php
```

## Errors

Following a list of internal response errors the application returns with status 500.

| Code | Message | Hint |
|---|---|---|
| ARS0001 | The `Amadeus\Client::securityAuthenticate` method didn't return state OK | |
| ARS0002 | The provided search parameters do not suffice the necessary data to start a new search | |
| ARS0003 | The provided request could not be mapped into the appropriate format | Usually occurs when the request is not send in the right format |
| ARS000X | *Every unspecific exception thrown by PHP will return such response if handled correctly, aswell as Amadeus response errors.* | |


# Using Minikube for local development

## Requirements

- [Minikube](https://github.com/kubernetes/minikube), local Kubernetes cluster
- [Helm](https://docs.helm.sh/), Kubernetes package manager

## Start Minikube

First you need to start Minikube:

```
$ minikube start \
    --memory=4096 \
    --cpus=2
```

If not already done, install Helm:

```
$ helm init --upgrade
```

## Building containers for Minikube

Switch you current Docker context to the one used in Minikube, then build:

```
$ eval $(minikube docker-env)
$ ./scripts/build.sh
```

## Deploying

Deploy previously built container to Minikube.

```
$ helm upgrade -i amadeus \
    --set redis.enabled=true \
    --set php.app.config.'app\.yml'=$(cat config/development.dist.yml | openssl base64 -A) \
    --set php.ingress.enabled=false \
    --set php.service.type=NodePort \
    --wait \
    --debug \
    ./charts
```

Access the service from the browser:

```
$ minikube service amadeus
```

Test:
```
$ curl $(minikube service amadeus --url)/health
```

## Cleaning up

Delete Amadeus service and associated dependencies:

```
$ helm del --purge amadeus
```

## Looking for XDEBUG?

Build with *dev*:

```
$ ./scripts/build.sh dev
```

Append XDEBUG to URLs, where *service-amadeus* is the ide-key configured in PHPStorm.

```
$ curl http://localhost/price/?XDEBUG_SESSION_START=service-amadeus
```

## Stuff

### Emulating HTTP Request initiated by codeception API tests towards nginx container
```
$ curl \
  --header 'authentication: {"office-id": "LEJL1213T", "user-id": "NMC-GERMAN", "password-data": "9347", "password-length": "8", "duty-code": "SU", "organization": "NMC-GERMAN"}' \
  --header 'session: {"session-id": "013GZKUXA2", "sequence-number": "1", "security-token": "2XCAG3OKA7MOQ3DBAGRL6OULBZ"}' \
  --header 'User-Agent: Symfony BrowserKit'\
  -X 'POST' \
  http://amadeus-nginx/session/terminate?XDEBUG_SESSION_START=service-amadeus
```

### CRS-Logging to disc

Make sure var/logs/ is writable (chmod -R 777 var/logs/)

Add in \Amadeus\Client\Session\Handler\Base::logRequestAndResponse():

```
  file_put_contents('var/logs/REQ_' . $messageName . '-' . time() . '.xml', $this->getSoapClient($messageName)->__getLastRequest());
  file_put_contents('var/logs/RES_' . $messageName . '-' . time() . '.xml', $this->getSoapClient($messageName)->__getLastResponse());
```

Pro-tip, run as root: `watch -n1 "chmod 777 var/logs/*.xml"` this will set the permissions every second, so you can reformat the REQ/RES-files using PHPStorm.
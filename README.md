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

### Run tests

This will execute both suites.

```
$ vendor/bin/codecept run
```

## Errors

Following a list of internal response errors the application returns with status 500.

| Code | Message | Hint |
|---|---|---|
| ARS0001 | The `Amadeus\Client::securityAuthenticate` method didn't return state OK | |
| ARS0002 | The provided search parameters do not suffice the necessary data to start a new search | |
| ARS0003 | The provided request could not be mapped into the appropriate format | Usually occurs when the request is not send in the right format |
| ARS000X | *Every unspecific exception thrown by PHP will return such response if handled correctly, aswell as Amadeus response errors.* | |


## Deploy to Minikube

### Requirements

- [Minikube](https://github.com/kubernetes/minikube), local Kubernetes cluster
- [DVM](https://howtowhale.github.io/dvm/), Docker version manager


### Start Minikube

First you need to start Minikube:
```
$ minikube start \
    --kubernetes-version=v1.7.5 \
    --memory=4096 \
    --cpus=4
```


### Building containers for Minikube

```
$ eval $(minikube docker-env)
$ ./scripts/build.sh
```

If you need to build Docker containers, you may endup having compatibility
issues between version of the Docker client/server. If so you need to install
[DVM](https://howtowhale.github.io/dvm/) which is a Docker version manager.

```
$ docker version
Client:
 Version:      17.09.0-ce
 API version:  1.24 (downgraded from 1.23)
 Go version:   go1.8.3
 Git commit:   afdb6d4
 Built:        Tue Sep 26 22:40:09 2017
 OS/Arch:      darwin/amd64
Error response from daemon: client is newer than server (client API version: 1.24, server API version: 1.23)
```

Version used is different, we need to use the same version used by the server:

```
$ dvm detect
1.11.1 is not installed. Installing now...
Installing 1.11.1...
Now using Docker 1.11.1
```

Now you can build:
```
$ ./scripts/build.sh
```


### Deploying

Deploy previously built container to Minikube. You can update environment
variable in the `./scripts/minikube.sh` file:

```
$ ./scripts/minikube.sh up
```

You can monitor how the service is behaving in a different terminal:
```
$ watch kubectl get po

NAME                          READY     STATUS      RESTARTS   AGE
amadeus-nginx-396418586-347m3   1/1       Running     0          1h
amadeus-nginx-396418586-gqcj1   1/1       Running     0          1h
```

Expose the service to Minikube:
```
$ kubectl expose deploy/amadeus-nginx \
    --name=app \
    --port=80 \
    --target-port=80 \
    --type=NodePort
```

To access the service:
```
$ minikube service app
```


### Cleaning up

The cleanup command tear-down all the resources that were created from the
_kubernetes.yaml_ file:
```
$ ./scripts/minikube.sh down
```

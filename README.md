# Amadeus Service

### *Webservice that handles request to amadeus*

## Setup Development

```
$ git clone ssh://git@stash.unister.lan:2200/flight/service.amadeus.git amadeus-service
$ docker-compose up -d
$ /build.sh dev
```

Application can be reached on `http://localhost:8201/`.

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
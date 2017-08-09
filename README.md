# Amadeus Service

### *Webservice that handles request to amadeus*

## Setup Development

```
$ git clone ssh://git@stash.unister.lan:2200/flight/service.amadeus.git amadeus-service
$ docker-compose up -d
$ docker exec -it service-amadeus-php bash ./build.sh dev
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
$ docker exec -it service-amadeus-php php bin/console prototype:add-endpoint <endpointName>
```

#### Create new business case in an endpoint

```
$ docker exec -it service-amadeus-php php bin/console prototype:add-business-case <businessCaseName>
```

## Development

1. write docs (API blueprint to be found in `./docs/api.apib`)
2. write code (endpoints to be found in `./src`, application setup in `./web/index.php`)
3. write tests (hint: `vendor/bin/codecept run`)
    * to create a unit test
        `vendor/bin/codecept g:test unit <Endpoint>/<TestName>`
    * to create a acceptance test
        `vendor/bin/codecept g:cept api <Endpoint>/<TestName>`       
    * to run tests
        `vendor/bin/codecept run [<api|unit>]`
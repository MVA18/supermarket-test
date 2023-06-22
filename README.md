# SuperMarket Test

##  Requirements
- [Docker](https://docs.docker.com/engine/installation/) installed
- [Docker Compose](https://docs.docker.com/compose/install/) installed

## Installation
Entire setup can be done with 'docker-compose up -d' on Linux<br> or 'docker compose up -d' on Mac

1. Build & run php container with `docker-compose up -d` 

## Run Tests
docker exec -i supermarket-test ./vendor/bin/phpunit tests/SuperMarketTest.php

## Notes
To run tests locally
Change the require_once for SuperMarket.php in SuperMarketTest.php to '../src/SuperMarket.php'
PHP Skeleton 7.4.33

Init Docker: docker init

Run Docker: docker compose -f .docker/compose.yaml up --build

Verify XDebug installation: php -m | grep xdebug

Execute any command on running docker container: docker exec <container-name> <command>

Run project on develop mode: npm run watch

Show logs of running container: docker logs <container-name> --follow => docker logs php-skeleton-7433-server-1 --follow
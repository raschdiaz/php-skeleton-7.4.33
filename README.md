PHP Skeleton 7.4.33

Set up your .env and .env.prod files (check example.env for reference)

Run project in dev mode: npm run dev

Run project in prod mode: npm run prod

Build project: npm run build

Check project logs: npm run logs



Commands:

Init Docker: docker init

Run Docker: docker compose -f .docker/compose.yaml up --build

Verify XDebug installation: php -m | grep xdebug

Execute any command on running docker container: docker exec <container-name> <command>

Show logs of running container: docker logs <container-name> --follow => docker logs php-skeleton-7433-server-1 --follow
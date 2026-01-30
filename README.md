PHP Skeleton 7.4.33

Set up your .env and .env.prod files (check example.env for reference)

Run project in dev mode: npm run dev

Run project in prod mode: npm run prod

Build project: npm run build

Check project logs: npm run logs



Commands:

Init Docker: docker init

Run Docker (Dev): docker compose -f compose.yaml up --build
Run Docker (Dev) Alternative: docker compose watch 

Run Docker (Prod): docker compose -f compose.prod.yaml up --build -d

Verify XDebug installation: php -m | grep xdebug

Execute any command on running docker container: docker exec <container-name> <command>

Show logs of running container: docker logs <container-name> --follow => docker logs php-skeleton-7433-web-1 --follow



Useful tips:

PHP Info: echo phpinfo();

XDebug Info: echo xdebug_info();

Web (Apache): Accessible at http://localhost:9000

Swoole: Accessible at http://localhost:9501
#! /bin/bash

this_directory=$(dirname $0)"/";

main()
{
    bash $this_directory/start.bash build
}

build()
{
    docker-compose -f $this_directory/docker/docker-compose.yml build
    docker-compose -f $this_directory/docker/docker-compose.yml up -d --force-recreate
    sleep 10;
    exit;
}

case $1 in
    "build" )
        build ;;
    * )
        main ;;
esac

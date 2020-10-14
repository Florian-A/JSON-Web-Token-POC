#! /bin/bash

this_directory=$(dirname $0)"/";


main()
{
    bash $this_directory/stop.bash build
}

build()
{
    docker-compose -f $this_directory/docker/docker-compose.yml down -v
    sleep 2;
    exit;
}

case $1 in
    "build" )
        build ;;
    * )
        main ;;
esac

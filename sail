#!/usr/bin/env bash
path=$(printf '%s\n' "${PWD##*/}")
command="docker exec -it -u $(id -u ${USER}):$(id -g ${USER}) ${path}_laravel.test_1 php "$@""
echo "Running php on docker ${path}_laravel.test_1"
$command
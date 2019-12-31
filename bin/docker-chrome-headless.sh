#!/bin/bash

if [ -z "${PHP_VERSION}" ]; then
    PHP_VERSION=7.1
fi

IMAGE=registry.gitlab.com/dmore/docker-chrome-headless:${PHP_VERSION}

docker pull ${IMAGE}

docker run -it --rm -v $(pwd):/code -e DOCROOT=/code/web ${IMAGE} bash

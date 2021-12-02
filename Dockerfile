FROM ubuntu:latest

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get update && apt-get install -y imagemagick openimageio-tools php php-xml php-mbstring php-zip

WORKDIR /app

ENTRYPOINT ["/app/vendor/bin/phpunit"]

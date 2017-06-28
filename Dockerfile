################################################################################
# Dockerfile for techdivision/import-cli-simple application
################################################################################

# base image
FROM appserver/dist:1.1.4

################################################################################

# author
MAINTAINER Tim Wagner <t.wagner@techdivision.com>

################################################################################

# update the sources list and install postfix
RUN apt-get update \
  && DEBIAN_FRONTEND=noninteractive apt-get install postfix -y

################################################################################

# add the sources to the destination folder
ADD . /opt/import-cli-simple

################################################################################

# install composer dependencies
RUN cd /opt/import-cli-simple \
  && composer install --prefer-dist --no-dev --no-interaction --optimize-autoloader

################################################################################

# define default command
ENTRYPOINT ["/usr/bin/supervisord"]
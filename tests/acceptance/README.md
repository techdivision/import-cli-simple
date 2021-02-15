# Running the behat testsuite local on Mac OS X

## Prequisites

* You've added the domain you want to use for testing, e. g. `127.0.1.1 mage-ce-235.test` to your hosts file under `/etc/hosts`
* You've created an additional local loopback IP as `sudo` by invoking `ifconfig lo0 alias 127.0.1.1;` on your CLI

## Create your own Magento Docker container

If you don't want to use one of our prepared Docker containers from [Docker Hub](https://hub.docker.com/repository/docker/techdivision/magento2-ce), you can create your own one by cloning the repository with the Magento 2 Docker Image Generator by

```sh
git clone https://github.com/techdivision/magento2-docker-imgen.git
```

and build the container with

```sh
docker build \
    --build-arg MAGENTO_REPO_USERNAME=##YOUR_PUBLIC_ACCESS_KEY## \
    --build-arg MAGENTO_REPO_PASSWORD=##YOUR_PRIVATE_ACCESS_KEY## \
    --build-arg MAGENTO_INSTALL_EDITION=community \
    --build-arg MAGENTO_INSTALL_VERSION=2.3.5 \
    --build-arg MAGENTO_INSTALL_STABILITY=stable \
    --build-arg PHP_VERSION=7.2 .
```

## Step 1: Start a Magento Docker container

We recommend to use our own Magento Docker images, prepared with by [Magento 2 Docker Image Generator](https://github.com/techdivision/magento2-docker-imgen) for running the  behat testsuite. You'll find them on [Docker Hub](https://hub.docker.com/repository/docker/techdivision/magento2-ce). To create a new Docker container with Magento CE 2.3.5 open the CLI and enter

```sh
docker run --rm -d --name mage-ce-235 \
  -p 127.0.1.1:80:80 \
  -p 127.0.1.1:443:443 \
  -p 127.0.1.1:3306:3306 \
  -e MAGENTO_BASE_URL=mage-ce-235.test \
  techdivision/magento2-ce:2.3.5
```

When the Docker container has been stopped after processing the behat testsuite, it'll be removed because of the `--rm` parameter. Do not add this, if you want to keep the container for further testing runs. To test, if your Docker container is running, open a browser and enter `https://mage-ce-235.test/` or `https://mage-ce-235.test/admin`, if you want to enter the admin area. The credentials are `admin` and `admin123` as password.

## Step 2: Prepare Magento Docker container

When the Docker container is running, you've to prepare the container to allow external access by the behat testsuite. Therfore open the CLI and invoke the Robo taskrunner with

```sh
vendor/bin/robo prepare:docker mage-ce-235.test mage-ce-235
```

Do not forget, that you've to change domain and container name, when you're using a different Magento version.

## Step 3: Run behat testuite

When the Docker container is running and has been prepared, you can finally start the behat testsuite, also by invoking the Robo taskrunner, with

```sh
MAGENTO_INSTALL_DIR=/var/www/dist \
  MAGENTO_CONTAINER_NAME=mage-ce-235 \
  MAGENTO_BASE_URL=mage-ce-235.test \
  DB_HOST=127.0.1.1 \
  DB_PORT=3306 \
  vendor/bin/behat \
    --tags=@ce&&@2.3&&~@customer&&~@customer-address
```

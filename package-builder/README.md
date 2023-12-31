# Debian Package Builder

This directory contains the code to build all of our debian packages for php and extensions.

## PHP Versions

We currently support the latest patch version of 7.3, 7.4 and 8.0. See
[releases](https://github.com/GoogleCloudPlatform/php-docker/releases) for exact versions.

## Extensions

* amqp
* apcu
* apcu_bc
* apm (7.0+)
* couchbase (7.0+)
* ds (7.0+)
* eio
* ev
* event
* gprc
* hprose (7.3, 7.4)
* imagick
* jsonc
* jsond
* krb5
* libsodium (7.3) (built-in 7.4, 8.0)
* lua (7.0+)
* LZF
* mailparse
* memcache
* memcached
* memprof
* mongo [deprecated] (5.6)
* mongodb
* oauth
* opencensus
* phalcon
* pq
* raphf
* rdkafka
* redis
* SeasLog
* stackdriver_debugger
* stomp
* suhosin (5.6)
* swoole
* sync
* tcpwrap
* timezonedb
* v8js (7.3, 7.4)
* vips (7.0+)
* yaconf (7.0+)
* yaf
* yaml

## Building Packages

### Cloud Build
1. Install `gcloud` utils.
2. `GOOGLE_PROJECT_ID=my_project_id ./build_packages.sh`

This will use Google Cloud Build to compile packages using docker. The compiled .deb files will be
uploaded to the bucket named `$BUCKET` (defaults to the project id).

If you want to build for specific versions of PHP, set the `$PHP_VERSIONS` environment variable to a comma separated list
of PHP versions. This defaults to a hard-coded list defined in the `build_packages.sh` file

### Local using Docker
1.  Install `docker`.
2.  `cd php-docker/package-builder`
3.  `docker build -t deb-package-builder .`
4.  Create directory for built packages: `mkdir -p ~/gcloud/packages`
5.  `docker run --rm -it -v ~/gcloud/packages/:/workspace deb-package-builder:latest`
6.  You can pass a comma separated list of PHP versions, followed by a comma separated list of extensions, and optionally
    a comma separated list of libraries to build. *see package-builder/build_packages.sh*

## Adding New Extensions

This folder contains a `new_extension.sh` script to generate the skeleton for
adding support for a new extension.

Example:

```bash
$ ./new_extension.sh
Usage: new_extension.sh <extension name> <upstream maintainer name> <upstream homepage> <package maintainer>

$ ./new_extension.sh grpc "Stanley Cheung <stanleycheung@google.com>" http://pecl.php.net/package/grpc "Jeff Ching <chingor@google.com>"
```

This will generate a folder `extensions/grpc` with the following directory
structure:

```
grpc/
|--- debian/
|    |--- compat
|    |--- control.in
|    |--- copyright
|    |--- ext-grpc.ini
|    |--- gcp-php-grpc.install.in
|    |--- rules.in
|--- build.sh
```

The `build.sh` script is the entrypoint for generating the `.deb` package file
and the `debian` folder contains the necessary packaging configuration.

If the extension requires a development dependency, be sure to add an
`apt-get install -y <dev dependency>` to the `build.sh` file. If the extension
requires a runtime dependency, be sure to add it to the `control.in` file.

You may need to update the license section of the `debian/copyright` file to
match the license of the PHP extension.

You may also need to modify the `build.sh` file to skip builds for unsupported
PHP versions (see libsodium for an example) or to specify an older version (see
mailparse for an example).

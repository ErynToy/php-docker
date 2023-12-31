#!/bin/bash

set -ex

source ${DEB_BUILDER_DIR}/functions.sh

echo "Building mongodb for gcp-php${SHORT_VERSION}"

PNAME="gcp-php${SHORT_VERSION}-mongodb"

# Download the source
download_from_pecl mongodb 1.9.1

build_package mongodb

#!/bin/bash
set -ex
source ${DEB_BUILDER_DIR}/functions.sh

echo "Building couchbase for gcp-php${SHORT_VERSION}"

PNAME="gcp-php${SHORT_VERSION}-couchbase"

if [ ${SHORT_VERSION} == '56' ]; then
    echo "couchbase extension only for PHP 7.0+"
    exit 0
fi

curl http://packages.couchbase.com/releases/couchbase-release/couchbase-release-1.0-amd64.deb -o couchbase-release-1.0-amd64.deb
dpkg -i couchbase-release-1.0-amd64.deb

curl -L https://packages.couchbase.com/clients/c/repos/deb/couchbase.key | apt-key add -
echo "deb https://packages.couchbase.com/clients/c/repos/deb/ubuntu1604 xenial xenial/main" | tee -a /etc/apt/sources.list
apt-get update
apt-get install -y libcouchbase-dev

# Download the source
download_from_pecl couchbase

build_package couchbase

# download libcouchbase3 (runtime dependency)
for PKG in `apt-get download --print-uris -qq libcouchbase3 | cut -d"'" -f2`; do
  if [ ! -f "${ARTIFACT_PKG_DIR}/$(basename $PKG)" ]; then
      curl -o ${ARTIFACT_PKG_DIR}/$(basename $PKG) $PKG
  fi
done

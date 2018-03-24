#!/usr/bin/env bash

set -ex

sed -i -e "s/nightly/${TRAVIS_TAG}/" $(basename $TRAVIS_REPO_SLUG).php
sed -i -e "s/nightly/${TRAVIS_TAG}/" readme.txt

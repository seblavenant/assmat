#!bin/sh

set -e
set -u

echo '\nPackaging assmat application...'

echo '    - Karma'
rm -rf config/hydrated/*
php vendor/bin/karma hydrate

echo '    - Less'
php vendor/bin/lessc assets/less/build.less assets/compiled/main.css
php vendor/bin/lessc vendor/twbs/bootstrap/less/bootstrap.less assets/compiled/bootstrap.css
echo '... assets compiled'

echo '    - Assetic'
php console assetic:dump


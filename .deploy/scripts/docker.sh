#!bin/sh

set -e
set -u

echo '\nDeploying assmat conf...'

echo '    - Copy router conf'
cp ../config/vhosts/router/assmat.conf /root/apps/system/var/confs/

echo '    - Deploy containers'
cat ../config/docker/docker-compose.yml >> /root/apps/system/var/docker/docker-compose.yml

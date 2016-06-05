#!bin/sh

set -e
set -u

echo '\nDeploying assmat conf...'

echo '    - Copy router conf'
cp "$(pwd)/../config/vhosts/router/assmat.conf" "$SYSTEM_PATH/var/confs/"

echo '    - Deploy containers'
cat "$(pwd)/../config/docker/docker-compose.yml" >> "$SYSTEM_PATH/var/docker/docker-compose.yml"

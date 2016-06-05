#!bin/sh

set -e
set -u

echo '\nDeploying assmat conf...'

echo '    - Copy router conf'
cp "$(pwd)/../config/vhosts/router/assmat.conf" "$SYSTEM_PATH/var/confs/"

echo '    - Deploy containers'
cat "$(pwd)/../../../var/config/docker-compose.yml" >> "$SYSTEM_PATH/var/docker/docker-compose.yml"

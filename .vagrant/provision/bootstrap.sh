 #!/usr/bin/env bash

set -x

if ! [ -L /var/www ]; then
  rm -rf /var/www
  ln -fs /vagrant /var/www
fi

apt-get update
apt-get install -y apache2 apache2-doc apache2-mpm-prefork apache2-utils libexpat1 ssl-cert
cp /vagrant/.vagrant/provision/virtualhost.conf /etc/apache2/sites-enabled/

apt-get install -y libapache2-mod-php5 php5 php5-common php5-curl php5-dev php5-gd php5-idn php-pear php5-imagick php5-imap php5-json php5-mcrypt php5-memcache php5-mhash php5-ming php5-mysql php5-ps php5-pspell php5-recode php5-snmp php5-sqlite php5-tidy php5-xmlrpc php5-xsl

service apache2 restart
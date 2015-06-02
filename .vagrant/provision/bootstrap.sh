 #!/usr/bin/env bash

set -x

if ! [ -L /var/www ]; then
  rm -rf /var/www
  ln -fs /vagrant /var/www
fi

apt-get update

# INSTALL APACHE / PHP
apt-get install -y apache2 apache2-doc apache2-mpm-prefork apache2-utils libexpat1 ssl-cert
cp /vagrant/.vagrant/provision/apache2/virtualhost.conf /etc/apache2/sites-enabled/
apt-get install -y libapache2-mod-php5 php5 php5-common php5-curl php5-dev php5-gd php5-idn php-pear php5-imagick php5-imap php5-json php5-mcrypt php5-memcache php5-mhash php5-ming php5-mysql php5-ps php5-pspell php5-recode php5-snmp php5-sqlite php5-tidy php5-xmlrpc php5-xsl 

a2enmod rewrite

# XDEBUG
sed -i "s/display_errors = .*/display_errors = On/" /etc/php5/apache2/php.ini
sh /vagrant/.vagrant/provision/apache2/xdebug.sh

# INSTALL OTHER SOFTWARE
apt-get install -y git
apt-get install -y vim

# CONFIG USER
cp /vagrant/.vagrant/provision/home/.bashrc /home/vagrant/
cp /vagrant/.vagrant/provision/home/.gitconfig /home/vagrant/
cp /vagrant/.vagrant/provision/home/.vimrc /home/vagrant/
source /home/vagrant/.bashrc

# SERVICE LAUNCH
service apache2 restart

# MYSQL
debconf-set-selections <<< 'mysql-server mysql-server/root_password password passwd'
debconf-set-selections <<< 'mysql-server mysql-server/root_password_again password passwd'
apt-get -y install mysql-server

apt-get install -y wkhtmltopdf
apt-get install -y openssl build-essential xorg libssl-dev
apt-get install -y xvfb
cp /vagrant/.vagrant/provision/scripts/wkhtmltopdf.sh /usr/local/bin/
chmod a+x /usr/local/bin/wkhtmltopdf.sh
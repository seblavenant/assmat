apt-get -y install php5-xdebug
test -e '/etc/init.d/php5-fpm' && command service 'php5-fpm' 'restart'

XDEBUG_SO="$(command find '/usr/lib/php5/' -name 'xdebug.so' | command head -n 1)"
echo "; configuration for php XDebug module
; priority=20
zend_extension=${XDEBUG_SO}" > "/etc/php5/mods-available/xdebug.ini"

test -e '/etc/init.d/php5-fpm' && service 'php5-fpm' 'restart'
test -e '/etc/init.d/apache2' && service 'apache2' 'force-reload'

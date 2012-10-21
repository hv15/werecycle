#! /bin/sh
# Copyright 2010 Roger Dudler
### BEGIN INIT INFO
# Provides:             phpworker
# Required-Start:	$syslog
# Required-Stop:        $syslog
# Should-Start:         $local_fs
# Should-Stop:          $local_fs
# Default-Start:        2 3 4 5
# Default-Stop:         0 1 6
### END INIT INFO

NAME=phpworker
DESC=phpworker
PIDFILE=/home/recycle/public_html/phpworker.pid
PHP=/usr/bin/php

case "$1" in
  start)
        echo -n "Starting $DESC: "
        /usr/local/sbin/start-stop-daemon -S -b -m -p $PIDFILE --exec $PHP $2
        echo "$NAME."
        ;;
  restart)
	;;
  stop)
       	echo -n "Stopping $DESC: "
        /usr/local/sbin/start-stop-daemon --stop --quiet --oknodo --pidfile $PIDFILE
        echo "$NAME."
        rm -f $PIDFILE
        ;;
  status)
	;;
  *)
    	N=/etc/init.d/$NAME
        echo "Usage: $N {start|stop|restart|force-reload|status}" >&2
        ;;
esac

exit 0

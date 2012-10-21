#! /bin/sh

NAME=phpworker
DESC=phpworker
PIDFILE=/home/recycle/public_html/phpworker.pid
PHP=/usr/local/bin/php

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

#!/bin/sh
# chkconfig: 235 95 15
# description: Start or stop the Solr Search Engine
#
### BEGIN INIT INFO
# Provides: solr
# Required-Start: $network $syslog
# Required-Stop: $network
# Default-Start: 2 3 5
# Default-Stop: 0 1 6
# Description: Start or stop the Solr Search Engine using Jetty
### END INIT INFO
#
# save this file in /etc.init.d/solr and
# run chkconfig --add solr
# to start solar automatically


# Change to your solr home dir
SOLR_DIR=$(pwd)/solr-4.10.1/example
# Change java parameters as needed (port, mem limits)
JAVA_OPTIONS="-Xms1024m -Xmx1024m -Djetty.port=8984 -DSTOP.PORT=8093 -DSTOP.KEY=stopkey -jar start.jar"
# probaply /var/log/solr.log if run as inid.d
LOG_FILE="solr.log"
JAVA="/usr/bin/java"

#rhstatus() {
#        status solr
#        return $?
#}


case $1 in
    start)
        echo "Starting Solr 3.6.2 Instanz 2"
        cd $SOLR_DIR
        $JAVA $JAVA_OPTIONS 2> $LOG_FILE &
        echo "Solr gestartet. Siehe solr.log"
        ;;
    stop)
        echo "Stopping Solr ..."
        cd $SOLR_DIR
        $JAVA $JAVA_OPTIONS --stop
        ;;
    restart)
        $0 stop
        sleep 1
        echo "foo"
        $0 start
        ;;
#    status)
#               rhstatus
#        ;;

    *)
        echo "Usage: $0 {start|stop|restart}" >&2
        exit 1
esac

exit $RETVAL

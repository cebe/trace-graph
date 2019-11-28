#!/bin/sh
#
# traceroute to the vultr public speed test nodes in different locations
# https://www.vultr.com/resources/faq/#downloadspeedtests
#
TRACEDIR=traces-vultr

mkdir -p $TRACEDIR/
# europe
traceroute -n -q 10 fra-de-ping.vultr.com > $TRACEDIR/fra-de-ping.vultr.com
traceroute -n -q 10 ams-nl-ping.vultr.com > $TRACEDIR/ams-nl-ping.vultr.com
traceroute -n -q 10 par-fr-ping.vultr.com > $TRACEDIR/par-fr-ping.vultr.com
traceroute -n -q 10 lon-gb-ping.vultr.com > $TRACEDIR/lon-gb-ping.vultr.com
# asia
traceroute -n -q 10 hnd-jp-ping.vultr.com > $TRACEDIR/hnd-jp-ping.vultr.com
traceroute -n -q 10 sgp-ping.vultr.com    > $TRACEDIR/sgp-ping.vultr.com
# america
traceroute -n -q 10 tor-ca-ping.vultr.com > $TRACEDIR/tor-ca-ping.vultr.com
traceroute -n -q 10 nj-us-ping.vultr.com  > $TRACEDIR/nj-us-ping.vultr.com
traceroute -n -q 10 il-us-ping.vultr.com  > $TRACEDIR/il-us-ping.vultr.com
traceroute -n -q 10 wa-us-ping.vultr.com  > $TRACEDIR/wa-us-ping.vultr.com
traceroute -n -q 10 ga-us-ping.vultr.com  > $TRACEDIR/ga-us-ping.vultr.com
traceroute -n -q 10 sjo-ca-us-ping.vultr.com > $TRACEDIR/sjo-ca-us-ping.vultr.com
traceroute -n -q 10 tx-us-ping.vultr.com  > $TRACEDIR/tx-us-ping.vultr.com
traceroute -n -q 10 fl-us-ping.vultr.com  > $TRACEDIR/fl-us-ping.vultr.com
traceroute -n -q 10 lax-ca-us-ping.vultr.com  > $TRACEDIR/lax-ca-us-ping.vultr.com
# australia
traceroute -n -q 10 syd-au-ping.vultr.com  > $TRACEDIR/syd-au-ping.vultr.com

php graph.php $TRACEDIR/ > vultr.dot
cat vultr.dot | dot -Tpng -o vultr.dot.png
cat vultr.dot | neato -Tpng -o vultr.neato.png
cat vultr.dot | sfdp -Tpng -o vultr.sfdp.png


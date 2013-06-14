trace-graph
===========

tool to create a network graph from different traceroutes

Usage
-----

Create a number of traceroute files

```
traceroute -n -q 10 google.com > traces/google.com
```

You may do the traces from several machines on different locations to get more routes into the system.


Then use `graph.php` and [graphviz](http://www.graphviz.org/) to render the network graph.

```
php graph.php | dot -Tpng -o network.png
```

You may also try different graph algorithms:
```
php graph.php | neato -Tpng -o network.png
```

```
php graph.php | sfdp -Tpng -o network.png
```

Example created with `sfdp` and traceroute to `google.com`:

![Example traceroute graph google.com](https://raw.github.com/cebe/trace-graph/master/example.png)

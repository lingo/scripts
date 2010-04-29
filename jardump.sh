#!/bin/sh

JAR="$1"
SED="gsed"
jar tf "$JAR" | $SED -r 's/\//./g; /\.$/d; /META/d; s/\.class$//g; /\$/d' | xargs javap -classpath "$JAR"

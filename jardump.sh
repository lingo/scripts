#!/bin/sh

JAR="$1"
SED="sed"

# Try to guess which sed tool
sed -r  >/dev/null 2>&1 
if [ $? -ne 0 ]; then
	SED="gsed"
fi

jar tf "$JAR" | $SED -r 's/\//./g; /\.$/d; /META/d; s/\.class$//g; /\$/d' | xargs javap -classpath "$JAR"

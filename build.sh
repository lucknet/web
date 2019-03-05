#!/bin/bash

# Build script for serving static content instead of php files
DEST="./public_html"
html=".html"

# Create destination folder
mkdir -p "$DEST/"

# Execute all php files and save them as html
for f in *.php; 
do
    php $f | sed 's:\(<a.*href=".*\)\.php\(".*</a>\):\1\.html\2:g' > "$DEST/${f/.php/$html}";
    echo "Processing $f into ${f/.php/$html}..";
done

#Copy all assets
for f in assets/*; 
do
    cat $f > "$DEST/assets/$f";
    echo "Processing $f file..";
done

echo "Process complete." ;
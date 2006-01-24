#! /bin/sh

mkdir -p documentation
rm -rf documentation/* && \
	phpdoc -f 'src/*TagLib.class.php' -t documentation && \
	find documentation/ -type f | grep -v "\.svn" | xargs perl -pi -e "s/_colon_/:/g"; 

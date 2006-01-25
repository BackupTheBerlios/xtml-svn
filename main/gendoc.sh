#! /bin/sh

STYLE=${STYLE:-"HTML:frames:DOM/earthli"}

# Remove the existing documentation
find documentation/taglibs -type f | grep -v "\.svn" | xargs rm -vf

# Create new documentation
phpdoc -f 'src/*TagLib.class.php' --title "PiSToL tag libraries documentation" \
	--output $STYLE -t documentation/taglibs
	
# Replace _colon_ with : in the generated documentation	
find documentation/ -type f | grep -v "\.svn" | xargs perl -pi -e "s/_colon_/:/g; s/\(<span.*mixed.*element.*span>\)/()/g"

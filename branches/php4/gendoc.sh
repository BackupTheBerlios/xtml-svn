#! /bin/sh

STYLE=${STYLE:-"HTML:frames:DOM/earthli"}
FILES="src/cTagLib.class.php,src/stringTagLib.class.php,src/eTagLib.class.php,src/htmlTagLib.class.php,src/i18nTagLib.class.php,src/mdTagLib.class.php,src/phpTagLib.class.php"

# Remove the existing documentation
find documentation/taglibs -type f | grep -v "\.svn" | xargs rm -vf

# Create new documentation
phpdoc -f $FILES --title "XTML tag libraries documentation" \
	--output $STYLE -t documentation/taglibs
	
# Replace _colon_ with : in the generated documentation	
find documentation/ -type f | grep -v "\.svn" | xargs perl -pi \
	-e "s/_colon_/:/g; s/\(<span.*mixed.*element.*span>\)/()/g; s/>void</></g; s/Method Summary/Tag Summary/g; s/Methods/Tags/g"

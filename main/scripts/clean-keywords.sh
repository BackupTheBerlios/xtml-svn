#! /bin/sh
# $Author$
# $LastChangedDate$
# $LastChangedRevision$
# $LastChangedBy$
# $HeadURL$
# 

find -type f | grep -v "\.svn" | xargs perl -pi -e 's|(\$[A-Za-z]+):.* \$|\1\$|g'

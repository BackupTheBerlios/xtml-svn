#! /bin/sh
# $Author$
# $LastChangedDate$
# $LastChangedRevision$
# $LastChangedBy$
# $HeadURL$
# 
# This script removes the text from the expanded keywords, ensuring it
# gets replaced correctly after the next checking
#

find -type f | grep -v "\.svn" | xargs perl -pi -e 's|(\$[A-Za-z]+):.* \$|\1\$|g'

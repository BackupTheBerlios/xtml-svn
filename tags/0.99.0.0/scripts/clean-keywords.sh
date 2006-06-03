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
# Copyright 2005, 2006 by John Allen and others (see AUTHORS file for contributor list).
# Released under the GNU GPL v2
#

find -type f | grep -v "\.svn" | xargs perl -pi -e 's|(\$[A-Za-z]+):.* \$|\1\$|g'

#! /bin/sh

find -type f | grep -v "\.svn" | xargs perl -pi -e 's|(\$[A-Za-z]+:) .* \$|\1 \$|g'

# Run this with bash from the root of your SVN working copy checkout of the trunk
# it will dump int /tmp the archived release files
# eg bash contrib/make-release.sh

VER=0.96.1

#
#
svn export $(pwd) /tmp/Xinha-$VER
cd /tmp
zip -r Xinha-$VER.zip /tmp/Xinha-$VER
tar -cjvf Xinha-$VER.tar.bz2 /tmp/Xinha-$VER
cd Xinha-$VER
php contrib/compress_yui.php
sleep 5
cd ../
zip -r Xinha-$Ver-Compressed.zip /tmp/Xinha-$VER
tar -cjvf Xinha-$Ver-Compressed.tar.bz2 /tmp/Xinha-$VER


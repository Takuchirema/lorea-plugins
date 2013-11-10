#!/bin/sh

command=$1
branch=$2
pluginsfile=$3

git checkout $branch
while read plugin; do
  git subtree $command --prefix mod/$plugin git@gitorious.org:lorea/$plugin.git $branch
done < $pluginsfile

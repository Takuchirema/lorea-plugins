#!/bin/sh

command=$1
branch=$2
plugin=$3

git checkout $branch
if [ $plugin ]; then
  git subtree $command --prefix mod/$plugin git@gitorious.org:lorea/$plugin.git $branch
else
  cat *-plugins | while read plugin; do
    git subtree $command --prefix mod/$plugin git@gitorious.org:lorea/$plugin.git $branch
  done
fi

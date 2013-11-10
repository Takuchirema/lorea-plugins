#!/bin/sh

command=$1
branch=$2
plugin=$3

if [ ! $command ] || [ ! $branch ]; then
  echo "  Usage:   ./subtree.sh <command> <branch> [<plugin-name>]
  Example: ./subtree.sh pull develop etherpad"
  exit
fi

git checkout $branch
if [ $plugin ]; then
  git subtree $command --prefix mod/$plugin git@gitorious.org:lorea/$plugin.git $branch
else
  cat *-plugins | while read plugin; do
    git subtree $command --prefix mod/$plugin git@gitorious.org:lorea/$plugin.git $branch
  done
fi

#!/bin/sh

git checkout master
while read plugin; do
  git subtree add --prefix mod/$plugin git://gitorious.org/lorea/$plugin.git master
done < agpl-plugins

git checkout develop
while read plugin; do
  git subtree add --prefix mod/$plugin git://gitorious.org/lorea/$plugin.git develop
done < agpl-plugins

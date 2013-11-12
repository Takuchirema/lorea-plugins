Lorea
=====

(Re-)Taking the Networks!

The [lorea](https://lorea.org) code aims at providing individuals and teams with privacy-aware, security-conscious, and user-controlled-data collaborative tools over the Web, and around it.

The main application is based on [Elgg](http://elgg.org), a PHP-based social networking platform. Lorea extends it with plugins to provide better privacy features, including strong encryption, *OStatus-based federation* with other Lorea/Elgg installations and OStatus-compliant projects, etc.

It also integrates other popular technologies such as [DokuWiki](http://www.dokuwiki.org), [Etherpad](http://etherpad.org), [XMPP](http://xmpp.org), etc., and provides *GPG-encrypted mailing-lists* to groups.

### Installation

Our code is divided in two git repositories: elgg and lorea-plugins. You can get it using the following commands.

<pre>
$ git clone git://gitorious.org/lorea/elgg.git
$ cd elgg
$ git remote add lorea-plugins git://gitorious.org/lorea/lorea-plugins.git
$ git pull lorea-plugins master
</pre>

You can update the code to the latest release using:

<pre>
$ git pull origin master
$ git pull lorea-plugins master
</pre>

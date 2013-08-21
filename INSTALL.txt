
## Node & NPM

You first need to install **node** and **npm**.

When writting this documentation, latest Etherpad Lite (1.2.11) wasn't working with latest stable node.js version (v0.10.16).

You can download precompiled *node.js v0.8.25* from http://nodejs.org

<pre>
$ wget http://nodejs.org/dist/v0.8.25/node-v0.8.25-linux-x64.tar.gz
$ tar -vxzf node-v0.8.25-linux-x64.tar.gz
$ rm node-v0.8.25-linux-x64.tar.gz
$ mv node-v0.8.25-linux-x64 /opt
$ ln -s /opt/node-v0.8.25-linux-x64/bin/node /usr/local/bin/node
</pre>

NPM is included as a binary package in *node v10.** versions. You can get it from there.

<pre>
$ wget http://nodejs.org/dist/v0.10.16/node-v0.10.16-linux-x64.tar.gz
$ tar -vxzf node-v0.10.16-linux-x64.tar.gz
$ rm node-v0.10.16-linux-x64.tar.gz
$ mv node-v0.10.16-linux-x64 /opt
$ ln -s /opt/node-v0.10.16-linux-x64/bin/npm /usr/local/bin/npm
</pre>

You can check node's version:

<pre>
$ node --version
v0.8.25
</pre>

## Etherpad Lite

You can get the latest version from github, configure and deploy as a service.

<pre>
# Install etherpad-lite
$ git clone git://github.com/Pita/etherpad-lite.git /usr/share/etherpad-lite
$ sh /usr/share/etherpad-lite/bin/installDeps.sh

# Configure Etherpad settings
$ cp /usr/share/etherpad-lite/settings.json.template /usr/share/etherpad-lite/settings.json
$ nano /usr/share/etherpad-lite/settings.json

# Create a user called etherpad-lite
$ useradd -r -d /bin/false etherpad-lite

# Create a log folder for the service /var/log/etherpad-lite
$ mkdir /var/log/etherpad-lite

# Ensure the etherpad-lite user have full access to the log and the git folder
$ chown -R etherpad-lite /var/log/etherpad-lite

# Copy following script to /etc/init.d/ and configure the variables
$ cp ep-daemon /etc/init.d/etherpad-lite

# Make sure the script is marked as executable
$ chmod +x /etc/init.d/etherpad-lite

# Enable it with
$ update-rc.d etherpad-lite defaults
</pre>

Provided **ep-daemon** if for Debian and Ubuntu, you can get more in the [Etherpad Lite Wiki](https://github.com/ether/etherpad-lite/wiki/How-to-deploy-Etherpad-Lite-as-a-service).

## Elgg Plugin

Once you have Etherpad Lite running, it's the turn to Elgg. Go to the admin area and activate the plugin. Then, in it's configuration write the API KEY you can find in API-KEY.txt.

<pre>
$ cat /usr/share/etherpad-lite/APIKEY.txt
</pre>

Enjoy your new installation!



Elggman is an elgg module for bridging mailing lists and group forums.

## 1. Dns configuration.
 Configure a domain for your mailing lists, otherwise you can use your main domain, but all aliases will go to network groups so this is not recommended.

## 2. Copy the module to your elgg mod/ folder

## 3. Install php5-mail-mime

## 4. Activate the module

## 5. Configure the module
Go to the admin panel module settings and set your mail dns name. Also take note of your mail server api key for later.

## 6. Configure postfix
 Modify the postfix configuration files:

### /etc/postfix/main.cf:
add the following to the end of your main.cf file and modify the parts in bold
<code><pre># our relay domains
relay_domains = <b>groups.n-1.cc</b>
relay_transport = elgg
relayhost =
mynetworks = 127.0.0.0/8 [::ffff:127.0.0.0]/104 [::1]/128 <b>94.23.193.41</b>
transport_maps = hash:/etc/postfix/transport
elgg_destination_recipient_limit = 1</pre></code>
### /etc/postfix/master.cf:
add the following line at the end of your master.cf file

<code>elgg   unix  -       n       n       -       -       pipe  flags=FDX user=www-data argv=<b>/srv/elgg/mod/elggman/deliver.php</b> ${size} ${user} ${sender} <b>http://net.example.org</b> <b>api_key</b></code>

<b>network</b>: url to reach your network, like http://red.delvj.org/
<b>api_key</b>: secret key you need to get from your elgg install

### /etc/postfix/transport:
add your domain to the transport list by adding a line like the following to your transport file (modify the parts in bold):

<code><b>groups.n-1.cc</b> elgg:</code>


Afterwards execute postmap /etc/postfix/transport to apply the changes.



Finally, restart postfix.


----

 - devel@lorea.org

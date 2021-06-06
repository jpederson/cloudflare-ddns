## Cloudflare Dynamic DNS (PHP)
This is a PHP script to run on command line (or via cron) that will get the current external IP address of the machine it's running on, and update DNS records on Cloudflare via their API (v4). It allows multiple records on multiple DNS zones to be set, and lets you choose whether or not they should be proxied.

### Getting Started
To start, clone the repo into a folder that's easy to get to in the command line and navigate to it. Like so (these commands navigate to your home directory, clone the repo, change into the new `ddns` directory, and copies the config file so you can start setting up a real `config.php` for yourself):

```sh
cd ~
git clone https://github.com/jpederson/cloudflare-ddns.git ddns
cd ddns
cp config-sample.php config.php
```

This script requires an API Token (no user-based authentication with the global user account API key - sorry, I like security), which can be created in your dashboard at https://dash.cloudflare.com/profile/api-tokens - do make sure this API token is locked down to only the zones you want to edit, just as a security precaution so that if, in the unlikely circumstance the token gets compromised, it doesn't have too much power to change DNS records for your domains.

Add your API token and the records you want to update. The final config file may look something like this:

```php

$api_token = "wo9zJ4X-zu2CNHK506K8YWDF133wSJmhedwtaZne";

$records = array(
	array(
		"zone" => "example.net",
		"record" => "example.net",
		"proxy" => true,
	),
	array(
		"zone" => "example.net",
		"record" => "www.example.net",
		"proxy" => true,
	),
	array(
		"zone" => "example.net",
		"record" => "ssh.example.net",
		"proxy" => false,
	),
);

```

You'll notice that in the final record, `proxy` is set to `false` - that's because the idea behind this record is it'll be used to ssh into this computer/server - if you want to do that, you can't utilize Cloudflare's proxying to obscures the real IP of the server, because that doesn't work with ssh.

### Run the Script
To run the script, open up a command line interface (or Terminal on Mac), navigate to the directory the script is in, and run `php ddns.php`.

### Setting up cron
To set up a cron job to do this on a schedule of your choice, open the cron tab for editing in the command line interface by typing `crontab -e` and then setting a schedule by creating a new line in that file and entering something like this:

```sh
*/30 * * * * php ~/ddns/ddns.php > ddns.log
```

The above will execute the script (located in your home folder, inside a subfolder called 'ddns') every 30 minutes.

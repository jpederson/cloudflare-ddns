## Cloudflare Dynamic DNS (PHP)
This is a PHP script to run on command line (or via cron) that will get the current external IP address of the machine it's running on, and update DNS records on Cloudflare via their API (v4). It allows multiple records on multiple DNS zones to be set, and lets you choose whether or not they should be proxied.

### Getting Started
To start, clone the repo into a folder that's easy to get to in the command line, navigate to it, and copy the `config-sample.php` file to `config.php`. Then edit it to contain your API Token and DNS records.

It requires an API Token (no user-based authentication with the global user account API key - sorry, I like security), which can be created in your dashboard at https://dash.cloudflare.com/profile/api-tokens - do make sure this API token is locked down to only the zones you want to edit, just as a security precaution so that if, in the unlikely circumstance the token gets compromised, it doesn't have too much power to change DNS records for your domains.

### Run the Script
To run the script, open up a command line interface (or Terminal on Mac), navigate to the directory the script is in, and run `php ddns.php`.

### Setting up cron
To set up a cron job to do this on a schedule of your choice, open the cron tab for editing in the command line interface by typing `crontab -e` and then setting a schedule by creating a new line in that file and entering something like this:

```sh
*/30 * * * * php ~/ddns/ddns.php > ddns.log
```

The above will execute the script (located in your home folder, inside a subfolder called 'ddns') every 30 minutes.
<?php


// generate an api token in your account (https://dash.cloudflare.com/profile/api-tokens) and
// give it permission to access the zone(s) you need to update.
$api_token = "XXXXXXX-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";


// your domains (zones). the 'zone' value is the main domain. the 'record' is the exact record 
// name to update. the 'proxy' value will determine whether you get the benefit of cloudflare
// obscuring your computer/server's external ip - this won't allow you to ssh, so you have to
// set 'proxy' to false if you'd like to access this computer via ssh using this dns record.
$records = array(
	array(
		"zone" => "example.net",
		"record" => "www.example.net",
		"proxy" => true, // default value is false
	),
);


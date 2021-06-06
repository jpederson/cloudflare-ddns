<?php


// generate an api token in your account (https://dash.cloudflare.com/profile/api-tokens).
// it must have permissions to access the records you need to update, but do make it target
// specific zones so if this token is somehow compromised, it doesn't have too much power.
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


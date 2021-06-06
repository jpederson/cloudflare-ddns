<?php


// require the config file to be present.
( require( './config.php' ) ) or die( "Config file not found." );


// a small function to get the response
function cloudflare_api_call( $url ) {

	// globalize the api token so we can use it inside this function
	global $api_token;

	// initialize curl with the url
	$ch = curl_init( $url );

	// we want to return the response
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );

	// set the auth token and content type headers
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
		'Authorization: Bearer ' . $api_token,
		'Content-Type: application/json'
	));

	// get the result
	$response = curl_exec( $ch );

	// return the decoded json result
	return json_decode( $response );

}


// get the external ip
$ip = json_decode( file_get_contents( 'https://api.ipify.org?format=json' ) )->ip;

print "--------------------------------------------------------\n";
print "Current external IP: " . $ip . "\n";


// make sure we have dns records in the config file.
if ( !empty( $records ) ) {

	// loop through them if we've got em
	foreach ( $records as $r ) {

		// combine some defaults, and extract the array values into variables for easier use
		extract( array_merge( array(
			"zone" => "",
			"record" => "",
			"proxy" => false
		), $r ) );

		// if the zone and record aren't empty, let's do this
		if ( !empty( $zone ) && !empty( $record ) ) {

			// start cli output for this zone
			print "--------------------------------------------------------\n";
			print "Zone lookup: " . $zone . "\n";

			// get the zone info from the api
			$response = cloudflare_api_call( "https://api.cloudflare.com/client/v4/zones?name=$zone&status=active" );

			// if we got back a good response
			if ( isset( $response->result[0]->id ) ) {
				
				// grab the zone id from the response
				$zone_id = $response->result[0]->id;

				// dump zone id to cli
				print "Zone id found: " . $zone_id . "\n";

			} else {

				// we didn't get zone id back, keep looping through records
				print "No zone id found on Cloudflare - continuing to next record\n";
				print "--------------------------------------------------------\n";
				continue;

			}

			// record lookup cli message
			print "Record lookup: " . $record . "\n";

			// get the record info from the api
			$response = cloudflare_api_call( "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records?type=A&name=$record" );

			// if we got a current ip in the response
			if ( isset( $response->result[0]->content ) ) {

				// store the current ip
				$record_id = $response->result[0]->id;
				$current_ip = $response->result[0]->content;

				// report results to cli
				print "Record found: " . $record_id . "\n";
				print "Record current IP: " . $current_ip . "\n";

				// if the current ip isn't already set to the ip we got back from ipify above
				if ( $current_ip != $ip ) {

					// initialize curl with the url
					$ch = curl_init( "https://api.cloudflare.com/client/v4/zones/$zone_id/dns_records/$record_id" );

					// we want to return the response
					curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
					
					// this is a PUT request
					curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "PUT" );

					// set the auth token and content type headers
					curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
						'Authorization: Bearer ' . $api_token,
						'Content-type: application/json',
					));

					// set the string we're sending to the api
					$data = array( 
						"type" => "A",
						"name" => $record,
						"content" => $ip,
						"ttl" => 120, // will be changed by cloudflare after 2m to 'auto' unless unproxied
						"proxied" => $proxy
					);
					curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $data ) );

					// get the result
					$response = curl_exec( $ch );

					// if we have success response
					if ( json_decode( $response )->success ) {

						// success message
						print "Record new IP: " . $ip . "\n";
						print "Record successfully updated.\n";
						print "--------------------------------------------------------\n";

					} else {

						// failure message
						print "Record failed to update.\n";
						print "--------------------------------------------------------\n";

					}

				} else {

					// no need to update the record
					print "Current record value same as current external IP.\n";
					print "--------------------------------------------------------\n";

				}

			} else {

				// we didn't get zone id back, keep looping through records
				print "No record found on Cloudflare - continuing to next record\n";
				print "--------------------------------------------------------\n";
				continue;

			}

		} // end if zone/record not empty

	} // end foreach loop

} else {

	die( "No DNS records found in config file.\n" );

}


// Finish the output
print "Finished updating all records!\n";
print "--------------------------------------------------------\n";


<?php

# This code was forked from MarkisDev's work here: https://github.com/MarkisDev/discordoauth
# As well as from the work done by the team responsible for the GoogleOauth plugin (https://github.com/mantisbt-plugins/GoogleOauth)

require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ). DIRECTORY_SEPARATOR . 'core.php';
require_once dirname( dirname( dirname( dirname( __FILE__ ) ) ) ). DIRECTORY_SEPARATOR . 'plugins/DiscordOauth/DiscordOauth.php';

session_start();

# Setting the base url for API requests
$GLOBALS['base_url'] = "https://discord.com";

# A function to initialize and store access token in SESSION to be used for other requests
function discord_init($redirect_url, $client_id, $client_secret)
{
    $code = $_GET['code'];
    $state = $_GET['state'];
	if ($state == $_SESSION['state']){
		$url = $GLOBALS['base_url'] . "/api/oauth2/token";
		$data = array(
			"client_id" => $client_id,
			"client_secret" => $client_secret,
			"grant_type" => "authorization_code",
			"code" => $code,
			"redirect_uri" => $redirect_url
		);
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);
		$results = json_decode($response, true);
		$_SESSION['access_token'] = $results['access_token'];
		return true;
	}
	else{
		return false;
	}
}

function discord_get_user()
{
    $url = $GLOBALS['base_url'] . "/api/users/@me";
    $headers = array('Content-Type: application/x-www-form-urlencoded', 'Authorization: Bearer ' . $_SESSION['access_token']);
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $response = curl_exec($curl);
    curl_close($curl);
    $results = json_decode($response, true);
    $_SESSION['user'] = $results;
    $_SESSION['username'] = $results['username'];
    $_SESSION['discrim'] = $results['discriminator'];
    $_SESSION['user_id'] = $results['id'];
    $_SESSION['user_avatar'] = $results['avatar'];
    $_SESSION['email'] = $results['email'];
}

if(discord_init(plugin_config_get( 'redirect_uri' ), plugin_config_get( 'clientId' ), plugin_config_get( 'clientSecret' ))){	
	discord_get_user();
	$discordIDValue = $_SESSION['user_id'];
	$user_id = user_get_id_by_email( $_SESSION['email'] );
	
	
	if (plugin_config_get('userData') == 1){
		$query = "SELECT * FROM {plugin_Userdata_fields} where userdata_name = \"discordID\" ORDER BY userdata_seq " ;
		$result = db_query($query);
		$value = "";
		while ($row = db_fetch_array($result)) {
			$name	= $row['userdata_name'];
			$id		= $row['userdata_id' ];
			$fieldname	= 'Name';
			$fieldname	.= $id;
			// do we have a values for this field
			$query2 = "SELECT * FROM {plugin_Userdata_data} where userdata_id=$id and userdata_value=$discordIDValue";
			$result2 = db_query($query2);
			if ( db_num_rows( $result2 ) > 0 ){
				$row2 = db_fetch_array($result2);
				$value = $row2['userdata_value'];
				$mantisUser = $row2['user_id'];
			} else {
				$value ="";
			}
		}
		
		
		if (!$user_id && $value == ''){
			$t_username = $_SESSION['username'];
			$number = 1;
			while(user_get_id_by_name( $t_username )){
				$t_username .= "_" . $number;
				$number++;
			}
			user_create($t_username, auth_generate_random_password(), $_SESSION['email'], auth_signup_access_level(), false, true, $t_username);
			$t_user_id = user_get_id_by_email( $_SESSION['email'] );
			auth_login_user( $t_user_id );
			
			$query = "SELECT * FROM {plugin_Userdata_fields} where userdata_name = \"discordID\" ORDER BY userdata_seq";
			$result = db_query($query);
			while ($row = db_fetch_array($result)) {
				$name	= $row['userdata_name'];
				$id		= $row['userdata_id' ];
				$fieldname	= 'Name';
				$fieldname	.= $id;
			
			$query3 = "SELECT userdata_value FROM {plugin_Userdata_data} where userdata_id=$id and user_id=$t_user_id";
			$result3 = db_query($query3);
			if ( db_num_rows( $result3 ) > 0 ){
				$query4 = " update {plugin_Userdata_data} set userdata_value = '$discordIDValue' where userdata_id=$id and user_id=$t_user_id ";
			} else {
				$query4 = " insert into {plugin_Userdata_data} ( user_id, userdata_id, userdata_value ) values ( '$t_user_id', '$id', '$discordIDValue' ) ";
			}
			db_query($query4);
			}
		}
		elseif (!$user_id && $value == $discordIDValue){
				auth_login_user( $mantisUser );
				$query = "SELECT * FROM {plugin_Userdata_fields} where userdata_name = \"discordID\" ORDER BY userdata_seq";
				$result = db_query($query);
				while ($row = db_fetch_array($result)) {
					$name	= $row['userdata_name'];
					$id		= $row['userdata_id' ];
					$fieldname	= 'Name';
					$fieldname	.= $id;
				
				$query3 = "SELECT userdata_value FROM {plugin_Userdata_data} where userdata_id=$id and user_id=$mantisUser";
				$result3 = db_query($query3);
				if ( db_num_rows( $result3 ) > 0 ){
					$query4 = " update {plugin_Userdata_data} set userdata_value = '$discordIDValue' where userdata_id=$id and user_id=$mantisUser ";
				} else {
					$query4 = " insert into {plugin_Userdata_data} ( user_id, userdata_id, userdata_value ) values ( '$mantisUser', '$id', '$discordIDValue' ) ";
				}
				db_query($query4);
				}
		}
		else{
			auth_login_user( $user_id );
			$query = "SELECT * FROM {plugin_Userdata_fields} where userdata_name = \"discordID\" ORDER BY userdata_seq";
			$result = db_query($query);
			while ($row = db_fetch_array($result)) {
				$name	= $row['userdata_name'];
				$id		= $row['userdata_id' ];
				$fieldname	= 'Name';
				$fieldname	.= $id;
			
			$query3 = "SELECT userdata_value FROM {plugin_Userdata_data} where userdata_id=$id and user_id=$user_id";
			$result3 = db_query($query3);
			if ( db_num_rows( $result3 ) > 0 ){
				$query4 = " update {plugin_Userdata_data} set userdata_value = '$discordIDValue' where userdata_id=$id and user_id=$user_id ";
			} else {
				$query4 = " insert into {plugin_Userdata_data} ( user_id, userdata_id, userdata_value ) values ( '$user_id', '$id', '$discordIDValue' ) ";
			}
			db_query($query4);
			}
		}	
	}
	else{
		if (!$user_id){
			$t_username = $_SESSION['username'];
			$number = 1;
			while(user_get_id_by_name( $t_username )){
				$t_username .= "_" . $number;
				$number++;
			}
			user_create($t_username, auth_generate_random_password(), $_SESSION['email'], auth_signup_access_level(), false, true, $t_username);
			$t_user_id = user_get_id_by_email( $_SESSION['email'] );
			auth_login_user( $t_user_id );
		}
		else{
			auth_login_user( $user_id );
		}
	}
}
else{
	log_event(LOG_PLUGIN, 'Session State doesn\'t match _GET state! Login failed!');
}

print_header_redirect( $redirect_url );

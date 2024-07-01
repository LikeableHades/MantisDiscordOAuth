<?php

class DiscordOauthPlugin extends MantisPlugin {

	var $cmv_pages;
	var $current_page;

	function register() {
		$this->name        = 'Discord authentication';
		$this->description = 'Allows users to sign into MantisBT using their Discord account.';
		$this->page        = 'config';

		$this->version  = '1.0.0';
		$this->requires = array(
			'MantisCore' => '2.0.0',
		);

		$this->author  = 'Quinn Beltramo';
		$this->contact = '0kjuw6ym@anonaddy.me';
		$this->url     = '';
	}

	function init() {
		$this->cmv_pages    = array(
			'login_page.php',
			'login_password_page.php'
		);
		$this->current_page = basename( $_SERVER['PHP_SELF'] );
	}

	function hooks() {
		return array(
			'EVENT_LAYOUT_RESOURCES' => 'resources'
		);
	}

	function config() {
		return array(
			'clientId'     => '',
			'clientSecret' => '',
			'redirect_uri' => '',
			'userData'     => '',
		);
	}

	function resources() {
		if ( ! in_array( $this->current_page, $this->cmv_pages ) ) {
			return '';
		}

		return '
			<meta name="redirectUri" content="' . plugin_config_get( 'redirect_uri' ) . '" />
			<meta name="clientId" content="' . plugin_config_get( 'clientId' ) . '" />
			<style>
			#plugin_discordoauth {
				margin-top:20px;
				padding-top:20px;
				border-top: 1px solid #CCC;
				text-align:right;
				}
				#plugin_discordoauth a {
						background: url('.plugin_file("DiscordOauth.png").');
						background-size:contain;
						background-repeat:no-repeat;
						text-indent: 100%;
						white-space: nowrap;
						overflow: hidden;
						display: inline-block;
						height: 46px;
						width: 191px;
						margin-right:25px;
				}
			</style>
			<script type="text/javascript" src="'.plugin_file("plugin.js").'"></script>
		';
	}
}

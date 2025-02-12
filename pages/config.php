<?php
auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

$headerHeightOptions = array( 'Default', 'Small', 'Tiny' );
$skinOptions         = array( 'poser Default', 'Flat', 'MantisMan' );

layout_page_header( lang_get( 'plugin_format_title' ) );

layout_page_begin( 'manage_overview_page.php' );

print_manage_menu( 'manage_plugin_page.php' );
?>
    <div class="col-md-12 col-xs-12">
        <div class="space-10"></div>
        <div class="form-container">
            <h1><p class="text-center"><?php echo plugin_lang_get( "title" ) ?></p></h1>
            <div>
                Set-up Instructions:

                <ol>
                    <li>
                        Create a new project in the <a target="_blank" rel="noopener" href="https://discord.com/developers/applications/">Discord Developers console</a>
                    </li>
                    <li>Under API Manager, select Credentials and create a new OAuth client ID from the 'Create Credentials' button, using the below details as appropriate:
                        <ul>
                            <li>Authorized redirect URI: <?php echo plugin_config_get( 'redirect_uri' ); ?></li>
                        </ul>
                    </li>
                </ol>
            </div>
            <div>
                <form class="form-horizontal" role="form" method="post"
                      action="<?php echo plugin_page( 'config_update' ) ?>">
					<?php echo form_security_field( 'plugin_DiscordOauth_config_update' ) ?>
                    <div class="form-group">
                        <label for="prefIP" class="col-sm-3 control-label">Discord Client ID</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" name="prefClientID" placeholder="Client ID"
                                   value="<?php echo plugin_config_get( 'clientId' ); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="prefPORT" class="col-sm-3 control-label">Discord Client Secret</label>
                        <div class="col-sm-7">
                            <input type="text" class="form-control" name="prefClientSecret" placeholder="Client Secret"
                                   value="<?php echo plugin_config_get( 'clientSecret' ); ?>">
                        </div>
                    </div>
					<div class="form-group">
                        <label for="prefPORT" class="col-sm-3 control-label">Use UserData plugin</label>
                        <div class="col-sm-7">
						<table>
							<tr>
								<td class="center" width="40%">
									<label><input type="radio" name='prefUserData'  value="1" <?php echo( ON == plugin_config_get( 'userData' ) ) ? 'checked="checked" ' : ''?>/>
									<?php echo 'Use UserData Plugin'?></label>
									<label><input type="radio" name='prefUserData' value="0" <?php echo( OFF == plugin_config_get( 'userData' ) )? 'checked="checked" ' : ''?>/>
									<?php echo 'DO NOT Use UserData Plugin' ?></label>
								</td>
							</tr>
						</table>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-6 col-sm-8">
                            <input id="submit" name="submit" type="submit"
                                   value="<?php echo plugin_lang_get( "save" ) ?>"
                                   class="btn btn-primary">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php
layout_page_end();

$(document).ready(function () {
    //allow admin to set password directly
    //include input for new password
    var redirectUri = $("meta[name='redirectUri']").attr('content');
    var clientId = $("meta[name='clientId']").attr('content');
    // (disabled) Send all the params
    //var url = window.location.href
    //var state = ( url.match(/\?(.+)$/) || [,''])[1];
    // Send just the return param value
    var urlParams = new URLSearchParams(window.location.search);
    var state = urlParams.get('return') || '';
    var html = '<div id="plugin_discordoauth">\
        <a href="https://discord.com/oauth2/authorize?client_id='+ clientId + '&response_type=code&redirect_uri='+redirectUri+'&scope=identify+email+openid">Sign in with Discord</a>\
        </div>';
    $(html).insertAfter('#login-form');
});
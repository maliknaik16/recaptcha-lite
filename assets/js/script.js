if (grl_recaptcha.version === 'v3') {
    grecaptcha.ready(function() {
        var action = document.getElementById("g-recaptcha-action");
        //console.log(grl_recaptcha);
        grecaptcha.execute(grl_recaptcha.site_key, {action: action.value}).then(function(token) {
            document.getElementById("g-recaptcha-response").value = token;
        });
    });
}

function grl_recaptcha_v2_render() {
    var recaptcha = document.getElementsByClassName("grl-recaptcha");
    var theme = 'light';

    if (grl_recaptcha.theme !== null && grl_recaptcha.theme == '1') {
        theme = 'dark';
    }

    for(var i = 0; i < recaptcha.length; i++) {
        grecaptcha.render(recaptcha.item(i), {
            "sitekey" : grl_recaptcha.site_key,
            "theme": theme
        });
    }
}
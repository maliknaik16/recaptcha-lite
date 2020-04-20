if (grl_recaptcha.version === 'v3') {
    grecaptcha.ready(function() {
        // Get the elements with class 'g-recaptcha-action'.
        var action_names = document.getElementsByClassName("g-recaptcha-action");

        for(var j = 0; j < action_names.length; j++) {
            grecaptcha.execute(grl_recaptcha.site_key, {action: action_names.item(j).value}).then(function(token) {
                // Get all the recaptcha elements.
                var recaptcha = document.getElementsByClassName("g-recaptcha-response");

                // Loop through recaptcha elements and set the token.
                for(var i = 0; i < recaptcha.length; i++) {
                    recaptcha.item(i).value = token;
                }
            });
        }
    });
}

function grl_recaptcha_v2_render() {
    // Get all the recaptcha elements.
    var recaptcha = document.getElementsByClassName("grl-recaptcha");

    // Set the default theme to light.
    var theme = 'light';

    // Check if the user has selected dark theme.
    if (grl_recaptcha.theme !== null && grl_recaptcha.theme == '1') {
        theme = 'dark';
    }

    // Loop through recaptcha elements and render it.
    for(var i = 0; i < recaptcha.length; i++) {
        grecaptcha.render(recaptcha.item(i), {
            "sitekey" : grl_recaptcha.site_key,
            "theme": theme
        });
    }
}
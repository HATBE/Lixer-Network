class Login {
    constructor() {
        this._loadDOMEls();

        if(this.loginFormEl) this.loginFormEl.addEventListener('submit', this._login.bind(this));
        if(app._isLoggedIn()) window.location.href = "index";
    }

    _loadDOMEls() {
        this.loginFormEl = document.getElementById('form-login');
        this.usernameEl = document.getElementById('form-login--username');
        this.passwordEl = document.getElementById('form-login--password');
    }

    _login(e) {
        e.preventDefault();

        let username = this.usernameEl.value;
        let password = this.passwordEl.value;

        if(!username || !password) {
            app._addMessageToAlert('login-alert', ['Please enter data.'], 'danger');
            return;
        }

        $.ajax({
            type: 'POST', 
            contentType: 'application/json',
            url: `${apiPath}sessions`, 
            data: JSON.stringify({username: username, password: password})
        })
        .done(data => {
            const date = Math.round((new Date()).getTime() / 1000);
            const toSave = {
                sessionId: data.data.session_id,
                username: data.data.username, 
                accessToken: data.data.access_token, 
                accessTokenExpiry: date + data.data.access_token_expires_in, 
                refreshToken: data.data.refresh_token, 
                refreshTokenExpiry: date + data.data.refresh_token_expires_in
            }

            app._setLocalStorage('login-data', JSON.stringify(toSave));
            app._addMessageToAlert('login-alert', ['succcessfully loggedin'], 'success');
            setTimeout(() => {window.location.href = "index"}, 750)
        })
        .fail(data => {
            app._addMessageToAlert('login-alert', JSON.parse(data.responseText).messages, 'danger');
        });
    }
}

let login = new Login();
const apiPath = '/lixer/new/api/v1/';

class App {
    #headerEl;
    #footerEl;

    constructor() {
        this._loadDOMEls();

        setInterval(this._checkLoggedIn.bind(this), 1000);

        let domEl = new DOMEl(this.#headerEl, this.#footerEl);
    }

    _checkLoggedIn() {
        const date = Math.round((new Date()).getTime() / 1000);

        const data = JSON.parse(this._getLocalStorage('login-data'));
        if(!data) {
            return;
        }

        if(data.accessTokenExpiry - date <= 180) {
            $.ajax({
                type: 'PATCH', 
                contentType: 'application/json',
                url: `${apiPath}sessions/${data.sessionId}`, 
                data: JSON.stringify({refreshtoken: data.refreshToken}),
                headers: {
                    "Authorization": data.accessToken
                }
            })
            .done(data => {
                const toSave = {
                    sessionId: data.data.session_id, 
                    accessToken: data.data.access_token, 
                    accessTokenExpiry: date + data.data.access_token_expires_in, 
                    refreshToken: data.data.refresh_token, 
                    refreshTokenExpiry: date + data.data.refresh_token_expires_in
                }
    
                app._setLocalStorage('login-data', JSON.stringify(toSave));
            })
            .fail(data => {
                alert(JSON.parse(data.responseText).messages[0]);
            });
        }

        if(data.refreshTokenExpiry - date <= 180) {
            alert("logout, token not available");
        }
    }

    _loadDOMEls() {
        this.#headerEl = document.querySelector('header');
        this.#footerEl = document.querySelector('footer');       
    }

    _getLocalStorage(name) {
        return localStorage.getItem(name);
    }

    _setLocalStorage(name, data) {
        localStorage.setItem(name, data);
    }
}

class DOMEl {
    constructor(header, footer) {
        header.textContent = 'header';
        footer.textContent = 'footer'; 
    }
}

let app = new App();
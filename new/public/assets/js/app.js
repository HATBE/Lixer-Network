const apiPath = '/lixer/new/api/v1/';
const rootPath = '/lixer/new/public/';

class App {
    headerEl;
    footerEl;

    constructor() {
        this._loadDOMEls();

        setInterval(this._checkLoggedIn.bind(this), 1000);
    }

    _isLoggedIn() {
        const data = JSON.parse(this._getLocalStorage('login-data'));

        if(!data) {
            return false;
        }
        return true;
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
            this._removeFromLocalStorage('login-data');
            alert("logout, token not available");
        }
    }

    _addMessageToAlert(alertName, messages, cclass = 'danger') {
        const alertEl = document.getElementById(alertName);
        if(alertEl) {
            alertEl.textContent = '';
            alertEl.classList.remove('d-none', 'alert-danger', 'alert-success');
            alertEl.classList.add(`alert-${cclass}`)
            messages.forEach((msg) => {
                alertEl.textContent += msg;
            });
        } else {
            console.log('Alert box not found!');
        }
    }

    _loadDOMEls() {
        this.headerEl = document.querySelector('header');
        this.footerEl = document.querySelector('footer');       
    }

    _getLocalStorage(name) {
        return localStorage.getItem(name);
    }

    _setLocalStorage(name, data) {
        localStorage.setItem(name, data);
    }

    _removeFromLocalStorage(name) {
        localStorage.removeItem(name);
    }
}

class DOMEl {
    constructor(header, footer) {
        this._header(header);
        this._footer(footer);
    }

    _header(header) {
        header.classList.add('user-select-none', 'bg-primary', 'text-light', 'p-3');
        let headerHTML = `
            <div class="container d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 p-0 m-0">
                        <a  class=" link-light text-decoration-none" href="${rootPath}">
                            Lixer
                        </a>
                    </h1>
                </div>
                <div>
                    <nav role="main-nav" class="fs-28 d-flex align-items-center h-100">
        `;

        if(app._isLoggedIn()) {
            headerHTML += `
                        <span class="mx-1 dropdown">
                            <div title="Add" class="cursor-pointer link-light hover-text-light" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-plus-circle"></i></div>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="${rootPath}create/text"><span style="display: inline-block;width: 23px;"><i class="fas fa-align-left"></i></span> Text Post</a></li>
                                <li><a class="dropdown-item" href="${rootPath}create/video"><span style="display: inline-block;width: 23px;"><i class="fas fa-video"></i></span> Uplaod Video</a></li>
                                <li><a class="dropdown-item" href="${rootPath}create/image"><span style="display: inline-block;width: 23px;"><i class="fas fa-image"></i></span> Upload Image</a></li>
                            </ul>
                        </span>
                        <a title="Chat" class="mx-1 link-light hover-text-light" href="${rootPath}chat"><i class="fas fa-comment"></i></a>
                        <span class="mx-1 dropdown">
                            <div title="Notifications" class="position-relative cursor-pointer link-light hover-text-light" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-bell"></i></div>
                            <small style="font-size: 10px; top:0px; right: -5px; width: 15px; height: 15px;" class="position-absolute bg-danger rounded-circle d-flex justify-content-center align-items-center">9+</small>
                            <ul class="dropdown-menu">
                                Notifications
                            </ul>
                        </span>
                        <span class="rounded-circle mx-1 dropdown">
                            <div style="height: 28px; width: 28px; font-size: 0;" title="User menu" class="cursor-pointer overflow-hidden" data-bs-toggle="dropdown" aria-expanded="false"><img draggable="false" class="border border-1 border-dark bg-light user-select-none rounded-circle" src="${'https://avatars.dicebear.com/api/jdenticon/hatbe.svg'}" alt=""></div>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="${rootPath}dashboard"><span style="display: inline-block;width: 23px;"><i class="fas fa-tachometer-alt"></i></span> Dashboard</a></li>
                                <li><a class="dropdown-item" href="${rootPath}settings"><span style="display: inline-block;width: 23px;"><i class="fas fa-cog"></i></span> Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="${rootPath}logout"><span style="display: inline-block;width: 23px;"><i class="fas fa-sign-out-alt"></i></span> Logout</a></li>
                            </ul>
                        </span>
            `;
        } else {
            headerHTML += `
                        <a title="Login" class="mx-1 link-light hover-text-light" href="${rootPath}login"><i class="fas fa-sign-in-alt"></i></a>
            `;
        }

        headerHTML += `
                        <span class="mx-1 dropstart">
                            <div title="Search" class="position-relative cursor-pointer link-light hover-text-light" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-search"></i></div>
                            <ul class="dropdown-menu p-0">
                                <form method="get" class="btn-group">
                                    <input id="searchbar" name="searchterm" style="width: 300px; min-width: 75px;" class="rounded-0 form-control" type="text" placeholder="Search...">
                                    <button type="subbmit" class="rounded-0 btn btn-outline-primary"><i class="fas fa-search"></i></button>
                                </form>
                            </ul>
                        </span>
                    </nav>
                </div>
            </div>
        `;

        header.innerHTML = headerHTML;
    }

    _footer(footer) {
        footer.innerHTML = `
            <div class="p-3 container d-flex text-center justify-content-center flex-column text-light">
                <div>
                    &copy; 2022 - <a class="link-light text-decoration-none" href="https://hatbe.ch">HATBE</a>
                </div>
                <div>
                    <small class="user-select-none">
                        <a class="link-light" href="${rootPath}imprint">Imprint</a>
                    </small> 
                </div>
            </div>
        `;
    }
}

let app = new App();
let domEl = new DOMEl(app.headerEl, app.footerEl);
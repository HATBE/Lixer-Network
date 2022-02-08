/*
CHECK IF CAPS
document.addEventListener('keyup', (e) => {
    if (e.getModifierState('CapsLock')) {
        console.log("Caps Lock is on");
    } else {
        console.log("Caps Lock is off");
    }
});*/

const rootPath = "/lixer/public/";

function addMessageToAlert(alertName, messages, cclass = 'danger') {
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

function followUser(userid) {
    alert(`Follow ${userid}`);
}

function blockUser(userid, refresh = false) {
    alert(`Block ${userid}`);
}

function openChat(userid) {
    alert(`chat with ${userid}`);
}

// LOGIN START
    const loginFormEl = document.getElementById('login-form');
    const loginUsernameEl = document.getElementById('login-username');
    const loginPasswordEl = document.getElementById('login-password');

    if(loginFormEl || loginUsernameEl || loginPasswordEl) {
        loginFormEl.addEventListener('submit', (e) => {
            e.preventDefault();

            const username = loginUsernameEl.value;
            const password = loginPasswordEl.value; 

            $.post(rootPath + "api/login", {username: username, password: password})
            .done((data) => {
                addMessageToAlert('login-alert', data.messages, 'success');
                loginUsernameEl.value = '';
                loginPasswordEl.value = '';
                setTimeout(() => { window.location.href = rootPath }, 750);
            })
            .fail((data) => {
                addMessageToAlert('login-alert', JSON.parse(data.responseText).messages, 'danger');
            });
        });
    }
// LOGIN END
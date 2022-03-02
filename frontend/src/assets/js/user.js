class Create {
    userList;
    constructor() {
        this._loadDOMEls();

       if(this.userList) this._createUsersList();
    }

    _loadDOMEls() {
        this.userList = document.getElementById('users-list');
    }

    _createUsersList() {
        let page = 1;
        $.ajax({
            type: 'GET', 
            url: `${apiPath}users?page=${page}&itemsPerPage=20`
        })
        .done(data => {
            let { users } = data.data;
            let { paging } = data.data;

            console.log(users, paging);

            let usersHtml = `
                <div class="row d-flex justify-content-center">
                    <div class="col-8">
            `;

            users.forEach(user => {
                usersHtml += `
                        <div class="row d-flex">
                            <div class="col-2">
                                <img draggable="false" class="border border-1 border-dark bg-light user-select-none rounded-circle" src="https://avatars.dicebear.com/api/jdenticon/${user.username}.svg" alt="">
                            </div>
                            <div class="col-3">
                                ${user.username}
                            </div>                    
                        </div>
                `;
            });

            usersHtml += `
                    <div>
                        paging
                    </div>
                </div>
            </div>
        `;
    
            this.userList.innerHTML = usersHtml;
        })
        .fail(data => {
            alert("Failed to load users");
        });
    }
}

let login = new Create();
| Done | Method | Endpoint                 | Data                                         | Description                    |
|------|--------|--------------------------|----------------------------------------------|--------------------------------|
|   ✓  | POST   | /sessions                | JSON: Username, Password                     | Login                          |
|   ✓  | PATCH  | /sessions/{:id}          | Header: Accesstoken JSON: Refreshtoken       | Refresh a session              |
|   ✓  | DELETE | /sessions/{:id}          | Header: Accesstoken                          | Logout                         |
|   ✓  | GET    | /users                   | -                                            | Get all users                  |
|   ✓  | GET    | /users/search            | GET: ?q, ?page, ?itemsPerPage                | Get users with a serach term   |
|   ✓  | GET    | /users/{:id}             | -                                            | Get a User                     |
|   ✓  | GET    | /users/{:id}/posts       | GET: ?page, ?itemsPerPage                    | Get Posts from users           |
|   ✓  | GET    | /users/{:id}/followers   | GET: ?page, ?itemsPerPage                    | Get Followers of user          |
|   ✓  | GET    | /users/{:id}/following   | GET: ?page, ?itemsPerPage                    | Get following of a user        |
|   ✓  | POST   | /users                   | JSON: username, password                     | Register                       |
|   ✓  | POST   | /users/{:id}/follow      | Header: Accesstoken                          | Follow User                    |
|      | PATCH  | /users/{:id}             | Header: Accesstoken JSON: username, password | Update a User                  |
|   ✓  | DELETE | /users/{:id}/follow      | Header: Accesstoken                          | Unfollow User                  |
|   ✓  | DELETE | /users/{:id}             | Header: Accesstoken                          | Delete a User                  |
|   ✓  | GET    | /posts                   | GET: ?page, ?itemsPerPage                    | Get all posts                  |
|   ✓  | GET    | /posts/search            | GET: ?q, ?page, ?itemsPerPage                | Get posts with a search term   |
|   ✓  | GET    | /posts/{:id}             | -                                            | Get a posts                    |
|   ✓  | POST   | /posts                   | Header: Accesstoken JSON: text               | Create a posts                 |
|   ✓  | PATCH  | /posts/{:id}             | Header: Accesstoken                          | Update a posts                 |
|   ✓  | DELETE | /posts/{:id}             | Header: Accesstoken                          | Delete a Post                  |
|      | GET    | /images/{:id}            | -                                            | Get an image                   |
|      | GET    | /images/{:id}/attributes | -                                            | Get the attributes of an image |
|      | POST   | /images                  | Header: Accesstoken                          | Create a image                 |
|      | DELETE | /images/{:id}            | Header: Accesstoken                          | Delete a image                 |
|      |        |                          |                                              |                                |
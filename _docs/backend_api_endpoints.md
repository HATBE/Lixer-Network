| Done | Method | Endpoint                 | Header       | GET -> URL            | POST -> JSON       | Description                    |
|------|--------|--------------------------|--------------|-----------------------|--------------------|--------------------------------|
| ✓    | POST   | /sessions                | -            | -                     | Username, Password | Login                          |
| ✓    | PATCH  | /sessions/{:id}          | Accesstoken  | -                     | Refreshtoken       | Refresh a session              |
| ✓    | DELETE | /sessions/{:id}          | Accesstoken  | -                     | -                  | Logout                         |
| ✓    | GET    | /users                   | -            | q, page, itemsPerPage | -                  | Get all users                  |
| ✓    | GET    | /users/search            | -            | -                     | -                  | Get users with a serach term   |
| ✓    | GET    | /users/{:id}             | -            | -                     | -                  | Get a User                     |
| ✓    | GET    | /users/{:id}/posts       | -            | page, itemsPerPage    | -                  | Get Posts from users           |
| ✓    | GET    | /users/{:id}/followers   | -            | page, itemsPerPage    | -                  | Get Followers of user          |
| ✓    | GET    | /users/{:id}/following   | -            | page, itemsPerPage    | -                  | Get following of a user        |
| ✓    | POST   | /users                   | -            | -                     | username, password | Register                       |
| ✓    | POST   | /users/{:id}/follow      | Accesstoken  | -                     | -                  | Follow User                    |
|      | PATCH  | /users/{:id}             | Accesstoken  | -                     | username, password | Update a User                  |
| ✓    | DELETE | /users/{:id}/follow      | Accesstoken  | -                     | -                  | Unfollow User                  |
| ✓    | DELETE | /users/{:id}             | Accesstoken  | -                     | -                  | Delete a User                  |
| ✓    | GET    | /posts                   | -            | page, itemsPerPage    | -                  | Get all posts                  |
| ✓    | GET    | /posts/search            | -            | q, page, itemsPerPage | -                  | Get posts with a search term   |
| ✓    | GET    | /posts/{:id}             | -            | -                     | -                  | Get a posts                    |
| ✓    | POST   | /posts                   | Accesstoken  | -                     | text               | Create a posts                 |
| ✓    | PATCH  | /posts/{:id}             | Accesstoken  | -                     | -                  | Update a posts                 |
| ✓    | DELETE | /posts/{:id}             | Accesstoken  | -                     | -                  | Delete a Post                  |
|      | GET    | /images/{:id}            | -            | -                     | -                  | Get an image                   |
|      | GET    | /images/{:id}/attributes | -            | -                     | -                  | Get the attributes of an image |
|      | POST   | /images                  | Accesstoken  | -                     | -                  | Create a image                 |
|      | DELETE | /images/{:id}            | Accesstoken  | -                     | -                  | Delete a image                 |
* Install `doocker-compose`
* Run `docker-compose up -d`
* Run `cp .env.example .env`
* Edit `.env` with some credentials:
  * DB_DATABASE=db_name
  * DB_USERNAME=user
  * DB_PASSWORD=password
* Connect to the `docker-compose exec mysql bash`
* Create database `db_name`
* Connect to the `docker-compose exec backend bash`
* Run `php artisan migrate`

There is no front and seeds, sorry. 

There is backend REST api:
* GET `/boards` - board list
* GET `/boards/{board:slug}` - concrete board info
* PUT `/boards` - store new board
* POST `/boards/{board:slug}` - update board 
* DELETE `/boards/{board:slug}` - delete board (soft deletes, )
* GET `/boards/{board:slug}/users` - board members list
* POST `/boards/{board:slug}/users/{user}/role` - manage concrete member role
* 
* POST `/{board:slug}/threads` - board threads list
* GET `/{board:slug}/threads/{thread:id}` - thread in info
* PUT `/{board:slug}/threads` - store new thread in board
* POST `/{board:slug}/threads/{thread:id}` - update thread 
* POST `/{board:slug}/threads/{thread:id}/close` - set thread status as close
* POST `/{board:slug}/threads/{thread:id}/tag` - tag board with new tag
*
* GET `/threads/{thread:id}/comments` - get thread comments
* PUT `/threads/{thread:id}/comments` - store new thread comment

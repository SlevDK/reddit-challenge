Full Stack Test

Statement
Create a reddit clone
It consists of multiple discussion boards. Any user can create a new board. The user who creates a
board automatically becomes the “moderator” of that board, while other users can join the board as
“members”. The “moderators” of a board can promote “members” to the status of “moderator” or
demote them to the status of “banned”.
On each board, multiple discussion topics called “threads” can be created. A thread while it is “open”
can be commented upon by any member and moderator, till it is marked as “closed”, which can only
be done by either the creator of the thread or one of the moderators. A moderator of the board can
create as many threads as they want but a normal member can only have one “open” thread in a
board at any given time. A user who is “banned” from a board can neither create threads nor
comment on them, but they can still read them.
Threads can be tagged and searched by those tags.
Backend
Create a Reddit Clone as a Rails app that has following main resources. Feel free to add other
resources as necessary:
● Users
● Boards
● Threads
● Tags
● Comments
Users: Users have username, email and password

Boards: Boards can be created by any user. They have a name and unique + URL safe but also
human and SEO friendly board-ID, which can be used as a path parameter. For example a board
called “Computer Science” can have a board-ID as “computer-science”.

Users can have memberships in multiple boards, and a membership designates them as “moderator”,
“member” or “banned” in that board. The creator of the board is automatically the first “moderator” of
that board. Other users can freely “join” a board and become “members” of that board. The
moderators can promote members to become moderators or demote them to be banned.

Threads: Threads are topics of discussion under boards. They have a title, a description and a
creator. Each thread is created with the status of “open”. While a thread is open, it can be commented
upon (see explanation of comments below), till it is closed. A thread can only be closed by either the
creator of the thread or any of the moderators of the board.
Moderators can create as many threads as they want under the board. Members can also create
threads, but they can only have one open thread at any given time. So if a member wants to create a
new thread, they must close all of their existing open threads first. Users banned from a board cannot
create threads, but they can still read them.

Comments: Comments can be added under a thread. They have a text and an author. There is no
limitation on the number of comments. The only limitation is that banned participants cannot comment
on a thread, but they can still read them. Comments can only be added till a thread is “open”

Tags: A tag is just a name and can have sub-tags. Each thread can be tagged by these tags and
sub-tags. Tags can be added to a thread by it’s creator and any of the board’s moderators. Threads
can be searched by tags.
Frontend
Use Angular to handle the aforementioned operations. Boards can be searched by name or reached
directly using the board-id. Topics within a board can be searched by title and additionally by tags and
sub-tags.

Things To Remember

Do’s
* Write unit tests
* Add proper comments wherever needed
* Use any language of your preference

Don'ts
* Do not spend more than 24 hours
* Do not spend too much time on UI (CSS)
* Do not use any gem for tagging feature
* Do not publish your code on internet

Bonus
* Deployment on Heroku/AWS
* Docs to setup the project (README)
* Seed data

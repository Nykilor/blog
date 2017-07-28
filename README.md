**Blog system**
===============
**Allows you to:**

* create,
* delete,
* edit
  Posts,
* use different templates for user and admin.

----------------------
**Define:**

* route => method => template(**twig**),
* access types.

---------------------------------------
Basic routes:
-------------------------------
* post/id 
* post, post?page=(int), post?limit=(int) (base limit is 10)
* author/id 
* comment/id
* index
* panel
* search?title=#, search?author=#, search?date=*UNIX_TIME_STAMP*> OR search?date=>*UNIX_TIME_STAMP*

**Json routes:**

* json/post/id
* json/post, json/post?page=(int), post?limit=(int)
* json/author/id
* json/comment/id

POST routes (ajax ready):
-------------------------------
* edit/post/id
* create/[post,author,comment]
* delete/[post,author,comment]/id
* edit_self/post/id
* upload/[doc,img]/[1,0] (to turn on/off rewrite)
* logout

---------------------------------

Model methods
===============

Basic:
-------------------------------------------
*All returned data is saved in **Core/Model/Basic::$content***

* **getAllPosts(limit = 0, offset = 0)** - returns all of post columns, authors name and authors id.
* **getOnePost(id)** - returns the posts id, content, date, title, authors id and name.
* **getOneComment(id)** - returns id, content, author and date of the comment.
* **getAllComments(post_id)** - returns id, content, author and date of the comments related with the id.
* **getAuthorPage(id)** - returns posts related with author, and the basic data about author.
* **searchFor(array $vars)** - searches for title, author or date, look to Core\Controller\Basic->search() at array vars.

POST
-------------------------------------------

* **createOne(route, post_data)** - creates a database entity (look at config.php 'routes', default ones are 'author', 'post', 'comment') the 'post_data' is an array: ['column' => 'value', 'column' => 'value' ...].
* **deleteOne(route, id)** - deletes an entity at route.
* **editOne(route, id, post_data, author_id = 0)** - edits the entity at route.
Still work in progress:
-------------------------------------------
* Basic examples.

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
* upload/[doc,img]/(1/0 to allow rewrite)
* logout

---------------------------------
Still work in progress:

* Basic examples.

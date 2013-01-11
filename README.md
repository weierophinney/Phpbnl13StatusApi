Sample API Module for PHPBenelux 2013 Tutorial
==============================================

This is a sample module for use with a PHPBenelux 2013 tutorial delivered by Rob
Allen and Matthew Weier O'Phinney. It is used to demonstrate creating a RESTful
JSON API that uses application/hal+json and application/api-problem+json to
create structured responses that utilize hypermedia.

This particular API allows posting and manipulating "status" -- we will focus on
text-only status and image + text status.

Notes
-----

SQL inheritance:

- http://stackoverflow.com/a/3579462/31459 -- for description
- http://stackoverflow.com/a/11065579/31459 -- for info on how to make it work
  in SQLite (have to enable the foreign key pragma)
- http://www.slideshare.net/billkarwin/sql-antipatterns-strike-back# -- for some
  examples of querying such a structure
- UNANSWERED: do I need transactions to do something like this?

Sample API Module for PHPBenelux 2013 Tutorial
==============================================

This is a sample module for use with a PHPBenelux 2013 tutorial delivered by Rob
Allen and Matthew Weier O'Phinney. It is used to demonstrate creating a RESTful
JSON API that uses application/hal+json and application/api-problem+json to
create structured responses that utilize hypermedia.

This particular API allows posting and manipulating "status" -- we will focus on
text-only status and image + text status.


TODO
----

- Fix StatusDbPersistence::update() to be a proper update, not a patch

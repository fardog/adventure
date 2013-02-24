Adventure
=========

A web-based "Choose your own adventure"-style mash-up engine.
&copy; 2012, 2013 Far Dog LLC  
Licensed under [GPLv2](http://www.gnu.org/licenses/gpl-2.0.txt)


Intro
-----

*Abandon hope all ye who enter here…*

[Adventure](http://adventure.fardogllc.com) was my first project after my self-
imposed web programming retirement, and after a 3 year absence, I was rusty.
What you see here isn't beautiful, but by glob it works.

It was my intention to re-implement an *Adventure 2.0* on Python + Django, but
honestly the idea started to bore me. I'd love to see someone continue on this
project—so here it is! GPL'd and all.

**Yes: I will accept sensible pull requests for merger into the public
[Adventure](http://adventure.fardogllc.com) instance!**


Installation
------------

Adventure is fairly tied to a 
[LAMP](http://en.wikipedia.org/wiki/LAMP_(software_bundle)) stack; it's unlikely
to work out-of-the-box on anything else.

Included under the "application" directory is a *database.sql*, which contains
a dump of the SQL schema. You should run this into your MySQL database, as
Adventure doesn't know how to create a schema by itself.

Database created? Good. Now, you'll need to create the necessary
*application/config/* files. Any that are named **[name]-example.php** will
need to be named to **[name].php** and filled with the appropriate information.
I've marked each one with a comment reading **/** ADVENTURE_REQUIRED **/**.

Once that's complete, you should be nearly rocking. Create yourself a user right
away; adventure places a lot of responsibility on User ID 1; a super-bad
practice but it is what it is.

Certain static pages are generated from the database, such as the terms. You can
check which from the *application/config/routes.php* file.

Submit issues for anything confusing. That is all.

–nate.


Warranty
--------

Copyright 2012, 2013 Far Dog LLC or its affiliates. All Rights Reserved.

Licensed under the GNU General Public License, Version 2.0 (the "License").
You may not use this file except in compliance with the License.
A copy of the License is located at

*http://www.gnu.org/licenses/gpl-2.0.txt*

or in the "LICENSE" file accompanying this file. This file is distributed
on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either
express or implied. See the License for the specific language governing
permissions and limitations under the License.


Misc
----

[Adventure](http://adventure.fardogllc.com) is built using EllisLab's
[CodeIgniter](http://ellislab.com/codeigniter) framework. See the attached
LICENSE file for further details.

The phrase "Choose your own adventure" is owned by whoever it is that still
publishes those books.
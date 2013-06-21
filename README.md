FWIENDS
=======

Creates a group of friends for the supplied facebook app credentials for testing purposes. Writes the username and credentials to a file.
You can use the credentials in the file to log into facebook, or log into a test website using facebook connect.
Users are only able to interact with your app, not the rest of the facebook world.   

Install 
-------

1) Clone / fork the repository 

2) php composer.phar install

Usage 
-----

1) Create a set of friends: **php bin/Fwiend.php fwiends:create --fwiendsFile=/tmp/fwiends.lock [APP ID] [APP SECRET]**

2) Get their login credentials so you can play with them: **cat/tmp/fwiends.lock**

3) Kill them when you're done: **php bin/Fwiend.php fwiends:delete --fwiendsFile=/tmp/fwiends.lock [APP ID] [APP SECRET]**

File Layout: 5 lines with four values. The users ID, the users access_token, email address and password. 

Creating a friendship group will provision five facebook test user accounts for your  facebook app. 
They are called Billie, Nay, Will, Raj and Robyn and have the following relationships: 

Billie is friends with everyone. 

Nay is friends with Billie and Will.

Will is friends with Billie, Nay and Raj.

Raj is friends with Billie and Will

Robyn is friends with Billie.


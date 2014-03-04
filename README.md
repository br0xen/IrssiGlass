IrssiGlass
========================
Drop all of this stuff onto your web server, make a database.sqlite and make sure it is writable by your web server.
Set up a project on the Google Developers Console (https://console.developers.google.com/project) that has access to the Google Mirror API.
Get your OAuth Client ID and Client Secret and put them in the config.php file in addition to filling in the other parameters in that file.
Set up the 'Redirect URIs' and 'Javascript Origins' in the OAuth section of the Developers Console.

The first time you hit the page it should have you authenticate through OAuth. Once you're in you can hit the 'Irssi Setup' button to see what needs to be done in Irssi to make it all work there.

A good place to start if you're struggling is:
https://developers.google.com/glass/quickstart/php
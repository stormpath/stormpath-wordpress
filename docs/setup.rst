.. _setup:

Setup
=====
This section covers the basic setup you need to perform in order to get started with Stormpath WordPress Plugin.

Register For Stormpath
----------------------
Now that you’ve decided to use Stormpath, the first thing you’ll want to do is create a new Stormpath account: https://api.stormpath.com/register

Create an API Key Pair
----------------------

Once you've created a new account, create a new API key pair by logging into
your dashboard and clicking the "Create an API Key" button.  This will generate
a new API key for you, and prompt you to download your key pair.

.. note::
    Please keep the API key pair file you just downloaded safe!  These two keys
    allow you to make Stormpath API requests, and should be properly protected,
    backed up, etc.

Once you've downloaded your `apiKey.properties` file, save it in your home
directory in a file named `~/.stormpath/apiKey.properties`.  To ensure no other
users on your system can access the file, you'll also want to change the file's
permissions.  You can do this by running::

    $ chmod go-rwx ~/.stormpath/apiKey.properties

Create a Stormpath Application
------------------------------

Next, you'll want to create a new Stormpath Application.

Stormpath allows you to provision any number of "Applications".  An
"Application" is just Stormpath's term for a project.

Let's say you want to build a few separate websites.  One site named
"dronewars.com", and another named "carswap.com".  In this case, you'd want to
create two separate Stormpath Applications, one named "dronewars" and another
named "carswap".  Each Stormpath Application should represent a real life
application of some sort.

The general rule is that you should create one Application per website (or
project).  Since we're just getting set up, you'll want to create a single
Application.

To do this, click the "Applications" tab in the Stormpath dashboard, then click
"Register an Application" and follow the on-screen instructions.

.. note::
    Use the default options when creating an Application, this way you'll be
    able to create users in your new Application without issue.

Now that you've created an Application, you're ready to plug Stormpath-Laravel
into your project!

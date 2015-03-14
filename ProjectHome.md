**NEW**: Released Tephlon v1\_1: More features, bugfixes and 3 new useful data structures!

**NEW**: Tutorial wiki page [Usage](Usage.md), [Codeigniter](Codeigniter.md) integration
# What is Tephlon #
Tephlon is a multi-driver (for now FileSystem and MySQL) PHP library that adds a persistence layer to your PHP objects, arrays or variables.

It's mainly file based, but adding a new driver for other kind of internal/external storage is really simple.

It is also very easy to integrate as an external library in popular PHP frameworks like [Codeigniter](http://codeigniter.com/).

![https://dl.dropbox.com/u/33077/CDN/tephlon/logo2012.png](https://dl.dropbox.com/u/33077/CDN/tephlon/logo2012.png)
# Reason behind #
Many websites use SQL without a real reason, just for implementing basic persistence of data.

For example: why would you put users into a SQL table if 90% of your SQL selects on the Users table will have a `"WHERE <primary key> = <value>"`?

Having SQL in your project puts you to:
  * Be prone to SQL injections
  * Writing a lot of ORM code just to map objects to SQL
  * Writing a lot of ORM code just to map SQL to objects
  * Complicated and difficult to automate database dumps for backup.

# Status of development #
Version 1.1 is generally available (just Filesystem driver). MySQL driver works in the SVN version.
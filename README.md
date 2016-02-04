# phpSLWrapper
The ultimate wrapper. The last PHP Framework I will ever do.

SL stands for Suck Less.

Php kind of sucks. You spend your time evaluating var_dump output, wondering why 0 + "test" does not equal "test" + 0 or why strlen and str_replace are named as they are named.
With this framework I can't help you with this. What I can do is enforce clean coding, and give you basic utilities which do most of the work you would have done in any project anyways.

Included so far is:
 - DataService: Save any object to a Database. You do not care about SQL. You do not care if the table already exists. You do not care if you've changed the properties of the objects. You just call saveToDatabase an stop caring.
 - Logger: You may log any of your Faillures or of your informational messages at the same place. You will recieve all messages at the end of execution.
 - a powerful autoloader. Do not care about including any class ever again, the autoloader does this for you. If you use correct namespacing, which is really not so hard to do (name your namespaces like your folder structure eh).
 
What will be included:
 - EmailService. Send an Email. Again, do not care about imap or smtpauth settings. Do not care how to append files, or save the E-Mail to the sent folder. 
 - I recommend using the MVC pattern, so I will create utilities (for example a BaseController) which handles all the POST, GET and FILES parameters.
 - some VERY easy way to put your application on top of the framework
 
I will only include what you really need. This should not become just another oversized framework. Also I will only provide high-level API's (as the DataService), and nothing which you can write in one hour for yourself. 

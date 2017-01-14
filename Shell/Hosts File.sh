sudo vi /etc/hosts
 
#Enter password when prompted
 
#Host file will be opened. Press "i" to edit the file. Move the cursor to the end (or where you want to edit).
#Add a "#" to the beginning of a line to comment (to label the entry)
#Enter domain name then a space then the domain name, for example:
 
198.60.222.202 example.com
 
#This will point example.com to the IP entered instead of where the domain is actually pointed.
 
#Press escape to stop editing
 
#Save and quit with the following
:wq

 
 
 
 
For Windows 8:
 
Right click Notepad and select Run as administrator.
In Notepad, open the following file:
c:\\Windows\\System32\\Drivers\\etc\\hosts
Make the necessary changes to the hosts file.
Click File -> Save to save your changes.

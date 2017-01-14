#Submit the plugin as a .zip file to WordPress:
#The submitted .zip should have all required files including a completed readme.txt
 
#Once accepted, a directory will be made for you (you will get an email)
 
#Official documentation here: https://wordpress.org/plugins/about/svn/
 
#Navigate to the parent directory where the folder will go
#A folder will be made in the next step, so don\'t make the folder itself.
cd ~/Desktop/Wordpress\\ Plugins/
 
#Running this command will make the folder.
#The last part of the path should be the same as the one sent from wordpress
svn co http://plugins.svn.wordpress.org/gearside-developer-dashboard
 
#Drag files into the /trunk directory
 
#Make a "banner" for the listing page as a PNG that is 772x250px named "banner-772x250.png"
#Screenshots should be named "screenshot-1.png", "screenshot-2.png", etc.
#These files should go in the /assets directory
 
#Add the new files through SVN
cd ~/Desktop/Wordpress\\ Plugins/gearside-developer-dashboard/
svn add */*
 
#Commit the added files
svn ci -m \'Adding first version of my plugin\

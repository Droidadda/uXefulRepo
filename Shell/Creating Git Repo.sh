###Creating a local directory of a remote git
#Create a folder that will contain the repo (I generally like to do [GitHub]/Repo-Name/
cd ~/Desktop/Whatever/GitHub/Nebula
git init
git remote add origin https://github.com/chrisblakley/Nebula.git
git pull origin master
 
 
#Modify a file (in Coda or wherever), then replace a file in the local git folder. Then upload it with:
cd ~/Desktop/Whatever/GitHub/Nebula
#Add the files to the "temp add" (or use "." as a wildcard)
git add js/main.js
#or: git add js/.
#or: git add .
#or (best because it adds/removes all changes): git add -A
git commit -m "Comment the update"
git push origin master
 
 
 
#If you are getting permission denied errors, make sure the local directory is writeable!
sudo chmod 777 ~/Desktop/Work\ in\ Progress/\[Resources\]/GitHub/Nebula/

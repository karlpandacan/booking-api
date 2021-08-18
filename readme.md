Project Setup:

	Fork the Project 
	Clone your Fork Project

	git clone git@172.21.0.46:<accountname>/<repositoryname>.git


Changes management

	Add your changes to the fork repository
	Commit your changes to the fork repository
	Once tested, add a new pull request of the changes you made 
	Wait for someone to review your pull request
	Once approved, the changes can now be merge to the master branch

Adding Upstream
    	git remote add upstream git@172.21.0.46:<accountname>/<repositoryname>.git

Merging Upstream Changes
	Commit and push your local changes to your fork repository
	Fetch all changes on the upstream : git fetch upstream
	Merge the changes of the upstream : git merge upstream/master

	If there are conflicts: resolved it and push to your fork repository.

Remember Always To Push to Fork Repository and Only Pull Request to Main Repository.


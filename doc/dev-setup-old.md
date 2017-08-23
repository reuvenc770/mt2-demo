##Dev Environment
If you do not have vagrant, virtualbox, and composer installed please do so before starting.
Make sure to run `composer install` as the box's admin user.

*	run `vagrant box add laravel/homestead`
*  create a new folder named `laravel`
*	Within this folder create a folder `homestead`. (There will be another folder in `laravel` called `mt2` after you clone the repo below.) 
* run `cd homestead`
* run `git clone https://github.com/laravel/homestead.git .`
*	run `bash init.sh`
* 	cd back to laravel, then clone this (MT2 Demo) repo into the folder and call it `mt2`. (`git clone https://USER@bitbucket.org/zetainteractive/mt2-demo.git mt2`)

This will create `Homestead.yaml` configuration file. The Homestead.yaml file will be placed in the `~/.homestead` hidden directory

* open `~/.homestead/Homestead.yaml` in any editor
* make sure `provider` reads `provider: virtualbox`

Time to setup the shared folders.  Make sure that the next section looks simlilar to your setup

	folders:
    	- map: /PATH/TO/LARAVEL/FOLDER
      		to: /home/vagrant/laravel

	sites:
    	- map: mt2.local
      	   to: /home/vagrant/laravel/mt2/public


*	add `192.168.10.10  mt2.local` to your hosts files
*	return to your homestead folder and run `vagrant up`
*       You may have to generate an rsa key; do so with this command: `ssh-keygen -t rsa -C "USER@homestead"` and try running `vagrant up` again.

*	This should bring up your box, going to the url that you mapped your host file to (mt2.local) should bring up the Laravel welcome page..

* cd into your `mt2` directory and create a `.env` file  here is my file, for local dev i use the DB drive for the queue so i can see the jobs pop on and off.

*	ssh into VM by running `vagrant ssh` from the `homestead` folder
* once there cd into `/home/vagrant/laravel/mt2/`
* run `php artisan migrate` this should create all the database tables
* when seed data is done(incomplete)  run `php artisan db:seed`.
* Log into the db `mysql -u homestead -p -h localhost` and run:
*               ``CREATE DATABASE `attribution` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;``
*               ``CREATE DATABASE `mt2_reports` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;``
* Exit the db and run `php artisan db:seed --class=UserRoles`.
* still in `mt2` folder, run `gulp`. You may have to run `npm update` first.

Now your mt2 environment should load.


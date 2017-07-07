https://codeship.com/projects/26fce870-efb1-0134-0287-46ab20fed9d7/status?branch=master
#Getting Started with MT2

##Dev Environment
If you do not have vagrant, virtualbox, and composer installed please do so before starting.
Make sure to run `composer install`.

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

		APP_ENV=local
		APP_DEBUG=true
		APP_KEY=W2QW3gI1dbRQ5Ecf2Det0e6tXvzPuPO0

		DB_HOST=localhost
		DB_DATABASE=homestead
		DB_USERNAME=homestead
		DB_PASSWORD=secret

		CACHE_DRIVER=redis
		SESSION_DRIVER=file
		QUEUE_DRIVER=database

		REDIS_HOST=localhost
		REDIS_PASSWORD=null
		REDIS_PORT=6379

		MAIL_DRIVER=smtp
		MAIL_HOST=mailtrap.io
		MAIL_PORT=2525
		MAIL_USERNAME=null
		MAIL_PASSWORD=null
		MAIL_ENCRYPTION=null

		SQS_KEY=AKIAI5BYPXVNCQ4UGOCQ
		SQS_SECRET=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
		SQS_PREFIX=https://sqs.us-east-1.amazonaws.com/609669786674
		SQS_QUEUE=mt2demo

		SLACK_URL=https://hooks.slack.com/services/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

		MT1_SUPP_DB_HOST=10.96.18.30
		MT1_SUPP_DB_DATABASE=supp
		MT1_SUPP_DB_USERNAME=mt2_read_user
		MT1_SUPP_DB_PASSWORD=Mtread12##

		MT1_SLAVE_DB_HOST=172.31.22.242
		MT1_SLAVE_DB_DATABASE=new_mail
		MT1_SLAVE_DB_USERNAME=mt_report_ro
		MT1_SLAVE_DB_PASSWORD=Tr33Wat#rZ

		MT1_MAIL_DB_HOST=localhost
		MT1_MAIL_DB_DATABASE=mt1mail
		MT1_MAIL_DB_USERNAME=homestead
		MT1_MAIL_DB_PASSWORD=secret

		AWEBER_KEY=Akx7uktCKqzyHa53wS0JPIhp
		AWEBER_SECRET=SO8Oe67Kv6Xh2z7Y7eGHLsxXhXrWFqIf5wvVQHH6

		MT1_SLAVE_DB3_PASS=d#t4pv#R
		MT1_SLAVE_DB3_USER=mt_data_read
		MT1_SLAVE_DB3_HOST=54.209.42.147
		MT1_SLAVE_DB3_PORT=22

		DATAEXPORT_FTP_HOST=ftp-01.mtroute.com
		DATAEXPORT_FTP_USER=espkenaspiremail
		DATAEXPORT_FTP_PASS=pHAquv2f

		SPRINT_CAMPAIGN_FTP_HOST=173.255.229.62
		SPRINT_CAMPAIGN_FTP_USER=60001
		SPRINT_CAMPAIGN_FTP_PASS=U{@y'5<@MraG%_J_

		SPRINT_UNSUB_FTP_HOST=173.255.229.62
		SPRINT_UNSUB_FTP_USER=60000
		SPRINT_UNSUB_FTP_PASS=>HjP`A5nbSvYy&EJ

		ATTR_DB_DATABASE=attribution
		REPORTS_DB_DATABASE=mt2_reports


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

## Running Console Commands & Queues

All these commands shouold be done on the VM itself

###Run Queue Workers
The queue is a general command and will try and work any jobs.
`php artisan queue:listen --sleep=3 --tries=3`

### Firing Command Line Jobs

all console commands are fired the same
`php artisan reports:downloadESP BlueHornet`
you can see  the list by running `php artisan list`

###Firing Specific Gulp Tasks

To run all gulp tasks: `gulp`

All other tasks use the 'run' option.

Example: `gulp --run=deployImages`

Available Tasks:  
`runTdd`  
`deployNodeModules`  
`deploySass`  
`deployTemplates`  
`deployImages`  
`deployBaseAngular`  
`deployMt2Js`  
`deployEspApiJs`  
`deployUserJs`  
`deployRoleJs`  
`deployJobJs`  
`deployShowInfoJs`  
`deployWizardJs`  
`deployYmlpCampaignJs`  
`deployClientJs`  
`deployClientGroupJs`   
`deployListProfileJs`  

## Laravel PHP Framework

[![Build Status](https://travis-ci.org/laravel/framework.svg)](https://travis-ci.org/laravel/framework)
[![Total Downloads](https://poser.pugx.org/laravel/framework/d/total.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Stable Version](https://poser.pugx.org/laravel/framework/v/stable.svg)](https://packagist.org/packages/laravel/framework)
[![Latest Unstable Version](https://poser.pugx.org/laravel/framework/v/unstable.svg)](https://packagist.org/packages/laravel/framework)
[![License](https://poser.pugx.org/laravel/framework/license.svg)](https://packagist.org/packages/laravel/framework)

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable, creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as authentication, routing, sessions, queueing, and caching.

Laravel is accessible, yet powerful, providing powerful tools needed for large, robust applications. A superb inversion of control container, expressive migration system, and tightly integrated unit testing support give you the tools you need to build any application with which you are tasked.

## Official Documentation

Documentation for the framework can be found on the [Laravel website](http://laravel.com/docs).

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](http://laravel.com/docs/contributions).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

### License

The Laravel framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)
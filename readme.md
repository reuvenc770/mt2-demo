#Getting Started with MT2

##Dev Environment
If you do not have vagrant and virutalbox installed please do so before starting

*	run `vagrant box add laravel/homestead`
*  create a new folder named `laravel`
	*	Within this folder create two more folders `homestead` and `mt2`
* run `cd homestead`
* run `git clone https://github.com/laravel/homestead.git .`
*	run `bash init.sh`
* 	cd into the `mt2` folder and clone this (MT2 Demo) repo into the `mt2` folder

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

*	This should bring up your box, going to the url that you mapped your host file to should bring up the Laravel welcome page..

* cd into your `mt2` directory and create a `.env` file  here is my file, for local dev i use the DB drive for the queue so i can see the jobs pop on and off.

		APP_ENV=local
		APP_DEBUG=true
		APP_KEY=W2QW3gI1dbRQ5Ecf2Det0e6tXvzPuPO0

		DB_HOST=localhost
		DB_DATABASE=homestead
		DB_USERNAME=homestead
		DB_PASSWORD=secret

		CACHE_DRIVER=file
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


*	ssh into VM by running `vagrant ssh` from the `homestead` folder
* once there cd into `/home/vagrant/laravel/mt2/`
* run `php artisan migrate` this should create all the database tables
* when seed data is done(incomplete)  run `php artisan db:seed`


## Running Console Commands & Queues

All these commands shouold be done on the VM itself

###Run Queue Workers
The queue is a general command and will try and work any jobs.
`php artisan queue:listen --sleep=3 --tries=3`

### Firing Command Line Jobs

all console commands are fired the same
`php artisan reports:downloadESP BlueHornet`
you can see  the list by running `php arisan list`





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
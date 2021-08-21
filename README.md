# Laravel Authentication and Profile Update

## Tech Specifications
	- "php": "^7.4|^8.0".
    - "laravel/framework": "^8.56".


### Features
	1. Passport Generated Token
		- token validation checking and responses through middleware
	2. Database migration
	3. Authentication
		- Validation for registration 
		- all possible responses
		- Validation for login
		- all possible responses for invalid login and signup
	4. Profile Update
		- User have to login into the system and then he can update his profile.

## Working Procedure
        - By default admin is created when we run the command `php artisan db:seed` and the admin email is `admin@gmail.com`
        - Admin will log into the system with the admin email and password is `123456`.
        - Admin will send an invitation to user with his user email. 
        - User will submit his/her user name and password. A 6 digit code will be generated and sent to the user email. User need to submit the code to complete the registration.
        - Now User can login with email and password and update his/her profile.

## Attachment
	- Json file of postman api collection in the root directory named `laravel authentication.postman_collection.json

## Project setup
	Project setup details are described below step by step: The front end project for this project is [here](https://github.com/zrshishir/product-frontend). First, follow these steps then front end [project](git@github.com:zrshishir/product-frontend) steps
		1. Download or clone the project from [auth](git@github.com:zrshishir/auth.git). 
		2. Go to the project's root directory and run the command `composer install` or `composer update`
		3. After successfully composer updation set up your database credentials on .env file
		4. Run the command `php artisan migrate`
		5. Run the command `php artisan passport:install`
		6. Run the command `php artisan passport:key`
		7. Run the command `php artisan db:seed`
		8. Run the command `php artisan storage:link`
		9. If you need to rollback the database, just run the command `php artisan migrate:rollback`
		10. If you are using LEMP stack then follow proper steps [here](https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-ubuntu-18-04) and if you are using other then run the command `php artisan serve` to get the domain name or service url that will have to be assigned in the [frontend code](git@github.com:zrshishir/product-frontend) `/src/api/product-frontend.js` ROOT_URL const.


### screenshots of project setup procedure details
	The working procedure is described below with screenshots:
	1. To install this project you will have composer installed. You can install this project two ways
		- Download the zip file from the repository and extract it on your pc

		- clone the project using git and the command is `git clone git@github.com:zrshishir/product-frontend.git`. 

![git clone](/screenshots/project_config/git_clone.png)

	2. Go to the project's root directory 

![go to root directory](/screenshots/project_config/go_to_project.png)

	3. run the command `composer update` or `composer install`

![composer upate](/screenshots/project_config/composer_update.png)

	4. Database credential set up

![db set up](/screenshots/project_config/database_config.png)

	5. smtp mail server setup

![smtp configuration](/screenshots/project_config/smtp_config.png)

    6. Run the command `php artisan migrate`

![migration](/screenshots/project_config/migrate.png)

    7. Run the command `php artisan passport:install`

![passport installation](/screenshots/project_config/passport_install.png)

    8. Run the command `php artisan passport:key`

![passport key](/screenshots/project_config/passport_key.png)

    9. Run the command to seed database `php artisan db:seed`

![database seeding](/screenshots/project_config/db_seed.png)

	10. Run the command `php artisan storage:link`

[comment]: <> (![storage link]&#40;/screenshots/terminal_5.png&#41;)

	7. Run the command `php artisan serve` and use this link on the postman url

[comment]: <> (![To run the project]&#40;/screenshots/terminal_6.png&#41;)

### Some screenshots of the project postman api: As I use LEMP stack for my local server environment, I have used domain name in the url
#### you can use localhost or ip. I have attached postman json file for the project. You can use it also. 
	1. Registration api and it's responses

![Email Invitation for Signup](/screenshots/api_details/mail_invitation.png)

	2. User registration with user name and password

![User Registration](/screenshots/api_details/user_register.png)

	3. Code verification 

![code verification](/screenshots/api_details/code_verification.png)

	4. Admin/User login

![Login](/screenshots/api_details/login.png)

	5. Profile update

![Profile Updating](/screenshots/api_details/profile_update.png)

## Thanks

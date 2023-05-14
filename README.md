# todo-api
Todo task fetch and store

# Getting started

## Installation

Clone the repository

    git clone https://github.com/simonishah63/todo-api-practical.git

Switch to the repo folder

    cd todo-api-practical

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env  
    
    for windows 

    copy .env.example .env

Generate a new application key

    php artisan key:generate

## Environment variables

- `.env` - Environment variables can be set in this file

***Note*** : You can quickly set the database information and other variables in this file and have the application fully working.

**Make sure you set the correct database connection information before running the migrations** [Environment variables](#environment-variables)

    php artisan migrate

## Database seeding

**Populate the database with seed data with relationships which includes users, tasks and notes. This can help you to quickly start testing the api or couple a frontend and start using it with ready content.**

Run the database seeder and you're done

    php artisan db:seed

***Note*** : It's recommended to have a clean database before seeding. You can refresh your migrations at any point to clean the database by running the following command

    php artisan migrate:refresh

Start the local development server

    php artisan serve

The api can be accessed at [http://127.0.0.1:8000/api](http://127.0.0.1:8000/api).

## Test User Login Credentials

Email: user@gmail.com

Password: User@123

## API Documentation

 To generate swagger api documentation execute below command 

    php artisan l5-swagger:generate

You can access api documentation using below url 

 http://127.0.0.1:8000/api/documentation


# Fictional Internal Banking Api
The api allows users with the employee role to be able to 
- Create Customers accout 
    - when a customer is being create first time an innitial bank account is created
- Add additional bank account to a customer
- Transfer Money between customer accounts
- View customer account details
- View account transaction history

## How to run the application
- Clone the repository
- install composer dependencies `composer install`
- Create a database and update the .env file with the database credentials
- Run the migration `php artisan migrate`
- Seed the database `php artisan db:seed`
    - It will create an employee account with the following credentials
        - email: employee@bank.com
        - password: password
- Run the application `php artisan serve`
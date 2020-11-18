## Paytabs Assignment

### Preview Live

http://54.162.52.67/

---

### Preview Setup

#### 1. Cloning and installing dependencies & environment:

``` shell
git clone https://github.com/Mohannad-tests/paytabs.git

cd paytabs

composer install

cp env .env
```

#### 2. Point to a local URL:

Edit the `$baseUrl` value in app configuration file `app/Config/App.php`
``` php
	/* ... explicitly and never rely on auto-guessing, especially in production
	| environments.
	|
	*/
	public $baseURL = 'http://54.162.52.67/';

	/*
	|--------------------------------------------------------------------------
	| Index File
	|--------------------------------------------------------------------------
```

To (assuming you will run on `http://localhost:8080`) :
``` php
public  $baseURL  =  'http://localhost:8080/';
```

#### 3. Database:
Open the `.env` file which was newly copied from `env` file in step 1.

At line 50, add database credentials (MySQL preferred):
```
#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------

database.default.hostname = localhost
database.default.database = paytabs
database.default.username = paytabs_db_user
database.default.password = paytabs_db_password
database.default.DBDriver = MySQLi
```

#### 4. Migrate the tables into the database
```
php spark migrate
```
#### 5. Start the server!

``` shell
php spark serve
```
Navigate to: `http://localhost:8080`

----

#### Development Setup

In addition to the preview setup instructions, in development you will neeed to:

#### 6. Enable Debug

Open `.env`. Change `CI_ENVIRONMENT` from `production` to `development`.

#### 7. Install Frontend dependencies:

``` shell
npm install

# run dev script
npm run watch

# run production script
npm run prod
```

----
### Assignment

• Please use Code-igniter Framework and create the project from scratch

• Please use one table design in the database for all categories and subs.

---------------------------------------------------------------------------------------

• Code-igniter application

• Main categories select box

• Unlimited subcategories of parent category.

• Should use Ajax.

Example

• Category A

• Category B

Select Category B

Will create other select box with

• SUB B1

• SUB B2

Selecting Sub B2 will create select box

• SUB SUB B2-1

• SUB SUB B2-2

And so on. 

• Github repo for your project.

---


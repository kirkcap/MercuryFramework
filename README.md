# Mercury
###The first PHP REST CRUD API Builder of the Earth!

Mercury is a software package consisting of a PHP application able to provide REST API for CRUD operations, which are build upon configurations, and an AngularJS Web Application, called Mercury Framework Configuration, which is a User Interface to do easily the Mercury Configuration.

Mercury was built having in mind what was called the **CCR concept**, which means **C***onnect* and **C***onfigure* - your Database(s), your Model(s), Router(s), Controller(s), etc, and **R***un*, which means that, for CRUD operations, you have not to write a single piece of code - doing the configurations in Mercury, it will make available for you the REST Services you need(and if you need some very specific REST service to work joined with the standard CRUD ones, Mercury offer a way to you build them, since you respect some simple rules).

To get Mercury up and running, download a ZIP file from https://github.com/kirkcap/MercuryFramework(I hope in a near future it will support Composer, I´m sorry by the inconvenience now), you will get a small file(3.6M), extract it, and inside the subfolder r1.1 you will have 3 folders, from which you will use only 2: backend - which contains the PHP backend, and frontend - which contains the Web App for configuration.

So, copy the backend folder to a location in your local server(it was developed/tested using WampServer 2.5) out of the www(the public folder), so, supposing you will use it for a project called "my-project", you can create the folder "my-project" under wamp/lamp, and copy the backend folder inside it. After that, copy the index.php file, which is in your downloaded package under r1.1 folder(same level as backend and frontend folders) to the wamp/lamp www folder, to make it public, open it in an editor and locate the following piece of code:

```
/* Adjust the path below to the place in your server where you will host the
 * framework code(out of public_html folder, for security reasons :)
 */
require_once __DIR__.'/../{{your-project-name}}/backend/api.php';
use com\mercuryfw\api as api;
```


And replace the block ***{{your-project-name}}*** with the name you given to the folder created out of www, in this sample, ***my-project***, so, this piece of code must be like this:


```
/* Adjust the path below to the place in your server where you will host the
 * framework code(out of public_html folder, for security reasons :)
 */
require_once __DIR__.'/../my-project/backend/api.php';
use com\mercuryfw\api as api;
```

This will prepare the index.php to instantiate Mercury main class, which will made the magic happens.
After that, you need to create a folder under www, which can have the same name you created out of www, or another name at your choice, in this sample I will sugest "my-project-config", and back to the package you extracted from the ZIP downloaded from Github, go to folder frontend, and copy the folder MercuryFWConfig to the new folder you created under www.

The next step is to open in a editor the constants.js file, which is in the path MercuryFWConfig\js, and locate the following piece of code:

```
    .constant('BackendConfig', {
        url: 'http://localhost:{{port}}/index.php/'
    })
```

Here, adjust the *url* constant according to your local environment, so, supposing your wamp/lamp is listening at ***8090*** port, and you keep the index.php file with this name(you can change it, no problem with that), all you need to do is replace the block ***{{port}}*** with your local server listening port, and this piece of code must be like this:


```
    .constant('BackendConfig', {
        url: 'http://localhost:8090/index.php/'
    })
```

And now, with your lamp/wamp started, you are ready to open the Mercury Framework Configuration App for the first time, open a web browser and type:


```
http://localhost:8090/my-project-config/MercuryFWConfig/
```

And the Mercury Framework Configuration App will load, and you will see this initial screen:

![Mercury Initial Screen](/r1.1/doc_images/Initial.jpg?raw=true "Mercury Initial Screen")

Now the magick begins, lets take as premise to the next steps that we have a MySQL database called foo_bar, with 2 tables, foo and bar:

![The foo_bar MySQL database](/r1.1/doc_images/foo_bar_db.jpg?raw=true "The foo_bar MySQL database")

Keeping this in mind, lets start the Connect & Configuration(or Configuration & Connection... doesn´t matters), so, lets directly to the ***Databases*** option, to configure our database connection:

![Database Connections Initial Screen](/r1.1/doc_images/DatabasesInitial.jpg?raw=true "Database Connections Initial Screen")

Here all available database connection configurations are shown, as it´s the first time we are entering Mercury, there is some the ***default*** connection, which as we will see, is empty. Clicking in ***Edit*** to adjust the configurations, we have the following:

![Database Connection Edit Screen](/r1.1/doc_images/DatabasesEdit.jpg?raw=true "Database Connection Edit Screen")

In this screen, lets fill the parameters as follow:

![foo_bar db connection filled](/r1.1/doc_images/DatabaseDefaultFooBar.jpg?raw=true "foo_bar db connection filled")

After saving these settings, lets configure our first model, going to ***Models*** option:

![Models Configuration Initial Screen](/r1.1/doc_images/ModelsInitial.jpg?raw=true "Models Configuration Initial Screen")

Here, click on ***New Model Configuration*** to start configuring a new model, the following screen will appear:

![New Model Popup](/r1.1/doc_images/NewModel.jpg?raw=true "New Model Popup")

Select the Database Configuration ***default*** which where configured previously:

![Select database connection](/r1.1/doc_images/NewModel_selectDB.jpg?raw=true "New Model - Select database connection")

After Database Connection selected, you can select the ***foo*** table:

![Select database table](/r1.1/doc_images/NewModel_selectTable.jpg?raw=true "New Model - Select database table")

And after Table selected, fill a name for the New Model, for example, ***fooModel***:

![Fill model name](/r1.1/doc_images/NewModel_fillModelName.jpg?raw=true "New Model - Fill model name")

So, clicking in ***Continue***, and the popup will close, showing all Model configuration data to be filled:

![Model data](/r1.1/doc_images/NewModel_form.jpg?raw=true "New Model - Model data")

Fill according with this screenshot, scroll down the form and click in the ***...*** button in front of ***tb_columns***:

![Model data Filled](/r1.1/doc_images/NewModel_formFilled.jpg?raw=true "New Model - Model data filled")

The popup to configure Table Columns behavior will be shown:

![Table Columns Popup](/r1.1/doc_images/NewModel_tbColumns.jpg?raw=true "New Model - Table Columns Popup")

As we can see, Mercury shows here the table structure - all fields available, and some data about them - if is a key, the label defined in the MySQL table structure, allowing to change(not in the database, only for Mercury Configurations), the database field type, and allowing set the default selection order. So, for this basic tutorial we let these fields are they are and click in ***Show/Insert/Update Configuration***, and in the next screen we mark the checkboxes according to this screenshot:

![Config. Show/Insert/Update](/r1.1/doc_images/NewModel_tbColumns_siucfg.jpg?raw=true "New Model - Config. Show/Insert/Update")

With this configuration, all fields will be shown, but only ***foo_name*** and ***foo_desc*** will be available for insert and update by user - as ***foo_code*** is **autonumber** in the table definition, user must not fill or change it, and ***foo_creat*** and ***foo_updat***, will be filled by default values configured in the next screen, clicking in ***Default Data Configuration***:

![Default values config.](/r1.1/doc_images/NewModel_tbColumns_defaultcfg.jpg?raw=true "New Model - Default values config.")

Here, lets configure ***foo_creat*** to be filled by a PHP Function:

![Default value for foo_creat using function](/r1.1/doc_images/NewModel_default_foo_creat_function.jpg?raw=true "New Model - Default value for foo_creat using function")

And lets set it to function ***current_timestamp***(yes, it´s the only function available by now, it´s only to see the possibilities...):

![Using current_timestamp PHP function](/r1.1/doc_images/NewModel_default_foo_creat_function_ct.jpg?raw=true "New Model - Default value for foo_creat using current_timestamp PHP function")

And now, lets configure it to be set only during the insert, and next, do the same for ***foo_updat***, but configuring it to be set only during update, and so we have:

![Defalt config. complete](/r1.1/doc_images/NewModel_default_complete.jpg?raw=true "New Model - Default config. complete")

With this, we have all the model configuration for ***foo*** table done, click in ***Close*** to close the popup, and in ***Save*** in the main form to save the Model configuration, and Mercury will redirect to the initial Model Configuration screen, listing the new model created:

![All existing models](/r1.1/doc_images/ModelsList.jpg?raw=true "Models List - all existing models")

After that, what is needed is to configure a Route to access our created model, so, clicking in ***Routes***:

![Routes initial screen](/r1.1/doc_images/RoutesInitial.jpg?raw=true "Routes Configuration Initial Screen")

Here, lets click in ***New Route Configuration***, and the a form will be opened to configure a New Route, quite simple:

![New Route](/r1.1/doc_images/NewRoute.jpg?raw=true "New Route")

We must fill a name for the route, which will be used in the URL to access the CRUD REST services for our previously configured model, so, lets call the new route ***foo***, this route will use the Mercury controller defined to deal with CRUD operations for any model configured, ***genericCRUDController***, the method assigned will be ***CRUD***, what means that this route will support ***GET(list/show), POST(insert), PUT(update) and DELETE(destroy)*** for the ***fooModel*** model we defined, so, the form filled will be in this way:

![New Route Filled](/r1.1/doc_images/NewRouteFilled.jpg?raw=true "New Route Filled")

So, clicking on ***Save***, our new Route is saved and the app returns to the Routes configured list:

![Available Routes](/r1.1/doc_images/RoutesList.jpg?raw=true "Available Routes")

And now, the REST CRUD services for our ***foo*** table is avialable to use ! Yeah, simple like that ! Supposing we put the index.php on the root of public_html/www folder of our local server, and that our local server port is 8090, all that is needed to access the service for the first time is to call the URL:

```
http://localhost:8090/index.php/foo
```

Doing it on Google Postman, we have:

![First test of foo](/r1.1/doc_images/PostManFoo_FirstTest.jpg?raw=true "First test of foo")

Obviously, there is nothing to show, as the table is empty, so, lets POST a new record to it:

![POST...](/r1.1/doc_images/PostManFoo_CreateFoo.jpg?raw=true "First POST of foo")

Look the answer of the service:

![POST response](/r1.1/doc_images/PostManFoo_CreateFooResponse.jpg?raw=true "Response of first POST of foo")

So, if we send a GET again, we will get this response:

![Showing the list again...](/r1.1/doc_images/PostManFoo_ShowAgain.jpg?raw=true "Showing the list again...")

Note the ***foo_code*** filled by the MySQL autonumber, and the ***foo_creat*** filled with *current_timestamp* as we set in the model configurations. Lets ***PUT*** an update to this foo code 1:

![Updating foo](/r1.1/doc_images/PostManFoo_UpdateFoo.jpg?raw=true "Updating foo with PUT")

And we have as answer:

![Update foo response](/r1.1/doc_images/PostManFoo_UpdateFooResponse.jpg?raw=true "Update foo response")

Getting specifically the foo code 1:

![GET foo 1](/r1.1/doc_images/PostManFoo_GetFoo1.jpg?raw=true "GET foo 1")

As we can see, foo_name and foo_desc where updated, and also foo_updat was updated with the current timestamp, leaving foo_creat with the value set when the record was created. After creating some more records, we have:

![GET all foo´s](/r1.1/doc_images/PostManFoo_MoreFoos.jpg?raw=true "GET all foos")

So, lets try to get some foo containing ```*Sec*``` in the description:

![GET foo by filter query](/r1.1/doc_images/PostManFoo_Filter.jpg?raw=true "GET foo by filter query")

As you see, filtering is very simple, to get all *foo* resources with code between 2 and 4, all needed is something like that:

```
http://localhost:8090/index.php/foo?foo_code|ge=2&foo_code|le=4
```
Where ```|ge``` after the field *foo_code* means to Mercury ```foo_code greater than or equals to...``` and ```|le``` means ```foo_code less than or equals to...```. Other possibilities are:

*|lt* - *less than*
*|gt* - *greater than*

So now, we have a complete set of REST services for CRUD operations for foo, without write a single line of code to have them, that is it. More complete documentation soon.

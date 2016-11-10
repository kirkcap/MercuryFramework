# Mercury
###The first CCR* REST API Builder of the Earth!

#Part I - some explanation...

*Mercury* is a software package consisting of 2 main pieces, a PHP application backend, built basically to get your database model(s) and provide REST services to deal with the available data, based on configurations done in the another piece of the product, an AngularJS Web Application called *Mercury Framework Configuration*, which is a User Interface to do easily all needed configurations, the whole product built having in mind what was called the **CCR concept**, which means **C***onnect* - your database(s), **C***onfigure* - your Model(s), Router(s), Controller(s), Authentication, etc, and **R***un*!


##Why Mercury ?

First of all, the name *Mercury* was given keeping in mind the first planet of our solar system, which is the minor one and also the fastest in his orbit around the Sun, and in the same way, *Mercury* is a software piece which is little in size, but able to be put to work - and do what is it purposes - really fast.

Second, *Mercury* was created from the idea that we are in XXI century, and with the current technologies available, there is no more reason to spend time to write code to deal with single CRUD tasks. There are several frameworks and micro-frameworks existing in the market which support REST services building but, all of them requires some coding to deal with CRUD operations - basically, at least define the model, write the controller with all operations/methods needed, and after that write the router configuration to the corresponding services.

What was in mind when creating *Mercury*, as said by the ***CCR*** concept presented before, was to build something which doesn´t need coding to do these repetitive tasks, you can connect it to your database(s), configure your models - according to the tables and views available in the configured database(s), configure the routes, and all needed REST services will be available, and of course, if there is some need to write more specific REST services - like build complex report data combining several models data, or merge your models data with external services, like geocoding, for a little example - provide the ways to do that with ease - without need to write any SQL code, keeping the standards.
This is the general spirit, go ahead to know a little bit more about the mechanics behind it.


##How does Mercury work ?

All configurations done in the so called *Mercury Framework Configuration* web application, are saved in JSON files in a folder in the path structure of *Mercury* backend, so, when you configure your database(s) connection(s), the settings are saved in a file named *databases.json*, when you create a new model, the settings done are saved to a file named *Models.json*, when you configure a new route, the settings are saved in a file named *routes.json*, all these files(there are more, these are the main ones get for example now) being available for the *Mercury* PHP backend, the heart of *Mercury* itself.

Inside *Mercury* backend, there is a model class called ***genericModel***, which when instantiated with a given Model name as parameter, reads the corresponding *Models.json* file to get the model settings, and you have a complete Model class to deal with that specific entity, with all configured operations available, and knowing from/to which database read/write data. Also, there is a controller class called ***genericCRUDController***, which is able to do all the basic CRUD tasks, instantiating the correct model through ***genericModel*** class and doing all operations needed according to the specific route configured - which is managed by the ***Router*** class, which receives the route called by the http request received, identify in the *routes.json* the specific route being called, identify the allowed methods according to the route settings, and delivers to the main API the controller to be called, the method to be executed by it, and the model to be used.

There is much more to know about *Mercury*, but these 3 pieces composes the heart, the secret recipe´s main ingredients behind it.



##What does Mercury actually support?

- The main feature: you can get your existing Database(s) and make their data available by REST services in a couple of minutes(logically, if you have a huge number of tables to be configured, it will spend more time, but I assure you that doesn´t matter the number of tables, 1 or 1000, configure it and make available as REST services through *Mercury* will be faster than write all the code needed in any existing framework in the market to do the same).
- MySQL(using mysqli and PDO) and PostGres(PDO) Databases;
- Authentication using JWT(with your own database model - you don´t need to create a specific table with a specific structure, you indicate your database table in which are saved the user credentials, indicate what are the Login and Password fields, and *Mercury* deals with it accordingly), for specific routes(you can configure routes to be acessed with our without need of token authentication);
- Automatic filling of table fields indicated to be filled with Token id, from the Token captured in the HTTP Request;
- CORS;
- Composed key tables;
- Different behavior for specific table fields(you can configure which fields must be returned when querying a model, which fields can be inserted/updated by the user, and define default values for insert/update fields);
- Logging;
- Filtering;
- Sorting;
- Paginating;



##What does Mercury is expected to support in a near future?

- More databases;
- Authorization profiles(without need of specific tables in your database model);
- BLOB files related to database records;
- Suggestions please ??


##Finally...

Is *Mercury* perfect? Of course not(yet!), there is much more to be improved and delivered, but it already shows that is possible to build softwares to bring high level productivity, allowing focus on what really matters instead of struggling with repetitive tasks that doesn´t make sense anymore, bringing great gains for the customers and projects, with respect in development time and quality - as the process is fast to be done and almost all standardized by configurations. Additionally, is also a great example of what can be done with a little bit of engineering and inovation spirit, combined with the desire to build something to cause a big impact. Said that, go ahead to put your *Mercury* instance to work and see by yourself how it can be a simple and powerfull tool to accelerate your developments.



#Part II - lets make the things happens...

##Installing Mercury

To get *Mercury* up and running, download a ZIP file from ```https://github.com/kirkcap/MercuryFramework```(I hope in a near future it will support Composer, I´m sorry by the inconvenience now), you will get a small file(11.3M), extract it, and inside the subfolder ***r1.1*** you will have 3 folders, from which you will use only 2: ***backend*** - which contains the PHP backend, and ***frontend*** - which contains the AngularJS Web App for configuration.

So, copy the ***backend*** folder to a location in your local server(it was developed/tested using WampServer 2.5) *out* of the ***www***(the public folder), so, supposing you will use it for a project called ***my-project***, you can create the folder ***my-project*** under wamp/lamp, and copy the ***backend*** folder inside it. After that, copy the ***index.php*** file, which is in your downloaded package under ***r1.1*** folder(same level as ***backend*** and ***frontend*** folders) to the wamp/lamp ***www*** folder, to make it public, so open it in an editor and locate the following block of code:

```
/* Adjust the path below to the place in your server where you will host the
 * framework code(out of public_html folder, for security reasons :)
 */
require_once __DIR__.'/../{{your-project-name}}/backend/api.php';
use com\mercuryfw\api as api;
```


And replace the block ***{{your-project-name}}*** with the name given to the folder created out of www, in this sample, ***my-project***, so, this block of code must be like this:


```
/* Adjust the path below to the place in your server where you will host the
 * framework code(out of public_html folder, for security reasons :)
 */
require_once __DIR__.'/../my-project/backend/api.php';
use com\mercuryfw\api as api;
```

This will prepare the ***index.php*** to instantiate *Mercury* main class, which will make the magick happens.
After that, you need to create a folder under ***www***, which can have the same name you created out of www, or another name at your choice, in this sample I will sugest ***my-project-config***, and back to the package you extracted from the ZIP downloaded from Github, go to folder ***frontend***, and copy the folder ***MercuryFWConfig*** to the new folder you created under ***www***.

The next step is to open in a editor the ***constants.js*** file, which is in the path ***MercuryFWConfig\js***, and locate the following block of code:

```
    .constant('BackendConfig', {
        url: 'http://localhost:{{port}}/index.php/'
    })
```

Here, adjust the ***url*** constant according to your local environment, so, supposing your wamp/lamp is listening at ***8090*** port, and you keep the ***index.php*** file with this name(you can change it, no problem with that, but adjust here to the correct name), all you need to do is replace the block ***{{port}}*** with your local server listening port, and this block of code must be like this:


```
    .constant('BackendConfig', {
        url: 'http://localhost:8090/index.php/'
    })
```

And now, with your lamp/wamp started, we are ready to open the *Mercury Framework Configuration* App for the first time, open a web browser and type:


```
http://localhost:8090/my-project-config/MercuryFWConfig/
```

And the *Mercury Framework Configuration* App will load, and you will see this initial screen:

![Mercury Initial Screen](/r1.1/doc_images/Initial.jpg?raw=true "Mercury Initial Screen")




##Connecting, Configuring and Running !

Now the most interesting part begins, which is Connect, Configure and Run our REST Services. Lets start !



###foo_bar database

To start this little CCR Tutorial, lets take as premise to the next steps that we have a MySQL database called ***foo_bar***, with 2 tables, ***foo*** and ***bar***:

![The foo_bar MySQL database](/r1.1/doc_images/foo_bar_db.jpg?raw=true "The foo_bar MySQL database")

And keeping this in mind, lets go ahead.


###Connecting to Database

To connect *Mercury* with our foo_bar database, we must go directly to the ***Databases*** option:

![Database Connections Initial Screen](/r1.1/doc_images/DatabasesInitial.jpg?raw=true "Database Connections Initial Screen")

Here all available database connection configurations are shown, and as it´s the first time we are entering Mercury, there is only the ***default*** connection, which as we will see, contains empty parameters. Clicking in ***Edit*** to adjust the configurations, we have the following:

![Database Connection Edit Screen](/r1.1/doc_images/DatabasesEdit.jpg?raw=true "Database Connection Edit Screen")

In this screen, we must fill the parameters as follow:

![foo_bar db connection filled](/r1.1/doc_images/DatabaseDefaultFooBar.jpg?raw=true "foo_bar db connection filled")
*(Mercury currently supports MySQL - with mysqli or PDO driver, and PostGres with PDO driver)*

And save our settings, clicking in ***Save*** button.



###Configuring Model for foo

The next step is to configure our first model, to do that, we must go to ***Models*** option:

![Models Configuration Initial Screen](/r1.1/doc_images/ModelsInitial.jpg?raw=true "Models Configuration Initial Screen")

Here, click on ***New Model Configuration*** to start the configuration of a new model, and the following screen will appear:

![New Model Popup](/r1.1/doc_images/NewModel.jpg?raw=true "New Model Popup")

Select the Database Configuration ***default*** which where configured previously:

![Select database connection](/r1.1/doc_images/NewModel_selectDB.jpg?raw=true "New Model - Select database connection")

After Database Connection selected, we can select the ***foo*** table(note that all database tables are available, and existing database views are included here to be selected also):

![Select database table](/r1.1/doc_images/NewModel_selectTable.jpg?raw=true "New Model - Select database table")

And after Table selected, fill a name for the New Model, for example, ***fooModel***:

![Fill model name](/r1.1/doc_images/NewModel_fillModelName.jpg?raw=true "New Model - Fill model name")

So, clicking in ***Continue***, the popup will close, showing all Model configuration data to be filled:

![Model data](/r1.1/doc_images/NewModel_form.jpg?raw=true "New Model - Model data")

Fill the form parameters according with the screenshot below, scroll down the form and click in the ***...*** button in front of ***tb_columns***:

![Model data Filled](/r1.1/doc_images/NewModel_formFilled.jpg?raw=true "New Model - Model data filled")

Clicking in the ***...*** opens the ***Model Fields Configuration*** popup, which is used to configure Table Columns behavior:

![Table Columns Popup](/r1.1/doc_images/NewModel_tbColumns.jpg?raw=true "New Model - Table Columns Popup")

As we can see, *Mercury* shows here the table structure - all fields available, and some data about them - if it is a key, the label defined in the MySQL table structure, allowing to change(not in the database, only for Mercury Configurations, and I must say, for the REST services it´s currently useless, but I have some ideas for the future...), the database field type, the bind type(used to mysqli driver) and allowing set the default selection order. So, for this basic tutorial we let these fields are they are and click in ***Show/Insert/Update Configuration*** tab, which as his name indicates, contains options to configure which fields must be Shown(returned by a GET), Inserted(filled/informed by user during a POST) and Updated(changed by user during a PUT). For the purposes of this tutorial, in this screen we must mark the checkboxes according to this screenshot:

![Config. Show/Insert/Update](/r1.1/doc_images/NewModel_tbColumns_siucfg.jpg?raw=true "New Model - Config. Show/Insert/Update")

With this configuration, all fields will be shown, but only ***foo_name*** and ***foo_desc*** will be available for insert and update by user - as ***foo_code*** is **autonumber** in the table definition, user must not fill or change it, and ***foo_creat*** and ***foo_updat***, will be filled by default values to be configured in the next screen, clicking in ***Default Data Configuration*** tab:

![Default values config.](/r1.1/doc_images/NewModel_tbColumns_defaultcfg.jpg?raw=true "New Model - Default values config.")

Here, we can configure default values for the table fields and in which moment the default values will be set, so lets configure ***foo_creat*** to be filled by the returned value of a PHP Function:

![Default value for foo_creat using function](/r1.1/doc_images/NewModel_default_foo_creat_function.jpg?raw=true "New Model - Default value for foo_creat using function")

And lets set it to use PHP function ***current_timestamp***(yes, it´s the only function available by now, it´s only to see the possibilities...):

![Using current_timestamp PHP function](/r1.1/doc_images/NewModel_default_foo_creat_function_ct.jpg?raw=true "New Model - Default value for foo_creat using current_timestamp PHP function")

And now, lets configure it to be set only during the insert, and next, do the same for ***foo_updat***, but configuring it to be set only during update, and so we have:

![Defalt config. complete](/r1.1/doc_images/NewModel_default_complete.jpg?raw=true "New Model - Default config. complete")

With this, we have all the model configuration for ***foo*** table done, click in ***Close*** to close the popup, and in ***Save*** in the main form to save the Model configuration, and *Mercury* will redirect to the initial Model Configuration screen, listing the new model created:

![All existing models](/r1.1/doc_images/ModelsList.jpg?raw=true "Models List - all existing models")




###Configuring Route for foo

Now, with our Model already configured, what is needed is to configure a Route to access our model data, so, clicking in ***Routes*** option, we have:

![Routes initial screen](/r1.1/doc_images/RoutesInitial.jpg?raw=true "Routes Configuration Initial Screen")

Here, click in ***New Route Configuration***, and a form will be opened to configure a New Route, quite simple:

![New Route](/r1.1/doc_images/NewRoute.jpg?raw=true "New Route")

We must fill a name for the route, which will be used in the URL to access the CRUD REST services for our previously configured model, so, lets call the new route ***foo***, this route will use the *Mercury* controller defined to deal with CRUD operations for any model configured, ***genericCRUDController***, the method assigned will be ***CRUD***, what means that this route will support ***GET(list/show), POST(insert), PUT(update) and DELETE(destroy)*** for the ***fooModel*** we defined, so, the form filled will be in this way:

![New Route Filled](/r1.1/doc_images/NewRouteFilled.jpg?raw=true "New Route Filled")

Now, clicking on ***Save***, our new Route is saved and the app returns to the Routes configured list:

![Available Routes](/r1.1/doc_images/RoutesList.jpg?raw=true "Available Routes")



###RUNning foo !!

Congratulations!! Now, with Connect & Configuration done, we arrived to the last phase: RUN !
The REST CRUD services for our ***foo*** table are avialable to use ! Yeah, simple like that ! Supposing we put the index.php on the root of public_html/www folder of our local server(as explaining in the ***"Installing Mercury"*** section), and that our local server port is 8090, all that is needed to access the service for the first time is to call the URL:

```
http://localhost:8090/index.php/foo
```

Doing it on *Google Postman*, we have:

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

As we can see, ***foo_name*** and ***foo_desc*** where updated, and also ***foo_updat*** was updated with the current timestamp, leaving ***foo_creat*** with the value set when the record was created. After creating some more records, we have:

![GET all foo´s](/r1.1/doc_images/PostManFoo_MoreFoos.jpg?raw=true "GET all foos")

So, lets try to get some foo containing ```*Sec*``` in the description:

![GET foo by filter query](/r1.1/doc_images/PostManFoo_Filter.jpg?raw=true "GET foo by filter query")

As you see, filtering is very simple, to get all *foo* resources with code between 2 and 4, all needed is something like that:

```
http://localhost:8090/index.php/foo?foo_code|ge=2&foo_code|le=4
```
Where ```|ge``` after the field ***foo_code*** means to *Mercury* ```foo_code greater than or equals to...``` and ```|le``` means ```foo_code less than or equals to...```. Other possibilities are:

*|lt* - ***less than***

*|gt* - ***greater than***




##Another CCR Sample(a little bit more complex)...

Well, let´s make the things more interesting, and configure Model and Route for ***bar***, which was defined as related to ***foo***, composing a master-detail relationship:

![bar table structure](/r1.1/doc_images/BarTableStructure.jpg?raw=true "Bar Table Structure")

With this, we will explore a little bit more the *Mercury* available resources.


###Configuring Model for bar

As we could see in the bar table structure in the previous screenshot, ***foo_code***, which is the primary key for ***foo*** table, is part of the primary key of ***bar*** table, joined with ***bar_code*** field. Lets configure a model to it, so in the same way as we did to configure ***foo***, go to ***Models***, click in ***New Model Configuration***, select the Database Config(in our case, ***default*** again), select the table ***bar***, and fill a name for the new Model, ***barModel***:

![bar model definition](/r1.1/doc_images/BarModelDefinition.jpg?raw=true "Bar Model Definition")

Now click in ***Continue***, and fill the remaining form fields as this:

![bar model form filled](/r1.1/doc_images/BarModelFormFilled.jpg?raw=true "Bar Model Form Filled")

Next, click in the ***...*** button in front of ***tb_columns***, to open the ***Model Fields Definition*** popup, go to ***Show/Insert/Update Configuration*** tab, and fill it according this:

![bar model fields definition](/r1.1/doc_images/BarModelFieldsDefinition.jpg?raw=true "Bar Model Fields Definition")

With these settings, will be possible to show(select) all bar fields, insert ***foo_code*** and ***bar_comt***, and update only ***bar_comt***, now we must define the default values for some fields, going on ***Default Data Configuration***, and filling it like that:

![bar model fields default](/r1.1/doc_images/BarModelFieldsDefault.jpg?raw=true "Bar Model Fields Default")

With these settings, when a new bar resource is created, ***bar_code*** field will be filled with result of a *subquery* with ***MAX*** on ***bar_code*** + 1, where ***foo_code*** equals to the value informed to it in the REST call(we will see about this soon), and ***bar_creat*** and ***bar_updat*** will be filled with ***current_timestamp*** PHP function, during insert and update respectively. Now we click in ***Close***, and in the Model Form we click in ***Save***, and now we have 2 models in the Models list:

![Models list updated](/r1.1/doc_images/ModelsListWithBar.jpg?raw=true "Models List Updated")



###Configuring Route for bar

The next step is to create a new Route for ***bar***, and at this moment I must explain that the *Mercury* way to deal with routes for master-detail tables is doing it in the following format: ```<master route>.<detail>```. In this way, *Mercury* will know that when we are trying to access something like that:

*http://{server address}/{path to mercury index.php}/****{master route}/{key}/{detail}***

What we want is to access the *detail route model data*, where the *master key* = ***{key}***, so keeping this in mind, as ***bar*** was defined as detail for ***foo***, the correct way to define route for ***bar***, is call it ***foo.bar***. To do that, we go to ***Routes*** option and click in ***New Route Configuration***, and we must fill the form parameters in this way:

![Bar route definition](/r1.1/doc_images/BarRouteDefinition.jpg?raw=true "Bar Route Definition")

We use again the ***genericCRUDController***, and ***CRUD*** method, indicating the ***barModel*** previously defined, save it, and it is done, the Routes List will be like this:

![Routes list updated](/r1.1/doc_images/RoutesListUpdated.jpg?raw=true "Routes List Updated")



###RUNning bar !!!

Now we have a set of REST services for ***bar***! Testing it with ***GET***, to read all ***bar*** related to ***foo*** with ***foo_code=1***:

![foo.bar first GET](/r1.1/doc_images/FooBarFirstGET.jpg?raw=true "foo.bar first GET")

POSTing a new ***bar***:

![foo.bar first POST](/r1.1/doc_images/FirstBarCreated.jpg?raw=true "foo.bar first POST")

GETting specifically ***bar*** with ***foo_code = 1*** and ***bar_code = 1***:

![GET foo/1/bar/1](/r1.1/doc_images/Bar1Foo1.jpg?raw=true "GET foo/1/bar/1")

PUTting an update:

![PUT foo/1/bar/1](/r1.1/doc_images/UpdateFoo1Bar1.jpg?raw=true "PUT foo/1/bar/1")

GETting ***bar*** for ***foo=2*** where ```bar_comt like "Second*"```:

![GET foo/2/bar with filter](/r1.1/doc_images/FilterBarForFoo2.jpg?raw=true "GET foo/2/bar with filter")



###End of second act(by now...)

So now, after these simple steps, we have a complete set of REST services for CRUD operations for ***foo*** which is a single key entity, and also for ***bar*** which is a composed key entity depending of ***foo***, without writing a single line of code to have them. That is it, this is the central idea regarding *Mercury*. Wait for more complete documentation soon.

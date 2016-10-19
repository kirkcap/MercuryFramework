# Mercury
###The first PHP REST CRUD API Builder of the Earth!

Mercury is a software package consisting of a PHP application able to provide REST API for CRUD operations, which are build upon configurations, and an AngularJS Web Application, called Mercury Framework Configuration, which is a User Interface to do easily the Mercury Configuration.

Mercury was built having in mind what was called the **CCR concept**, which means **C***onnect* and **C***onfigure* - your Database(s), your Model(s), Router(s), Controller(s), etc, and **R***un*, which means that, for CRUD operations, you have not to write a single piece of code - doing the configurations in Mercury, it will make available for you the REST Services you need(and if you need some very specific REST service to work joined with the standard CRUD ones, Mercury offer a way to you build them, since you respect some simple rules).

To get Mercury up and running, download a ZIP file from https://github.com/kirkcap/MercuryFramework(I hope in a near future it will support Composer, I´m sorry by the inconvenience now), you will get a small file(3.6M), extract it, and inside the subfolder r1.1 you will have 3 folders, from which you will use only 2: backend - which contains the PHP backend, and frontend - which contains the Web App for configuration.

So, copy the backend folder to a location in your local server(it was developed/tested using WampServer 2.5) out of the www(the public folder), so, suposing you will use it for a project called "my-project", you can create the folder "my-project" under wamp/lamp, and copy the backend folder inside it. After that, copy the index.php file, which is in your downloaded package under r1.1 folder(same level as backend and frontend folders) to the wamp/lamp www folder, to make it public, open it in an editor and locate the following piece of code:

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

And the Mercury Framework Configuration App will load.

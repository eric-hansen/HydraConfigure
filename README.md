HydraConfigure
===============

This is a PHP wrapper to the API for HydraConfig.

Also, this is my first real attempt at supporting Composer.

## Testing

PHPUnit is used for unit testing and the like.  Look at the ```./tests/``` folder for the available unit tests.

Running PHPUnit (assuming in ```./vendor``` and running in ```.```) will look like this:

```
vendor/bin/phpunit -c phpunit.xml
```

## Using

Its advised to create a new config.json file in EricHansen/HydraConfigre/ or otherwise somewhere accessible, and
create a new HydraConfigure instance that way:

```
$hdyra = new HydraConfigure("", "", "path/to/config.json");
```

Your ```config.json``` file is a simple JSON file formatted like so:

```
{
    "id": "clientId",
    "secret": "clientSecret"
}
```

Then simply call the HTTP verb along with the API and any arguments:

```
$hydra->post("config", array("new_config_stuff_here"));
$hydra->get("config");
$hydra->put("watch", array("config_to_watch"));
```

## TO DO

### Unable to pass arguments to some API calls

As the HydraConfigure class functions via __call(), passing arguments isn't exactly easy for some features.

I.e.: right now you cannot call ```$hydra->get("config", array("prod,qa"));``` to get the production or QA configs,
this is partially in fault of PHP.  However, there are workarounds that will be implemented.
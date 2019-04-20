# Upgrade from 3.2.1 to 3.3.0

## Configuration

### Passing Serial

With the new `--serial` parameter, it is possible to pass the unique serial, which will be used for the import 
process, as commandline option. Instead of generating an UUID, the passed serial will be used to generate the
temporary import directory that contains the import artefacts as well as persisting the temporary import data
during the import process.

### Events

This release comes with additional events as well as the possiblity to register events on operation level.

#### New Events

The new events `app.set.up` and `app.tear.down` has been added. The events will be fired when the
main application, that processes the import, will be started and shutdown.

The event `app.tear.down` will also be invoked when an error occurs and the import process has 
been stopped unexpected.

#### Events on Operation Level

Beside to possiblity to register global events, up with this version it is possible to register events on
operation level. This avoids execution of events that only provide functionality for a dedicated operation
e. g. a listener that exports products only in add-update operation like 

```json
{
  ...,
  "operations" : [
    {
      "name" : "add-update",
      "listeners" : [
        {
          "subject.artefact.process.success" : [
            "my_library.listeners.export.something"
          ]
        }
      ]
    },
    ...
  ]
}
```

### New Listeners

The functionality to initialize the registry with the basic import information has been extracted from the main
application into generic listeners. Therefore it is mandatory to add these listeners to the configuration file, 
e. g.

```json
{
  ...,
  "listeners" : [
    {
      "app.set.up" : [
        "import.listener.render.ansi.art",
        "import.listener.initialize.registry"
      ]
    },
    {
      "app.tear.down" : [
        "import.listener.clear.registry"
      ]
    }
  ],
  ...
}
```

### Composite Observers

To make configuration less complex, this version comes with a set of composite observers that combines
the default observers. Configuration of the composite observers will take place in the DI configuration
wheras in in most scenarios the configuration doesn't need to be touched.

As the composition of the observers didn't change since version 3.2.1, it's not mandatory to update the
configuration file, but it's recommended.

Have a look at the sample configuration files to see the necessary modifications.
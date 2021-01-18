# Upgrade from 3.8.37 to 3.8.38

## Hook Aware Observers

Up from this version, it is also possible to create hook aware observers. As for the subjects, for 
observers the hooks `setUp()` and `tearDown()` are available. To use the hooks, the observer has to
implement the interface `TechDivision\Import\Interfaces\HookAwareObserverInterface`.

> Please be aware, that the new hook functionality is **NOT** available when using the composite observer. 
> As of backwards compatibility reasons this functionality will be added with the first 4.0.0 release.

## Deprecations

### Deprecated Classes

The following classes have been deprecated will be replace in the upcoming 4.0.x release

* `TechDivision\Import\Observers\GenericValidatorObserver` use `TechDivision\Import\Observers\GenericValidationObserver` instead
* `TechDivision\Import\Observers\GenericColumnCollectorObserver` use `TechDivision\Import\Observers\GenericHookAwareColumnCollectorObserver` instead
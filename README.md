# Api Interceptor 

ApiPlatform does not use controllers. Although the symphony framework is only built on the concept of `controller`. An interceptor is an attempt to replace controller functions. This is something that is required when you do not have a controller, but only a resource object.

## Description

The interceptor method runs at the specified `level` when the specified request `method` is called. You can pass some `attributes`. The `Intercept` method receives _request_ objects and _events_. The third parameter is the _attributes_ for the interceptor.

## Annotations

```php
/**
* @Interceptor("App\Interceptor\ResourceInterceptor")
**/
class ResourceEntity
{
    //
}

class ResourceInterceptor
{
    /**
     * @Intercept("read", method="PUT")
     */
    public function update()
    {
        //
    }
}
```

### Interceptor Annotation
 
Indicates the interceptor class for this resource.

### Intercept Annotation

Specified for an interceptor class method. Indicates at which point to intercept execution. And run this method.

```php
// Parameters
$level;
$method;
$attributes;
```

#### Levels

Initial concept
* Init - first stage of request
* Read - request reading moment
* Write - record response result
* Audit - after sending a response
* Finish - terminal stage of request

Additional
* preRead postRead
* preWrite postWrite

#### Methods

GET, POST, PUT, PATCH, DELETE

#### Attributes

Not necessary. Something to pass to the interceptor method.


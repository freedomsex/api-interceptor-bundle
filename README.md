# Api Interceptor 

> BETA. 1.0.* - these are drafts

ApiPlatform does not use controllers. Although the symphony framework is only built on the concept of `controller`. An interceptor is an attempt to replace controller functions. This is something that is required when you do not have a controller, but only a resource object.

## Description

The interceptor method runs at the specified `level` when the specified request `method` is called. You can pass some `attributes`. The `Intercept` method receives  _events_ object. The second parameter is the _attributes_ for the interceptor.

At the moment, you can intercept a specific resource using the `Interceptor` or any request using the `RequestHandler`.

## Annotations

```php
/**
* @Interceptor("App\Interceptor\ResourceInterceptor")
**/
class ResourceEntity
{
    //
}

class ResourceInterceptor implements InterceptorInterface
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

The interceptor class must be declared as a `public` service. You can  set the `tag` _"interceptor"_ for the interceptor service. You can simply implement the `InterceptorInterface` interface. Then the tag will be automatically installed and the service will be public.

### Interceptor Annotation
 
Indicates the interceptor class for this resource.

### Intercept Annotation

Specified for an interceptor class method. Indicates at which point to intercept execution. And run this method. Сase is not important.

```php
// Parameters
$level;
$method;
$attributes;
```

#### Levels

Event Listeners Api-Platform default. Сase is not important

Constant           | Event             | Priority |
-------------------|-------------------|----------|
`PRE_READ`         | `kernel.request`  | 5        |
`POST_READ`        | `kernel.request`  | 3        |
`PRE_DESERIALIZE`  | `kernel.request`  | 3        |
`POST_DESERIALIZE` | `kernel.request`  | 1        |
`PRE_VALIDATE`     | `kernel.view`     | 65       |
`POST_VALIDATE`    | `kernel.view`     | 63       |
`PRE_WRITE`        | `kernel.view`     | 33       |
`POST_WRITE`       | `kernel.view`     | 31       |
`PRE_SERIALIZE`    | `kernel.view`     | 17       |
`POST_SERIALIZE`   | `kernel.view`     | 15       |
`PRE_RESPOND`      | `kernel.view`     | 9        |
`POST_RESPOND`     | `kernel.response` | 0        |
  
Initial Shortcut concept
* Init(_REQUEST_) - first stage of request
* **Read**(_POST_DESERIALIZE_) - request `data reading` moment
* **Write**(_PRE_SERIALIZE_) - `record data` response result
* Audit(_TERMINATE_) - after sending a response

Additional(with 0 priority)
* REQUEST `kernel.request`
* VIEW `kernel.view`
* RESPONSE `kernel.response`
* TERMINATE `kernel.terminate`

#### Methods

GET, POST, PUT, PATCH, DELETE

#### Attributes

Not necessary. Something to pass to the interceptor method.

---

## RequestHandler

Create a request interceptor class. Apply the `RequestHandlerInterface` interface. You can use the _request.handler_ tag. Set the interceptor annotation to the selected method.

```php
use FreedomSex\ApiInterceptorBundle\Contract\RequestHandlerInterface;

class SomeHandler implements RequestHandlerInterface
{ 
    /**
     * @Intercept("PRE_SERIALIZE", method="GET")
     */
    public function someMethod($event)
    {
        //
    } 
}
```

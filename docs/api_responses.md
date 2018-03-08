# Using API Responses
There are two types of API responses upon a successful request. If a single resource is requested then an `ApiResource` is 
returned, if multiple resources are requested then an `ApiResourceSet` is returned.

## The `ApiResourceSet` class
When the API returns multiple resources, for example when getting an overview page, an instance of the `ApiResourceSet` class
is returned. The instance of this class contains the requested resources. Traverse each individual resource by using a
`foreach`-loop on the instance:

```php
$certificates = $client->resource('certificates')->get();

foreach ($certificates as $certificate) {  
    // Each item is an instance of an ApiResource.
    echo $certificate->id."\n";
}
```

## The `ApiResource` class
Each resource returned by the API is transformed to an `ApiResource` instance. This makes it possible to have easy access
to the attributes, type and ID of the resource. Each of these fields can be accessed as if it is a property on the class:

```php
$certificate = $client->resource('certificates')->get('VX09kwR3KxNo');

echo "ID: ".$certificate->id."\n";
echo "Domain: ".$certificate->domain."\n";
echo "Wildcard: ".$certificate->wildcard."\n";
// etc.
```

## Relations
A resource can have multiple relations to one or more resources. To access a relation you can call the relation name
as if it were a method on `ApiResource`. A prepared instance of a new request is returned that can be retrieved by
calling its `get()` method (thus returning an `ApiResource` or `ApiResourceSet`):

```php
$certificate = $client->resource('certificates')->get('VX09kwR3KxNo');

$domainResource = $certificate->domain()->get();
```

If you only want the data of the relation in the `ApiResource` itself, you can get it by using the `raw()` method. This
will return a (multidimensional) array with the resource type and id:

```php
$domainRelationData = $certificate->domain()->raw();
```

---

[&laquo; Making API Calls](calls.md) | [Back to the index](index.md) | [Exceptions &raquo;](exceptions.md)

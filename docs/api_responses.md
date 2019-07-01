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
    echo $certificate->id()."\n";
}
```

## The `ApiResource` class
Each resource returned by the API is transformed to an `ApiResource` instance. This makes it possible to have easy access
to the attributes, resourceType and ID of the resource. Each of these fields can be accessed as if it is a property on the class:

```php
$certificate = $client->resource('certificates')->id('VX09kwR3KxNo')->get();

echo "ID: ".$certificate->id()."\n";
echo "Domain: ".$certificate->attribute('domain')."\n";
echo "Wildcard: ".$certificate->attribute('wildcard')."\n";
// etc.
```

## Relations
A resource can have multiple relations to one or more resources. To access a relation you can call the `relation` or `relationship` method on `ApiResource`.
A prepared instance of a new request is returned that can be retrieved by calling its `get()` method (thus returning an `ApiResource` or `ApiResourceSet`):

```php
$certificate = $client->resource('certificates')->id('VX09kwR3KxNo')->get();

$domainResource = $certificate->relation('domain')->get();
```

If you only want the resource identifiers, you can get it by using the `getResourceIdentifiers()` method. This
will return a (`ApiResourceSet` with) the ApiResourceIdentifier:

```php
$domainRelation = $certificate->relationship('domain')->getResourceIdentifiers();
```

---

[&laquo; Making API Calls](calls.md) | [Back to the index](index.md) | [Exceptions &raquo;](exceptions.md)

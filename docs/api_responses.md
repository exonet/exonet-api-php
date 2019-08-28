# Using API Responses
There are two types of API responses upon a successful request. If a single resource is requested then an [`Resource`](resources.md) is 
returned, if multiple resources are requested then an `ResourceSet` is returned.

## The `ResourceSet` class
When the API returns multiple resources, for example when getting an overview page, an instance of the `ResourceSet` class
is returned. The instance of this class contains the requested resources. Traverse each individual resource by using a
`foreach`-loop on the instance:

```php
$certificates = $client->resource('certificates')->get();

foreach ($certificates as $certificate) {
    // Each item is an instance of an Resource.
    echo $certificate->id()."\n";
}
```

## The [`Resource`](resources.md) class
Each resource returned by the API is transformed to an [`Resource`](resources.md) instance. This makes it possible to have easy access
to the attributes, resourceType and ID of the resource. Each of these fields can be accessed as if it is a property on the class:

```php
$certificate = $client->resource('certificates')->id('VX09kwR3KxNo')->get();

echo "ID: ".$certificate->id()."\n";
echo "Domain: ".$certificate->attribute('domain')."\n";
echo "Wildcard: ".$certificate->attribute('wildcard')."\n";
// etc.
```

## Relations
A resource can have multiple relations to one or more resources. To access a relation you can call the `relation` or `relationship` method on `Resource`.
A prepared instance of a new request is returned that can be retrieved by calling its `get()` method (thus returning an `Resource` or `ResourceSet`):

```php
$certificate = $client->resource('certificates')->id('VX09kwR3KxNo')->get();

$domainResource = $certificate->relation('domain')->get();
```

If you only want the resource identifiers, you can get it by using the `getResourceIdentifiers()` method. This
will return a (`ResourceSet` with) the ResourceIdentifier:

```php
$domainRelation = $certificate->relationship('domain')->getResourceIdentifiers();
```

---

[&laquo; Making API Calls](calls.md) | [Back to the index](index.md) | [Exceptions &raquo;](exceptions.md)

# Exceptions
If a bad API request is made or an API request fails, an exception is thrown. There are several specific exceptions, but all
exceptions thrown by this package are extending `Exonet\Exceptions\ExonetApiException`.

| Exception | When is it thrown?
| --------- | ------------------
| `AuthenticationException` | When no (valid) authentication method is set.
| `AuthorizationException` | When no (valid) credentials are set. When the used authentication method isn't allowed to access the requested resource.
| `InvalidRequestException` | When the structure of the request is invalid.
| `NotFoundException` | When the requested resource does not exists.
| `ValidationException` | When the data in the request is not valid.

When no sensible exception can be thrown, an `UnknownException` will be thrown.

---

[&laquo; Using API Responses](api_responses.md) | [Back to the index](index.md)

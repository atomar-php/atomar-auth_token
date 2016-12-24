Auth Token
========

Allows developers to easily create secure authentication tokens to be used for authentication without credentials. The tokens are managed internally so all you need to do is create a token then use the provided methods to perform operations with it.

###Methods

* `AuthTokenAPI::get_token($user) => string` - generates a new token that is linked to the RedBean user object `$user`. 
* `AuthTokenAPI::validate_token($token_string) => int/bool` - checks if a token is valid and returns the user id or false.
* `AuthTokenAPI::login($token_string) => bool` - logs in the user associated with a token.
* `AuthTokenAPI::trash_token($token_string)` - trashes a token.

The login method shown above utilizes the core method `A::authorize($user_id)` to peform the actual logging in.
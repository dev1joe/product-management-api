# Product Management API

# Learning
## PHP
### Heredoc & Nowdoc
- Heredoc encloses the result in <strong>double</strong> quotes, meaning that <strong>it can have variables inside</strong>.
- Nowdoc encloses the result in <strong>single</strong> quotes, it can NOT have variables inside.

## Slim Framework
### Middlewares
- Each new middleware layer surrounds any existing middleware layers. The concentric structure expands outwardly as additional middleware layers are added.
- The last middleware layer added is the first to be executed. 

## Doctrine
### Doctrine database abstraction layer (DBAL) types
- check them [here](https://www.doctrine-project.org/projects/doctrine-dbal/en/4.1/reference/types.html)

### ORM meta-data using attributes
- check attributes reference [here](https://www.doctrine-project.org/projects/doctrine-orm/en/3.2/reference/attributes-reference.html)

### Why specify the "Entity" and "Table" attributes ??
  - Entity attribute is required, it marks a PHP class as an entity to be persisted in the DB
  - while Table attribute is optional, it describes the table the entity is persisted in

### Migration version aliases
- first - Migrate down to before the first version.
- prev - Migrate down to before the previous version.
- next - Migrate up to the next version.
- latest - Migrate up to the latest version.

### Relationships mapping mistake I made 😅
- the problem was that the **"foreign key"** was of type VARCHAR not an INT **in all tables**
- me: I knew where was the problem, I added the Column attribute which made the problem
- ChatGPT: That makes sense. The Column attribute should not be used on a property that represents a relationship to another entity, like ManyToOne. Using Column here can cause Doctrine to treat the property as a basic column instead of a foreign key reference, which led to the issue you encountered.
- third migration corrected that mistake

## RESTful APIs
- Application Programming Interface (API)
- Representational State Transfer (REST): a set of architectural principles for building web services.
- Read: [RESTful web API design (Microsoft)](https://learn.microsoft.com/en-us/azure/architecture/best-practices/api-design)
- Read: [Microsoft Azure REST API Guidelines](https://github.com/microsoft/api-guidelines/blob/vNext/azure/Guidelines.md)
- Read: [The Web API Checklist](https://mathieu.fenniak.net/the-api-checklist/)
- Read: [What is a REST API?](https://blog.postman.com/rest-api-examples/)
- Read: [Best Practices for REST APIs](https://www.linkedin.com/pulse/best-practices-rest-apis-sergey-idelson/)
- Read: [REST API Best Practices](https://restfulapi.net/resource-naming/)
- Read: [Rules for creating a RESTful API](https://chatgpt.com/share/677d2db6-f570-800c-b5ea-5d783f731bf1)
- For API testing use [Dynamic Variables](https://learning.postman.com/docs/tests-and-scripts/write-scripts/variables-list)
- Metadata and default query params in API responses [here](https://chatgpt.com/share/67be06af-b354-800c-b7aa-3e648eab2d51) 

## HTML, files, MIME types
### accepting a file in HTML form tag
- I found that I need to define `enctype` attribute with value `multipart/form-data` to be able to send files to the server
- but why is that ?? and what does the new terminology mean ?

### talk about file size
- if we want to validate that the uploaded file is five Megabytes
- Megabytes = 5
- Kilobytes = Megabytes * 1024
- Bytes = Kilobytes * 1024

### security when receiving files from the user
- when receiving a file, you validate it
  - successful upload
  - size ?
  - name ?
  - type
- but when it comes to types, the file type can be spoofed !! even the UploadedFileInterface->getClientMedaType function documentation tells you to not trust the output of this function 

### MIME types reference ?? you got it!
- what are MIME types ?? (write it here for reference)
- a reference by internet assigned numbers authority (iana) [here](https://www.iana.org/assignments/media-types/media-types.xhtml)

## Cross-Site Resources Sharing (CORS) best practices
- only enable CORS when necessary to minimize security risks
- use specific origin allowlists rather than allowing access from all domains
- limit the methods and headers allowed in CORS requests to reduce potential vulnerabilities

## Security
### Cookies: configure options (in case of website):
  - httponly: only access the session using http (because it's accessible by js by default)
  - secure: HTTPS only, it's never sent on an insecure HTTP connection (except for localhost) 
  - samesite: 
  - more information at [MDN docs](https://developer.mozilla.org/en-US/docs/Web/HTTP/Cookies)

### Sessions
  - regenerate session id on login and logout (in case of website)
  - 

### cross-site scripting (XSS) protection

### JSON Web Tokens (JWT)
  - https://jwt.io/introduction
  - JWT secret is used to sign and verify your tokens, it's extremely important because:
    - if someone discovers your secret, they can forge valid tokens
    - if the secret is too short or predictable, it can be brute-forced
    - generate secret using `openssl rand -base64 64` (bash)
  - can I have mandatory parameters in the payload ? yes, if they are important, but make sure to document them.
  - should the payload be validated? Absolutely.
  - how to use JWT? check the link ☝️
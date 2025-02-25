# Relationships
### customer and comments
- if a customer deletes their account, the comments shouldn't be deleted. to implement that, the comment's customer foreign key can be null. Is that ok ? or will it produce issues ?

### comments and products
- on delete cascade

### order and invoice
- each order will reference its invoice and vice versa

### customer and order (on customer delete functions)
- if a customer deletes their account, customer should be warned in case of any undelivered orders.
- after confirmation, customer's completed orders stay, pending orders gets canceled.

### customer and address (on customer delete functions)
- if the customer is deleted, their address should also be deleted

### order item (on customer delete functions)
- this entity is a weak entity, with the product and customer as defining entities
- if the customer is deleted, the order item will be deleted
- if the product is deleted, the order item will be deleted, notify the customer that we no longer sell this deleted product

### warehouse and address
- the program will allow the same address to be assigned to multiple warehouses in case there are multiple warehouses in the same area

### inventory
- on warehouse deletion cascade
- on product deletion cascade

# Rules
| rule                                                                            | implementors                  |
|---------------------------------------------------------------------------------|-------------------------------|
| person (customer/admin) email must be unique across customers and admins tables | CreatCustomerRequestValidator |
| a guest can't access admin pages                                                | AdminAuthorizationMiddleware  |
| a customer can't access admin pages                                             | AdminAuthorizationMiddleware  |
| any logged in client can't access any login or registration forms               | GuestMiddleware               |
| for any person, max phone number length is 20 characters                        | Customer & Admin entities     |

# order status
- Pending: The order has been received, but processing has not yet begun.
- Processing: The order is being prepared for shipment, including tasks such as packaging and inventory verification.
- Shipped: The order has been dispatched to the customer’s shipping address.
- Out for Delivery: The order is with the courier or postal service, awaiting final delivery.
- Delivered: The order has been successfully delivered to the customer.
- Canceled: The order has been canceled by the customer or the e-commerce website.
- Returned: The order has been returned to the seller due to customer dissatisfaction or other reasons.

# invoice status
// TODO: check stripe payment gateway first

# Currency representation
// TODO: check stripe

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
- chatGpt: That makes sense. The Column attribute should not be used on a property that represents a relationship to another entity, like ManyToOne. Using Column here can cause Doctrine to treat the property as a basic column instead of a foreign key reference, which led to the issue you encountered.
- third migration corrected that mistake

## RESTful APIs
- Application Programming Interface (API)
- REpresentational State Transfer (REST): a set of architectural principles for building web services.
- Read: [What is a REST API?](https://blog.postman.com/rest-api-examples/)
- Read: [Best Practices for REST APIs](https://www.linkedin.com/pulse/best-practices-rest-apis-sergey-idelson/)
- Read: [REST API Best Practices](https://restfulapi.net/resource-naming/)
- Read: [Rules for creating a RESTful API](https://chatgpt.com/share/677d2db6-f570-800c-b5ea-5d783f731bf1)
- For API testing use [Dynamic Variables](https://learning.postman.com/docs/tests-and-scripts/write-scripts/variables-list)
- Metadata and default query params in API responses [here](https://chatgpt.com/share/67be06af-b354-800c-b7aa-3e648eab2d51) 

- [x] Products API
- [ ] Categories API
- [ ] Manufacturer API
- [ ] Warehouse API
- [ ] Customer API
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
- use specific origin whitelists rather than allowing access from all domains
- limit the methods and headers allowed in CORS requests to reduce potential vulnerabilities

## customer's secure connection
1. configure cookie options:
   - httponly: only access the session using http (because it's accessible by js by default)
   - secure: HTTPS only, it's never sent on an insecure HTTP connection (except for localhost) 
   - samesite: 
2. cross-site scripting (XSS) protection
3. regenerate session id
- more information at [MDN docs](https://developer.mozilla.org/en-US/docs/Web/HTTP/Cookies)

## JavaScript
### recommended approach
- <strong>Fetch and display the main data first, then activate filtering</strong>
- take products' pagination for example, it's better to fetch and display products first, then activate filtering (by category, by price, etc..) and sorting (by release data, price, rating, etc....)
- <strong>why this approach is better ?</strong>
  1. User experience: Displaying products first ensures that the client can see content immediately rather than waiting for filtering options to load.
  2. Data availability: It's better to make sure that data is accessible before activating filtering functionality, this way the application will be much efficient. If order is flipped, resources maybe wasted. 

# Project Future
- make a dynamic attributes system (suggested by [chatGPT](https://chatgpt.com/share/6713d5db-cf0d-47b4-93f0-305d9cbd7709))
- advanced filtering (i5, i7 CPUs for laptops)
- sub-categories (phones > [i-phone, Samsung, phone accessories])
- Complaints system (using UI and emails)
- sales tracking (store sales quantity)
- advanced CMS
- capacity for more employees (manger, supervisor, product manager, order manager)
- order tracking 
- Maybe add the feature of archiving products, for data analysis sake.
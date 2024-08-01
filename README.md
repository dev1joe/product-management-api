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
- Why specify the "Entity" and "Table" attributes ??
  - Entity attribute is required, it marks a PHP class as an entity to be persisted in the DB
  - while Table attribute is optional, it describes the table the entity is persisted in

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
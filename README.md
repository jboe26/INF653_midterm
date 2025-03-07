# Quotes API

Joshua Boepple
INF653 Back End Web Development - Midterm

This project is a RESTful API designed to manage quotes, authors, and categories. It utilizes PHP and PostgreSQL to provide a backend for accessing and manipulating quote data.

Deployed Project: [Your Deployed Project Link]

## API Endpoints

### Quotes

* **GET /quotes:** Retrieves all quotes.
* **GET /quotes/{id}:** Retrieves a single quote by ID.
* **POST /quotes:** Creates a new quote.
* **PUT /quotes:** Updates an existing quote.
* **DELETE /quotes:** Deletes a quote.

### Authors

* **GET /authors:** Retrieves all authors.
* **GET /authors/{id}:** Retrieves a single author by ID.
* **POST /authors:** Creates a new author.
* **PUT /authors:** Updates an existing author.
* **DELETE /authors:** Deletes an author.

### Categories

* **GET /categories:** Retrieves all categories.
* **GET /categories/{id}:** Retrieves a single category by ID.
* **POST /categories:** Creates a new category.
* **PUT /categories:** Updates an existing category.
* **DELETE /categories:** Deletes a category.

## Database

This project utilizes PostgreSQL for data storage.

## Usage

You can use tools like Postman or `curl` to interact with the API.

## Error Handling

The API returns appropriate HTTP status codes and JSON error messages for invalid requests.
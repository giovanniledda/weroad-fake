# WeRoadFake

A brief description of how to setup the project.


## Installation

After cloning the repo from the Github link, please make sure to complete the following basic steps to setup any Laravel project:

- 1: run `composer install`
- 2: setup DB
- 3: compile your own version of .env file.

Install weroad-fake by launching its migrations and base seeders:

```bash
  php artisan migrate:fresh --seed
```
This will create DB tables and will populate "roles" table with main roles: "Admin" and "Editor".



## Users creation

To create a new user launch the following Laravel command:

```bash
  php artisan weroad-fake:create-user {userFullName} {userEmail} [--A|admin]
```

If "userFullName" and "userEmail" (will be used as username) have correct values, then you'll be asked to insert a password for the user.
The user will be created as an "Editor" by default, but if option "--admin" is present, then the role "Admin" will be also added to it.



## API Reference

#### User login

```http
POST            api/v1/login
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `email` | `string` | **Required**. User email |
| `password` | `string` | **Required**. User password |

If credentials are correct, the API will return the `access_token` that you must use to authenticate the request to other APIs.

#### User logout

The route is obviously private.

```http
POST            api/v1/logout
```

#### Travels list

A public (no auth) endpoint to get a list of paginated travels. It returns only `public` travels.
If an authenticated user accesses the API, then **also private travels will be shown**.

```http
GET or HEAD        api/v1/travels
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `page` | `integer` | The page number |

#### Travel create

A private (admin) endpoint to create new travels.

```http
POST            api/v1/travels
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `name` | `string` | **Required**. A unique name for the travel (max 140 chars) |
| `description` | `string` | **Required**. Travel's description |
| `days` | `integer` | **Required**. Number of days the travel lasts |


#### Travel update

A private (editor) endpoint to update a travel.

```http
PUT or PATCH       api/v1/travels/{travel}
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `travel` | `string` | **Required**. Travel's UUID |
| `name` | `string` | **Required**. A unique name for the travel (max 140 chars) |
| `description` | `string` | **Required**. Travel's description |
| `days` | `integer` | **Required**. Number of days the travel lasts |



#### Travel delete

A private (admin) endpoint to delete a travel.

```http
DELETE       api/v1/travels/{travel}
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `travel` | `string` | **Required**. Travel's UUID |



#### Tour delete

A private (admin) endpoint to delete a tour.

```http
DELETE       api/v1/tours/{tour}
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `tour` | `string` | **Required**. Tour's UUID |


#### Travel's Tour create

A private (admin) endpoint to create new tours for a travel.

```http
POST            api/v1/travels/{travel}/tour
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `travel` | `string` | **Required**. Travel's UUID |
| `name` | `string` | **Required**. A unique name for the tour (max 140 chars) |
| `startingDate` | `date (Y-m-d)` | **Required**. Tour's start (>= today) |
| `endingDate` | `date (Y-m-d)` | Tour's end (>= startingDate). If missing then will be calculated as `startingDate + Travel's days` |
| `price` | `integer` | **Required**. Tour's price |


#### Travel's Tours list

A public (no auth) endpoint to get a list of paginated tours by the travel `slug` (e.g. all the tours of the travel `foo-bar`). Users can filter (search) the results by `priceFrom`, `priceTo`, `dateFrom` (from that `startingDate`) and `dateTo` (until that `startingDate`). User can sort the list by `price` asc and desc. They will **always** be sorted, after every additional user-provided filter, by `startingDate` asc.

```http
GET or HEAD        api/v1/travels/{travelSlug}/tours
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `travelSlug` | `string` | **Required**. Travel's slug |
| `page` | `integer` | The page number |
| `priceFrom` | `integer` | Filters by price |
| `priceTo` | `integer` | Filters by price |
| `dateFrom` | `date (Y-m-d)` | Filters by starting date |
| `dateTo` | `date (Y-m-d)` | Filters by starting date |
| `sortByPrice` | `string` | Values (asc, desc). Orders results by price |


**Warning**: in order to pass authentication controls, all "private" APIs must have the `access_token` retrieved from the login API, as a "Bearer" token on the "Authorization" field of the Request's Header.


## Running Tests

To run tests, run the following command

```bash
  php artisan test
```


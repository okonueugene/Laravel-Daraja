# Mpesa Express STK Push Request in Laravel

This repository provides a Laravel implementation of the Safaricom Mpesa Express STK Push request using the Daraja API. It allows you to initiate secure mobile money transactions. The service can be accessed via the following URL: `host:8000/api/stkpush`.

## Prerequisites

Before you can use this STK Push request service in Laravel, make sure you have the following prerequisites in place:

1. A working Mpesa Express Sandbox account.
2. A registered Safaricom developer account for API access.

## Installation

To get started, follow these steps:

1. Clone the repository using the following command:

```bash
git clone https://github.com/okonueugene/Laravel-Daraja.git
```

2. Navigate to the project directory and install the required dependencies:

```bash

cd Laravel-Daraja && composer install

```

3. Create a `.env` file in the root directory of the project and copy the contents of the `.env.example` file into it:

```bash

cp .env.example .env


```

4. Create a `.env` file in the root directory of the project and copy the contents of the `.env.example` file into it:

```bash

cp .env.example .env


```

5. Generate an application key:

```bash

php artisan key:generate

```

6. Run the application:

```bash

php artisan serve

```

It will run on the default port, which is usually 8000. You can access it at http://localhost:8000.

## Usage

You can easily use the STK Push request in Laravel by making a POST request to the following URL: host:8000/api/stkpush. Configure your .env file with the necessary input parameters:

```bash

MPESA_CONSUMER_KEY=YOUR_CONSUMER_KEY
MPESA_CONSUMER_SECRET=YOUR_CONSUMER_SECRET
MPESA_SHORT_CODE=YOUR_SHORT_CODE
MPESA_PASSKEY=YOUR_PASSKEY
MPESA_INITIATOR_NAME=YOUR_INITIATOR_NAME
MPESA_INITIATOR_PASSWORD=YOUR_INITIATOR_PASSWORD
MPESA_TRANSACTION_TYPE=YOUR_TRANSACTION_TYPE
MPESA_TRANSACTION_AMOUNT=YOUR_TRANSACTION_AMOUNT
MPESA_TRANSACTION_REFERENCE=YOUR_TRANSACTION_REFERENCE
MPESA_TRANSACTION_DESCRIPTION=YOUR_TRANSACTION_DESCRIPTION
MPESA_CALLBACK_URL=YOUR_CALLBACK_URL

```

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.

## License

This project is licensed under the [MIT](https://choosealicense.com/licenses/mit/) license.

```

```

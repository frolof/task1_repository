# Transaction Processor

This project is a PHP-based transaction processor that calculates commissions for transactions using the BIN number and currency exchange rates.

## Task comment

1. API http://api.exchangeratesapi.io/latest require access_key, so provided code can not work. I guess it was intended trap.
2. Following transaction not exist and generates error. I guess it was additional trap.:
```
{"bin":"41417360","amount":"130.00","currency":"USD"}
```
3. I would suggest adding a caching layer for retrieving exchange rates. It would increase performance, but the task description specifically stated not to add any additional functionalities.
4. I noticed that when I make several requests to the https://lookup.binlist.net/ API, I start receiving a 429 Too Many Requests error. I'm not sure if this is an additional trap or not. I can implement a solution, but I need to know the API limitations.
5. The task took me around 1 hour.

## Prerequisites

- Docker
- Docker Compose

## Installation

1. Clone the repository:

    ```bash
    git clone https://github.com/frolof/transaction-processor.git
    cd transaction-processor
    ```

2. Install the necessary dependencies using Composer:

    ```bash
    composer install --ignore-platform-reqs
    ```

3. Copy the `.env.example` file to `.env` and add your `access_key`:

    ```bash
    cp .env.example .env
    ```

    Edit the `.env` file and set your `access_key`:

    ```plaintext
    EXCHANGE_RATE_API_ACCESS_KEY=your_access_key_here
    ```

## Project Structure

```
project/
│
├── bin/
│   └── app.php
├── src/
│   ├── Exception/
│   │   ├── TransactionProcessorException.php
│   │   ├── BinListException.php
│   │   ├── ExchangeRateException.php
│   │   └── ExchangeRateNotFoundException.php
│   └── Service/
│       ├── TransactionProcessor.php
│       ├── BinListService.php
│       ├── BinListServiceInterface.php
│       ├── ExchangeRateService.php
│       └── ExchangeRateServiceInterface.php
├── tests/
│   ├── TransactionProcessorTest.php
│   ├── BinListServiceTest.php
│   └── ExchangeRateServiceTest.php
├── var/
│   └── coverage/ (created automatically)
├── .env
├── .env.example
├── .gitignore
├── composer.json
├── composer.lock
├── Dockerfile
├── docker-compose.yml
├── phpunit.xml
└── README.md
```

## Building and run all containers
To build and run all containers, use the following command:

```bash
docker-compose up --build 
```

## Running the Application

To build and run the application container only, use the following command:

```bash
docker-compose up --build php
```

This command will build, start the Docker container and run the `app.php` script with the provided `input.txt` file.

## Running Tests

To run the PHPUnit tests, use the following command:

```bash
docker-compose run test
```

## Generating Code Coverage Report

To generate the code coverage report, use the following command:

```bash
docker-compose run coverage
```

The coverage report will be available in the `var/coverage` directory in HTML format. You can open the `var/coverage/index.html` file in a web browser to view the detailed coverage report.

## Running PHP-CS-Fixer

To run PHP-CS-Fixer and automatically fix coding standards issues, use the following command:

```bash
docker-compose run cs-fixer
```

## Usage

The `app.php` script processes transactions provided in an `input.txt` file. Each transaction is a JSON object in the following format:

```json
{"bin":"45717360","amount":"100.00","currency":"EUR"}
```

### Example `input.txt` file

```
{"bin":"45717360","amount":"100.00","currency":"EUR"}
{"bin":"516793","amount":"50.00","currency":"USD"}
{"bin":"45417360","amount":"10000.00","currency":"JPY"}
{"bin":"41417360","amount":"130.00","currency":"USD"}
{"bin":"4745030","amount":"2000.00","currency":"GBP"}
```

### Example Output

```
1.00
0.47
1.66
2.40
43.71
```

## Code Explanation

### `TransactionProcessor.php`

- Processes transactions and calculates commissions.
- Converts amounts to EUR based on exchange rates.
- Applies different commission rates for EU and non-EU issued cards.

### `BinListService.php`

- Retrieves BIN data from the `https://lookup.binlist.net/` API.
- Checks if a country is in the EU.

### `ExchangeRateService.php`

- Retrieves exchange rates from the `http://api.exchangeratesapi.io/latest` API.
- Throws an exception if the exchange rate for a given currency is not found.
- Loads the `access_key` from the `.env` file.

### Exceptions

- `TransactionProcessorException`: Base exception for the transaction processor.
- `BinListException`: Exception for errors related to BIN list service.
- `ExchangeRateException`: Exception for errors related to exchange rate service.
- `ExchangeRateNotFoundException`: Exception for when an exchange rate is not found.

## License

This project is licensed under the MIT License. See the LICENSE file for more details.

### Notes

- Ensure you have a valid `access_key` for the `http://api.exchangeratesapi.io/latest` API and update the `.env` file accordingly.
- The coverage directory (`var/coverage`) will be created automatically when generating the code coverage report.

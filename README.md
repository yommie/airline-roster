# Airline Roster

This is a simple Laravel application that parses airline roster data from roster files. The files can be in the following formats:

- PDF
- Excel
- txt
- HTML
- Webcal

> Currently, only a parser for the HTML file format has been written. A sample file can be found at `/public/sample/CrewConnex.html`

## Getting Started
These instructions will get a copy of the project up and running on your machine.

### Prerequisites
- Docker compose `version 2` installed on your machine.

### Installation

1. Clone the repository to your local machine:

```bash
git clone git@github.com:yommie/airline-roster.git
```

2. Copy the contents of `.env.example` into a newly created `.env` file and modify the values according to your setup.

3. Build and run the Docker containers by running the following command:

```bash
make run
```

Application would be available locally on port `8686`

## Sample Rosters
Sample rosters can be found in the `storage/sample` directory

## Tests
To run tests, use the `make tests` command.

If you would like to see coverage information, use the `make tests-coverage` command.

To see coverage results, navigate to `http://127.0.0.1:8686/tests/index.html`

## API Endpoints

### GET /health

**Request**

```bash
curl --location 'http://127.0.0.1:8686/health'
```

**Response `200`**:

```json
{
    "status": "OK"
}
```

### POST /api/v1/register

**Request**

```bash
curl --location --request POST 'http://127.0.0.1:8686/api/v1/register' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--data-raw '{
    "name": "Yommie Airlines",
    "email": "yommie@airlines.com",
    "password": "password"
}'
```

**Response `201`**

```json
{
    "name": "Yommie Airlines",
    "email": "yommie@airlines.com",
    "updated_at": "2024-04-15T06:15:53.000000Z",
    "created_at": "2024-04-15T06:15:53.000000Z",
    "id": 1
}
```

### POST /api/v1/login

**Request**

```bash
curl --location --request POST 'http://127.0.0.1:8686/api/v1/login' \
--header 'Accept: application/json' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "yommie@airlines.com",
    "password": "password"
}'
```
**Response `200`**

```json
{
    "access_token": "1|drVUr4s6yZpbygt6wyBHV60VdbvCfVDbWqjSdCKYc522df40"
}
```

### POST /api/v1/upload-roster

**Request**

```bash
curl --location --request POST 'http://127.0.0.1:8686/api/v1/upload-roster' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 1|drVUr4s6yZpbygt6wyBHV60VdbvCfVDbWqjSdCKYc522df40' \
--form 'roster=@"/path/to/project/storage/sample/ValidRoster.html"'
```

**Response `201`**

```json
{
    "message": "Imported rosters"
}
```

### GET /api/v1/activities

This endpoint can be used to achieve the following:

- give all events between date x and y.
- give all flights for the next week.
- give all Standby events for the next week.
- give all flights that start on the given location.

> All date formats passed to this endpoint must be in the `Y-m-d` format

The following options are available for query filters

- `event`: filter activities by type
  - `off`
  - `check_in`
  - `check_out`
  - `flight`
  - `stand_by`
  - `unknown`
  > Usage: `/api/v1/activities?event=flight`

- `date`: filter activities by specific date
  - `Y-m-d`: 2022-01-11
  > Usage: `/api/v1/activities?date=2022-01-11`

- `week`: filter activities by specific week
  - `next`
  - `previous`
  - `current`
  - `Y-m-d`
  > Usage: `/api/v1/activities?week=previous`

- `start_date`: filter activities by start date
  - `Y-m-d`: 2022-01-11
  > Usage: `/api/v1/activities?start_date=2022-01-11`

- `end_date`: filter activities by end date
  - `Y-m-d`: 2022-01-11
  > Usage: `/api/v1/activities?end_date=2022-01-11`

**NOTE**: Only one out of date, week or a combination of start_date and end_date can be used.

**Request**

```bash
curl --location 'http://127.0.0.1:8686/api/v1/activities' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 1|drVUr4s6yZpbygt6wyBHV60VdbvCfVDbWqjSdCKYc522df40'
```

**Response `200`**

```json
{
    "data": [
        {
            "id": 1,
            "date": "2022-01-10 00:00:00",
            "activity_type": "check_in",
            "activity_type_id": null,
            "activity_start": "2022-01-10 07:45:00",
            "activity_end": null,
            "created_at": "2024-04-15T06:17:20.000000Z",
            "updated_at": "2024-04-15T06:17:20.000000Z",
            "user": {
                "id": 1,
                "name": "Yommie Airlines"
            },
            "activity_data": [],
            "extra_data": []
        },
        {
            "id": 2,
            "date": "2022-01-10 00:00:00",
            "activity_type": "flight",
            "activity_type_id": 1,
            "activity_start": "2022-01-10 08:45:00",
            "activity_end": "2022-01-10 09:35:00",
            "created_at": "2024-04-15T06:17:20.000000Z",
            "updated_at": "2024-04-15T06:17:20.000000Z",
            "user": {
                "id": 1,
                "name": "Yommie Airlines"
            },
            "activity_data": {
                "id": 1,
                "flight_number": "DX 0077",
                "departure_location": "KRP",
                "departure_time": "2022-01-10 08:45:00",
                "arrival_location": "CPH",
                "arrival_time": "2022-01-10 09:35:00",
                "block_hours": 0,
                "flight_duration": 0,
                "night_duration": 0,
                "activity_duration": 0,
                "extension_duration": 0,
                "passengers_on_flight": null,
                "aircraft_registration_number": "OYJRY",
                "created_at": "2024-04-15T06:17:20.000000Z",
                "updated_at": "2024-04-15T06:17:20.000000Z"
            },
            "extra_data": []
        }
    ],
    "links": {
        "first": "http://127.0.0.1:8686/api/v1/activities?page=1",
        "last": "http://127.0.0.1:8686/api/v1/activities?page=27",
        "prev": null,
        "next": "http://127.0.0.1:8686/api/v1/activities?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 27,
        "links": [],
        "path": "http://127.0.0.1:8686/api/v1/activities",
        "per_page": 2,
        "to": 2,
        "total": 54
    }
}
```

### POST /api/v1/logout

**Request**

```bash
curl --location --request POST 'http://127.0.0.1:8000/api/v1/logout' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 6|XJwgggECz7ytd9OW1YPfUJVrOkVvuUE4ykD2Aa8z562a465e'
```

**Response `200`**

```json
{
    "message": "Logged out"
}
```

## Assumptions taken

### Date
In the roster, the `date` column has only `day` and `date` (`Mon 10`, `Tue 11`...) values. For cases where the period range spans over multiple months, there is no realistic way of determine the `month` of the `date` column.

One hypothesis would be to assume for a period spanning over multiple months, take `30 Jan 22 - 03 Feb 22` as an example, the day range would be extracted to give `30 - 03`. Then the months can be tracked by iterating over each day as follows:

- `Sun 30`: January
- `Mon 31`: January
- `Tue 01`: February
- `Wed 02`: February
- `Wed 03`: February

The logic would switch to the next `month` when a `01` indicating the start of a new `month` is encountered, or the next `day` is less than the previous `day` which would also indicate a new `month` has been entered.

### Why this approach was not taken:
- There is no rule indicating that all `days` in a `month` would be present in a roster
- There is also no rule indicating that all `months` in a period range would be present in a roster.

Given the conditions above there is no guarantee for consistent parsing of the dates.

### Format assumptions taken for parsing:
- `Mon 10`: `month` and `year` would be fetched from `period start`
- `Mon 10 Jan`: only `year` would be fetched from `period start`
- `Mon 10 Jan 22`: all date parameters would be fetched from `value`

## Future improvements:
- Write unit and integration tests
- Validate each roster activity before inserting into DB to prevent things like duplicates, etc.
- Make queue process data in background via a redis instance

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

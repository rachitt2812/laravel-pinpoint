# laravel-pinpoint

Mail driver to send email using AWS Pinpoint

## Installation

```
composer require codaptive/laravel-pinpoint
```

## Configuration

- Set `MAIL_DRIVER=pinpoint`
- Add the following to the `.env`
  ```
  AWS_KEY
  AWS_SECRET
  AWS_DEFAULT_REGION
  AWS_PINPOINT_APPLICATION_ID
  ```
- You can also do `php artisan vendor:publish` and modify the `config/pinpoint.php` file to use existing env vars.

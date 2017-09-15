# WordPress Context
A WordPress context library based on WordPress' built-in template hierarchy.

## What It Does
Designed to be used after the main WordPress query has run, this context library will return an array of slugs representing the current context.

## How to Use It

1. Add to your project via [Composer](https://getcomposer.org/):

```bash
$ composer require wpscholar/wp-context
```

2. Make sure you have added the Composer autoloader to your project:

```php
<?php 

require __DIR__ . '/vendor/autoload.php';
```

3. Call the static method for fetching context:

```php
<?php

use wpscholar\WordPress\Context;

$context = Context::getContext();
```
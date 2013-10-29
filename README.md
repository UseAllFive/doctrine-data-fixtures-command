Doctrine DataFixtures command
=============================
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/dc5ed060-c9d4-4916-9941-e784024532f1/small.png)](https://insight.sensiolabs.com/projects/dc5ed060-c9d4-4916-9941-e784024532f1)

This is a version of the [Symfony 2 Doctrine Data Fixtures](https://github.com/doctrine/DoctrineFixturesBundle) command that does NOT require symfony.

## Installation
Via composer:
```bash
php composer.phar require useallfive/doctrine-data-fixtures-command dev-master
```

If you don't already have your own version of the Doctrine CLI tool, you can copy the one provided with doctrine and place it wherever you see fit. For this example we'll place it in the project root. It's also worth mentioning that you can utilize [a composer post install/update hook](http://getcomposer.org/doc/articles/scripts.md) to take care of this for you.
```bash
cp vendor/bin/doctrine.php doctrine
```

Add the command to the `$commands` array in your CLI script.

```php
// ...
$commands = array(
    new \UseAllFive\Command\LoadDataFixturesDoctrineCommand(),
);
```

You're all set!

### Notes
Unlike the symfony 2 version of this, you'll need to specify a fixtures path.
```bash
php doctrine fixtures:load /path/to/fixtures
```

# Extension for Yii2 to dynamically configuration parameters stored in the database
This is extension allow storing configuration parameters of application in database and management they dynamically from admin panel.

## Install
Add `skinka/yii2-config` to the project's composer.json dependencies and run php composer.phar install

# Usage
Before you can go on you need to create those tables in the database. To do this, you can use the migration stored in `@vendor/skinka/yii2-config/src/migrations`:
`yii migrate --migrationPath=@vendor/skinka/yii2-config/src/migrations`

after create the migration to the new dynamic parameters

```php
    public function up()
    {
        Config::setNew('adminEmail', 'Administrator email', 'admin@site.com', 
            Config::TYPE_STRING, Config::INPUT_INPUT, [['email']], [], '', 0);
            
        Config::setNew('dateTimeFormat', 'Datetime format for site', 'php:d.m.Y H:i:s', 
            Config::TYPE_STRING, Config::INPUT_INPUT, [['string']], [],
            'Date in PHP format. All formats can be seen here: http://php.net/manual/en/function.date.php', 1);
            
        Config::setNew('autoConfirmRegistration', 'Automatic registration', true, 
            Config::TYPE_BOOLEAN, Config::INPUT_DROPDOWN, [['integer']], [0 => 'Off', 1 => 'On'], 
            'If enabled, the user at the email will not receive a notification of the activation', 2);
            
        //Others
    }

    public function down()
    {
        Config::delete('adminEmail');
        Config::delete('dateTimeFormat');
        Config::delete('autoConfirmRegistration');
        
        //Others
    }
```

for IDE tips create a class

```php
use skinka\yii2\extension\config\Config;

/**
 * Class Cfg
 *
 * @method  static string adminEmail
 * @method  static string dateTimeFormat
 * @method  static integer autoConfirmRegistration
 */
class Cfg extends Config
{

}
```

use parameters

```php
if (Cfg::autoConfirmRegistration()) {
    echo Cfg::adminEmail();
}
```

## Manage in admin panel

Add action in controller

```php
    public function actions()
    {
        return [
            'config' => [
                'class' => 'skinka\yii2\extension\config\actions\ConfigAction',
            ]
        ];
    }
```

This is a Symfony flex skeleton for a minimal Symfony ToDo app
project, used for teaching CSC4101 at Telecom SudParis.

Source code can be found at
https://github.com/olberger/tspcsc4101-todo-skeleton

Composer packages can be found at
https://packagist.org/packages/oberger/tspcsc4101-todo-skeleton

To test, use :
 $ composer create-project oberger/tspcsc4101-todo-skeleton todo-app v7.*

Changelog :
 - v7.5 : Update to Symfony 6.3
 - v7.x : Included JQuery dynamic interface
 - v6.x : ApiPlatform
 - v5.x : Add Pastes entity with its CRUD controller
 - v4.x : Add camurphy/bootstrap-menu-bundle and boostrap (via
          StartBootstrap "bare" distribution)
 - v3.x : Add templating with Twig
 - v2.x : Add basic Web interface and EasyAdmin dashboard
 - v1.x : initial version with CLI commands
 
Recreating it :
 - symfony new todo --version=stable
 - symfony composer require symfony/monolog-bundle
 - symfony composer require -n symfony/orm-pack
 - symfony composer require --dev -n symfony/maker-bundle
 - symfony composer require --dev doctrine/doctrine-fixtures-bundle
 - symfony console make:entity # for Todo
 - symfony console make:command # for app:list-todos, app:show-todo

Testing development branches:
 $ symfony composer create-project oberger/tspcsc4101-todo-skeleton todo-app "2.x-dev"

-- Olivier Berger

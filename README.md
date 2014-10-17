# Shade Framework

Shade is a general-purpose PHP micro framework for developing any kinds of web applications.

## Key features

* **Lightness**. The framework contains only base components that are mandatory for any modern web project. And that's it. It's up to developers to bring other components depends on project requirements.
* **Simplicity**. The codebase of the framework is small enough to read it all in a short time. Easy installation process via composer and built-in skeleton generator will save your time.
* **Performance**. Overhead of the framework converges to zero. Execution time of 'hello world' action on average machine takes 0.001 sec.
* **Personalisation**. Built-in skeleton generator creates applications with chosen namespace name independent from the framework files but inheriting it's components.

## Installation

As the framework installs via Composer the last one should be set up before. For instruction please refer to <https://getcomposer.org/>.

Under the directory chosen to hold your project and framework files:

    composer -sdev create-project shade/shade ./

After Composer gets required dependencies you will be asked for the name of your application that will be used as it's namespace and path in case you want to have it in a different location. Later you are free to create as many other application as you wish with Shade CLI.

Configure your web server: the document root should be _web/_ under your application path, the single entry point is _index.php_.

Hope you will enjoy!
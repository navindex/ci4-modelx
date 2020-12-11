# Navindex:ModelX

***

Model extension for CodeIgniter 4.

**NOTE: *This package is not functional yet. Watch this space to check the progress*.**

## 1. Requirements

- PHP 7.2+
- CodeIgniter 4

## 2. Features

**Model** and **Entity** support for

- Alternate keys (unique keys)
- Composite primary and alternate keys (a combination of 2 or more fields)
- Auto increment field for composite primary key
- Junction (associative) tables repesenting many-to-many relationships
- Database views
- Encrypted fields

## 3. Installation

Installation is best done via Composer. Assuming Composer is installed globally, you may use the following command:

```bash
    > composer require navindex/modelx
```

This will add the latest stable release of **Navindex:ModelX** as a module to your project. Note that you may need to adjust your project's [minimum stability](http://webtips.krajee.com/setting-composer-minimum-stability-application/) in order to use **Navindex:ModelX** while it is in beta.

### 3.1. Manual Installation

Should you choose not to use Composer to install, you can clone or download this repo and then enable it by editing **app/Config/Autoload.php** and adding the **Navindex:ModelX** namespace to the **$psr4** array. For example, if you copied it into **app/ThirdParty**:

```php
    $psr4 = [
        'Config'          => APPPATH . 'Config',
        APP_NAMESPACE     => APPPATH,
        'App'             => APPPATH,
        'Navindex\ModelX' => APPPATH .'ThirdParty/ModelX/src',
    ];
```

## 4. Configuration

Once installed the only thing you need is to create the new Models and Entities by extending `Navindex\ModelX\Models\BaseModel` or `Navindex\ModelX\Entities\BaseEntity` classes, respectfully.

## 5. Overview

## 6. Customization

## 7. Credits

Thanks to [EllisLab](https://ellislab.com) for originally creating CodeIgniter and the [British Columbia Institute of Technology](https://bcit.ca/) for continuing the project. Thanks to all the developers and contibutors working on [CodeIgniter 4](https://github.com/bcit-ci/CodeIgniter4).

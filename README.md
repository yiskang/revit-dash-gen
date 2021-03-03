# Dash Revit API Documentation Generator

![Platforms](https://img.shields.io/badge/platform-Windows|MacOS-lightgray.svg)
![PHP](https://img.shields.io/badge/PHP-7.3-blue.svg)
[![MIT](https://img.shields.io/badge/License-MIT-blue.svg)](http://opensource.org/licenses/MIT)

# Description

Helper tool to convert Autodesk Revit API CHM file to [Dash](http://kapeli.com/) documentation for comfortable local or mobile browsing and without CHM viewer installed.

## Requirements

- PHP version 5.4.0 and above.
- Use [Composer](http://getcomposer.org/) to install package dependencies.
- Use [Dash](http://kapeli.com/) app to open the generated DocSet.

## Usage

1. Download Revit SDK installer from https://www.autodesk.com/developer-network/platform-technologies/revit
2. Install Revit SDK to your Windows machine
3. Clone this repo: git clone https://github.com/yiskang/revit-dash-gen
4. Extract `RevitAPI.chm` from the SDK installation folder
5. Copy `RevitAPI.chm` to the root folder of this project aside to the index.php
6. Install dependencies, so run `composer install` in your terminal
7. Run this project
    ```
    php index.php --out=./test/revit-api-2021-1.docset --id=revit --name='Revit 2021 test' ./RevitAPI-2021-1.chm
    ```

## License

This sample is licensed under the terms of the [MIT License](http://opensource.org/licenses/MIT). Please see the [LICENSE](LICENSE) file for full details.

## Written by

Eason Kang [@yiskang](https://twitter.com/yiskang), [Forge Partner Development](http://forge.autodesk.com)
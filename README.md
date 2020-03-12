<p align="center">
<img width="200" src="https://github.com/DirectoryTree/Scout/blob/master/public/img/logo-circle.png" alt="Scout Logo">
<br/>Scout audits your LDAP directory so you don't have to.
</p>

<p align="center">
    <a href="https://travis-ci.com/DirectoryTree/Scout"><img src="https://img.shields.io/travis/DirectoryTree/Scout.svg?style=flat-square"/></a>
    <a href="https://scrutinizer-ci.com/g/DirectoryTree/Scout/?branch=master"><img src="https://img.shields.io/scrutinizer/g/DirectoryTree/Scout/master.svg?style=flat-square"/></a>
    <a href="https://packagist.org/packages/DirectoryTree/Scout"><img src="https://img.shields.io/packagist/l/directorytree/scout.svg?style=flat-square"/></a>
</p>

## About Scout

🚨 Scout is still in heavy development - it is not ready for production. 🚨

Scout is a web application that periodically scans your LDAP directory, detecting and logging all
changes that occur to objects and their attributes. Scout comes with great features that will
help you and your IT team:

- Automated domain wide change notifications
- Completely customizable notifiers to generate notifications based on conditions
- Automated password notifications. Know when user passwords have been changed, preventing security issues
- Automated password expiry notifications. Notify users when their password is about to expire
- Perform password resets from a web UI, and notify the user of their new temporary password

## Requirements

- PHP >= 7.2
- PHP LDAP Extension enabled
- An LDAP Server

## Installation

To install Scout, run the following git command to pull down the latest release:

```bash
git clone https://github.com/directorytree/scout
```

Then, navigate inside the created directory and run the following comamnd to install Scout's dependencies with Composer:

```bash
composer install 
```

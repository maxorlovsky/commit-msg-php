# commit-msg

**commit-msg** is a commit-msg hook installer for `git` with configuration available in `composer.json`

This is a fork of my other package created for [javascript](https://github.com/maxorlovsky/git-commit-msg). Why this package is better than other provided in packagist, this one is installed automatically as you add it to your dependencies list. You don't need to ask your developers to run some installation commands separately for every project you have.

The idea of this library is to force using semantic-release rules in commit-message using [Angular Commit Message Conventions](https://github.com/angular/angular.js/blob/master/DEVELOPERS.md#-git-commit-guidelines)

### Installation (Composer)

Run
```
composer install commit-msg --dev
```

This will replace commit-msg in your .git/hooks folder with code, that will run checks on every git commit.

### Configuration

Configuration is simple and is done in `composer.json`, you just need to add commit-msg object to "config" parameter:

```php
"config": {
    "commit-msg": {
        "types": [
            "feat",
            "fix",
            "chore",
            "docs",
            "refactor",
            "style",
            "perf",
            "test",
            "revert"
        ],
        "lineLength": 72,
        "scope": {
            "mandatory": true,
            "rules": "Task-(\\d+)"
        }
    }
}
```

commit-msg->types (array of strings) will add rules, so your git commit messages must start using those types like
```
feat: <message>
```

or

```
feat(scope/filename): <message>
```

commit-msg->lineLength (integer) will make sure that lines in your commit message are always less or equal to the number you set into this config

commit-msg->scope (array) will make sure that scope is always following rules specified as Regular Expression

This package is WIP, so propose your ideas - open issue or create pull request.
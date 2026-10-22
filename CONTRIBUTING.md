# Contributing

Thank you for considering contributing to this project! Every contribution is welcome and helps improve the quality of the project. To ensure a smooth process and maintain high code quality, please follow the steps below.

## Requirements

- [DDEV](https://ddev.readthedocs.io/en/stable/)

## Preparation

```bash
# Clone repository
git clone https://github.com/move-elevator/typo3-chameleon-login.git
cd typo3-chameleon-login

# Start the project with DDEV
ddev start

# Install dependencies
ddev composer install

# Set up TYPO3
ddev init-typo3
```

You can access the TYPO3 backend at <https://typo3-chameleon-login.ddev.site/typo3/>.

To log in, use the username `admin` and the password `Password1!`.

## Run linters

```bash
# All linters
ddev cgl lint

# Specific linters
ddev cgl lint:composer
ddev cgl lint:editorconfig
ddev cgl lint:language
ddev cgl lint:php
ddev cgl lint:typoscript
ddev cgl lint:yaml

# Fix all CGL issues
ddev cgl fix

# Fix specific CGL issues
ddev cgl fix:composer
ddev cgl fix:editorconfig
ddev cgl fix:php
```

## Code style

PHP CS Fixer enforces most conventions, run `ddev cgl fix:php` to apply them:

- Native functions, classes and constants are imported, e.g. `use function in_array;`
- Imports from the same namespace are grouped, e.g. `use TYPO3\CMS\Core\Page\{JavaScriptModuleInstruction, PageRenderer};`
- Comparisons use yoda style, e.g. `null === $season`

One convention is not covered by the fixer: write boolean negations as an explicit comparison, e.g. `false === in_array($effect, self::EFFECTS, true)` instead of `!in_array(…)`.

## Run static code analysis

```bash
# All static code analyzers
ddev cgl sca

# Specific static code analyzers
ddev cgl sca:php

# Composer dependency analysis
ddev cgl analyze

# Rector migrations
ddev cgl migration
```

## Run tests

```bash
ddev composer test
```

## Commit messages

Commit messages follow [Conventional Commits](https://www.conventionalcommits.org/). Allowed types are `chore`, `docs`, `feat`, `fix`, `refactor` and `test`, e.g. `feat: add season preview`. A `commit-msg` hook installed by `ddev start` validates every message.

## Create a release

Releases are prepared with [version-bumper](https://github.com/eliashaeussler/version-bumper). It derives the next version from the Conventional Commits since the last tag, writes it to `extra.typo3/cms.version` in `composer.json` (shown as extension version in TYPO3), updates `composer.lock` and creates a release commit. `main` is protected, so the release commit goes through a pull request:

```bash
git switch -c release/x.y.z origin/main

# Preview the next version
ddev composer bump-version --dry-run --release

# Create the release commit
ddev composer bump-version --release
git push -u origin release/x.y.z
```

Pass a range (`major`, `minor`, `patch`) or an explicit version to override the detected one, e.g. `ddev composer bump-version 0.1.0 --release` for the first release.

After the pull request is merged, tag the release commit on `main`, e.g. with a GitHub release (tag `x.y.z`, target `main`). version-bumper does not create the tag: "Rebase and merge" gives the commit a new hash, so a tag created on the release branch would not point to a commit on `main`.

The version lives in `extra` on purpose: Composer and Packagist take the version from the Git tag and ignore this field.

Commits created inside DDEV use the Git identity of the container. To commit with your own name, add a `.gitconfig` with your `[user]` section to `~/.ddev/homeadditions/` and run `ddev restart`.

## Submit a pull request

After completing your work, **open a pull request** and provide a description of your changes. Ideally, your PR should reference an issue that explains the problem you are addressing.

All mentioned code quality tools will run automatically on every pull request. For more details, see the relevant [workflows][1].

[1]: .github/workflows

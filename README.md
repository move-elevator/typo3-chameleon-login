<div align="center">

<img alt="TYPO3 Chameleon Login Logo" height="150" src="Resources/Public/Icons/Extension.svg" width="150">

# TYPO3 extension `typo3_chameleon_login`

![TYPO3](https://img.shields.io/badge/TYPO3-14.3-orange.svg)
[![CGL](https://img.shields.io/github/actions/workflow/status/move-elevator/typo3-chameleon-login/cgl.yml?label=cgl&logo=github)](https://github.com/move-elevator/typo3-chameleon-login/actions/workflows/cgl.yml)
[![Tests](https://img.shields.io/github/actions/workflow/status/move-elevator/typo3-chameleon-login/tests.yml?label=tests&logo=github)](https://github.com/move-elevator/typo3-chameleon-login/actions/workflows/tests.yml)
[![License](https://poser.pugx.org/move-elevator/typo3-chameleon-login/license)](LICENSE.md)

</div>

This extension changes the background image of the TYPO3 backend login with the seasons. Editors maintain season records such as "Christmas", "Summer" or "Sale week" with a period, an image and an optional background color. A scheduled command writes the currently active season into a config file, which the extension merges into `TYPO3_CONF_VARS` on bootstrap.

## ✨ Features

- Season records with title, image, period, optional background color and priority
- Several images per season, a random one is shown on each visit of the login screen
- Animated color gradient as an alternative to the background image
- Seasonal branding: login logo, highlight color and footnote, e.g. a red button and "Happy holidays" at Christmas
- Optional animated effects per season: snowfall, falling leaves, cherry blossoms, confetti or rising hearts
- Yearly recurring seasons, including periods across New Year (e.g. 20.12. – 06.01.)
- Higher priority wins when several seasons overlap
- Changes to season records take effect on the login screen immediately after saving
- Schedulable console command `chameleon-login:apply` to switch seasons automatically
- No changes to `additional.php` needed, the extension loads its configuration itself

## 🔥 Installation

### Requirements

- TYPO3 14.3
- PHP 8.4+

### Composer

```bash
composer require move-elevator/typo3-chameleon-login
```

Afterwards, set up the extension and flush the caches:

```bash
vendor/bin/typo3 extension:setup
vendor/bin/typo3 cache:flush
```

## 🚀 Quick start

1. Create a sys folder, e.g. "Login seasons".
2. Add records of type "Login season" with the list module.
3. Required fields: title, valid from, valid until and either an image or at least two gradient colors. Background images must be AVIF or WebP, logos SVG, AVIF or WebP. The extension delivers the original file, so only compressed modern formats are accepted.
4. Optional fields: background color, effect, logo, highlight color, footnote, yearly recurring, priority, disabled.
5. Schedule the command `chameleon-login:apply` in the scheduler module, e.g. daily at night.

Saving, hiding, deleting or moving a season record applies the active season right away. The scheduled command is still needed so the login screen switches to the next season when its period starts.

The list view sorts records by priority and shows title, period, recurring and priority by default.

## ⚙️ Configuration

The extension writes `config/system/typo3_chameleon_login.generated.php` whenever season records change and whenever the command runs. It sets:

| Setting | Description |
|---------|-------------|
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']['loginBackgroundImage']` | Public URL of the active season's image |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['loginBackgroundImages']` | All image URLs of the active season if it has more than one. The login screen picks one at random, `loginBackgroundImage` holds the first one as fallback |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']['loginLogo']` and `['loginLogoAlt']` | Public URL and alternative text of the active season's logo |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']['loginHighlightColor']` | Highlight color of the active season (hex) |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']['loginFootnote']` | Footnote of the active season |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['loginBackgroundColor']` | Background color of the active season (hex), rendered as CSS custom property `--chameleon-login-bg-color` |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['loginBackgroundGradient']` | Gradient colors (hex) of the active season, if it uses the background type "Animated gradient" |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['loginBackgroundGradientSpeed']` | Gradient animation speed: `slow`, `medium` or `fast` |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['effect']` | Effect of the active season, e.g. `snow` |

If no season is active, the file contains an empty array and the TYPO3 defaults apply. Do not edit the file manually, the next change or run overwrites it.

## 📚 Documentation

### Run manually

```bash
vendor/bin/typo3 chameleon-login:apply
```

The new values take effect on the backend login with the next request.

### Season selection

The command checks all enabled seasons, ordered by priority (highest first), then by uid. The first season that matches today wins.

- **One-time seasons** match between the "valid from" and "valid until" dates.
- **Recurring seasons** only compare day and month, so they match every year. Periods across New Year are supported.

### Animated gradient

Instead of an image, a season can use the background type "Animated gradient" with two to four colors. The gradient slowly drifts across the login screen. For users who prefer reduced motion, it is shown as a static gradient.

### Effects

A season can show an animated effect on the login screen:

| Effect | Description |
|--------|-------------|
| `snow` | Falling snowflakes |
| `leaves` | Falling autumn leaves |
| `blossoms` | Drifting cherry blossom petals |
| `confetti` | Colorful confetti, e.g. for New Year |
| `hearts` | Rising hearts, e.g. for Valentine's Day |

Effects are drawn on a single `<canvas>` behind the login form and only load on the login screen. They respect `prefers-reduced-motion` and pause while the browser tab is hidden.

## 🧑‍💻 Contributing

Please have a look at [`CONTRIBUTING.md`](CONTRIBUTING.md).

## ⭐ License

This project is licensed under [GNU General Public License 2.0 (or later)](LICENSE.md).

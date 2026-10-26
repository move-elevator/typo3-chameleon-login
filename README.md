<div align="center">

<img alt="TYPO3 Chameleon Login Logo" height="150" src="Resources/Public/Icons/Extension.svg" width="150">

# TYPO3 extension `typo3_chameleon_login`

![TYPO3](https://img.shields.io/badge/TYPO3-14.3-orange.svg)
[![CGL](https://img.shields.io/github/actions/workflow/status/move-elevator/typo3-chameleon-login/cgl.yml?label=cgl&logo=github)](https://github.com/move-elevator/typo3-chameleon-login/actions/workflows/cgl.yml)
[![Tests](https://img.shields.io/github/actions/workflow/status/move-elevator/typo3-chameleon-login/tests.yml?label=tests&logo=github)](https://github.com/move-elevator/typo3-chameleon-login/actions/workflows/tests.yml)
[![License](https://poser.pugx.org/move-elevator/typo3-chameleon-login/license)](LICENSE.md)

</div>

This extension changes the background image of the TYPO3 backend login with the seasons. Editors maintain season records such as "Christmas", "Summer" or "Sale week" with a period, an image and an optional background color. Whenever the login screen is shown, the extension determines the currently active season and merges its settings into `TYPO3_CONF_VARS`.

## ✨ Features

- Season records with title, image, period, optional background color and priority
- Several images per season, a random one is shown on each visit of the login screen
- Animated color gradient as an alternative to the background image
- Seasonal branding: login logo, highlight color and footnote, e.g. a red button and "Happy holidays" at Christmas
- Dark mode variants of background images, background color, gradient colors and highlight color
- Optional animated effects per season: snowfall, falling leaves, cherry blossoms, confetti or rising hearts
- Yearly recurring seasons, including periods across New Year (e.g. 20.12. – 06.01.)
- Higher priority wins when several seasons overlap
- Seasons switch automatically when their period starts, no scheduler task needed
- Changes to season records take effect on the login screen immediately after saving
- No changes to `additional.php` needed

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

1. Open the module "Site → Login seasons" and create a season.
2. Required fields: title, valid from, valid until and either an image or at least two gradient colors. Background images must be AVIF or WebP, logos SVG, AVIF or WebP. The extension delivers the original file, so only compressed modern formats are accepted.
3. Optional fields: background color, effect, logo, highlight color, footnote, dark mode variants, yearly recurring, priority, disabled.

The module lists all seasons by priority and shows which one is currently active on the login screen, which ones are overridden by a season with a higher priority, and which ones are upcoming, expired or disabled.

Seasons are stored on the root level (pid 0), so no sys folder is needed.

### Permissions for editors

Non-admin editors need a backend group with:

- Access to the module "Login seasons" (Access Lists → Modules)
- Read and write access to the tables "Login Season" and "File Reference" (Access Lists → Tables)
- The allowed excludefield "File Reference: Image manipulation" to set a focus area (Access Lists → Allowed excludefields)
- A file mount with the season images and logos

### Upgrading from 0.1

Version 0.1 stored seasons in a sys folder. Run the upgrade wizard "Move login seasons to the root level" to move existing seasons to the root level:

```bash
vendor/bin/typo3 upgrade:run typo3ChameleonLogin_seasonRootLevelMigration
```

Afterwards, the sys folder can be deleted.

## ⚙️ Configuration

On the login, password reset and MFA screens, the extension merges the active season into `TYPO3_CONF_VARS`. It sets:

| Setting | Description |
|---------|-------------|
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']['loginBackgroundImage']` | Public URL of the active season's image |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['loginBackgroundImages']` | All images of the active season, each with `url` and `position` (focus area as `background-position`, empty without focus area). The login screen picks one at random, `loginBackgroundImage` holds the first URL as fallback |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']['loginLogo']` and `['loginLogoAlt']` | Public URL and alternative text of the active season's logo |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']['loginHighlightColor']` | Highlight color of the active season (hex) |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['backend']['loginFootnote']` | Footnote of the active season |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['loginBackgroundColor']` | Background color of the active season (hex), rendered as CSS custom property `--chameleon-login-bg-color` |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['loginBackgroundGradient']` | Gradient colors (hex) of the active season, if it uses the background type "Animated gradient" |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['loginBackgroundGradientSpeed']` | Gradient animation speed: `slow`, `medium` or `fast` |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['effect']` | Effect of the active season, e.g. `snow` |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['loginBackgroundImagesDark']` | Images for dark mode in the same format, one is picked at random |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['loginBackgroundColorDark']` | Background color for dark mode (hex) |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['loginBackgroundGradientDark']` | Gradient colors for dark mode (hex) |
| `$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['typo3_chameleon_login']['loginHighlightColorDark']` | Highlight color for dark mode (hex) |

If no season is active, nothing is changed and the TYPO3 defaults apply.

## 📚 Documentation

### Season selection

The extension checks all enabled seasons, ordered by priority (highest first), then by uid. The first season that matches today wins.

- **One-time seasons** match between the "valid from" and "valid until" dates.
- **Recurring seasons** only compare day and month, so they match every year. Periods across New Year are supported.

### Animated gradient

Instead of an image, a season can use the background type "Animated gradient" with two to four colors. The gradient slowly drifts across the login screen. For users who prefer reduced motion, it is shown as a static gradient.

### Image focus area

Background images fill the height of the screen. On narrow screens, parts of the image are cut off on the sides. To keep text or a logo in the image visible, open the image editor of a background image and move the focus area onto it. The login screen then positions the image so that the focus area stays in view.

Only the focus area is used: the extension delivers the original file, so a crop in the image editor has no effect. Without a focus area, the image is centered.

### Dark mode

The login screen follows the color scheme of the operating system or browser. A season can define its own background images, background color, gradient colors and highlight color for a dark color scheme. The dark mode fields sit next to their regular counterparts. Empty fields fall back to the regular values. The dark gradient is only used if the season has the background type "Animated gradient" and at least two dark colors.

For the logo, use an SVG that adapts to the color scheme itself, e.g. with `prefers-color-scheme` inside the SVG.

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

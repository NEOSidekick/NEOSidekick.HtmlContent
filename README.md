[![Latest Stable Version](https://poser.pugx.org/neosidekick/htmlcontent/v/stable)](https://packagist.org/packages/codeq/htmlcontent)
[![License](https://poser.pugx.org/neosidekick/htmlcontent/license)](LICENSE)

# HTML Content nodes for Neos CMS

#### The successor of [CodeQ.HtmlWidget](https://github.com/code-q-web-factory/neos-htmlwidget)

Create content nodes with HTML code, e.g. external players or iframe embeds. Manage who can create and editor those nodes with the role `NEOSidekick.HtmlContent:HtmlWidgetDefinitionEditor`.

## Features

* Validates HTML and prevents the backend from breaking by not rendering broken HTML
* Automatically removes JavaScript code in the backend, to not break the Neos Administration
* Used media assets can be set on the node, so these assets can not be deleted.


[![HTML Content Demo](https://img.youtube.com/vi/QLe6tRWsYWQ/0.jpg)](https://youtu.be/QLe6tRWsYWQ)

*The development and the public-releases of this package are generously sponsored by [Code Q Web Factory](http://codeq.at).*

## Installation

NEOSidekick.HtmlContent is available via packagist. `"neosidekick/htmlcontent" : "~2.0"` to the require section of the composer.json or run:

```bash
composer require neosidekick/htmlcontent
```

We use semantic-versioning so every breaking change will increase the major-version number.

## You want to define your own NodeType or different Fusion?

Just set the NodeType to abstract and do your own thing:
```yaml
'NEOSidekick.HtmlContent:Content.Html':
  abstract: true
```
```yaml
'YOUR.PACKAGE:Content.Html':
  superTypes:
    'NEOSidekick.HtmlContent:Content.Html': true
```

```neosfusion
prototype(YOUR.PACKAGE:Content.Html) < prototype(Neos.Neos:ContentComponent) {
  renderer = afx`
    <div class="container">
      <NEOSidekick.HtmlContent:Content.Html isEditable={false}/>
    </div>
  `
}
```

## License

Licensed under MIT, see [LICENSE](LICENSE)

## Contribution

We will gladly accept contributions. Please send us pull requests.

### Testing

The package contains unit tests to verify the HTML parsing and validation logic.
You can run the tests using PHPUnit.

If you are using ddev (recommended):

```bash
ddev exec bin/phpunit -c UnitTests.xml DistributionPackages/NEOSidekick.HtmlContent/Tests/Unit
```

If you are running it directly:

```bash
bin/phpunit -c UnitTests.xml DistributionPackages/NEOSidekick.HtmlContent/Tests/Unit
```

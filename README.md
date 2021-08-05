# DTRT Table

[![GitHub release](https://img.shields.io/github/v/tag/dotherightthing/wpdtrt-table)](https://github.com/dotherightthing/wpdtrt-table/releases) [![Build Status](https://github.com/dotherightthing/wpdtrt-table/workflows/Build%20and%20release%20if%20tagged/badge.svg)](https://github.com/dotherightthing/wpdtrt-table/actions?query=workflow%3A%22Build+and+release+if+tagged%22) [![GitHub issues](https://img.shields.io/github/issues/dotherightthing/wpdtrt-table.svg)](https://github.com/dotherightthing/wpdtrt-table/issues)

Shortcode to author a vertical table with overflow hint and scrollbar.

## Setup and Maintenance

Please read [DTRT WordPress Plugin Boilerplate: Workflows](https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Workflows).

## WordPress Installation

Please read the [WordPress readme.txt](readme.txt).

## WordPress Usage

### Styling

Core CSS properties may be overwritten by changing the variable values in your theme stylesheet.

See `scss/variables/_css.scss`.

### Shortcode

Please use the provided shortcode to embed a table:

```php
<!-- within the editor -->
[wpdtrt_table_shortcode option="value"]

// in a PHP template, as a template tag
<?php echo do_shortcode( '[wpdtrt_table_shortcode option="value"]' ); ?>
```

Options

1. `caption="Demo table"` - Appears as a heading above the table
2. `widths="20%|30%|auto"` - Column widths (`auto` or `N%`), separated by upright bars
3. `headers="Column 1|Column 2|Column 3"` - Column headers (`th`), separated by upright bars
4. `cols="A|B|C|1|2|3|Foo|Bar|Baz"` - Column content (`td`), separated by upright bars; rows are created based on the number of `headers`

## Dependencies

None.

## Demo pages

* Vertical tables: [Don't Believe The Hype - Day 39](https://dontbelievethehype.co.nz/tourdiaries/asia/east-asia/mongolia/39/kharkhorin/)

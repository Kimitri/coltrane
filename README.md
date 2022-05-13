# Coltrane - A command line utility for CSS color transformations

Coltrane is a command line utility to transform CSS (and SVG) colors between different representations. It supports transformations between Display-P3 (with and without alpha), hex, hsl, hsla, rgb and rgba. Currently, it's one of the few tools that support the wide gamut Display-P3 colors.

## Installation

Coltrane is written in PHP and can be installed using Composer: `composer global require kimitri/coltrane`

After this the `coltrane` executable is installed in the Composer binary directory (typically `~/.composer/vendor/bin`). Make sure that directory is included in your `$PATH`.

## Usage

Coltrane is designed to be integrated into various automated workflows and therefore works great in a wide variety of use cases. By default Coltrane reads its input from `stdin` and outputs to `stdout` but you can use plain old files by specifying the `--infile` and `--outfile` (`-i` and `-o`) options.

### Palette alignment

Coltrane also provides a handy palette alignment function and a bunch of built-in palettes. Palette is specified using the `--palette` (`-p`) option and palettes are simple text files consisting of regular hex colors (one color per line) without the preceding # character. This format is supported by the [Lospec website](https://lospec.com/palette-list) (`.HEX` files). To use a custom palette, just give the `--palette` option the path to your palette file.

Coltrane comes with a bunch of retro themed palettes from the [Lospec website](https://lospec.com/palette-list). These built-in palettes can be listed by running `coltrane palettes`. To use a built-in palette, just give the `--palette` option the name of the built-in palette.

### Alpha channel

Some color formats (in Coltrane's case these are display-p3a, hsla and rgba) support alpha channels and Coltrane provides a few ways to specify target alpha values. Alpha values range from 0 (fully transparent) to 1 (fully opaque). Alpha can be set using the `--alpha` (`-a`) option and there are two different modes of operation:

- Use a static alpha value (e.g. `--alpha .8` to use alpha value of 0.80).
- Map alpha to the R, G, B or alpha channel of the input color (e.g. `--alpha r` to use the relative red channel value as alpha). Please note that R, G and B channels are also used with hsla colors. To preserve the original alpha of the input color, make sure to pass the `-a a` option.

### Usage examples

1. Read a CSS file with colors defined as hexadecimal values and output the same CSS with colors transformed into wide gamut Display-P3: `coltrane hex2display-p3 -i source.css -o wide-gamut.css`
2. Take a CSS string from the macOS clipboard, transform all colors defined as `rgb()` into wide gamut Display-P3 and store the resulting CSS back to clipboard: `pbpaste | coltrane rgb2display-p3 | pbcopy`
3. Take a CSS string from the macOS clipboard, transform all colors defined as hexadecimal values into `rgba()` using the original alpha value of the input color, align them to the classic CGA palette and write the resulting CSS into a file: `pbpaste | coltrane hex2rgba -a a -p cga1-hi -o cga-is-still-cool.css`

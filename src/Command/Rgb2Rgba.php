<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;

use Spatie\Color\Rgb;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;

class Rgb2Rgba extends AbstractCommand {
  /**
   * Configures the command
   */
	public function configure(): void {
		$this->setName('rgb2rgba')
		    ->setDescription('Convert rgb color values to rgba.')
		    ->setHelp('Converts rgb color values (e.g. "rgb(121,14,212)") to decimal rgba values (e.g. "rgba(121,14,212,0.8)").')
		    ->addDefaultOptions()
		    ->addAlphaOption();
	}

  /**
   * Transforms the input source code.
   *
   * @param  InputInterface $input  Command input.
   * @param  string         $source Input source code.
   * @return string                 Transformed source code.
   */
	public function transform(InputInterface $input, string $source): string {
		return preg_replace_callback(Regex::RGB, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$color = Rgb::fromString('rgb(' . $matches[1] . ')');
				$aligned = $this->applyPalette($input, $color);
				$rgba = $aligned->toRgba();
				$alpha = $this->alpha($input, $rgba);

				return $rgba->toRgba($alpha);
			}

			return $matches[0];
		}, $source);
	}
}

<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;

use Spatie\Color\Hsl;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;

class Hsl2Hsla extends AbstractCommand {
  /**
   * Configures the command
   */
	public function configure(): void {
		$this->setName('hsl2hsla')
		    ->setDescription('Convert hsl color values to hsla.')
		    ->setHelp('Converts hsl color values (e.g. "hsl(207,22%,10%)") to decimal hsla values (e.g. "hsla(207,22%,10%,0.8)").')
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
		return preg_replace_callback(Regex::HSL, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$color = Hsl::fromString('hsl(' . $matches[1] . ')');
				$aligned = $this->applyPalette($input, $color);
				$hsla = $aligned->toHsla();
				$alpha = $this->alpha($input, $hsla);

				return $hsla->toHsla($alpha);
			}

			return $matches[0];
		}, $source);
	}
}

<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;

use Spatie\Color\Rgba;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;

class Rgba2Rgb extends AbstractCommand {
  /**
   * Configures the command
   */
	public function configure(): void {
		$this->setName('rgba2rgb')
		    ->setDescription('Convert rgba color values to rgb.')
		    ->setHelp('Converts rgba color values (e.g. "rgba(121,14,212,0.8)") to decimal rgb values (e.g. "rgb(121,14,212)").')
		    ->addDefaultOptions();
	}

  /**
   * Transforms the input source code.
   *
   * @param  InputInterface $input  Command input.
   * @param  string         $source Input source code.
   * @return string                 Transformed source code.
   */
	public function transform(InputInterface $input, string $source): string {
		return preg_replace_callback(Regex::RGBA, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$color = Rgba::fromString('rgba(' . $matches[1] . ')');
				$aligned = $this->applyPalette($input, $color);
				
				return $aligned->toRgb();
			}

			return $matches[0];
		}, $source);
	}
}

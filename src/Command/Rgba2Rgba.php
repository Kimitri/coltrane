<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

use Spatie\Color\Rgba;
use Spatie\Color\Color;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;

class Rgba2Rgba extends AbstractCommand {
  /**
   * Configures the command
   */
	public function configure(): void {
		$this->setName('rgba2rgba')
		    ->setDescription('Apply palette and/or alpha transformation to rgba color values.')
		    ->setHelp('Applies palette and/or alpha transformation to rgba color values (e.g. "rgba(121,14,212,0.8)").')
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
		return preg_replace_callback(Regex::RGBA, function(array $matches) use ($input) {
			if (count($matches) > 1) {
				$color = Rgba::fromString('rgba(' . $matches[1] . ')');
        $original_alpha = $color->alpha();
				$aligned = $this->applyPalette($input, $color);
				$rgba = $aligned->toRgba($original_alpha);
				$alpha = $this->alpha($input, $rgba);

				return $rgba->toRgba($alpha);
			}

			return $matches[0];
		}, $source);
	}

	/**
	 * Defines the alpha option used with hsla and rgba colors.
	 */
	protected function addAlphaOption(): self {
		return $this->addOption('alpha', 'a', InputOption::VALUE_OPTIONAL, 'Alpha value (between 0.0 and 1.0) or component (r, g, b or a) to use as alpha', null);
	}

	/**
	 * Gets the desired alpha value.
	 * 
	 * @param  InputInterface 	$input Command input.
	 * @param  Color          	$color Color.
	 * @return float 									 Alpha value (0..1).
	 */
	public function alpha(InputInterface $input, Color $color): float {
    if ($input->getOption('alpha')) {
      return parent::alpha($input, $color);
    }
    
    return floatval($color->alpha());
	}
}

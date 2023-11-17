<?php
namespace Coltrane\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

use Spatie\Color\Color;

use Coltrane\Command\AbstractCommand;
use Coltrane\Regex;
use Coltrane\Color\DisplayP3a;

class DisplayP3a2Rgba extends AbstractCommand {
  public function configure(): void {
    $this->setName('display-p3a2rgba')
        ->setDescription('Convert Display-P3a color values to rgba.')
        ->setHelp('Converts Display-P3a color values (e.g. "color(display-p3 0.1765 0.3059 0.4353 / 0.8)") to rgba values (e.g. "rgba(121,14,212,0.8)").')
        ->addDefaultOptions()
        ->addAlphaOption();
  }

  public function transform(InputInterface $input, string $source): string {
    return preg_replace_callback(Regex::DISPLAYP3A, function(array $matches) use ($input) {
      if (count($matches) > 1) {
        $original = DisplayP3a::fromString('color(display-p3 ' . $matches[1] . ')');
        $original_alpha = $original->alpha();
        $aligned = $this->applyPalette($input, $original)->toRgba($original_alpha);
        $alpha = $this->alpha($input, $aligned);
        
        return $aligned->toRgba($alpha);
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

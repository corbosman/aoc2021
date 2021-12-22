#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';

function load() : array
{
    $file = input('inputs/input.txt');

    $enhancer  = str_split($file[0]);
    $pixels    = array_map(fn($i) => str_split($i), array_splice($file, 2));
    $image    = new Image($pixels);
    $enhancer = new Enhancer($enhancer);
    return [$image, $enhancer];
}

class Enhancer {
    // this is alternating between . and # depending on the enhancer.
    public string $background = '.';

    public function __construct(public ?array $enhancer) {}

    public function process(array $pixels) : array
    {
        $processed_image = [];
        $width   = count($pixels[0]);
        $height  = count($pixels);

        for($x=-1; $x<$height+1; $x++) {
            $scanline = [];
            for($y=-1; $y<$width+1; $y++) {
                $pixel = $this->compute_pixel($pixels, $x, $y);
                $scanline[] = $pixel;
            }
            $processed_image[] = $scanline;
        }
        $this->set_background();
        return $processed_image;
    }

    public function compute_pixel(array $pixels, $x, $y) : string
    {
        $neighbor_value = 256;  // top left pixel is worth 256
        $value = 0;

        foreach(range(-1,1) as $dx) {
            foreach(range(-1,1) as $dy) {
                $neighbor_pixel = $this->get_pixel($pixels, $x+$dx, $y+$dy);
                $value += $neighbor_pixel === '#' ? $neighbor_value : 0;
                $neighbor_value >>= 1;
            }
        }
        return $this->enhancer[$value];
    }

    public function get_pixel($pixels, $x, $y) : string
    {
        return $pixels[$x][$y] ?? $this->background;
    }

    public function set_background() : void
    {
        if ($this->enhancer[0] === '.') return;                     // not necessary when enhancer does not swap background
        $this->background = $this->background === '#' ? '.' : '#';  // swap the background color each iteration
    }
}

class Image
{
    public function __construct(public ?array $pixels) {}

    public function process(Enhancer $enhancer, int $iterations) : Image
    {
        for($i=0; $i<$iterations; $i++) {
            $this->pixels = $enhancer->process($this->pixels);
        }
        return $this;
    }

    public function count_lit_pixels() : int
    {
        return count(array_filter(array_merge(...$this->pixels),fn($p)=>$p === '#'));
    }
}

[$image, $enhancer] = load();

$time1              = microtime(true);
$image              = $image->process($enhancer, 2);
$lit_pixels_a       = $image->count_lit_pixels();
$time2              = microtime(true);
$image              = $image->process($enhancer, 48);
$lit_pixels_b       = $image->count_lit_pixels();
$time3              = microtime(true);

solution($lit_pixels_a, $time1, $time2, '20a');
solution($lit_pixels_b, $time2, $time3, '20b');

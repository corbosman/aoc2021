<?php
print('<pre>');
$start = microtime(true);

$handle = fopen('inputs/input.txt', 'r');
if ($handle) {
	
	while (($line = fgets($handle)) !== false) {
		
		if (!empty(trim($line))){
			$data = trim($line);
		}

	}

	fclose($handle);

}

preg_match_all('/[\d-]+/', $data, $data);

$x0 = $data[0][0];
$x1 = $data[0][1];
$y0 = $data[0][2];
$y1 = $data[0][3];

$result = [];

for ($sx = 10; $sx < 300; $sx++){
	for ($sy = -100; $sy < 200; $sy++){
		
		$tx = $sx;
		$ty = $sy;
	
		$x = 0;
		$y = 0;

		$hit = 0;
		$max = -100;
		
		while ($x <= $x1 && $y >= $y0){
			
			$x += $tx;
			$tx = max($tx - 1, 0);
			
			$y += $ty;
			$ty = $ty - 1;
			
			$max = max($y, $max);
			
			if ($x >= $x0 && $x <= $x1 && $y >= $y0 && $y <= $y1){
				$hit = 1;
			}
			
		}
		
		if ($hit){
			$result[$sx.' '.$sy] = $max;
		}

	}
}

print(round((microtime(true) - $start)*1000, 1) . "ms\n\n");

print_r(count($result));
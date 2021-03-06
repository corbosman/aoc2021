#!/usr/bin/env php
<?php
require __DIR__ . '/../../vendor/autoload.php';
$time1 = microtime(true);

function read_transmission() : Generator
{
    foreach(str_split(input('inputs/input.txt')[0]) as $hex) {
        foreach(str_split(substr('000' . decbin(hexdec($hex)), -4)) as $bit) {
            yield $bit;
        }
    }
}

class Packet
{
    const SUM     = 0;
    const PRODUCT = 1;
    const MIN     = 2;
    const MAX     = 3;
    const SCALAR  = 4;
    const GREATER = 5;
    const LOWER   = 6;
    const EQUAL   = 7;

    protected int $value;
    protected int $length = 0;
    protected array $packets = [];

    public function __construct(public int $version, public int $type){}

    public function version() : int
    {
        return $this->version;
    }

    // for 16a
    public function checksum()
    {
        $sum = $this->version();
        foreach($this->packets() as $packet) {
            $sum += $packet->checksum();
        }
        return $sum;
    }

    // for 16b
    public function evaluate()
    {
        $values = map(fn($packet)=>$packet->evaluate(), $this->packets());

        return match($this->type()) {
            self::SUM     => array_sum($values),
            self::PRODUCT => array_product($values),
            self::MIN     => min($values),
            self::MAX     => max($values),
            self::SCALAR  => $this->get_value(),
            self::GREATER => (int) $values[0] > $values[1],
            self::LOWER   => (int) $values[0] < $values[1],
            self::EQUAL   => (int) $values[0] === $values[1],
        };
    }

    public function type() : int
    {
        return $this->type;
    }

    public function set_value(int $value) : void
    {
        $this->value = $value;
    }

    public function get_value() : int
    {
        return $this->value;
    }

    public function set_length(int $length) : void
    {
        $this->length = $length;
    }

    public function get_length() : int
    {
        return $this->length;
    }

    public function add_packets($packets) : void
    {
        $packets = is_array($packets) ? $packets : [$packets];
        foreach ($packets as $packet) {
            $this->packets[] = $packet;
        }
    }

    public function packets() : array
    {
        return $this->packets;
    }
}

class PacketDecoder
{
    protected int $total_bits = 0;

    public function __construct(public generator $stream){}

    public function decode() : Packet
    {
        $version = bindec($this->read_bits(3));
        $type    = bindec($this->read_bits(3));
        $packet  = new Packet($version, $type);

        switch ($packet->type) {
            case Packet::SCALAR:
                $literal = $this->decode_literal();
                $packet->set_value($literal);
                break;
            default:
                $sub_packets = $this->decode_operator();

                foreach($sub_packets as $sub_packet) {
                    $this->total_bits += $sub_packet->get_length();
                }

                $packet->add_packets($sub_packets);
                break;
        }

        $packet->set_length($this->total_bits);
        return $packet;
    }

    protected function decode_literal() : int
    {
        $literal = '';
        do {
            $bits = $this->read_bits(5);
            $literal .= substr($bits, 1);
        } while ($bits[0] == 1);
        return bindec($literal);
    }

    protected function decode_operator() : array
    {
        $mode = bindec($this->read_bits(1));

        return match($mode) {
            0 => $this->read_sub_packets_by_bits(),
            1 => $this->read_sub_packets_by_count()
        };
    }

    protected function read_sub_packets_by_bits() : array
    {
        $packets = [];
        $sub_packet_length = bindec($this->read_bits(15));
        $bits = 0;

        do {
            $sub_packet = (new PacketDecoder($this->stream))->decode();
            $bits += $sub_packet->get_length();
            $packets[] = $sub_packet;
        } while ($bits < $sub_packet_length);

        return $packets;
    }

    protected function read_sub_packets_by_count() : array
    {
        $packets = [];
        $number_of_packets = bindec($this->read_bits(11));

        for($i=0; $i<$number_of_packets; $i++) {
            $packets[] = (new PacketDecoder($this->stream))->decode();
        }

        return $packets;
    }

    protected function read_bits($num) : string
    {
        $bits='';
        for($i=0; $i<$num; $i++) {
            $bits .= $this->stream->current();
            $this->total_bits += 1;
            $this->stream->next();
        }
        return $bits;
    }
}

$msg    = read_transmission();
$packet = (new PacketDecoder($msg))->decode();
$output = $packet->checksum();
$time2  = microtime(true);

solution($output, $time1, $time2, '16a');

$time1  = microtime(true);
$output = $packet->evaluate();
$time2  = microtime(true);
solution($output, $time1, $time2, '16b');

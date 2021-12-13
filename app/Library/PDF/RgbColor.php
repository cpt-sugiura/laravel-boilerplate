<?php

namespace App\Library\PDF;

class RgbColor
{
    public int $r;
    public int $g;
    public int $b;

    /**
     * FontColor constructor.
     * @param int $r
     * @param int $g
     * @param int $b
     */
    public function __construct(int $r, int $g, int $b)
    {
        $this->r = $r;
        $this->g = $g;
        $this->b = $b;
    }

    public function forFpdiArray(): array
    {
        return [$this->r, $this->g, $this->b];
    }
}

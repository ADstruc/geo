<?php

namespace Brick\Geo\Tests\IO;

use Brick\Geo\IO\WKBTools;
use Brick\Geo\IO\WKBWriter;
use Brick\Geo\IO\WKTReader;
use Brick\Geo\Point;

/**
 * Unit tests for class WKBWriter.
 */
class WKBWriterTest extends WKBAbstractTest
{
    /**
     * @dataProvider providerWrite
     *
     * @param string  $wkt       The WKT to read.
     * @param string  $wkb       The expected WKB output, hex-encoded.
     * @param integer $byteOrder The byte order to use.
     */
    public function testWrite($wkt, $wkb, $byteOrder)
    {
        $writer = new WKBWriter();
        $writer->setByteOrder($byteOrder);

        $reader = new WKTReader();

        $geometry = $reader->read($wkt);
        $output = $writer->write($geometry);

        $this->assertSame($wkb, bin2hex($output));
    }

    /**
     * @return \Generator
     */
    public function providerWrite()
    {
        foreach ($this->providerLittleEndianWKB() as list($wkt, $wkb)) {
            yield [$wkt, $wkb, WKBTools::LITTLE_ENDIAN];
        }

        foreach ($this->providerBigEndianWKB() as list($wkt, $wkb)) {
            yield [$wkt, $wkb, WKBTools::BIG_ENDIAN];
        }
    }

    /**
     * @dataProvider providerWriteEmptyPointThrowsException
     * @expectedException \Brick\Geo\Exception\GeometryIOException
     *
     * @param Point $point
     */
    public function testWriteEmptyPointThrowsException(Point $point)
    {
        $writer = new WKBWriter();
        $writer->write($point);
    }

    /**
     * @return array
     */
    public function providerWriteEmptyPointThrowsException()
    {
        return [
            [Point::xyEmpty()],
            [Point::xyzEmpty()],
            [Point::xymEmpty()],
            [Point::xyzmEmpty()]
        ];
    }
}

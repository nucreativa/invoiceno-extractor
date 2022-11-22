<?php

declare(strict_types=1);

use Nucreativa\InvoiceNoExtractor\Extractor;
use PHPUnit\Framework\TestCase;

final class ExtractorTest extends TestCase
{
    public function testCanDetectSingleInvoice(): void
    {
        $sample = "CF220901571_C17-173332";

        $extractor = new Extractor();
        $extractedInvoices = $extractor->extract($sample);
        $expectedInvoices = ['C17-173332'];

        $this->assertEqualsCanonicalizing($expectedInvoices, $extractedInvoices);
    }

    public function testCanDetectSingleInvoiceInRandomText(): void
    {
        $sample = "BO BAGI KPNO BAG INTERNASIONAL BY JS RPLNS ATM OFF PRMSS O/VNDR PT BSI BLN AGT22 INV.NOC01-174354-3 5 IN99992210072553";

        $extractor = new Extractor();
        $extractedInvoices = $extractor->extract($sample);
        $expectedInvoices = ['C01-174354'];

        $this->assertEqualsCanonicalizing($expectedInvoices, $extractedInvoices);
    }

    public function testCanDetectMultiInvoiceWithPrefixByUnderscore(): void
    {
        $sample = "CF220901571_C17-173332_35";

        $extractor = new Extractor();
        $extractedInvoices = $extractor->extract($sample);
        $expectedInvoices = [
            'C17-173332',
            'C17-173333',
            'C17-173334',
            'C17-173335'
        ];

        $this->assertEqualsCanonicalizing($expectedInvoices, $extractedInvoices);
    }

    public function testCanDetectMultiInvoiceWithPrefixByComma(): void
    {
        $sample = "CF221001384_C01-175714,24,35,36/DNMRP/OCT/22";

        $extractor = new Extractor();
        $extractedInvoices = $extractor->extract($sample);
        $expectedInvoices = [
            'C01-175714',
            'C01-175724',
            'C01-175735',
            'C01-175736'
        ];

        $this->assertEqualsCanonicalizing($expectedInvoices, $extractedInvoices);
    }

    public function testCanDetectMultiInvoiceWithPrefixCombinationUnderscoreAndComma(): void
    {
        $sample = "CF221001384_C01-175714_15,24,35_36/DNMRP/OCT/22";

        $extractor = new Extractor();
        $extractedInvoices = $extractor->extract($sample);
        $expectedInvoices = [
            'C01-175714',
            'C01-175715',
            'C01-175724',
            'C01-175735',
            'C01-175736'
        ];

        $this->assertEqualsCanonicalizing($expectedInvoices, $extractedInvoices);
    }

    public function testCanDetectMultiInvoiceWithPrefixAndChangePrefix(): void
    {
        $sample = "CF221001384_C01-175714_15,24,C02-175735_36/DNMRP/OCT/22";

        $extractor = new Extractor();
        $extractedInvoices = $extractor->extract($sample);
        $expectedInvoices = [
            'C01-175714',
            'C01-175715',
            'C01-175724',
            'C02-175735',
            'C02-175736'
        ];

        $this->assertEqualsCanonicalizing($expectedInvoices, $extractedInvoices);
    }

    public function testCanDetectMultiInvoiceWithPrefixInMiddleRandomText(): void
    {
        $sample = "^C01-173744/BN-RP/AUG/22danC01-173745/BN-RP/AUG/22";

        $extractor = new Extractor();
        $extractedInvoices = $extractor->extract($sample);
        $expectedInvoices = [
            'C01-173744',
            'C01-173745'
        ];

        $this->assertEqualsCanonicalizing($expectedInvoices, $extractedInvoices);
    }

    public function testCanDetectMultiInvoiceWithPrefixInMiddleRandomText2(): void
    {
        $sample = "BO PT OTSUKA DISTRIBUTION INDONESIA 3300001431 C01-173166/08/22 C01-174627/09/22 IN99992210031608";

        $extractor = new Extractor();
        $extractedInvoices = $extractor->extract($sample);
        $expectedInvoices = [
            'C01-173166',
            'C01-174627'
        ];

        $this->assertEqualsCanonicalizing($expectedInvoices, $extractedInvoices);
    }

    public function testCanDetectMultiInvoiceInOrderAndFixInvoice(): void
    {
        $sample = "CF220901571_C17-173332, C06-173269, C02- 173306_08";

        $extractor = new Extractor();
        $extractedInvoices = $extractor->extract($sample);
        $expectedInvoices = [
            'C02-173306',
            'C02-173307',
            'C02-173308',
            'C06-173269',
            'C17-173332'
        ];

        $this->assertEqualsCanonicalizing($expectedInvoices, $extractedInvoices);
    }
}

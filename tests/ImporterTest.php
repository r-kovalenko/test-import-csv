<?php

namespace Tests;

use Importer\Importer;
use Importer\Report;
use PHPUnit\Framework\TestCase;

/**
 * Class ImporterTest
 * @package Tests
 */
class ImporterTest extends TestCase
{
    /**
     * Tests Importer::process
     */
    public function testProcess()
    {
        /** @var Importer $importer */
        $importer = $this->createImporter();
        $result   = $importer->process($this->getFile(), $this->getMapping());

        // 2 merchants
        $this->assertEquals(2, $result->getMerchantCount());

        // with 3 batches
        $this->assertEquals(3, $result->getBatchCount());

        // with 5 transactions
        $this->assertEquals(5, $result->getTransactionCount());
    }

    /**
     * Creates an importer instance for testing purposes
     */
    private function createImporter()
    {
        $stmt1 = $this->createMock(\PDOStatement::class);
        $stmt1->expects($this->exactly(1))
            ->method('execute')
            ->willReturn(true);
        $stmt1->expects($this->exactly(1))
            ->method('rowCount')
            ->willReturn(2);

        $stmt2 = $this->createMock(\PDOStatement::class);
        $stmt2->expects($this->exactly(1))
            ->method('execute')
            ->willReturn(true);
        $stmt2->expects($this->exactly(1))
            ->method('rowCount')
            ->willReturn(3);

        $stmt3 = $this->createMock(\PDOStatement::class);
        $stmt3->expects($this->exactly(1))
            ->method('execute')
            ->willReturn(true);
        $stmt3->expects($this->exactly(1))
            ->method('rowCount')
            ->willReturn(5);

        $pdo = $this->createMock('PDO');

        $arg1 = "INSERT IGNORE INTO merchants (mid,dba) VALUES ('344858307505959269','Merchant #344858307505959269'),('344858307505959269','Merchant #344858307505959269'),('344858307505959269','Merchant #344858307505959269'),('79524081202206784','Merchant #79524081202206784'),('79524081202206784','Merchant #79524081202206784')";
        $arg2 = "INSERT IGNORE INTO batches (batch_date,batch_ref_num,mid,batch_id) VALUES ('2018-05-05','307965163216534420635657','344858307505959269','344858307505959269_307965163216534420635657_2018-05-05'),('2018-05-05','713911985564755663442139','344858307505959269','344858307505959269_713911985564755663442139_2018-05-05'),('2018-05-05','713911985564755663442139','344858307505959269','344858307505959269_713911985564755663442139_2018-05-05'),('2018-05-05','865311392860455095554114','79524081202206784','79524081202206784_865311392860455095554114_2018-05-05'),('2018-05-05','865311392860455095554114','79524081202206784','79524081202206784_865311392860455095554114_2018-05-05')";
        $arg3 = "INSERT IGNORE INTO transactions (trans_id,trans_date,trans_type,trans_card_type,trans_card_num,trans_amount,batch_id) VALUES ('1','2018-05-04','Sale','VI','803158******3281','20.94','344858307505959269_307965163216534420635657_2018-05-05'),('2','2018-05-04','Sale','VI','821278******8615','64.98','344858307505959269_713911985564755663442139_2018-05-05'),('3','2018-05-04','Sale','DC','909582******9260','4.04','344858307505959269_713911985564755663442139_2018-05-05'),('4','2018-05-04','Sale','VI','437054******9193','68.26','79524081202206784_865311392860455095554114_2018-05-05'),('5','2018-05-04','Refund','VI','877098******7670','-56.27','79524081202206784_865311392860455095554114_2018-05-05')";
        $pdo->expects($this->exactly(3))
            ->method('prepare')
            ->withConsecutive([$arg1], [$arg2], [$arg3])
            ->willReturnOnConsecutiveCalls($stmt1, $stmt2, $stmt3);

        return new Importer($pdo);
    }

    /**
     * Gets a sample report
     *
     * @return string Full path to a sample report
     */
    private function getFile(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'samples' . DIRECTORY_SEPARATOR . 'small.csv';
    }

    /**
     * Gets a sample mapping
     *
     * @return string[] Sample mapping
     */
    private function getMapping(): array
    {
        return [
            Report::TRANSACTION_DATE        => 'Transaction Date',
            Report::TRANSACTION_TYPE        => 'Transaction Type',
            Report::TRANSACTION_CARD_TYPE   => 'Transaction Card Type',
            Report::TRANSACTION_CARD_NUMBER => 'Transaction Card Number',
            Report::TRANSACTION_AMOUNT      => 'Transaction Amount',
            Report::BATCH_DATE              => 'Batch Date',
            Report::BATCH_REF_NUM           => 'Batch Reference Number',
            Report::MERCHANT_ID             => 'Merchant ID',
            Report::MERCHANT_NAME           => 'Merchant Name',
        ];
    }
}

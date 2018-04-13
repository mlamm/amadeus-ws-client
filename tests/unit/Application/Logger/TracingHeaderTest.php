<?php

use Flight\Service\Amadeus\Application\Logger\TracingHeader;
use Symfony\Component\HttpFoundation\Request;

/**
 * TracingHeaderTest.php
 *
 * @covers Flight\Service\Amadeus\Application\Logger\TracingHeader
 *
 * @copyright Copyright (c) 2017 Invia Flights Germany GmbH
 * @author    Invia Flights Germany GmbH <teamleitung-dev@invia.de>
 * @author    Fluege-Dev <fluege-dev@invia.de>
 */
class TracingHeaderTest extends \Codeception\Test\Unit
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * Test parsing the tracing information from header
     */
    public function testWithCorrectSubmittedTracingHeader()
    {
        $header = '417f344f1a9f2e3bcf54b4dceb8140a1-/2';

        $tracingHeader = new TracingHeader($header);

        $this->assertSame('417f344f1a9f2e3bcf54b4dceb8140a1-', $tracingHeader->getTraceId());
        $this->assertSame(2, $tracingHeader->getSpanId());
    }

    /**
     * Test generating new tracing header data if parsing was not successfully
     */
    public function testWithIncorrectSubmittedTracingHeader()
    {
        $header = 'foobarbarz';

        $request = new Request();
        $request->headers->set(TracingHeader::TRACING_HEADER_NAME, $header);

        $tracingHeader = new TracingHeader($request);
        $this->assertRegExp('@^(\w{32})$@', $tracingHeader->getTraceId());
        $this->assertSame(0, $tracingHeader->getSpanId());
    }

    /**
     * Test generating new tracing header data if no tracing header was submitted
     */
    public function testWithoutSubmittedTracingHeader()
    {
        $tracingHeader = new TracingHeader(new Request());

        $this->assertRegExp('@^(\w{32})$@', $tracingHeader->getTraceId());
        $this->assertSame(0, $tracingHeader->getSpanId());
    }

    /**
     * Test enriching the log record with tracing header data
     *
     * @param array $record
     *
     * @dataProvider applyDataProvider
     */
    public function testProcessLogRecord(array $record)
    {
        $header = '417f344f1a9f2e3bcf54b4dceb8140a1/1';

        $tracingHeader = new TracingHeader($header);

        $expected = [
            'trace' => [
                'id'   => '417f344f1a9f2e3bcf54b4dceb8140a1',
                'span' => 1
            ]
        ];

        $this->assertSame(array_merge($record, $expected), $tracingHeader->processLogRecord($record));
    }

    /**
     * Data provider for testProcessLogRecord
     *
     * @return array
     */
    public function applyDataProvider(): array
    {
        $data = [];

        $data['with empty record'] = [[]];

        $data['with filled record'] = [
            [
                'data_one' => 'foo',
                'data_two' => 'bar'
            ]
        ];

        return $data;
    }
}
